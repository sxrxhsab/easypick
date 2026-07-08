<?php
session_start();
require_once 'db.php';

// Variables navbar
$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPick – <?= __('a_propos') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* (reprendre les styles de base de <?= __('boutique') ?>s) */
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
            <h1>À <span>propos</span></h1>
            <p>Découvrez l'histoire et les valeurs d'EasyPick.</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">

            <h2>Notre histoire</h2>
            <p>
                Fondée en <strong>2025</strong> par <strong>Sabeur Samy</strong>, EasyPick est née d'une passion pour la technologie et d'une frustration : passer des heures à comparer des fiches techniques pour trouver le bon produit.
            </p>
            <p>
                Nous avons décidé de créer un espace où chaque accessoire tech est soigneusement sélectionné, testé et validé par notre équipe. Notre mission est simple : vous aider à <strong>"picker" (choisir)</strong> le meilleur produit, sans prise de tête.
            </p>

            <h2>Nos valeurs</h2>
            <ul>
                <li><strong>Qualité</strong> – Nous ne sélectionnons que des <?= __('produits') ?> premium, testés par nos soins.</li>
                <li><strong>Transparence</strong> – Prix clairs, descriptions honnêtes, <?= __('avis') ?> authentiques.</li>
                <li><strong>Service</strong> – Une équipe disponible 24/7 pour répondre à vos questions.</li>
                <li><strong>Confiance</strong> – Paiement sécurisé, <?= __('livraison') ?> fiable, retours facilités.</li>
            </ul>

            <h2>Notre équipe</h2>
            <p>
                Derrière EasyPick, une équipe de passionnés de tech : des développeurs, des designers, des testeurs <?= __('produits') ?> et des experts en e-commerce. Nous sommes tous animés par la même envie : rendre la technologie accessible à tous.
            </p>

            <h2>Pourquoi EasyPick ?</h2>
            <p>
                Parce que nous croyons que la technologie doit simplifier la vie, pas la compliquer. Que vous soyez un professionnel, un gamer, un créatif ou simplement curieux, nous sommes là pour vous guider vers les meilleurs choix.
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
