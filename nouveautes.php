<?php
ob_start();
session_start();
require_once 'db.php';

// Fonction de traduction
if (!function_exists('__')) {
    function __($text) {
        return $text;
    }
}

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';

// ✅ CORRECTION : Utiliser TRUE au lieu de 1 pour PostgreSQL
$stmt = $pdo->query("SELECT * FROM produits WHERE est_nouveau = TRUE ORDER BY created_at DESC");
$produits = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Nouveautés</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

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
        .navbar-simple .nav-icons { display: flex; gap: 20px; align-items: center; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .page-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); text-align: center; }
        .page-hero h1 { font-size: 34px; font-weight: 900; }
        .page-hero h1 span { color: #ff6a00; }
        .page-hero p { color: rgba(255,255,255,0.4); font-size: 15px; }

        .products-section { padding: 30px 0 60px; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .product-card { background: #1A1A1A; border-radius: 16px; padding: 20px; text-align: center; transition: transform 0.3s; border: 1px solid rgba(255,255,255,0.06); }
        .product-card:hover { transform: translateY(-8px); border-color: rgba(255,106,0,0.2); }
        .product-card img { width: 100%; height: 200px; object-fit: contain; background: #0D0D0D; border-radius: 12px; margin-bottom: 15px; padding: 10px; }
        .product-card .product-name { font-size: 18px; font-weight: 600; margin-bottom: 6px; }
        .product-card .product-price { font-size: 22px; font-weight: 700; color: #ff6a00; }
        .product-card .badge-new { display: inline-block; background: #00b894; color: #fff; padding: 2px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; margin: 8px 0; }
        .btn-add { background: transparent; border: 2px solid #ff6a00; color: #ff6a00; padding: 8px 20px; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-add:hover { background: #ff6a00; color: #fff; }

        .empty { text-align: center; padding: 60px 0; color: rgba(255,255,255,0.4); }
        .empty i { font-size: 48px; margin-bottom: 16px; }

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
            .products-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .products-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-simple">
        <div class="nav-container">
            <div class="logo-text">
                <span class="easy">EASY</span><span class="pick">PICK</span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="boutique.php">Boutique</a></li>
                <li><a href="nouveautes.php" class="active">Nouveautés</a></li>
                <li><a href="promotions.php">Promotions</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            <div class="nav-icons">
                <a href="#"><i class="fas fa-search"></i></a>
                <a href="#"><i class="far fa-heart"></i></a>
                <?php if ($user_connecte): ?>
                    <a href="mon-compte.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
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
            <h1>Nos <span>Nouveautés</span></h1>
            <p>Découvrez les derniers produits ajoutés à notre sélection.</p>
        </div>
    </section>

    <!-- ===== PRODUITS ===== -->
    <section class="products-section">
        <div class="container">
            <?php if (empty($produits)): ?>
                <div class="empty">
                    <i class="fas fa-box-open"></i>
                    <p>Aucune nouveauté pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($produits as $produit): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($produit['image'] ?? 'https://picsum.photos/seed/' . $produit['id'] . '/300/200') ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" />
                            <div class="product-name"><?= htmlspecialchars($produit['nom']) ?></div>
                            <span class="badge-new">Nouveau</span>
                            <div class="product-price">
                                <?= number_format($produit['prix'], 2, ',', ' ') ?> €
                            </div>
                            <button class="btn-add">Ajouter au panier</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            &copy; 2026 EasyPick – Tous droits réservés. Design par <a href="#">Samy Sabeur</a>.
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