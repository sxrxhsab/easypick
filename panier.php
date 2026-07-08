<?php
session_start();
require_once 'db.php';

$panier = $_SESSION['panier'] ?? [];
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
            'sous_total' => $sous_total,
            'image' => $produit['image'],
        ];
        $total += $sous_total;
    }
}
$nb_articles = array_sum($panier);
// ... le reste du HTML identique
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Mon <?= __('panier') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <style>
        /* ---- Réutilisation du même style que <?= __('boutique') ?>.php ---- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 30px; }

        /* Navbar simplifiée */
        .navbar-simple {
            width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center; position: sticky; top: 0; z-index: 1000; padding: 0 30px;
        }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .nav-menu { display: flex; align-items: center; gap: 30px; list-style: none; }
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; letter-spacing: 0.3px; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-menu li a.active { color: #fff; }
        .navbar-simple .nav-menu li a.active::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; align-items: center; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .nav-icons .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 12px rgba(255,106,0,0.4); }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        /* Hero */
        .hero-shop {
            position: relative; padding: 30px 0 25px; min-height: 160px;
            background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%);
            overflow: hidden; display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .hero-shop .container { position: relative; z-index: 1; text-align: center; }
        .hero-shop h1 { font-size: 38px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
        .hero-shop h1 .orange { color: #ff6a00; }
        .hero-shop p { color: rgba(255,255,255,0.5); font-size: 15px; margin-top: 4px; }

        /* ---- SECTION <?= __('panier') ?> ---- */
        .cart-section { padding: 40px 0 80px; background: #151515; }

        /* <?= __('panier') ?> vide */
        .cart-empty { text-align: center; padding: 60px 0; }
        .cart-empty i { font-size: 72px; color: rgba(255,255,255,0.08); margin-bottom: 20px; }
        .cart-empty h2 { font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        .cart-empty p { color: rgba(255,255,255,0.4); margin-bottom: 25px; }
        .cart-empty .btn-continue { display: inline-block; padding: 14px 40px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border-radius: 60px; font-weight: 700; transition: all 0.3s; }
        .cart-empty .btn-continue:hover { transform: scale(1.05); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }

        /* Tableau du <?= __('panier') ?> */
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table th { text-align: left; padding: 14px 10px; color: rgba(255,255,255,0.3); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .cart-table td { padding: 18px 10px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }

        .cart-table .product-cell { display: flex; align-items: center; gap: 16px; }
        .cart-table .product-cell img { width: 70px; height: 70px; object-fit: contain; background: #1A1A1A; border-radius: 12px; padding: 8px; }
        .cart-table .product-cell .name { font-weight: 600; font-size: 15px; }
        .cart-table .product-cell .name a { color: #fff; transition: color 0.3s; }
        .cart-table .product-cell .name a:hover { color: #ff6a00; }

        .cart-table .qty-cell { display: flex; align-items: center; gap: 6px; }
        .cart-table .qty-cell button { width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.08); background: transparent; color: #fff; cursor: pointer; font-size: 16px; transition: all 0.3s; }
        .cart-table .qty-cell button:hover { background: rgba(255,106,0,0.15); border-color: #ff6a00; }
        .cart-table .qty-cell input { width: 44px; height: 32px; background: #1A1A1A; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; color: #fff; text-align: center; font-size: 15px; font-weight: 600; font-family: 'Poppins', sans-serif; outline: none; }
        .cart-table .qty-cell input:focus { border-color: #ff6a00; }

        .cart-table .price-cell { font-weight: 700; font-size: 17px; color: #ff6a00; }
        .cart-table .<?= __('total') ?>-cell { font-weight: 700; font-size: 18px; color: #ff6a00; }
        .cart-table .remove-cell a { color: rgba(255,255,255,0.2); transition: color 0.3s; font-size: 16px; }
        .cart-table .remove-cell a:hover { color: #ff4444; }

        /* Résumé */
        .cart-summary {
            margin-top: 35px;
            padding: 30px;
            background: #1A1A1A;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.06);
            max-width: 400px;
            margin-left: auto;
        }
        .cart-summary .line { display: flex; justify-content: space-between; padding: 10px 0; color: rgba(255,255,255,0.6); font-size: 15px; }
        .cart-summary .line.<?= __('total') ?> { font-size: 22px; font-weight: 700; color: #fff; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 18px; margin-top: 6px; }
        .cart-summary .line.<?= __('total') ?> .amount { color: #ff6a00; }
        .cart-summary .btn-checkout {
            display: block; width: 100%; padding: 16px 0; background: linear-gradient(135deg, #ff6a00, #ff7d1a);
            color: #fff; border: none; border-radius: 16px; font-weight: 700; font-size: 18px; cursor: pointer;
            transition: all 0.3s; text-align: center; margin-top: 16px; font-family: 'Poppins', sans-serif;
        }
        .cart-summary .btn-checkout:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        .cart-summary .btn-continue-shop { display: block; text-align: center; color: rgba(255,255,255,0.3); font-size: 14px; margin-top: 14px; transition: color 0.3s; }
        .cart-summary .btn-continue-shop:hover { color: #fff; }

        .cart-actions { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }
        .btn-update-cart { padding: 12px 32px; background: #1A1A1A; color: #fff; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-update-cart:hover { background: #ff6a00; border-color: #ff6a00; }

        /* Responsive */
        @media (max-width: 992px) {
            .cart-table { display: block; overflow-x: auto; }
            .cart-summary { max-width: 100%; }
        }

        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu {
                display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%;
                background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06);
                box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }

            .hero-shop h1 { font-size: 28px; }

            .cart-table th { font-size: 11px; padding: 10px 6px; }
            .cart-table td { padding: 12px 6px; }
            .cart-table .product-cell img { width: 50px; height: 50px; }
            .cart-table .product-cell .name { font-size: 13px; }
            .cart-table .price-cell { font-size: 15px; }
            .cart-table .<?= __('total') ?>-cell { font-size: 15px; }
            .cart-table .qty-cell input { width: 36px; height: 28px; font-size: 13px; }
            .cart-table .qty-cell button { width: 28px; height: 28px; font-size: 14px; }

            .cart-summary { padding: 20px; }
            .cart-summary .line.<?= __('total') ?> { font-size: 18px; }

            .btn-update-cart { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR SIMPLIFIÉE ===== -->
    <nav class="navbar-simple">
        <div class="nav-container">
            <a href="index.php" class="logo-text"><span class="easy">EASY</span><span class="pick">PICK</span></a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php"><?= __('accueil') ?></a></li>
                <li><a href="boutique.php"><?= __('boutique') ?></a></li>
                <li><a href="#"><?= __('nouveautes') ?></a></li>
                <li><a href="#"><?= __('promotions') ?></a></li>
                <li><a href="#"><?= __('contact') ?></a></li>
            </ul>
            <div class="nav-icons">
                <a href="#" aria-label="Recherche"><i class="fas fa-search"></i></a>
                <a href="#" aria-label="Favoris"><i class="far fa-heart"></i></a>
                <a href="#" aria-label="Compte"><i class="far fa-user"></i></a>
                <a href="panier.php" aria-label='<?= __('panier') ?>' style="position:relative;">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge"><?= $nb_articles ?></span>
                </a>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="hero-shop">
        <div class="container">
            <h1><span class="orange">MON</span> <?= __('panier') ?></h1>
            <p>Vérifiez vos articles avant de finaliser votre commande.</p>
        </div>
    </section>

    <!-- ===== PANIER ===== -->
    <section class="cart-section">
        <div class="container">

            <?php if (empty($articles)): ?>
                <!-- Panier vide -->
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Votre <?= __('panier') ?> est vide</h2>
                    <p>Découvrez nos <?= __('produits') ?> et ajoutez vos favoris !</p>
                    <a href="boutique.php" class="btn-continue"><?= __('continuer_achats') ?></a>
                </div>
            <?php else: ?>
                <!-- Panier avec articles -->
                <form method="POST" action="panier-maj.php">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th style="width:50%;">Produit</th>
                                <th style="width:15%;">Prix</th>
                                <th style="width:20%;"><?= __('quantite') ?></th>
                                <th style="width:15%;"><?= __('total') ?></th>
                                <th style="width:5%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>" />
                                        <span class="name"><a href="produit.php?id=<?= $article['id'] ?>"><?= htmlspecialchars($article['nom']) ?></a></span>
                                    </div>
                                </td>
                                <td class="price-cell"><?= number_format($article['prix'], 2, ',', ' ') ?> €</td>
                                <td>
                                    <div class="qty-cell">
                                        <button type="button" onclick="updateQty(this, -1)">−</button>
                                        <input type="number" name="quantites[<?= $article['id'] ?>]" value="<?= $article['quantite'] ?>" min="1" max="99" class="qty-input" />
                                        <button type="button" onclick="updateQty(this, 1)">+</button>
                                    </div>
                                </td>
                                <td class="total-cell"><?= number_format($article['sous_total'], 2, ',', ' ') ?> €</td>
                                <td class="remove-cell">
                                    <a href="<?= __('panier') ?>-<?= __('supprimer') ?>.php?id=<?= $article['id'] ?>" onclick="return confirm('Supprimer cet article ?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="cart-actions">
                        <button type="submit" class="btn-update-cart"><i class="fas fa-sync-alt"></i> <?= __('mettre_a_jour') ?></button>
                    </div>
                </form>

                <!-- Résumé -->
                <div class="cart-summary">
                    <div class="line"><span>Sous-<?= __('total') ?></span> <span><?= number_format($total, 2, ',', ' ') ?> €</span></div>
                    <div class="line"><span><?= __('livraison') ?></span> <span>Offerte</span></div>
                    <div class="line total"><span><?= __('total') ?></span> <span class="amount"><?= number_format($total, 2, ',', ' ') ?> €</span></div>
                    <a href="checkout.php" class="btn-checkout"><?= __('passer_commande') ?></a>
                    <a href="boutique.php" class="btn-continue-shop"><i class="fas fa-arrow-left"></i> <?= __('continuer_achats') ?></a>
                </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer style="background:#0F0F0F; padding:40px 0 20px; border-top:1px solid rgba(255,255,255,0.04);">
        <div class="container">
            <div style="text-align:center; color:rgba(255,255,255,0.12); font-size:13px;">
                &copy; 2026 EasyPick – <?= __('tous_droits_reserves') ?>. <?= __('design_par') ?> <a href="#" style="color:#ff6a00;">Sarah Sabeur</a>.
            </div>
        </div>
    </footer>

    <!-- ===== JAVASCRIPT ===== -->
    <script>
        // Hamburger
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));

        // <?= __('quantite') ?> : boutons + / -
        function updateQty(btn, delta) {
            const input = btn.closest('.qty-cell').querySelector('.qty-input');
            let val = parseInt(input.value) + delta;
            if (val < 1) val = 1;
            if (val > 99) val = 99;
            input.value = val;
            // Déclencher l'événement "change" pour que le formulaire prenne en compte la nouvelle valeur
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
        }

        // Mise à jour automatique après un changement de quantité (délai)
        let timer;
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    this.closest('form').submit();
                }, 800);
            });
        });
    </script>

</body>
</html>
