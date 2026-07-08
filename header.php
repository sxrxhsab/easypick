<?php

// ===== MULTILANGUE =====
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'fr';
$_SESSION['lang'] = $lang;
$translations = [];
if (file_exists(__DIR__ . '/lang/' . $lang . '.php')) {
    $translations = require_once __DIR__ . '/lang/' . $lang . '.php';
}
function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

?><?php
// header.php - Navbar commune à toutes les pages
// (les variables $nb_articles, $user_connecte, $user_role doivent être définies AVANT d'inclure ce fichier)
?>
<nav class="navbar-simple">
    <div class="nav-container">
        <a href="index.php" class="logo-text"><span class="easy">EASY</span><span class="pick">PICK</span></a>
        <ul class="nav-menu" id="navMenu">
    <li><a href="index.php"><?= __('accueil') ?></a></li>
<li><a href="boutique.php"><?= __('boutique') ?></a></li>
<li><a href="nouveautes.php"><?= __('nouveautes') ?></a></li>
<li><a href="promotions.php"><?= __('promotions') ?></a></li>
<li><a href="contact.php"><?= __('contact') ?></a></li>
        <!-- ===== ICÔNES NAVBAR DYNAMIQUES ===== -->
         <div class="nav-icons">

    <!-- ... autres icônes ... -->
    <a href="?lang=fr" style="color:#ff6a00; font-weight:700; font-size:14px;">FR</a>
<a href="?lang=en" style="color:rgba(255,255,255,0.4); font-weight:700; font-size:14px;">EN</a>
    <a href="wishlist.php" aria-label="Favoris"><i class="fas fa-heart"></i></a>
    <!-- ... -->
</div>
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
