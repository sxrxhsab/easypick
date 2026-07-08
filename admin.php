<?php
ob_start();
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Statistiques
$nb_produits = $pdo->query('SELECT COUNT(*) FROM produits')->fetchColumn();
$nb_commandes = $pdo->query('SELECT COUNT(*) FROM commandes')->fetchColumn();
$nb_utilisateurs = $pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();

$chiffre_affaires = $pdo->query("SELECT SUM(total) FROM commandes WHERE statut = 'payee'")->fetchColumn();
$chiffre_affaires = $chiffre_affaires ? number_format($chiffre_affaires, 2, ',', ' ') : '0,00';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
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
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; letter-spacing: 0.3px; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; align-items: center; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .admin-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); }
        .admin-hero h1 { font-size: 34px; font-weight: 900; }
        .admin-hero h1 span { color: #ff6a00; }
        .admin-hero p { color: rgba(255,255,255,0.4); font-size: 15px; margin-top: 4px; }

        .section { padding: 30px 0 60px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #1A1A1A; padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.06); text-align: center; }
        .stat-card .number { font-size: 28px; font-weight: 900; color: #ff6a00; }
        .stat-card .label { color: rgba(255,255,255,0.4); font-size: 14px; margin-top: 4px; }

        .admin-menu { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 30px; }
        .admin-menu a { padding: 12px 24px; background: #1A1A1A; border-radius: 14px; border: 1px solid rgba(255,255,255,0.06); font-weight: 600; transition: all 0.3s; }
        .admin-menu a:hover { background: rgba(255,106,0,0.1); border-color: rgba(255,106,0,0.2); color: #ff6a00; }
        .admin-menu a.active { background: rgba(255,106,0,0.1); border-color: #ff6a00; color: #ff6a00; }

        @media (max-width: 768px) {
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }
            .admin-hero h1 { font-size: 28px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-simple">
        <div class="nav-container">
            <a href="index.php" class="logo-text"><span class="easy">EASY</span><span class="pick">PICK</span></a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="boutique.php">Boutique</a></li>
                <li><a href="nouveautes.php">Nouveautés</a></li>
                <li><a href="promotions.php">Promotions</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-icons">
                <a href="mon-compte.php" aria-label="Mon compte"><i class="fas fa-user"></i></a>
                <a href="logout.php" aria-label="Déconnexion"><i class="fas fa-sign-out-alt"></i></a>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="admin-hero">
        <div class="container">
            <h1>Tableau de bord <span>Admin</span></h1>
            <p>Gérez vos produits, commandes et utilisateurs.</p>
        </div>
    </section>

    <!-- ===== CONTENU ===== -->
    <section class="section">
        <div class="container">

            <!-- Menu admin -->
            <div class="admin-menu">
                <a href="admin.php" class="active"><i class="fas fa-chart-pie"></i> Tableau de bord</a>
                <a href="admin-produits.php"><i class="fas fa-box"></i> Produits</a>
                <a href="admin-commandes.php"><i class="fas fa-shopping-bag"></i> Commandes</a>
                <a href="admin-utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a>
            </div>

            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="number"><?= $nb_produits ?></div>
                    <div class="label">Produits</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= $nb_commandes ?></div>
                    <div class="label">Commandes</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= $nb_utilisateurs ?></div>
                    <div class="label">Utilisateurs</div>
                </div>
                <div class="stat-card">
                    <div class="number"><?= $chiffre_affaires ?> €</div>
                    <div class="label">Chiffre d'affaires</div>
                </div>
            </div>

            <div style="background:#1A1A1A; border-radius:16px; padding:20px; border:1px solid rgba(255,255,255,0.06);">
                <p style="color:rgba(255,255,255,0.4); font-size:14px;">Bienvenue dans votre espace d'administration. Utilisez le menu ci-dessus pour gérer votre boutique.</p>
            </div>

        </div>
    </section>

    <footer style="background:#0F0F0F; padding:30px 0 20px; border-top:1px solid rgba(255,255,255,0.04); text-align:center; color:rgba(255,255,255,0.12); font-size:13px;">
        <div class="container">
            &copy; 2026 EasyPick – Tous droits réservés. Design par <a href="#" style="color:#ff6a00;">Samy Sabeur</a>.
        </div>
    </footer>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
        }
    </script>
</body>
</html>