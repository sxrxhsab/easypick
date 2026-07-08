<?php
session_start();
require_once 'db.php';

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPick – <?= __('cgv') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* (mêmes styles que a-propos.php) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        .navbar-simple { width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; position: sticky; top: 0; z-index: 1000; padding: 0 30px; }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .nav-menu { display: flex; align-items: center; gap: 30px; list-style: none; }
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .page-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); text-align: center; }
        .page-hero h1 { font-size: 34px; font-weight: 900; }
        .page-hero h1 span { color: #ff6a00; }
        .page-hero p { color: rgba(255,255,255,0.4); font-size: 15px; margin-top: 4px; }

        .page-content { padding: 40px 0 80px; }
        .page-content h2 { font-size: 28px; font-weight: 700; margin: 30px 0 15px; color: #ff6a00; }
        .page-content h2:first-of-type { margin-top: 0; }
        .page-content p { color: rgba(255,255,255,0.7); line-height: 1.8; font-size: 16px; margin-bottom: 15px; }
        .page-content ul { list-style: none; padding: 0; }
        .page-content ul li { color: rgba(255,255,255,0.7); line-height: 1.8; padding: 6px 0; padding-left: 20px; position: relative; }
        .page-content ul li::before { content: '▸'; color: #ff6a00; position: absolute; left: 0; }

        .footer { background: #0F0F0F; padding: 40px 0 20px; border-top: 1px solid rgba(255,255,255,0.04); text-align: center; color: rgba(255,255,255,0.12); font-size: 13px; }
        .footer a { color: #ff6a00; }

        @media (max-width: 768px) {
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }
            .page-hero h1 { font-size: 28px; }
            .page-content { padding: 30px 0 60px; }
            .page-content h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <section class="page-hero">
        <div class="container">
            <h1>Conditions générales <span>de vente</span></h1>
            <p>Dernière mise à jour : 8 juillet 2026</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">

            <h2>Article 1 – Champ d'application</h2>
            <p>
                Les présentes Conditions Générales de Vente (<?= __('cgv') ?>) régissent les relations entre EasyPick et ses clients dans le cadre de la vente de <?= __('produits') ?> technologiques sur le site <strong>easypick.onrender.com</strong>.
            </p>

            <h2>Article 2 – <?= __('produits') ?></h2>
            <p>
                Les <?= __('produits') ?> proposés à la vente sont décrits sur le site avec leurs caractéristiques principales. Les photos sont non contractuelles. Les prix sont indiqués en euros, toutes taxes comprises (TTC).
            </p>

            <h2>Article 3 – Commande</h2>
            <p>
                La validation de la commande par le client vaut acceptation des <?= __('cgv') ?>. Un <?= __('email') ?> de confirmation est envoyé après paiement. EasyPick se réserve le droit d'<?= __('annuler') ?> ou de refuser toute commande pour des motifs légitimes.
            </p>

            <h2>Article 4 – Prix et paiement</h2>
            <p>
                Les prix sont ceux en vigueur au moment de la commande. Le paiement s'effectue en ligne via Stripe (carte bancaire). Les transactions sont sécurisées.
            </p>

            <h2>Article 5 – <?= __('livraison') ?></h2>
            <p>
                Les délais de <?= __('livraison') ?> sont indiqués lors de la commande. Les frais de <?= __('livraison') ?> sont offerts. La <?= __('livraison') ?> est assurée par des transporteurs partenaires.
            </p>

            <h2>Article 6 – Retours et remboursements</h2>
            <p>
                Conformément à la loi, vous disposez d'un délai de 14 jours pour retourner un produit. Les frais de retour sont à votre charge sauf si le produit est défectueux.
            </p>

            <h2>Article 7 – Droit applicable</h2>
            <p>
                Les présentes <?= __('cgv') ?> sont soumises au droit français. Tout litige sera porté devant les tribunaux compétents.
            </p>

        </div>
    </section>

    <footer class="footer">
        <div class="container">
            &copy; 2026 EasyPick – <?= __('tous_droits_reserves') ?>. <?= __('design_par') ?> <a href="#">Sarah Sabeur</a>.
        </div>
    </footer>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
    </script>

</body>
</html>
