<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Récupérer toutes les commandes avec les infos client
$commandes = $pdo->query('SELECT * FROM commandes ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Admin Commandes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* (reprendre les styles de admin.php) */
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
        .admin-menu { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 30px; }
        .admin-menu a { padding: 12px 24px; background: #1A1A1A; border-radius: 14px; border: 1px solid rgba(255,255,255,0.06); font-weight: 600; transition: all 0.3s; }
        .admin-menu a:hover { background: rgba(255,106,0,0.1); border-color: rgba(255,106,0,0.2); color: #ff6a00; }
        .admin-menu a.active { background: rgba(255,106,0,0.1); border-color: #ff6a00; color: #ff6a00; }

        .table-wrap { overflow-x: auto; background: #1A1A1A; border-radius: 16px; border: 1px solid rgba(255,255,255,0.06); padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 14px 16px; color: rgba(255,255,255,0.3); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 14px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .statut { padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .statut.en_attente { background: rgba(255,193,7,0.15); color: #ffc107; }
        .statut.payee { background: rgba(0,184,148,0.15); color: #00b894; }
        .statut.expediee { background: rgba(13,202,240,0.15); color: #0dcaf0; }
        .statut.livree { background: rgba(40,167,69,0.15); color: #28a745; }
        .statut.annulee { background: rgba(255,68,68,0.15); color: #ff4444; }

        .btn-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-actions a { padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; transition: all 0.3s; }
        .btn-view { background: rgba(13,202,240,0.1); color: #0dcaf0; border: 1px solid rgba(13,202,240,0.2); }
        .btn-view:hover { background: rgba(13,202,240,0.2); }

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
            th, td { padding: 10px 12px; font-size: 13px; }
        }
    </style>
</head>
<body>

    <?php
    // Variables pour la navbar
    $nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
    $user_connecte = isset($_SESSION['user_id']);
    $user_role = $_SESSION['user_role'] ?? '';
    include 'header.php';
    ?>

    <section class="admin-hero">
        <div class="container">
            <h1>Gestion des <span>commandes</span></h1>
            <p>Suivez et gérez toutes les commandes passées sur votre boutique.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <div class="admin-menu">
                <a href="admin.php"><i class="fas fa-chart-pie"></i> Tableau de bord</a>
                <a href="admin-produits.php"><i class="fas fa-box"></i> Produits</a>
                <a href="admin-commandes.php" class="active"><i class="fas fa-shopping-bag"></i> Commandes</a>
                <a href="#"><i class="fas fa-users"></i> Utilisateurs</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $cmd): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($cmd['reference']) ?></strong></td>
                            <td><?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?><br>
                                <span style="font-size:12px; color:rgba(255,255,255,0.3);"><?= htmlspecialchars($cmd['email']) ?></span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
                            <td><strong><?= number_format($cmd['total'], 2, ',', ' ') ?> €</strong></td>
                            <td><span class="statut <?= $cmd['statut'] ?>"><?= str_replace('_', ' ', $cmd['statut']) ?></span></td>
                            <td>
                                <div class="btn-actions">
                                    <a href="admin-commande-detail.php?id=<?= $cmd['id'] ?>" class="btn-view"><i class="fas fa-eye"></i> Voir</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
    </script>

</body>
</html>