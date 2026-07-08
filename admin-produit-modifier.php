<?php
ob_start();
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin-produits.php');
    exit;
}

// Récupérer le produit
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$id]);
$produit = $stmt->fetch();
if (!$produit) {
    header('Location: admin-produits.php');
    exit;
}

$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $slug = strtolower(trim(str_replace(' ', '-', $nom)));
    $description = trim($_POST['description'] ?? '');
    $prix = (float) ($_POST['prix'] ?? 0);
    $prix_old = !empty($_POST['prix_old']) ? (float) $_POST['prix_old'] : null;
    $stock = (int) ($_POST['stock'] ?? 0);
    $categorie_id = (int) ($_POST['categorie_id'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $est_promo = isset($_POST['est_promo']) ? 1 : 0;
    $est_nouveau = isset($_POST['est_nouveau']) ? 1 : 0;
    $est_top = isset($_POST['est_top']) ? 1 : 0;

    if (empty($nom) || empty($description) || $prix <= 0 || empty($image)) {
        $erreur = 'Veuillez remplir tous les champs obligatoires.';
    } else {
        $stmt = $pdo->prepare('UPDATE produits SET nom=?, slug=?, description=?, prix=?, prix_old=?, stock=?, categorie_id=?, image=?, est_promo=?, est_nouveau=?, est_top=? WHERE id=?');
        $stmt->execute([$nom, $slug, $description, $prix, $prix_old, $stock, $categorie_id, $image, $est_promo, $est_nouveau, $est_top, $id]);
        $succes = 'Produit modifié avec succès !';
        
        // Recharger les données
        $stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
        $stmt->execute([$id]);
        $produit = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Modifier produit</title>
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
        .navbar-simple .nav-menu { display: flex; align-items: center; gap: 30px; list-style: none; }
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-menu li a.active { color: #fff; }
        .navbar-simple .nav-menu li a.active::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; gap: 20px; align-items: center; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .section { padding: 40px 0 80px; }
        .form-box { background: #1A1A1A; padding: 40px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.06); }
        .form-box h1 { font-size: 28px; font-weight: 900; margin-bottom: 6px; }
        .form-box h1 span { color: #ff6a00; }
        .form-box .sub { color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 25px; }
        
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px; color: rgba(255,255,255,0.7); }
        .form-group label .required { color: #ff6a00; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 12px 16px; background: #0F0F0F; 
            border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; 
            color: #fff; font-size: 14px; font-family: 'Poppins', sans-serif; 
            outline: none; transition: border-color 0.3s; 
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
            border-color: #ff6a00; 
            box-shadow: 0 0 0 3px rgba(255,106,0,0.06); 
        }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-group .checkbox-group { display: flex; gap: 20px; flex-wrap: wrap; padding-top: 4px; }
        .form-group .checkbox-group label { display: flex; align-items: center; gap: 8px; font-weight: 400; cursor: pointer; font-size: 14px; }
        .form-group .checkbox-group input[type="checkbox"] { width: 18px; height: 18px; accent-color: #ff6a00; margin: 0; }
        
        .btn-submit { 
            padding: 14px 30px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); 
            color: #fff; border: none; border-radius: 14px; 
            font-weight: 700; font-size: 16px; cursor: pointer; 
            transition: all 0.3s; font-family: 'Poppins', sans-serif; 
        }
        .btn-submit:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        
        .error { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff4444; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        .success { background: rgba(0,184,148,0.1); border: 1px solid rgba(0,184,148,0.2); color: #00b894; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        
        .back-link { display: inline-block; margin-top: 16px; color: rgba(255,255,255,0.3); transition: color 0.3s; }
        .back-link:hover { color: #fff; }
        
        @media (max-width:768px) { 
            .container { padding: 0 16px; } 
            .form-box { padding: 24px 16px; }
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
            .navbar-simple .nav-menu.open { display: flex; }
            .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); }
            .navbar-simple .hamburger { display: flex; }
            .navbar-simple .nav-icons { gap: 14px; }
            .navbar-simple .nav-icons a { font-size: 16px; }
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
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="admin-produits.php" class="active">Produits</a></li>
                <li><a href="admin-produit-ajouter.php">Ajouter</a></li>
                <li><a href="admin-commandes.php">Commandes</a></li>
                <li><a href="admin-utilisateurs.php">Utilisateurs</a></li>
            </ul>
            <div class="nav-icons">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
                <button class="hamburger" id="hamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ===== CONTENU ===== -->
    <section class="section">
        <div class="container">
            <div class="form-box">
                <h1>Modifier le <span>produit</span></h1>
                <div class="sub">ID #<?= $produit['id'] ?> – <?= htmlspecialchars($produit['nom']) ?></div>

                <?php if ($erreur): ?>
                    <div class="error"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>
                <?php if ($succes): ?>
                    <div class="success"><?= htmlspecialchars($succes) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nom du produit <span class="required">*</span></label>
                        <input type="text" name="nom" required value="<?= htmlspecialchars($produit['nom']) ?>" />
                    </div>
                    
                    <div class="form-group">
                        <label>Description <span class="required">*</span></label>
                        <textarea name="description" required><?= htmlspecialchars($produit['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Prix (€) <span class="required">*</span></label>
                        <input type="number" name="prix" step="0.01" required value="<?= $produit['prix'] ?>" />
                    </div>
                    
                    <div class="form-group">
                        <label>Ancien prix (€) (optionnel)</label>
                        <input type="number" name="prix_old" step="0.01" value="<?= $produit['prix_old'] ?>" />
                    </div>
                    
                    <div class="form-group">
                        <label>Stock <span class="required">*</span></label>
                        <input type="number" name="stock" required value="<?= $produit['stock'] ?>" />
                    </div>
                    
                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="categorie_id">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $produit['categorie_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>URL de l'image <span class="required">*</span></label>
                        <input type="text" name="image" required value="<?= htmlspecialchars($produit['image']) ?>" />
                        <small>Chemin de l'image dans le dossier uploads/</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Badges</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="est_promo" <?= $produit['est_promo'] ? 'checked' : '' ?> /> Promo</label>
                            <label><input type="checkbox" name="est_nouveau" <?= $produit['est_nouveau'] ? 'checked' : '' ?> /> Nouveau</label>
                            <label><input type="checkbox" name="est_top" <?= $produit['est_top'] ? 'checked' : '' ?> /> Top vente</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                </form>

                <div style="margin-top:16px;">
                    <a href="admin-produits.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
                </div>
            </div>
        </div>
    </section>

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