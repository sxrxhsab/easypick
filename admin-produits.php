<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Fonction de traduction
if (!function_exists('__')) {
    function __($text) {
        return $text;
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Récupérer tous les produits
$stmt = $pdo->query('SELECT * FROM produits ORDER BY id DESC');
$produits = $stmt->fetchAll();

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Admin Produits</title>
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
        .product-img { width: 50px; height: 50px; object-fit: contain; background: #0F0F0F; border-radius: 8px; padding: 4px; }
        .btn-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-actions a { padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; transition: all 0.3s; }
        .btn-edit { background: rgba(255,106,0,0.1); color: #ff6a00; border: 1px solid rgba(255,106,0,0.2); }
        .btn-edit:hover { background: rgba(255,106,0,0.2); }
        .btn-delete { background: rgba(255,68,68,0.1); color: #ff4444; border: 1px solid rgba(255,68,68,0.2); }
        .btn-delete:hover { background: rgba(255,68,68,0.2); }
        .btn-add { display: inline-block; padding: 10px 24px; background: #ff6a00; color: #fff; border-radius: 12px; font-weight: 700; transition: all 0.3s; margin-bottom: 20px; }
        .btn-add:hover { background: #ff7d1a; transform: scale(1.02); box-shadow: 0 8px 25px rgba(255,106,0,0.2); }
        .badge { padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge.promo { background: rgba(255,106,0,0.15); color: #ff6a00; }
        .badge.new { background: rgba(0,184,148,0.15); color: #00b894; }
        .badge.top { background: rgba(253,203,110,0.15); color: #fdcb6e; }
        .stock-low { color: #ffc107; font-weight: 700; }
        .stock-out { color: #ff4444; font-weight: 700; }

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

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-simple">
        <div class="nav-container">
            <a href="index.php" class="logo-text"><span class="easy">EASY</span><span class="pick">PICK</span></a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="admin-produits.php" class="active">Produits</a></li>
                <li><a href="admin-commandes.php">Commandes</a></li>
                <li><a href="admin-utilisateurs.php">Utilisateurs</a></li>
            </ul>
            <div class="nav-icons">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="admin-hero">
        <div class="container">
            <h1>Gestion des <span>Produits</span></h1>
            <p>Ajoutez, modifiez ou supprimez des produits.</p>
        </div>
    </section>

    <!-- ===== CONTENU ===== -->
    <section class="section">
        <div class="container">

            <div class="admin-menu">
                <a href="admin.php"><i class="fas fa-chart-pie"></i> Tableau de bord</a>
                <a href="admin-produits.php" class="active"><i class="fas fa-box"></i> Produits</a>
                <a href="admin-commandes.php"><i class="fas fa-shopping-bag"></i> Commandes</a>
                <a href="admin-utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a>
            </div>

            <a href="admin-produit-ajouter.php" class="btn-add"><i class="fas fa-plus"></i> Ajouter un produit</a>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Badges</th>
                            <th>Actions</th>
                            <th>Prix vente</th>
<th>Prix achat</th>
<th>Marge</th>
<th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produits as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td>
                                <?php if (!empty($p['image'])): ?>
                                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="" class="product-img" style="width:50px; height:50px; object-fit:contain; background:#0F0F0F; border-radius:8px; padding:4px;" />
                                <?php else: ?>
                                    <span style="color:rgba(255,255,255,0.2);">Aucune</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><?= number_format($p['prix'], 2, ',', ' ') ?> €</td>
                            <td>
                                <?php if ($p['stock'] <= 0): ?>
                                    <span class="stock-out">❌ Rupture</span>
                                <?php elseif ($p['stock'] < 5): ?>
                                    <span class="stock-low">⚠️ <?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <?= $p['stock'] ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['est_promo']): ?><span class="badge promo">Promo</span> <?php endif; ?>
                                <?php if ($p['est_nouveau']): ?><span class="badge new">Nouveau</span> <?php endif; ?>
                                <?php if ($p['est_top']): ?><span class="badge top">Top</span> <?php endif; ?>
                            </td>
                            <td><?= number_format($p['prix'], 2, ',', ' ') ?> €</td>
<td style="color:#888; font-size:13px;">
    <?= $p['prix_achat'] ? number_format($p['prix_achat'], 2, ',', ' ') . ' €' : '-' ?>
</td>
<td>
    <?php 
    if ($p['prix_achat'] > 0) {
        $marge = $p['prix'] - $p['prix_achat'];
        $marge_pct = round(($marge / $p['prix_achat']) * 100);
        $couleur = $marge_pct > 50 ? '#00b894' : ($marge_pct > 20 ? '#ffc107' : '#ff4444');
        echo '<span style="color:' . $couleur . '; font-weight:700;">+' . $marge_pct . '%</span>';
    } else {
        echo '-';
    }
    ?>
</td>
                            <td>
                                <div class="btn-actions">
                                    <a href="admin-produit-modifier.php?id=<?= $p['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="admin-produit-supprimer.php?id=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer ce produit ?')"><i class="fas fa-trash"></i> Supprimer</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <footer style="background:#0F0F0F; padding:30px 0 20px; border-top:1px solid rgba(255,255,255,0.04); text-align:center; color:rgba(255,255,255,0.12); font-size:13px;">
        <div class="container">&copy; 2026 EasyPick – Tous droits réservés. Design par <a href="#" style="color:#ff6a00;">Samy Sabeur</a>.</div>
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