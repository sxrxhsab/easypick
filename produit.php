<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: boutique.php');
    exit;
}

// Récupérer le produit
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: boutique.php');
    exit;
}

// Récupérer les produits similaires (même catégorie)
$stmt = $pdo->prepare('SELECT * FROM produits WHERE categorie_id = ? AND id != ? ORDER BY RANDOM() LIMIT 4');
$stmt->execute([$produit['categorie_id'], $id]);
$similaires = $stmt->fetchAll();

// Nombre d'articles dans le panier
$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;

// Variables pour la navbar
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – <?= htmlspecialchars($produit['nom']) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 30px; }

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
        .navbar-simple .nav-icons .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 12px rgba(255,106,0,0.4); }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .hero-shop { position: relative; padding: 30px 0 25px; min-height: 200px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); overflow: hidden; display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .hero-shop .container { position: relative; z-index: 1; text-align: center; }
        .hero-shop h1 { font-size: 38px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
        .hero-shop h1 .orange { color: #ff6a00; }
        .hero-shop .breadcrumb { color: rgba(255,255,255,0.3); font-size: 14px; margin-top: 4px; }
        .hero-shop .breadcrumb a { color: #ff6a00; }
        .hero-shop .breadcrumb .sep { margin: 0 8px; color: rgba(255,255,255,0.1); }
        .hero-shop .breadcrumb .current { color: rgba(255,255,255,0.4); }

        .product-section { padding: 40px 0 60px; background: #151515; }
        .product-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: start; }
        .product-gallery { position: sticky; top: 90px; }
        .main-image { border-radius: 20px; overflow: hidden; background: #1A1A1A; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 16px; }
        .main-image img { width: 100%; height: 480px; object-fit: contain; padding: 30px; transition: transform 0.4s; }
        .main-image:hover img { transform: scale(1.02); }
        .thumbnails { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .thumbnails img { border-radius: 14px; background: #1A1A1A; padding: 12px; height: 90px; object-fit: contain; border: 2px solid transparent; cursor: pointer; transition: all 0.3s; border-color: rgba(255,255,255,0.06); }
        .thumbnails img:hover { border-color: rgba(255,106,0,0.3); transform: scale(1.03); }
        .thumbnails img.active { border-color: #ff6a00; box-shadow: 0 0 20px rgba(255,106,0,0.15); }

        .product-info .product-name { font-size: 32px; font-weight: 700; line-height: 1.2; margin-bottom: 6px; }
        .product-info .product-ref { color: rgba(255,255,255,0.25); font-size: 13px; margin-bottom: 12px; }
        .product-info .product-rating { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .product-info .product-rating .stars { color: #ffb800; font-size: 18px; }
        .product-info .product-rating .stars .grey { color: #444; }
        .product-info .product-rating .reviews-count { color: rgba(255,255,255,0.4); font-size: 14px; }
        .product-info .product-rating .reviews-count a { color: #ff6a00; }
        .product-info .product-price { display: flex; align-items: center; gap: 16px; margin-bottom: 18px; }
        .product-info .product-price .current { font-size: 36px; font-weight: 900; color: #ff6a00; letter-spacing: -0.5px; }
        .product-info .product-price .old { font-size: 20px; color: rgba(255,255,255,0.2); text-decoration: line-through; }
        .product-info .product-price .discount { background: rgba(255,106,0,0.15); color: #ff6a00; padding: 4px 14px; border-radius: 30px; font-size: 13px; font-weight: 700; }
        .product-info .product-short-desc { color: rgba(255,255,255,0.6); font-size: 16px; line-height: 1.7; margin-bottom: 25px; border-left: 3px solid #ff6a00; padding-left: 16px; }
        .product-actions { display: flex; flex-wrap: wrap; gap: 16px; align-items: center; margin-bottom: 25px; }
        .qty-selector { display: flex; align-items: center; background: #1A1A1A; border-radius: 60px; border: 1px solid rgba(255,255,255,0.06); overflow: hidden; }
        .qty-selector button { width: 48px; height: 48px; background: transparent; border: none; color: #fff; font-size: 22px; font-weight: 300; cursor: pointer; transition: background 0.3s; }
        .qty-selector button:hover { background: rgba(255,106,0,0.12); }
        .qty-selector input { width: 50px; height: 48px; background: transparent; border: none; color: #fff; text-align: center; font-size: 18px; font-weight: 700; font-family: 'Poppins', sans-serif; outline: none; }
        .qty-selector input::-webkit-outer-spin-button, .qty-selector input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .qty-selector input[type="number"] { -moz-appearance: textfield; }
        .btn-add-cart { flex: 1; min-width: 180px; padding: 14px 30px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 60px; font-weight: 700; font-size: 18px; cursor: pointer; transition: all 0.4s; box-shadow: 0 8px 30px rgba(255,106,0,0.15); font-family: 'Poppins', sans-serif; text-align: center; display: inline-block; }
        .btn-add-cart:hover { transform: scale(1.02); box-shadow: 0 12px 40px rgba(255,106,0,0.25); background: linear-gradient(135deg, #ff7d1a, #ff8c33); }
        .btn-add-cart.added { background: #00b894; }
        .btn-buy-now { padding: 14px 35px; background: transparent; color: #fff; border: 2px solid rgba(255,255,255,0.15); border-radius: 60px; font-weight: 700; font-size: 18px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-buy-now:hover { background: rgba(255,255,255,0.04); color: #ff6a00; border-color: #ff6a00; transform: scale(1.02); }
        .product-extras { display: flex; flex-wrap: wrap; gap: 20px; padding: 20px 0; border-top: 1px solid rgba(255,255,255,0.06); margin-top: 6px; }
        .product-extras .extra-item { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.5); font-size: 14px; }
        .product-extras .extra-item i { color: #ff6a00; font-size: 18px; }
        .product-extras .extra-item .in-stock { color: #00b894; }

        .product-tabs { margin-top: 45px; }
        .tabs-nav { display: flex; gap: 30px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 0; flex-wrap: wrap; }
        .tabs-nav button { background: none; border: none; color: rgba(255,255,255,0.4); font-size: 17px; font-weight: 700; font-family: 'Poppins', sans-serif; padding: 10px 0 14px; cursor: pointer; position: relative; transition: color 0.3s; }
        .tabs-nav button::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 0; height: 3px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .tabs-nav button:hover { color: #fff; }
        .tabs-nav button.active { color: #ff6a00; }
        .tabs-nav button.active::after { width: 100%; }
        .tab-content { padding: 30px 0; }
        .tab-pane { display: none; animation: fadeIn 0.4s; }
        .tab-pane.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .tab-pane p { color: rgba(255,255,255,0.6); line-height: 1.8; font-size: 16px; }
        .tab-pane ul { list-style: none; color: rgba(255,255,255,0.6); font-size: 16px; }
        .tab-pane ul li { padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.04); display: flex; gap: 12px; }
        .tab-pane ul li strong { color: #fff; min-width: 130px; }
        .tab-pane ul li:last-child { border-bottom: none; }

        .review-item { padding: 18px 0; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .review-item:last-child { border-bottom: none; }
        .review-item .review-header { display: flex; align-items: center; gap: 14px; margin-bottom: 6px; }
        .review-item .review-header .review-avatar { width: 40px; height: 40px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 15px; }
        .review-item .review-header .review-name { font-weight: 600; color: #fff; }
        .review-item .review-header .review-date { color: rgba(255,255,255,0.2); font-size: 12px; margin-left: auto; }
        .review-item .review-stars { color: #ffb800; font-size: 14px; margin-bottom: 4px; }
        .review-item .review-text { color: rgba(255,255,255,0.5); font-size: 15px; line-height: 1.6; }

        .related-products { padding: 40px 0 70px; background: #151515; border-top: 1px solid rgba(255,255,255,0.04); }
        .related-products .section-title { font-size: 28px; font-weight: 700; margin-bottom: 30px; text-align: center; }
        .related-products .section-title span { color: #ff6a00; }
        .related-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; }
        .related-card { background: #202020; border-radius: 20px; padding: 20px 20px 24px; text-align: center; transition: transform 0.4s, box-shadow 0.4s; border: 1px solid rgba(255,255,255,0.06); }
        .related-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(255,106,0,0.08); border-color: rgba(255,106,0,0.1); }
        .related-card img { height: 140px; object-fit: contain; background: #151515; border-radius: 14px; margin-bottom: 12px; width: 100%; padding: 12px; }
        .related-card h4 { font-size: 15px; font-weight: 600; margin-bottom: 4px; color: #fff; }
        .related-card .related-price { font-size: 20px; font-weight: 700; color: #ff6a00; }
        .related-card .related-price .old { font-size: 14px; color: rgba(255,255,255,0.2); text-decoration: line-through; margin-right: 6px; font-weight: 400; }
        .related-card .btn-related { margin-top: 12px; padding: 8px 24px; border-radius: 50px; background: transparent; border: 1.5px solid #ff6a00; color: #ff6a00; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .related-card .btn-related:hover { background: #ff6a00; color: #fff; box-shadow: 0 8px 25px rgba(255,106,0,0.15); }

        .footer { background: #0F0F0F; padding: 40px 0 20px; border-top: 1px solid rgba(255,255,255,0.04); text-align: center; color: rgba(255,255,255,0.12); font-size: 13px; }
        .footer a { color: #ff6a00; }

        @media (max-width: 992px) { .product-layout { grid-template-columns: 1fr; gap: 35px; } .product-gallery { position: relative; top: 0; } .main-image img { height: 380px; } .related-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .container { padding: 0 16px; } .navbar-simple { height: 60px; padding: 0 16px; } .navbar-simple .logo-text { font-size: 18px; } .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); } .navbar-simple .nav-menu.open { display: flex; } .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); } .navbar-simple .hamburger { display: flex; } .navbar-simple .nav-icons { gap: 14px; } .navbar-simple .nav-icons a { font-size: 16px; } .hero-shop h1 { font-size: 28px; } .main-image img { height: 280px; padding: 20px; } .thumbnails img { height: 70px; padding: 8px; } .product-info .product-name { font-size: 26px; } .product-info .product-price .current { font-size: 30px; } .product-actions { flex-direction: column; align-items: stretch; } .btn-buy-now { text-align: center; padding: 14px; } .btn-add-cart { min-width: 0; } .tabs-nav { gap: 16px; } .tabs-nav button { font-size: 15px; padding: 8px 0 12px; } .related-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; } }
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
                <li><a href="nouveautes.php"><?= __('nouveautes') ?></a></li>
                <li><a href="promotions.php"><?= __('promotions') ?></a></li>
                <li><a href="contact.php"><?= __('contact') ?></a></li>
            </ul>
            <div class="nav-icons">
                <a href="#" aria-label="Recherche"><i class="fas fa-search"></i></a>
                <a href="#" aria-label="Favoris"><i class="far fa-heart"></i></a>
                <?php if ($user_connecte): ?>
                    <a href="mon-compte.php" aria-label='<?= __('mon_compte') ?>'><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="login.php" aria-label='<?= __('connexion') ?>'><i class="fas fa-user"></i></a>
                <?php endif; ?>
                <a href="panier.php" aria-label='<?= __('panier') ?>' style="position:relative;">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge"><?= $nb_articles ?></span>
                </a>
                <?php if ($user_connecte && $user_role === 'admin'): ?>
                    <a href="admin.php" aria-label="Admin"><i class="fas fa-cog"></i></a>
                <?php endif; ?>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== HERO ===== -->
    <section class="hero-shop">
        <div class="container">
            <h1><span class="orange">FICHE</span> PRODUIT</h1>
            <div class="breadcrumb">
                <a href="index.php"><?= __('accueil') ?></a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <a href="boutique.php"><?= __('boutique') ?></a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <span class="current"><?= htmlspecialchars($produit['nom']) ?></span>
            </div>
        </div>
    </section>

    <!-- ===== PRODUIT ===== -->
    <section class="product-section">
        <div class="container">
            <div class="product-layout">

                <!-- Galerie -->
                <div class="product-gallery">
                    <div class="main-image">
                        <img id="mainImage" src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" />
                    </div>
                    <div class="thumbnails">
                        <img src="<?= htmlspecialchars($produit['image']) ?>" alt="Vue principale" class="active" onclick="changeImage(this, '<?= htmlspecialchars($produit['image']) ?>')" />
                        <?php 
                        $images = json_decode($produit['images'], true);
                        if ($images && is_array($images)):
                            foreach ($images as $img): 
                        ?>
                            <img src="<?= htmlspecialchars($img) ?>" alt="Vue supplémentaire" onclick="changeImage(this, '<?= htmlspecialchars($img) ?>')" />
                        <?php endforeach; endif; ?>
                    </div>
                </div>

                <!-- Infos -->
                <div class="product-info">
                    <h1 class="product-name"><?= htmlspecialchars($produit['nom']) ?></h1>
                    <div class="product-ref">Référence : EAS-<?= str_pad($produit['id'], 4, '0', STR_PAD_LEFT) ?></div>

                    <div class="product-rating">
                        <div class="stars">
                            <?php
                            $note = round($produit['note'] * 2) / 2;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $note) echo '<i class="fas fa-star"></i>';
                                elseif ($i - 0.5 <= $note) echo '<i class="fas fa-star-half-alt"></i>';
                                else echo '<i class="fas fa-star grey"></i>';
                            }
                            ?>
                        </div>
                        <span class="reviews-count"><?= number_format($produit['note'], 1, ',', ' ') ?>/5 – <a href="#reviews"><?= $produit['nb_avis'] ?> <?= __('avis') ?></a></span>
                    </div>

                    <div class="product-price">
                        <span class="current"><?= number_format($produit['prix'], 2, ',', ' ') ?> €</span>
                        <?php if ($produit['prix_old']): ?>
                            <span class="old"><?= number_format($produit['prix_old'], 2, ',', ' ') ?> €</span>
                            <span class="discount">-<?= round((1 - $produit['prix'] / $produit['prix_old']) * 100) ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-short-desc">
                        <?= nl2br(htmlspecialchars($produit['description'])) ?>
                    </div>

                    <div class="product-actions">
                        <div class="qty-selector">
                            <button onclick="updateQty(-1)">−</button>
                            <input type="number" id="qtyInput" value="1" min="1" max="99" />
                            <button onclick="updateQty(1)">+</button>
                        </div>
                        <a href="panier-ajouter.php?id=<?= $produit['id'] ?>&qte=1" class="btn-add-cart">
                            <i class="fas fa-shopping-cart"></i> <?= __('ajouter') ?> au <?= __('panier') ?>
                        </a>
                        <button class="btn-buy-now" onclick="buyNow()"><?= __('acheter_maintenant') ?></button>
                    </div>

                    <div class="product-extras">
                        <span class="extra-item"><i class="fas fa-truck"></i> <?= __('livraison') ?> offerte</span>
                        <span class="extra-item"><i class="fas fa-undo-alt"></i> Retours sous 30 jours</span>
                        <span class="extra-item"><i class="fas fa-shield-alt"></i> Garantie 2 ans</span>
                        <span class="extra-item"><i class="fas fa-check-circle in-stock"></i> <?= __('en_stock') ?> (<?= $produit['stock'] ?> unités)</span>
                    </div>

                    <!-- Onglets -->
                    <div class="product-tabs">
                        <div class="tabs-nav">
                            <button class="active" data-tab="desc">Description</button>
                            <button data-tab="specs">Caractéristiques</button>
                            <button data-tab="reviews" id="reviews"><?= __('avis') ?> clients</button>
                        </div>
                        <div class="tab-content">

                            <div class="tab-pane active" id="tab-desc">
                                <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
                                <p style="margin-top:15px;">Ce produit est soigneusement sélectionné par EasyPick pour vous garantir qualité et performance.</p>
                            </div>

                            <div class="tab-pane" id="tab-specs">
                                <ul>
                                    <li><strong>Référence</strong> EAS-<?= str_pad($produit['id'], 4, '0', STR_PAD_LEFT) ?></li>
                                    <li><strong>Catégorie</strong> <?= $produit['categorie_id'] ?></li>
                                    <li><strong>Stock</strong> <?= $produit['stock'] ?> unités</li>
                                    <li><strong>Note</strong> <?= number_format($produit['note'], 1, ',', ' ') ?>/5</li>
                                    <li><strong>Nombre d'<?= __('avis') ?></strong> <?= $produit['nb_avis'] ?></li>
                                </ul>
                            </div>

                            <!-- ✅ SECTION AVIS CORRIGÉE -->
                            <div class="tab-pane" id="tab-reviews">
                                <?php
                                // Récupérer les avis du produit
                                $stmt = $pdo->prepare('SELECT * FROM avis WHERE produit_id = ? ORDER BY created_at DESC');
                                $stmt->execute([$id]);
                                $avis = $stmt->fetchAll();
                                ?>

                                <?php if (empty($avis)): ?>
                                    <p style="color:rgba(255,255,255,0.4);">Aucun <?= __('avis') ?> pour le moment. Soyez le premier à donner votre <?= __('avis') ?> !</p>
                                <?php else: ?>
                                    <?php foreach ($avis as $a): ?>
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-avatar"><?= strtoupper(substr($a['nom'], 0, 2)) ?></div>
                                            <span class="review-name"><?= htmlspecialchars($a['nom']) ?></span>
                                            <span class="review-date"><?= date('d/m/Y', strtotime($a['created_at'])) ?></span>
                                        </div>
                                        <div class="review-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $a['note']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-star grey"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="review-text"><?= nl2br(htmlspecialchars($a['commentaire'])) ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <!-- ✅ Messages de succès/erreur -->
                                <?php if (isset($_SESSION['succes_avis'])): ?>
                                    <div style="background:rgba(0,184,148,0.1); border:1px solid rgba(0,184,148,0.2); color:#00b894; padding:10px 14px; border-radius:10px; margin-bottom:16px; font-size:13px;">
                                        <?= htmlspecialchars($_SESSION['succes_avis']) ?>
                                        <?php unset($_SESSION['succes_avis']); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['erreur_avis'])): ?>
                                    <div style="background:rgba(255,68,68,0.1); border:1px solid rgba(255,68,68,0.2); color:#ff4444; padding:10px 14px; border-radius:10px; margin-bottom:16px; font-size:13px;">
                                        <?= htmlspecialchars($_SESSION['erreur_avis']) ?>
                                        <?php unset($_SESSION['erreur_avis']); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- ✅ Formulaire accessible à TOUS (même sans compte) -->
                                <div style="margin-top:30px; padding-top:20px; border-top:1px solid rgba(255,255,255,0.06);">
                                    <h4 style="font-size:18px; font-weight:700; margin-bottom:12px;"><?= __('donner_votre_avis') ?></h4>
                                    <form method="POST" action="ajouter-avis.php">
                                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>" />

                                        <div class="form-group" style="margin-bottom:12px;">
                                            <label style="display:block; font-size:14px; font-weight:600; margin-bottom:4px; color:rgba(255,255,255,0.7);">Votre <?= __('nom') ?> *</label>
                                            <input type="text" name="nom" required placeholder="Votre nom" style="width:100%; padding:12px 16px; background:#0F0F0F; border:1px solid rgba(255,255,255,0.06); border-radius:12px; color:#fff; font-size:14px; font-family:'Poppins', sans-serif; outline:none;" />
                                        </div>

                                        <div class="form-group" style="margin-bottom:12px;">
                                            <label style="display:block; font-size:14px; font-weight:600; margin-bottom:4px; color:rgba(255,255,255,0.7);">Note :</label>
                                            <div style="display:flex; gap:12px; font-size:24px; color:#ffb800;">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star" onclick="setNote(<?= $i ?>)" style="cursor:pointer; color:#444;"></i>
                                                <?php endfor; ?>
                                                <input type="hidden" name="note" id="note" value="0" required />
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom:12px;">
                                            <label style="display:block; font-size:14px; font-weight:600; margin-bottom:4px; color:rgba(255,255,255,0.7);">Commentaire :</label>
                                            <textarea name="commentaire" required placeholder="Partagez votre expérience avec ce produit..." style="width:100%; padding:12px 16px; background:#0F0F0F; border:1px solid rgba(255,255,255,0.06); border-radius:12px; color:#fff; font-size:14px; font-family:'Poppins', sans-serif; outline:none; min-height:80px; resize:vertical;"></textarea>
                                        </div>

                                        <button type="submit" style="padding:10px 24px; background:linear-gradient(135deg, #ff6a00, #ff7d1a); color:#fff; border:none; border-radius:14px; font-weight:700; font-size:14px; cursor:pointer; transition:all 0.3s; font-family:'Poppins', sans-serif;">
                                            <i class="fas fa-paper-plane"></i> <?= __('publier') ?> mon <?= __('avis') ?>
                                        </button>
                                    </form>
                                </div>

                            </div>
                            <!-- FIN SECTION AVIS -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PRODUITS SIMILAIRES ===== -->
    <?php if (!empty($similaires)): ?>
    <section class="related-products">
        <div class="container">
            <h2 class="section-title">Vous aimerez <span>aussi</span></h2>
            <div class="related-grid">
                <?php foreach ($similaires as $similaire): ?>
                <div class="related-card">
                    <img src="<?= htmlspecialchars($similaire['image']) ?>" alt="<?= htmlspecialchars($similaire['nom']) ?>" />
                    <h4><?= htmlspecialchars($similaire['nom']) ?></h4>
                    <div class="related-price">
                        <?php if ($similaire['prix_old']): ?>
                            <span class="old"><?= number_format($similaire['prix_old'], 2, ',', ' ') ?> €</span>
                        <?php endif; ?>
                        <?= number_format($similaire['prix'], 2, ',', ' ') ?> €
                    </div>
                    <button class="btn-related" onclick="window.location.href='produit.php?id=<?= $similaire['id'] ?>'">Voir</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 EasyPick – <?= __('tous_droits_reserves') ?>. <?= __('design_par') ?> <a href="#">Sarah Sabeur</a>.</p>
        </div>
    </footer>

    <script>
        // Hamburger
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));

        // Galerie
        function changeImage(thumb, src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnails img').forEach(img => img.classList.remove('active'));
            thumb.classList.add('active');
        }

        // Quantité
        function updateQty(change) {
            const input = document.getElementById('qtyInput');
            let val = parseInt(input.value) + change;
            if (isNaN(val) || val < 1) val = 1;
            if (val > 99) val = 99;
            input.value = val;
        }

        // Acheter maintenant
        function buyNow() {
            alert('🛒 Redirection vers la page de paiement...');
            window.location.href = 'checkout.php';
        }

        // Onglets
        const tabs = document.querySelectorAll('.tabs-nav button');
        const panes = {
            desc: document.getElementById('tab-desc'),
            specs: document.getElementById('tab-specs'),
            reviews: document.getElementById('tab-reviews')
        };
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                tabs.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                Object.values(panes).forEach(p => p.classList.remove('active'));
                const target = btn.dataset.tab;
                if (panes[target]) panes[target].classList.add('active');
            });
        });

        // Fonction pour sélectionner la note avec les étoiles
        function setNote(value) {
            document.getElementById('note').value = value;
            const stars = document.querySelectorAll('.tab-pane.active .fa-star');
            stars.forEach((star, index) => {
                star.style.color = index < value ? '#ffb800' : '#444';
            });
        }
    </script>
<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>