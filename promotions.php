<?php
ob_start();
session_start();
require_once 'db.php';

// Récupérer les produits en promotion (prix_original > prix_actuel)
$stmt = $pdo->query('SELECT * FROM produits WHERE prix_original > prix_actuel ORDER BY id DESC');
$promotions = $stmt->fetchAll();

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';

// Fonction de traduction si elle n'existe pas
if (!function_exists('__')) {
    function __($text) {
        return $text;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Promotions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1500px; margin: 0 auto; padding: 0 30px; }
        
        /* NAVBAR */
        .navbar-simple {
            width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: center; position: sticky; top: 0; z-index: 1000; padding: 0 30px;
        }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .logo-text .sub { font-weight: 300; font-size: 10px; color: rgba(255,255,255,0.7); letter-spacing: 0.5px; margin-top: -2px; display: block; }
        .navbar-simple .nav-menu { display: flex; align-items: center; gap: 30px; list-style: none; }
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-menu li a.active { color: #ff6a00; }
        .navbar-simple .nav-menu li a.active::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; gap: 20px; align-items: center; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .cart-badge { position: absolute; top: -8px; right: -10px; background: #ff6a00; color: #fff; font-size: 10px; font-weight: 700; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        /* HERO */
        .page-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); text-align: center; }
        .page-hero h1 { font-size: 34px; font-weight: 900; }
        .page-hero h1 span { color: #ff6a00; }
        .page-hero p { color: rgba(255,255,255,0.4); font-size: 15px; }

        /* PRODUCTS */
        .products-section { padding: 40px 0 80px; background: #151515; }
        .products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 35px; }
        .product-card { background: #202020; border-radius: 20px; overflow: hidden; padding: 24px 24px 28px; border: 1px solid rgba(255,255,255,0.06); transition: transform 0.4s, box-shadow 0.4s; position: relative; }
        .product-card:hover { transform: translateY(-12px); box-shadow: 0 30px 70px rgba(255,106,0,0.12); border-color: rgba(255,106,0,0.12); }
        .product-card .product-image-wrap { position: relative; overflow: hidden; border-radius: 16px; background: #151515; margin-bottom: 16px; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; }
        .product-card .product-image-wrap img { width: 100%; height: 100%; object-fit: contain; padding: 20px; transition: transform 0.5s; }
        .product-card:hover .product-image-wrap img { transform: scale(1.08); }
        .product-card .badges { position: absolute; top: 14px; left: 14px; display: flex; flex-direction: column; gap: 6px; z-index: 2; }
        .product-card .badges .badge { padding: 4px 14px; border-radius: 30px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .product-card .badges .badge.promo { background: #ff6a00; color: #fff; }
        .product-card .action-buttons { position: absolute; top: 14px; right: 14px; display: flex; flex-direction: column; gap: 8px; z-index: 2; opacity: 0; transform: translateX(12px); transition: all 0.4s; }
        .product-card:hover .action-buttons { opacity: 1; transform: translateX(0); }
        .product-card .action-buttons button { width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(21,21,21,0.85); backdrop-filter: blur(8px); color: #fff; font-size: 16px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.06); z-index: 3; }
        .product-card .action-buttons button:hover { background: #ff6a00; transform: scale(1.1); box-shadow: 0 8px 30px rgba(255,106,0,0.25); border-color: #ff6a00; }
        .product-card .product-name { font-size: 16px; font-weight: 600; margin-bottom: 4px; color: #fff; }
        .product-card .product-rating { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .product-card .product-rating .stars { color: #ffb800; font-size: 13px; }
        .product-card .product-rating .stars .grey { color: #444; }
        .product-card .product-rating .count { color: rgba(255,255,255,0.2); font-size: 12px; }
        .product-card .product-price { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .product-card .product-price .current { font-size: 32px; font-weight: 900; color: #ff6a00; letter-spacing: -0.5px; }
        .product-card .product-price .old { font-size: 16px; color: rgba(255,255,255,0.2); text-decoration: line-through; font-weight: 400; }
        .product-card .product-price .reduction { background: #ff6a00; color: #fff; padding: 2px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .product-card .card-actions { display: flex; gap: 10px; margin-top: auto; z-index: 2; position: relative; }
        .product-card .card-actions .btn-add { width: 100%; padding: 14px 0; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 16px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.4s; font-family: 'Poppins', sans-serif; box-shadow: 0 6px 25px rgba(255,106,0,0.12); text-align: center; display: inline-block; }
        .product-card .card-actions .btn-add:hover { transform: scale(1.03); box-shadow: 0 10px 35px rgba(255,106,0,0.25); background: linear-gradient(135deg, #ff7d1a, #ff8c33); }

        .empty { text-align: center; padding: 60px 0; color: rgba(255,255,255,0.4); }
        .empty i { font-size: 48px; margin-bottom: 16px; }

        /* FOOTER */
        .footer { background: #0F0F0F; padding: 40px 0 20px; border-top: 1px solid rgba(255,255,255,0.04); text-align: center; color: rgba(255,255,255,0.12); font-size: 13px; }
        .footer a { color: #ff6a00; }

        /* RESPONSIVE */
        @media (max-width: 1200px) { .products-grid { grid-template-columns: repeat(3,1fr); gap: 30px; } }
        @media (max-width: 992px) { .products-grid { grid-template-columns: repeat(2,1fr); gap: 28px; } }
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
            .products-grid { grid-template-columns: 1fr 1fr; gap: 20px; }
        }
        @media (max-width: 480px) { .products-grid { grid-template-columns: 1fr; gap: 24px; } }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-simple">
        <div class="nav-container">
            <div class="logo-text">
                <span><span class="easy">EASY</span><span class="pick">PICK</span></span>
                <span class="sub">By Samy Sabeur</span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="boutique.php">Boutique</a></li>
                <li><a href="nouveautes.php">Nouveautés</a></li>
                <li><a href="promotions.php" class="active">Promotions</a></li>
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
                <?php if ($user_connecte && $user_role === 'admin'): ?>
                    <a href="admin.php"><i class="fas fa-cog"></i></a>
                <?php endif; ?>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="container">
            <h1>Nos <span>Promotions</span></h1>
            <p>Profitez de nos offres exceptionnelles !</p>
        </div>
    </section>

    <!-- ===== PRODUCTS ===== -->
    <section class="products-section">
        <div class="container">
            <?php if (empty($promotions)): ?>
                <div class="empty">
                    <i class="fas fa-tags"></i>
                    <p>Aucune promotion en cours pour le moment. Revenez bientôt !</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($promotions as $produit): 
                        $reduction = round((($produit['prix_original'] - $produit['prix_actuel']) / $produit['prix_original']) * 100);
                    ?>
                    <div class="product-card">
                        <div class="product-image-wrap">
                            <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" />
                            <div class="badges">
                                <span class="badge promo">-<?= $reduction ?>%</span>
                            </div>
                            <div class="action-buttons">
                                <button class="fav-btn" onclick="event.stopPropagation(); toggleFav(this)"><i class="far fa-heart"></i></button>
                                <button onclick="event.stopPropagation(); quickView(this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="product-name"><?= htmlspecialchars($produit['nom']) ?></div>
                        <div class="product-rating">
                            <span class="stars">
                                <?php
                                $note = round($produit['note'] * 2) / 2;
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $note) echo '<i class="fas fa-star"></i>';
                                    elseif ($i - 0.5 <= $note) echo '<i class="fas fa-star-half-alt"></i>';
                                    else echo '<i class="fas fa-star grey"></i>';
                                }
                                ?>
                            </span>
                            <span class="count">(<?= $produit['nb_avis'] ?> avis)</span>
                        </div>
                        <div class="product-price">
                            <span class="current"><?= number_format($produit['prix_actuel'], 2, ',', ' ') ?> €</span>
                            <span class="old"><?= number_format($produit['prix_original'], 2, ',', ' ') ?> €</span>
                            <span class="reduction">-<?= $reduction ?>%</span>
                        </div>
                        <div class="card-actions">
                            <a href="panier-ajouter.php?id=<?= $produit['id'] ?>&qte=1" class="btn-add">Ajouter au panier</a>
                        </div>
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
        // Menu hamburger
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
        }

        function toggleFav(btn) {
            const icon = btn.querySelector('i');
            icon.classList.toggle('far');
            icon.classList.toggle('fas');
            btn.classList.toggle('fav-active');
        }

        function quickView(btn) {
            const card = btn.closest('.product-card');
            const name = card.querySelector('.product-name').textContent;
            alert('🖥️ Aperçu rapide : ' + name);
        }
    </script>

</body>
</html>