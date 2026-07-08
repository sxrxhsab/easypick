<?php
ob_start();
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_connecte = true;
$user_role = $_SESSION['user_role'] ?? '';
$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Mon compte</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 30px; }

        /* NAVBAR */
        .navbar-simple {
            width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center; position: sticky; top: 0; z-index: 1000; padding: 0 30px;
        }
        .navbar-simple .nav-container { max-width: 1200px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .nav-menu { display: flex; align-items: center; gap: 30px; list-style: none; }
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-menu li a.active { color: #fff; }
        .navbar-simple .nav-menu li a.active::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; align-items: center; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        /* PAGE HERO */
        .page-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); text-align: center; }
        .page-hero h1 { font-size: 34px; font-weight: 900; }
        .page-hero h1 span { color: #ff6a00; }

        /* COMPTE */
        .account-section { padding: 50px 0 80px; background: #151515; }
        .account-grid { display: grid; grid-template-columns: 280px 1fr; gap: 50px; }
        .account-sidebar { background: #1A1A1A; border-radius: 20px; padding: 30px; border: 1px solid rgba(255,255,255,0.06); height: fit-content; }
        .account-sidebar .user-info { text-align: center; margin-bottom: 25px; }
        .account-sidebar .user-info .avatar { width: 80px; height: 80px; border-radius: 50%; background: #ff6a00; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 32px; font-weight: 700; color: #fff; }
        .account-sidebar .user-info h3 { font-size: 18px; font-weight: 700; }
        .account-sidebar .user-info p { color: rgba(255,255,255,0.4); font-size: 14px; }
        .account-sidebar ul { list-style: none; }
        .account-sidebar ul li { margin-bottom: 8px; }
        .account-sidebar ul li a { display: block; padding: 12px 16px; border-radius: 12px; color: rgba(255,255,255,0.6); transition: all 0.3s; font-size: 14px; }
        .account-sidebar ul li a:hover { background: rgba(255,106,0,0.08); color: #fff; }
        .account-sidebar ul li a.active { background: rgba(255,106,0,0.12); color: #ff6a00; }
        .account-sidebar ul li a i { margin-right: 12px; width: 20px; text-align: center; }

        .account-content { background: #1A1A1A; border-radius: 20px; padding: 35px; border: 1px solid rgba(255,255,255,0.06); }
        .account-content h2 { font-size: 24px; font-weight: 700; margin-bottom: 20px; }
        .account-content h2 span { color: #ff6a00; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item { background: #151515; padding: 16px 20px; border-radius: 12px; }
        .info-item label { display: block; color: rgba(255,255,255,0.3); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-item .value { font-size: 16px; font-weight: 600; color: #fff; }

        .btn-deconnexion { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #ff4444; color: #fff; border: none; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.3s; }
        .btn-deconnexion:hover { background: #ff6666; transform: scale(1.02); }

        .footer { background: #0F0F0F; padding: 40px 0 20px; border-top: 1px solid rgba(255,255,255,0.04); text-align: center; color: rgba(255,255,255,0.12); font-size: 13px; }
        .footer a { color: #ff6a00; }

        @media (max-width: 992px) {
            .account-grid { grid-template-columns: 1fr; gap: 30px; }
            .info-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }
            .container { padding: 0 16px; }
            .account-sidebar { padding: 20px; }
            .account-content { padding: 20px; }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-simple">
        <div class="nav-container">
            <div class="logo-text">
                <span><span class="easy">EASY</span><span class="pick">PICK</span></span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="boutique.php">Boutique</a></li>
                <li><a href="nouveautes.php">Nouveautés</a></li>
                <li><a href="promotions.php">Promotions</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-icons">
                <a href="#"><i class="fas fa-search"></i></a>
                <a href="#"><i class="far fa-heart"></i></a>
                <a href="mon-compte.php" style="color:#ff6a00;"><i class="fas fa-user"></i></a>
                <a href="panier.php" style="position:relative;">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge"><?= $nb_articles ?></span>
                </a>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="container">
            <h1>Mon <span>Compte</span></h1>
        </div>
    </section>

    <!-- ===== ACCOUNT ===== -->
    <section class="account-section">
        <div class="container">
            <div class="account-grid">
                <!-- Sidebar -->
                <aside class="account-sidebar">
                    <div class="user-info">
                        <div class="avatar">
                            <?= strtoupper(substr($user['prenom'] ?? 'U', 0, 1) . substr($user['nom'] ?? 'N', 0, 1)) ?>
                        </div>
                        <h3><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h3>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    <ul>
                        <li><a href="mon-compte.php" class="active"><i class="fas fa-user"></i> Mon profil</a></li>
                        <li><a href="mes-commandes.php"><i class="fas fa-box"></i> Mes commandes</a></li>
                        <li><a href="mes-avis.php"><i class="fas fa-star"></i> Mes avis</a></li>
                        <li><a href="wishlist.php"><i class="fas fa-heart"></i> Ma wishlist</a></li>
                        <li><a href="logout.php" style="color:#ff4444;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                    </ul>
                </aside>

                <!-- Content -->
                <div class="account-content">
                    <h2>Bienvenue, <span><?= htmlspecialchars($user['prenom']) ?></span> 👋</h2>
                    <p style="color:rgba(255,255,255,0.4); margin-bottom: 30px;">Voici les informations de votre compte.</p>

                    <div class="info-grid">
                        <div class="info-item">
                            <label>Nom complet</label>
                            <div class="value"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></div>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <div class="value"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                        <div class="info-item">
                            <label>Téléphone</label>
                            <div class="value"><?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?></div>
                        </div>
                        <div class="info-item">
                            <label>Rôle</label>
                            <div class="value"><?= $user['role'] === 'admin' ? 'Administrateur' : 'Client' ?></div>
                        </div>
                        <div class="info-item" style="grid-column: 1/-1;">
                            <label>Adresse</label>
                            <div class="value"><?= htmlspecialchars($user['adresse'] ?? 'Non renseignée') ?></div>
                        </div>
                    </div>

                    <a href="logout.php" class="btn-deconnexion"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            &copy; 2026 EasyPick – Tous droits réservés. Design par <a href="#">Samy Sabeur</a>.
        </div>
    </footer>

    <script>
        // Hamburger
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
        }
    </script>

</body>
</html>