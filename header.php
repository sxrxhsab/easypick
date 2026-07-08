<?php
// header.php - Navbar commune à toutes les pages
// (les variables $nb_articles, $user_connecte, $user_role doivent être définies AVANT d'inclure ce fichier)
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
            <a href="#" class="lang-selector" data-lang="en" style="color:rgba(255,255,255,0.4); font-weight:700; font-size:14px;">EN</a>

            <a href="#" aria-label="Recherche"><i class="fas fa-search"></i></a>
            <a href="wishlist.php" aria-label="Favoris"><i class="fas fa-heart"></i></a>
            <?php if ($user_connecte): ?>
                <a href="mon-compte.php" data-i18n="mon_compte" aria-label="Mon compte"><i class="fas fa-user"></i></a>
            <?php else: ?>
                <a href="login.php" data-i18n="connexion" aria-label="Connexion"><i class="fas fa-user"></i></a>
            <?php endif; ?>
            <a href="panier.php" data-i18n="panier" aria-label="Panier" style="position:relative;">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge"><?= $nb_articles ?></span>
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