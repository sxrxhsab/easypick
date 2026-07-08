<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin-categories.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->execute([$id]);
$categorie = $stmt->fetch();

if (!$categorie) {
    header('Location: admin-categories.php');
    exit;
}

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $slug = strtolower(trim(str_replace(' ', '-', $nom)));

    if (empty($nom)) {
        $erreur = 'Veuillez saisir un nom.';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE categories SET nom = ?, slug = ? WHERE id = ?');
            $stmt->execute([$nom, $slug, $id]);
            $succes = 'Catégorie modifiée avec succès !';
            $categorie['nom'] = $nom;
            $categorie['slug'] = $slug;
        } catch (PDOException $e) {
            $erreur = 'Erreur : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modifier la catégorie</title>
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

        .section { padding: 40px 0 80px; }
        .form-box { background: #1A1A1A; padding: 30px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.06); }
        .form-box h2 { font-size: 24px; font-weight: 700; margin-bottom: 6px; }
        .form-box h2 span { color: #ff6a00; }
        .form-box .sub { color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px; color: rgba(255,255,255,0.7); }
        .form-group input { width: 100%; padding: 12px 16px; background: #0F0F0F; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; color: #fff; font-size: 14px; font-family: 'Poppins', sans-serif; outline: none; transition: border-color 0.3s; }
        .form-group input:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.06); }
        .btn-submit { padding: 12px 30px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 14px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-submit:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        .error { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff4444; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        .success { background: rgba(0,184,148,0.1); border: 1px solid rgba(0,184,148,0.2); color: #00b894; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        .back-link { display: inline-block; margin-top: 16px; color: rgba(255,255,255,0.3); transition: color 0.3s; }
        .back-link:hover { color: #fff; }

        @media (max-width: 768px) { .container { padding: 0 16px; } .form-box { padding: 20px; } }
    </style>
</head>
<body>

    <?php
    $nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
    $user_connecte = isset($_SESSION['user_id']);
    $user_role = $_SESSION['user_role'] ?? '';
    include 'header.php';
    ?>

    <section class="section">
        <div class="container">
            <div class="form-box">
                <h2>Modifier la <span>catégorie</span></h2>
                <div class="sub">ID #<?= $categorie['id'] ?> – <?= htmlspecialchars($categorie['nom']) ?></div>

                <?php if ($erreur): ?>
                    <div class="error"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>
                <?php if ($succes): ?>
                    <div class="success"><?= htmlspecialchars($succes) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nom de la catégorie *</label>
                        <input type="text" name="nom" required value="<?= htmlspecialchars($categorie['nom']) ?>" />
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Enregistrer</button>
                </form>

                <div style="margin-top:16px;">
                    <a href="admin-categories.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
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