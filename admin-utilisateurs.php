<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Récupérer tous les utilisateurs
$stmt = $pdo->query('SELECT * FROM utilisateurs ORDER BY id DESC');
$utilisateurs = $stmt->fetchAll();

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Admin <?= __('utilisateurs') ?></title>
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
        .admin-menu { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 30px; }
        .admin-menu a { padding: 12px 24px; background: #1A1A1A; border-radius: 14px; border: 1px solid rgba(255,255,255,0.06); font-weight: 600; transition: all 0.3s; }
        .admin-menu a:hover { background: rgba(255,106,0,0.1); border-color: rgba(255,106,0,0.2); color: #ff6a00; }
        .admin-menu a.active { background: rgba(255,106,0,0.1); border-color: #ff6a00; color: #ff6a00; }

        .table-wrap { overflow-x: auto; background: #1A1A1A; border-radius: 16px; border: 1px solid rgba(255,255,255,0.06); padding: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 14px 16px; color: rgba(255,255,255,0.3); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        td { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 14px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .role { padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .role.admin { background: rgba(255,106,0,0.15); color: #ff6a00; }
        .role.user { background: rgba(13,202,240,0.15); color: #0dcaf0; }
        .btn-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-actions a { padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; transition: all 0.3s; }
        .btn-edit { background: rgba(255,106,0,0.1); color: #ff6a00; border: 1px solid rgba(255,106,0,0.2); }
        .btn-edit:hover { background: rgba(255,106,0,0.2); }
        .btn-delete { background: rgba(255,68,68,0.1); color: #ff4444; border: 1px solid rgba(255,68,68,0.2); }
        .btn-delete:hover { background: rgba(255,68,68,0.2); }

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
            .admin-hero h1 { font-size: 28px; }
            th, td { padding: 10px 12px; font-size: 13px; }
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <section class="admin-hero">
        <div class="container">
            <h1>Gestion des <span><?= __('utilisateurs') ?></span></h1>
            <p>Consultez et gérez les comptes des clients inscrits.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <div class="admin-menu">
                <a href="admin.php"><i class="fas fa-chart-pie"></i> <?= __('tableau_de_bord') ?></a>
                <a href="admin-produits.php"><i class="fas fa-box"></i> <?= __('produits') ?></a>
                <a href="admin-commandes.php"><i class="fas fa-shopping-bag"></i> <?= __('commandes') ?></a>
                <a href="admin-utilisateurs.php" class="active"><i class="fas fa-users"></i> <?= __('utilisateurs') ?></a>
            </div>

            <div class="table-wrap">
                <?php if (empty($utilisateurs)): ?>
                    <div class="empty">Aucun utilisateur inscrit.</div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= __('nom') ?></th>
                                <th><?= __('email') ?></th>
                                <th>Rôle</th>
                                <th>Inscrit le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><span class="role <?= $user['role'] ?>"><?= $user['role'] ?></span></td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="btn-actions">
                                        <a href="admin-utilisateur-<?= __('modifier') ?>.php?id=<?= $user['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> <?= __('modifier') ?></a>
                                        <a href="admin-utilisateur-<?= __('supprimer') ?>.php?id=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet utilisateur ?')"><i class="fas fa-trash"></i> <?= __('supprimer') ?></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <footer style="background:#0F0F0F; padding:30px 0 20px; border-top:1px solid rgba(255,255,255,0.04); text-align:center; color:rgba(255,255,255,0.12); font-size:13px;">
        <div class="container">&copy; 2026 EasyPick – Admin</div>
    </footer>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
    </script>
</body>
</html>
