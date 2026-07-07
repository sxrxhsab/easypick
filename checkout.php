<?php
session_start();
require_once 'db.php';
require_once 'config-stripe.php';

// Vérifier que le panier n'est pas vide
if (empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit;
}

// Récupérer les articles du panier
$panier = $_SESSION['panier'];
$articles = [];
$total = 0;

if (!empty($panier)) {
    $ids = array_keys($panier);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produits = $stmt->fetchAll();

    foreach ($produits as $produit) {
        $id = $produit['id'];
        $qte = $panier[$id];
        $sous_total = $produit['prix'] * $qte;
        $articles[] = [
            'id' => $id,
            'nom' => $produit['nom'],
            'prix' => $produit['prix'],
            'quantite' => $qte,
            'sous_total' => $sous_total
        ];
        $total += $sous_total;
    }
}
$nb_articles = array_sum($panier);

// Si le formulaire est soumis, on crée la session Stripe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $code_postal = trim($_POST['code_postal']);
    $ville = trim($_POST['ville']);
    $pays = trim($_POST['pays']);

    // Stocker les infos dans une session temporaire pour la confirmation
    $_SESSION['checkout_data'] = [
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'adresse' => $adresse,
        'code_postal' => $code_postal,
        'ville' => $ville,
        'pays' => $pays,
        'total' => $total,
        'articles' => $articles
    ];

    try {
        // Créer la session Stripe Checkout
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => array_map(function($article) {
                return [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $article['nom'],
                        ],
                        'unit_amount' => $article['prix'] * 100, // en centimes
                    ],
                    'quantity' => $article['quantite'],
                ];
            }, $articles),
            'mode' => 'payment',
            'success_url' => STRIPE_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => STRIPE_CANCEL_URL,
            'customer_email' => $email,
            'metadata' => [
                'prenom' => $prenom,
                'nom' => $nom,
                'telephone' => $telephone,
                'adresse' => $adresse,
                'code_postal' => $code_postal,
                'ville' => $ville,
                'pays' => $pays,
            ]
        ]);

        // Rediriger vers Stripe
        header('Location: ' . $session->url);
        exit;

    } catch (Exception $e) {
        $erreur = 'Erreur de paiement : ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Paiement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 30px; }

        .navbar-simple {
            width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center; position: sticky; top: 0; z-index: 1000; padding: 0 30px;
        }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .nav-menu { display: flex; align-items: center; gap: 30px; list-style: none; }
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; align-items: center; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .nav-icons .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 12px rgba(255,106,0,0.4); }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .hero-shop { position: relative; padding: 30px 0 25px; min-height: 160px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); overflow: hidden; display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .hero-shop .container { position: relative; z-index: 1; text-align: center; }
        .hero-shop h1 { font-size: 38px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
        .hero-shop h1 .orange { color: #ff6a00; }
        .hero-shop p { color: rgba(255,255,255,0.5); font-size: 15px; margin-top: 4px; }

        .checkout-section { padding: 40px 0 80px; background: #151515; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 380px; gap: 50px; align-items: start; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: rgba(255,255,255,0.7); }
        .form-group input, .form-group select { width: 100%; padding: 14px 18px; background: #1A1A1A; border: 1px solid rgba(255,255,255,0.06); border-radius: 14px; color: #fff; font-size: 15px; font-family: 'Poppins', sans-serif; outline: none; transition: border-color 0.3s; }
        .form-group input:focus, .form-group select:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.06); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .order-summary { background: #1A1A1A; border-radius: 20px; padding: 28px; border: 1px solid rgba(255,255,255,0.06); position: sticky; top: 90px; }
        .order-summary h3 { font-size: 18px; font-weight: 700; margin-bottom: 16px; }
        .order-summary .item { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: rgba(255,255,255,0.6); border-bottom: 1px solid rgba(255,255,255,0.04); }
        .order-summary .item:last-child { border-bottom: none; }
        .order-summary .item .qty { color: rgba(255,255,255,0.3); }
        .order-summary .total { display: flex; justify-content: space-between; padding: 16px 0 0; margin-top: 12px; border-top: 1px solid rgba(255,255,255,0.06); font-size: 20px; font-weight: 700; }
        .order-summary .total .amount { color: #ff6a00; }

        .payment-section { margin-top: 24px; }
        .payment-section h4 { font-size: 15px; font-weight: 700; margin-bottom: 12px; }
        .payment-section .card-icons { display: flex; gap: 12px; margin-bottom: 16px; }
        .payment-section .card-icons i { font-size: 32px; color: rgba(255,255,255,0.15); transition: color 0.3s; }
        .payment-section .card-icons i:hover { color: #fff; }

        .btn-pay {
            width: 100%; padding: 16px 0; background: linear-gradient(135deg, #ff6a00, #ff7d1a);
            color: #fff; border: none; border-radius: 16px; font-weight: 700; font-size: 18px;
            cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; margin-top: 8px;
        }
        .btn-pay:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }

        .btn-back { display: inline-block; color: rgba(255,255,255,0.3); font-size: 14px; transition: color 0.3s; margin-top: 16px; }
        .btn-back:hover { color: #fff; }

        .error { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff4444; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }

        @media (max-width: 992px) { .checkout-grid { grid-template-columns: 1fr; gap: 35px; } .order-summary { position: relative; top: 0; } }
        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }
            .hero-shop h1 { font-size: 28px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <?php
    $nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
    $user_connecte = isset($_SESSION['user_id']);
    $user_role = $_SESSION['user_role'] ?? '';
    include 'header.php';
    ?>

    <section class="hero-shop">
        <div class="container">
            <h1><span class="orange">PAIEMENT</span></h1>
            <p>Finalisez votre commande en toute sécurité.</p>
        </div>
    </section>

    <section class="checkout-section">
        <div class="container">

            <?php if (isset($erreur)): ?>
                <div class="error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="checkout-grid">

                    <div>
                        <h2 style="font-size:22px; font-weight:700; margin-bottom:20px;">Informations de livraison</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Prénom *</label>
                                <input type="text" name="prenom" required placeholder="Jean" />
                            </div>
                            <div class="form-group">
                                <label>Nom *</label>
                                <input type="text" name="nom" required placeholder="Dupont" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required placeholder="jean.dupont@email.com" />
                        </div>

                        <div class="form-group">
                            <label>Téléphone *</label>
                            <input type="tel" name="telephone" required placeholder="06 12 34 56 78" />
                        </div>

                        <div class="form-group">
                            <label>Adresse *</label>
                            <input type="text" name="adresse" required placeholder="12 rue de la Paix" />
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Code postal *</label>
                                <input type="text" name="code_postal" required placeholder="75001" />
                            </div>
                            <div class="form-group">
                                <label>Ville *</label>
                                <input type="text" name="ville" required placeholder="Paris" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Pays *</label>
                            <select name="pays" required>
                                <option value="France">France</option>
                                <option value="Belgique">Belgique</option>
                                <option value="Suisse">Suisse</option>
                                <option value="Luxembourg">Luxembourg</option>
                                <option value="Canada">Canada</option>
                            </select>
                        </div>

                        <div class="payment-section">
                            <h4>💳 Paiement sécurisé</h4>
                            <div class="card-icons">
                                <i class="fab fa-cc-visa"></i>
                                <i class="fab fa-cc-mastercard"></i>
                                <i class="fab fa-cc-paypal"></i>
                                <i class="fab fa-cc-apple-pay"></i>
                            </div>
                            <p style="color:rgba(255,255,255,0.3); font-size:14px; margin-bottom:12px;">
                                <i class="fas fa-lock" style="color:#ff6a00;"></i>
                                Paiement 100% sécurisé via Stripe. Vos données bancaires ne sont jamais stockées.
                            </p>
                        </div>
                    </div>

                    <div>
                        <div class="order-summary">
                            <h3>Résumé de la commande</h3>

                            <?php foreach ($articles as $article): ?>
                            <div class="item">
                                <span><?= htmlspecialchars($article['nom']) ?> <span class="qty">×<?= $article['quantite'] ?></span></span>
                                <span><?= number_format($article['sous_total'], 2, ',', ' ') ?> €</span>
                            </div>
                            <?php endforeach; ?>

                            <div class="item" style="color:rgba(255,255,255,0.3);">
                                <span>Livraison</span>
                                <span>Offerte</span>
                            </div>

                            <div class="total">
                                <span>Total</span>
                                <span class="amount"><?= number_format($total, 2, ',', ' ') ?> €</span>
                            </div>

                            <button type="submit" class="btn-pay">
                                <i class="fas fa-lock"></i> Payer <?= number_format($total, 2, ',', ' ') ?> €
                            </button>

                            <div style="text-align:center; margin-top:12px;">
                                <a href="panier.php" class="btn-back"><i class="fas fa-arrow-left"></i> Retour au panier</a>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </section>

    <footer style="background:#0F0F0F; padding:30px 0 20px; border-top:1px solid rgba(255,255,255,0.04); text-align:center; color:rgba(255,255,255,0.12); font-size:13px;">
        <div class="container">&copy; 2026 EasyPick – Tous droits réservés.</div>
    </footer>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
    </script>

</body>
</html>