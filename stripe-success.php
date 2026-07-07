<?php
session_start();
require_once 'db.php';
require_once 'config-stripe.php';
require_once 'config-email.php'; // 👈 NOUVEAU : inclusion du fichier email

// Récupérer l'ID de session
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;

if (!$session_id) {
    header('Location: index.php');
    exit;
}

try {
    // Récupérer la session Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($session->payment_status === 'paid') {
        // Récupérer les données de commande stockées
        $data = $_SESSION['checkout_data'] ?? null;

        if ($data) {
            // Créer la commande dans la BDD
            $reference = 'EAS-' . date('Y') . '-' . strtoupper(uniqid());
            $utilisateur_id = $_SESSION['user_id'] ?? null;

            $stmt = $pdo->prepare('INSERT INTO commandes 
                (utilisateur_id, email, prenom, nom, adresse, code_postal, ville, pays, telephone, total, reference, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "payee")');
            $stmt->execute([
                $utilisateur_id,
                $data['email'],
                $data['prenom'],
                $data['nom'],
                $data['adresse'],
                $data['code_postal'],
                $data['ville'],
                $data['pays'],
                $data['telephone'],
                $data['total'],
                $reference
            ]);

            $commande_id = $pdo->lastInsertId();

            // Insérer les lignes de commande
            foreach ($data['articles'] as $article) {
                $stmt = $pdo->prepare('INSERT INTO lignes_commandes (commande_id, produit_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)');
                $stmt->execute([$commande_id, $article['id'], $article['quantite'], $article['prix']]);
            }

            // 👇 ENVOI DE L'EMAIL DE CONFIRMATION (AJOUT ICI)
            $envoi = envoyerEmail(
                $data['email'],
                $data['prenom'],
                $reference,
                $data['total'],
                $data['articles']
            );

            if ($envoi) {
                $email_status = 'Un email de confirmation a été envoyé.';
            } else {
                $email_status = 'L\'email n\'a pas pu être envoyé (vérifiez la configuration).';
            }
            // 👆 FIN DE L'AJOUT

            // Vider le panier et les données temporaires
            $_SESSION['panier'] = [];
            unset($_SESSION['checkout_data']);

            $prenom = $data['prenom'];
            $email = $data['email'];
            $total = $data['total'];
        } else {
            // Données manquantes (cas exceptionnel)
            header('Location: index.php');
            exit;
        }
    } else {
        // Paiement non confirmé
        header('Location: panier.php');
        exit;
    }

} catch (Exception $e) {
    // Erreur Stripe
    header('Location: panier.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Confirmation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 600px; margin: 0 auto; padding: 0 20px; }

        .navbar-simple { width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; padding: 0 30px; }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }

        .confirmation-section { flex: 1; display: flex; align-items: center; justify-content: center; padding: 60px 0; }
        .confirmation-box { background: #1A1A1A; border-radius: 24px; padding: 50px 40px; text-align: center; border: 1px solid rgba(255,255,255,0.06); max-width: 560px; width: 100%; }
        .confirmation-box .icon { font-size: 72px; color: #00b894; margin-bottom: 16px; }
        .confirmation-box h1 { font-size: 32px; font-weight: 900; margin-bottom: 8px; }
        .confirmation-box h1 span { color: #ff6a00; }
        .confirmation-box p { color: rgba(255,255,255,0.5); font-size: 16px; line-height: 1.7; margin-bottom: 6px; }
        .confirmation-box .order-number { color: #ff6a00; font-weight: 700; font-size: 20px; margin: 16px 0; }
        .confirmation-box .btn-continue { display: inline-block; padding: 14px 44px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border-radius: 60px; font-weight: 700; font-size: 16px; transition: all 0.3s; margin-top: 20px; }
        .confirmation-box .btn-continue:hover { transform: scale(1.05); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }

        @media (max-width: 768px) {
            .container { padding: 0 16px; }
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .confirmation-box { padding: 30px 20px; }
            .confirmation-box h1 { font-size: 26px; }
        }
    </style>
</head>
<body>

    <nav class="navbar-simple">
        <div class="nav-container">
            <a href="index.php" class="logo-text"><span class="easy">EASY</span><span class="pick">PICK</span></a>
        </div>
    </nav>

    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-box">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h1>Commande <span>confirmée</span></h1>
                <p>Merci <strong><?= htmlspecialchars($prenom ?? '') ?></strong> !</p>
                <p>Votre commande a été enregistrée avec succès.</p>

                <div class="order-number">#<?= htmlspecialchars($reference ?? '') ?></div>

                <p style="font-size:14px; color:rgba(255,255,255,0.3);">
                    <?= isset($email_status) ? htmlspecialchars($email_status) : 'Un email de confirmation vous a été envoyé.' ?>
                    <?php if (isset($email)): ?><br><strong><?= htmlspecialchars($email) ?></strong><?php endif; ?>
                </p>

                <a href="index.php" class="btn-continue"><i class="fas fa-home"></i> Retour à l'accueil</a>
            </div>
        </div>
    </section>

    <footer style="background:#0F0F0F; padding:30px 0 20px; border-top:1px solid rgba(255,255,255,0.04); text-align:center; color:rgba(255,255,255,0.08); font-size:13px;">
        <div class="container">&copy; 2026 EasyPick – Tous droits réservés.</div>
    </footer>

</body>
</html>