<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$erreur = '';
$succes = '';

// Récupérer les catégories et marques
$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
$marques = $pdo->query('SELECT * FROM marques')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des champs texte
    $nom = trim($_POST['nom'] ?? '');
    $slug = strtolower(trim(str_replace(' ', '-', $nom)));
    $description = trim($_POST['description'] ?? '');
    $prix = (float) ($_POST['prix'] ?? 0);
    $prix_old = !empty($_POST['prix_old']) ? (float) $_POST['prix_old'] : null;
    $stock = (int) ($_POST['stock'] ?? 0);
    $categorie_id = (int) ($_POST['categorie_id'] ?? 0);
    $marque_id = !empty($_POST['marque_id']) ? (int) $_POST['marque_id'] : null;
    $est_promo = isset($_POST['est_promo']) ? 1 : 0;
    $est_nouveau = isset($_POST['est_nouveau']) ? 1 : 0;
    $est_top = isset($_POST['est_top']) ? 1 : 0;

    // ---- Gestion de l'upload d'image ----
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $max_size = 5 * 1024 * 1024; // 5 Mo

        // Vérifier l'extension
        if (!in_array($extension, $allowed)) {
            $erreur = 'Format d\'image non autorisé. Utilisez JPG, PNG, GIF ou WEBP.';
        }
        // Vérifier la taille
        elseif ($file['size'] > $max_size) {
            $erreur = 'L\'image est trop lourde. Maximum 5 Mo.';
        } else {
            // Créer le dossier uploads s'il n'existe pas
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $nom_fichier = uniqid() . '.' . $extension;
            $destination = $upload_dir . $nom_fichier;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $image_path = $destination;
            } else {
                $erreur = 'Erreur lors de l\'upload de l\'image. Vérifiez les droits du dossier uploads/.';
            }
        }
    } else {
        $erreur = 'Veuillez sélectionner une image.';
    }

    // ---- Gestion des images multiples (optionnel) ----
    $images_paths = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if (!empty($_FILES['images']['name'][$key])) {
                $extension = strtolower(pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION));
                if (in_array($extension, $allowed)) {
                    $nom_fichier = uniqid() . '.' . $extension;
                    if (move_uploaded_file($tmp_name, $upload_dir . $nom_fichier)) {
                        $images_paths[] = $upload_dir . $nom_fichier;
                    }
                }
            }
        }
    }

    // Si tout est bon, on insère dans la BDD
    if (empty($erreur) && !empty($image_path)) {
        if (empty($nom) || empty($description) || $prix <= 0) {
            $erreur = 'Veuillez remplir tous les champs obligatoires (*).';
        } else {
            $images_json = !empty($images_paths) ? json_encode($images_paths) : null;
            
            $stmt = $pdo->prepare('INSERT INTO produits 
                (nom, slug, description, prix, prix_old, stock, categorie_id, marque_id, image, images, est_promo, est_nouveau, est_top) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $nom, $slug, $description, $prix, $prix_old, $stock,
                $categorie_id, $marque_id, $image_path, $images_json,
                $est_promo, $est_nouveau, $est_top
            ]);
            $succes = 'Produit ajouté avec succès !';
            
            // Notification newsletter si 10 produits
            if (file_exists('newsletter-notification.php')) {
                require_once 'newsletter-notification.php';
                if (function_exists('envoyerNotificationNouveauxProduits')) {
                    envoyerNotificationNouveauxProduits(1);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Ajouter un produit</title>
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
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
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
        .form-group input[type="file"] { padding: 10px; background: #0F0F0F; cursor: pointer; }
        .form-group input[type="file"]::file-selector-button {
            padding: 8px 16px; background: #ff6a00; color: #fff;
            border: none; border-radius: 8px; cursor: pointer;
            transition: background 0.3s;
        }
        .form-group input[type="file"]::file-selector-button:hover {
            background: #ff8833;
        }
        .form-group .preview {
            margin-top: 10px; max-width: 200px; border-radius: 12px;
            overflow: hidden; border: 1px solid rgba(255,255,255,0.06);
            display: none;
        }
        .form-group .preview img { width: 100%; display: block; }
        .form-group small { display: block; color: rgba(255,255,255,0.3); font-size: 12px; margin-top: 4px; }

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

        @media (max-width:768px) { .container { padding: 0 16px; } .form-box { padding: 24px 16px; } 
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
                <li><a href="admin-produits.php">Produits</a></li>
                <li><a href="admin-produit-ajouter.php" class="active">Ajouter</a></li>
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
                <h1>Ajouter un <span>produit</span></h1>
                <div class="sub">Remplissez les informations du nouveau produit.</div>

                <?php if ($erreur): ?>
                    <div class="error"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>
                <?php if ($succes): ?>
                    <div class="success"><?= htmlspecialchars($succes) ?></div>
                <?php endif; ?>

                <?php if (!$succes): ?>
                <form method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Nom du produit <span class="required">*</span></label>
                        <input type="text" name="nom" required placeholder="Ex: Casque Bluetooth Pro" />
                    </div>

                    <div class="form-group">
                        <label>Description <span class="required">*</span></label>
                        <textarea name="description" required placeholder="Description détaillée..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Prix (€) <span class="required">*</span></label>
                        <input type="number" name="prix" step="0.01" required placeholder="59.99" />
                    </div>

                    <div class="form-group">
                        <label>Ancien prix (€) (optionnel)</label>
                        <input type="number" name="prix_old" step="0.01" placeholder="79.99" />
                    </div>

                    <div class="form-group">
                        <label>Stock <span class="required">*</span></label>
                        <input type="number" name="stock" required placeholder="50" />
                    </div>

                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="categorie_id">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Marque</label>
                        <select name="marque_id">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($marques as $marque): ?>
                                <option value="<?= $marque['id'] ?>"><?= htmlspecialchars($marque['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Image du produit <span class="required">*</span></label>
                        <input type="file" name="image" accept="image/*" required onchange="previewImage(this)" />
                        <div class="preview" id="previewContainer">
                            <img id="previewImg" src="#" alt="Aperçu" />
                        </div>
                        <small>Formats acceptés : JPG, PNG, GIF, WEBP. Poids max : 5 Mo.</small>
                    </div>

                    <div class="form-group">
                        <label>Badges</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="est_promo" /> Promo</label>
                            <label><input type="checkbox" name="est_nouveau" /> Nouveau</label>
                            <label><input type="checkbox" name="est_top" /> Top vente</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Ajouter le produit</button>
                </form>
                <?php endif; ?>

                <div style="margin-top:16px;">
                    <a href="admin-produits.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Aperçu de l'image
        function previewImage(input) {
            const preview = document.getElementById('previewContainer');
            const img = document.getElementById('previewImg');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        // Hamburger
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
        }
    </script>

</body>
</html>