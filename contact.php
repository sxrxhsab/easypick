<?php
ob_start();
session_start();
require_once 'db.php';

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';

// Fonction de traduction si elle n'existe pas
if (!function_exists('__')) {
    function __($text) {
        return $text;
    }
}

// Traitement du formulaire de contact
$message_envoye = false;
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse email invalide.';
    } else {
        // Envoyer l'email (à adapter)
        $to = 'contact@easypick.com';
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        $corps = "Nom : $nom\n";
        $corps .= "Email : $email\n";
        $corps .= "Sujet : $sujet\n\n";
        $corps .= "Message :\n$message";

        if (mail($to, $sujet, $corps, $headers)) {
            $message_envoye = true;
        } else {
            $erreur = 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 30px; }

        /* NAVBAR */
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
        .navbar-simple .nav-icons { display: flex; align-items: center; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        /* HERO */
        .page-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); text-align: center; }
        .page-hero h1 { font-size: 34px; font-weight: 900; }
        .page-hero h1 span { color: #ff6a00; }
        .page-hero p { color: rgba(255,255,255,0.4); font-size: 15px; }

        /* CONTACT */
        .contact-section { padding: 50px 0 80px; background: #151515; }
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
        .contact-info h2 { font-size: 28px; font-weight: 700; margin-bottom: 20px; }
        .contact-info h2 span { color: #ff6a00; }
        .contact-info p { color: rgba(255,255,255,0.6); line-height: 1.8; margin-bottom: 30px; }
        .contact-info .info-item { display: flex; gap: 15px; margin-bottom: 20px; }
        .contact-info .info-item i { color: #ff6a00; font-size: 22px; width: 30px; margin-top: 4px; }
        .contact-info .info-item .text { color: rgba(255,255,255,0.7); }
        .contact-info .info-item .text strong { color: #fff; display: block; }

        .contact-form { background: #1A1A1A; border-radius: 20px; padding: 40px; border: 1px solid rgba(255,255,255,0.06); }
        .contact-form .form-group { margin-bottom: 20px; }
        .contact-form .form-group label { display: block; color: rgba(255,255,255,0.6); font-size: 14px; font-weight: 600; margin-bottom: 8px; }
        .contact-form .form-group input,
        .contact-form .form-group textarea { width: 100%; padding: 14px 18px; background: #151515; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; color: #fff; font-family: 'Poppins', sans-serif; font-size: 14px; transition: border-color 0.3s; outline: none; }
        .contact-form .form-group input:focus,
        .contact-form .form-group textarea:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.08); }
        .contact-form .form-group textarea { resize: vertical; min-height: 120px; }
        .contact-form .btn-submit { width: 100%; padding: 16px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 14px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .contact-form .btn-submit:hover { transform: scale(1.02); box-shadow: 0 10px 35px rgba(255,106,0,0.3); }

        .alert-success { background: rgba(0, 184, 148, 0.15); border: 1px solid #00b894; color: #00b894; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; }
        .alert-error { background: rgba(255, 68, 68, 0.15); border: 1px solid #ff4444; color: #ff4444; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; }

        /* FOOTER */
        .footer { background: #0F0F0F; padding: 40px 0 20px; border-top: 1px solid rgba(255,255,255,0.04); text-align: center; color: rgba(255,255,255,0.12); font-size: 13px; }
        .footer a { color: #ff6a00; }

        @media (max-width: 992px) {
            .contact-grid { grid-template-columns: 1fr; gap: 40px; }
            .contact-info { text-align: center; }
            .contact-info .info-item { justify-content: center; }
        }
        @media (max-width: 768px) {
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }
            .container { padding: 0 16px; }
            .contact-form { padding: 24px 20px; }
        }
    </style>
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar-simple">
        <div class="nav-container">
            <div class="logo-text">
                <span><span class="easy">EASY</span><span class="pick">PICK</span></span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="boutique.php">Boutique</a></li>
                <li><a href="nouveautes.php">Nouveautés</a></li>
                <li><a href="promotions.php">Promotions</a></li>
                <li><a href="contact.php" class="active">Contact</a></li>
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
            <h1><span>Contactez-nous</span></h1>
            <p>Une question ? Un projet ? N'hésitez pas à nous écrire.</p>
        </div>
    </section>

    <!-- ===== CONTACT ===== -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2>Discutons <span>ensemble</span></h2>
                    <p>Notre équipe est là pour répondre à toutes vos questions. Que vous soyez un particulier ou une entreprise, nous sommes à votre écoute.</p>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div class="text">
                            <strong>Email</strong>
                            contact@easypick.com
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div class="text">
                            <strong>Téléphone</strong>
                            +33 1 23 45 67 89
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="text">
                            <strong>Adresse</strong>
                            123 Rue de la Tech, 75000 Paris
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div class="text">
                            <strong>Horaires</strong>
                            Lundi - Vendredi : 9h - 18h
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <?php if ($message_envoye): ?>
                        <div class="alert-success">
                            ✅ Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.
                        </div>
                    <?php endif; ?>
                    <?php if ($erreur): ?>
                        <div class="alert-error">
                            ⚠️ <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nom">Nom complet</label>
                            <input type="text" id="nom" name="nom" placeholder="Votre nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="votre@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="sujet">Sujet</label>
                            <input type="text" id="sujet" name="sujet" placeholder="Sujet de votre message" required value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" placeholder="Votre message..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn-submit">Envoyer le message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            &copy; 2026 EasyPick – Tous droits réservés. Design par <a href="#">Samy Sabeur</a>.
        </div>
    </footer>

    <script>
        // Hamburger
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
        }
    </script>

</body>
</html>