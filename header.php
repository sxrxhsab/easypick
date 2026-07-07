<?php
// header.php - Navbar commune à toutes les pages
// (les variables $nb_articles, $user_connecte, $user_role doivent être définies AVANT d'inclure ce fichier)
?>
<nav class="navbar-simple">
    <div class="nav-container">
        <a href="index.php" class="logo-text"><span class="easy">EASY</span><span class="pick">PICK</span></a>
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="boutique.php">Boutique</a></li>
            <li><a href="#">Nouveautés</a></li>
            <li><a href="#">Promotions</a></li>
            <li><a href="#">Contact</a></li>
        </ul>

        <!-- ===== ICÔNES NAVBAR DYNAMIQUES ===== -->
        <div class="nav-icons">
            <a href="#" aria-label="Recherche"><i class="fas fa-search"></i></a>
            <a href="#" aria-label="Favoris"><i class="far fa-heart"></i></a>
            <?php if ($user_connecte): ?>
                <a href="mon-compte.php" aria-label="Mon compte"><i class="fas fa-user"></i></a>
            <?php else: ?>
                <a href="login.php" aria-label="Connexion"><i class="fas fa-user"></i></a>
            <?php endif; ?>
            <a href="panier.php" aria-label="Panier" style="position:relative;">
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