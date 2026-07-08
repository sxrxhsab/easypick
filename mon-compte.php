<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les commandes du client
$stmt = $pdo->prepare('SELECT * FROM commandes WHERE utilisateur_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – <?= __('mon_compte') ?></title>
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

        .page-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); text-align: center; }
        .page-hero h1 { font-size: 34px; font-weight: 900; }
        .page-hero h1 span { color: #ff6a00; }
        .page-hero p { color: rgba(255,255,255,0.4); font-size: 15px; margin-top: 4px; }

        .section { padding: 40px 0 80px; }
        .welcome { background: #1A1A1A; border-radius: 20px; padding: 30px; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .welcome h2 { font-size: 22px; font-weight: 700; }
        .welcome h2 span { color: #ff6a00; }
        .welcome .btn-logout { padding: 10px 24px; background: rgba(255,68,68,0.1); color: #ff4444; border: 1px solid rgba(255,68,68,0.2); border-radius: 12px; font-weight: 600; transition: all 0.3s; }
        .welcome .btn-logout:hover { background: rgba(255,68,68,0.2); }

        .<?= __('commandes') ?>-table { width: 100%; border-collapse: collapse; background: #1A1A1A; border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.06); }
        .<?= __('commandes') ?>-table th { text-align: left; padding: 14px 18px; color: rgba(255,255,255,0.3); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .<?= __('commandes') ?>-table td { padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 14px; }
        .<?= __('commandes') ?>-table tr:last-child td { border-bottom: none; }
        .statut { padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .statut.en_attente { background: rgba(255,193,7,0.15); color: #ffc107; }
        .statut.payee { background: rgba(0,184,148,0.15); color: #00b894; }
        .statut.expediee { background: rgba(13,202,240,0.15); color: #0dcaf0; }
        .statut.livree { background: rgba(40,167,69,0.15); color: #28a745; }
        .statut.annulee { background: rgba(255,68,68,0.15); color: #ff4444; }
        .empty { text-align: center; padding: 40px 0; color: rgba(255,255,255,0.3); }

        @media (max-width: 768px) {
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }
            .<?= __('commandes') ?>-table { font-size: 13px; display: block; overflow-x: auto; }
            .<?= __('commandes') ?>-table th, .<?= __('commandes') ?>-table td { padding: 10px 12px; }
            .welcome { flex-direction: column; align-items: stretch; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
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
                <a href="mon-compte.php" aria-label='<?= __('mon_compte') ?>'><i class="fas fa-user"></i></a>
                <a href="panier.php" aria-label='<?= __('panier') ?>' style="position:relative;">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="page-hero">
        <div class="container">
            <h1>Mon <span>compte</span></h1>
            <p>Gérez vos informations et suivez vos <?= __('commandes') ?>.</p>
        </div>
    </section>

    <!-- ===== CONTENU ===== -->
    <section class="section">
        <div class="container">

           <div class="welcome">
    <div>
        <h2>Bonjour <span><?= htmlspecialchars($_SESSION['user_prenom']) ?></span> !</h2>
        <p style="color:rgba(255,255,255,0.4); font-size:14px;"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
    </div>
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <!-- ✅ AJOUTE LE LIEN VERS LES FAVORIS ICI -->
        <a href="wishlist.php" style="padding:10px 24px; background:rgba(255,106,0,0.1); color:#ff6a00; border:1px solid rgba(255,106,0,0.2); border-radius:12px; font-weight:600; transition:all 0.3s;">
            <i class="fas fa-heart"></i> Mes favoris
        </a>
        <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> <?= __('deconnexion') ?></a>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="admin.php" style="padding:10px 24px; background:rgba(255,106,0,0.1); color:#ff6a00; border:1px solid rgba(255,106,0,0.2); border-radius:12px; font-weight:600; transition:all 0.3s;"><i class="fas fa-cog"></i> Admin</a>
        <?php endif; ?>
    </div>
</div>

            <h3 style="font-size:20px; font-weight:700; margin-bottom:16px;">📦 Mes <?= __('commandes') ?></h3>

            <?php if (empty($commandes)): ?>
                <div class="empty">
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <a href="boutique.php" style="color:#ff6a00; font-weight:600;">Découvrir nos <?= __('produits') ?></a>
                </div>
            <?php else: ?>
                <table class="commandes-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th><?= __('total') ?></th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $cmd): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($cmd['reference']) ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
                            <td><?= number_format($cmd['total'], 2, ',', ' ') ?> €</td>
                            <td><span class="statut <?= $cmd['statut'] ?>"><?= str_replace('_', ' ', $cmd['statut']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </section>

    <footer style="background:#0F0F0F; padding:30px 0 20px; border-top:1px solid rgba(255,255,255,0.04); text-align:center; color:rgba(255,255,255,0.12); font-size:13px;">
        <div class="container">
            &copy; 2026 EasyPick – <?= __('tous_droits_reserves') ?>.
        </div>
    </footer>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
    </script>
</body>
</html>
