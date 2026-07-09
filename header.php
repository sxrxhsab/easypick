<?php
// header.php - Navbar commune à toutes les pages

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir les variables par défaut
$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';

// Compter les produits dans la wishlist
$nb_wishlist = 0;
if (isset($_SESSION['user_id'])) {
    try {
        require_once 'db.php';
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM wishlist WHERE utilisateur_id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $nb_wishlist = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        $nb_wishlist = 0;
    }
}
?>
<nav class="navbar-simple">
    <div class="nav-container">
        <!-- Logo -->
        <a href="index.php" class="logo-text">
            <span class="easy">EASY</span><span class="pick">PICK</span>
        </a>

        <!-- Menu -->
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php" data-i18n="accueil">Accueil</a></li>
            <li><a href="boutique.php" data-i18n="boutique">Boutique</a></li>
            <li><a href="nouveautes.php" data-i18n="nouveautes">Nouveautés</a></li>
            <li><a href="promotions.php" data-i18n="promotions">Promotions</a></li>
            <li><a href="contact.php" data-i18n="contact">Contact</a></li>
        </ul>

        <!-- Icônes -->
        <div class="nav-icons">
            <!-- Sélecteur de langue -->
            <a href="#" class="lang-selector" data-lang="fr" style="color:#ff6a00; font-weight:700; font-size:14px;">FR</a>
            <a href="#" class="lang-selector" data-lang="en" style="color:rgba(255,255,255,0.4); font-weight:400; font-size:14px;">EN</a>

            <a href="#" aria-label="Recherche"><i class="fas fa-search"></i></a>
            
            <!-- Wishlist -->
            <a href="wishlist.php" aria-label="Wishlist" style="color:#ff6a00; position:relative;">
    <i class="fas fa-heart"></i>
    <?php if ($nb_wishlist > 0): ?>
        <span class="wishlist-badge"><?= $nb_wishlist ?></span>
    <?php endif; ?>
</a>

            <?php if ($user_connecte): ?>
                <a href="mon-compte.php" data-i18n="mon_compte" aria-label="Mon compte"><i class="fas fa-user"></i></a>
            <?php else: ?>
                <a href="login.php" data-i18n="connexion" aria-label="Connexion"><i class="fas fa-user"></i></a>
            <?php endif; ?>

            <!-- Panier -->
            <a href="panier.php" data-i18n="panier" aria-label="Panier" style="position:relative;">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge" style="position:absolute; top:-8px; right:-10px; background:#ff6a00; color:#fff; font-size:9px; font-weight:700; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 0 12px rgba(255,106,0,0.4);"><?= $nb_articles ?></span>
            </a>

            <?php if ($user_connecte && $user_role === 'admin'): ?>
                <a href="admin.php" data-i18n="admin" aria-label="Admin"><i class="fas fa-cog"></i></a>
            <?php endif; ?>

            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- Inclusion du script de traduction -->
<script src="lang.js"></script>

<!-- Styles pour le menu hamburger et la navbar -->
<style>
    .navbar-simple {
        width: 100%;
        height: 68px;
        background: #181818;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        position: sticky;
        top: 0;
        z-index: 1000;
        padding: 0 30px;
    }
    .navbar-simple .nav-container {
        max-width: 1500px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .navbar-simple .logo-text {
        font-weight: 900;
        font-size: 22px;
        letter-spacing: 1px;
    }
    .navbar-simple .logo-text .easy {
        color: #ff6a00;
    }
    .navbar-simple .logo-text .pick {
        color: #fff;
    }
    .navbar-simple .nav-menu {
        display: flex;
        align-items: center;
        gap: 30px;
        list-style: none;
    }
    .navbar-simple .nav-menu li a {
        font-weight: 500;
        font-size: 14px;
        color: rgba(255,255,255,0.6);
        transition: color 0.3s;
        letter-spacing: 0.3px;
        padding: 4px 0;
        position: relative;
    }
    .navbar-simple .nav-menu li a::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 0;
        height: 2px;
        background: #ff6a00;
        border-radius: 10px;
        transition: width 0.3s;
    }
    .navbar-simple .nav-menu li a:hover {
        color: #fff;
    }
    .navbar-simple .nav-menu li a:hover::after {
        width: 100%;
    }
    .navbar-simple .nav-menu li a.active {
        color: #fff;
    }
    .navbar-simple .nav-menu li a.active::after {
        width: 100%;
    }
    .navbar-simple .nav-icons {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .navbar-simple .nav-icons a {
        color: rgba(255,255,255,0.6);
        font-size: 18px;
        transition: color 0.3s;
        position: relative;
    }
    .navbar-simple .nav-icons a:hover {
        color: #ff6a00;
    }
    .navbar-simple .nav-icons .cart-badge,
    .navbar-simple .nav-icons .wishlist-badge {
        position: absolute;
        top: -6px;
        right: -8px;
        background: #ff6a00;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 12px rgba(255,106,0,0.4);
    }
    .navbar-simple .hamburger {
        display: none;
        flex-direction: column;
        gap: 4px;
        cursor: pointer;
        background: none;
        border: none;
        padding: 4px;
    }
    .navbar-simple .hamburger span {
        display: block;
        width: 24px;
        height: 2px;
        background: #fff;
        border-radius: 10px;
        transition: 0.3s;
    }

    @media (max-width: 768px) {
        .navbar-simple {
            height: 60px;
            padding: 0 16px;
        }
        .navbar-simple .logo-text {
            font-size: 18px;
        }
        .navbar-simple .nav-menu {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 60px;
            left: 0;
            width: 100%;
            background: #181818;
            padding: 24px 20px;
            gap: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        .navbar-simple .nav-menu.open {
            display: flex;
        }
        .navbar-simple .nav-menu li a {
            font-size: 16px;
            color: rgba(255,255,255,0.7);
        }
        .navbar-simple .hamburger {
            display: flex;
        }
        .navbar-simple .nav-icons {
            gap: 14px;
        }
        .navbar-simple .nav-icons a {
            font-size: 16px;
        }
    }
</style>