<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin-utilisateurs.php');
    exit;
}

// Récupérer l'utilisateur
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: admin-utilisateurs.php');
    exit;
}

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];

    if (!in_array($role, ['admin', 'user'])) {
        $erreur = 'Rôle invalide.';
    } else {
        $stmt = $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?');
        $stmt->execute([$role, $id]);
        $succes = 'Rôle mis à jour avec succès !';
        // Recharger l'utilisateur
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
    }
}

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – <?= __('modifier') ?> utilisateur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 20px; }

        .navbar-simple { width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; padding: 0 30px; }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .nav-icons { display: flex; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .admin-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); }
        .admin-hero h1 { font-size: 34px; font-weight: 900; }
        .admin-hero h1 span { color: #ff6a00; }
        .admin-hero p { color: rgba(255,255,255,0.4); font-size: 15px; margin-top: 4px; }

        .section { padding: 30px 0 60px; }
        .form-box { background: #1A1A1A; padding: 30px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.06); }
        .form-box h2 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .form-box .sub { color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px; color: rgba(255,255,255,0.7); }
        .form-group select { width: 100%; padding: 12px 16px; background: #0F0F0F; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; color: #fff; font-size: 14px; font-family: 'Poppins', sans-serif; outline: none; transition: border-color 0.3s; }
        .form-group select:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.06); }
        .btn-submit { padding: 12px 30px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 14px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-submit:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        .error { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff4444; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        .success { background: rgba(0,184,148,0.1); border: 1px solid rgba(0,184,148,0.2); color: #00b894; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        .back-link { display: inline-block; margin-top: 16px; color: rgba(255,255,255,0.3); transition: color 0.3s; }
        .back-link:hover { color: #fff; }

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
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <section class="admin-hero">
        <div class="container">
            <h1><?= __('modifier') ?> l'<span>utilisateur</span></h1>
            <p>#<?= $user['id'] ?> – <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="form-box">
                <h2><?= __('modifier') ?> le rôle</h2>
                <div class="sub">Rôle actuel : <strong><?= $user['role'] ?></strong></div>

                <?php if ($erreur): ?>
                    <div class="error"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>
                <?php if ($succes): ?>
                    <div class="success"><?= htmlspecialchars($succes) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role">
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> <?= __('enregistrer') ?></button>
                </form>

                <div style="margin-top:16px;">
                    <a href="admin-utilisateurs.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
    </script>
</body>
</html>
