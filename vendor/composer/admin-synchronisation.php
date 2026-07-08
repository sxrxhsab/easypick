<?php
session_start();
require_once 'db.php';
require_once 'suppliers/BigBuy.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync'])) {
    $api_key = $_POST['api_key'] ?? '';
    if (empty($api_key)) {
        $error = 'Veuillez saisir une clé API.';
    } else {
        try {
            $bigbuy = new BigBuySupplier($api_key);
            $products = $bigbuy->getProducts(1, 50);
            
            if (!isset($products['data'])) {
                throw new Exception('Aucun produit retourné.');
            }
            
            foreach ($products['data'] as $product) {
                // Vérifier si le produit existe déjà
                $stmt = $pdo->prepare('SELECT id FROM produits WHERE sku = ?');
                $stmt->execute([$product['sku']]);
                if (!$stmt->fetch()) {
                    // Insertion du produit
                    $stmt = $pdo->prepare('INSERT INTO produits (nom, description, prix, stock, image, sku) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([
                        $product['name'],
                        $product['description'],
                        $product['price'],
                        $product['stock'],
                        $product['image'],
                        $product['sku']
                    ]);
                } else {
                    // Mise à jour du produit
                    $stmt = $pdo->prepare('UPDATE produits SET prix = ?, stock = ? WHERE sku = ?');
                    $stmt->execute([$product['price'], $product['stock'], $product['sku']]);
                }
            }
            
            $message = count($products['data']) . ' produits synchronisés avec succès !';
        } catch (Exception $e) {
            $error = 'Erreur : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synchronisation BigBuy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* (mêmes styles que admin) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        .navbar-simple { width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; padding: 0 30px; }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }

        .admin-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); }
        .admin-hero h1 { font-size: 34px; font-weight: 900; }
        .admin-hero h1 span { color: #ff6a00; }
        .admin-hero p { color: rgba(255,255,255,0.4); font-size: 15px; margin-top: 4px; }

        .section { padding: 30px 0 60px; }
        .form-box { background: #1A1A1A; padding: 30px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.06); max-width: 600px; margin: 0 auto; }
        .form-box h2 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .form-box .sub { color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 4px; color: rgba(255,255,255,0.7); }
        .form-group input { width: 100%; padding: 12px 16px; background: #0F0F0F; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; color: #fff; font-size: 14px; font-family: 'Poppins', sans-serif; outline: none; transition: border-color 0.3s; }
        .form-group input:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.06); }
        .btn-submit { padding: 12px 30px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 14px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-submit:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        .message { padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: 14px; }
        .message.success { background: rgba(0,184,148,0.1); border: 1px solid rgba(0,184,148,0.2); color: #00b894; }
        .message.error { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff4444; }
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

    <section class="admin-hero">
        <div class="container">
            <h1>Synchronisation <span>BigBuy</span></h1>
            <p>Importez les produits du catalogue BigBuy.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="form-box">
                <h2>🔗 Connexion API BigBuy</h2>
                <div class="sub">Entrez votre clé API pour synchroniser les produits.</div>

                <?php if ($message): ?>
                    <div class="message success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="message error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Clé API BigBuy *</label>
                        <input type="text" name="api_key" placeholder="Entrez votre clé API BigBuy..." required />
                    </div>
                    <button type="submit" name="sync" class="btn-submit"><i class="fas fa-sync-alt"></i> Synchroniser</button>
                </form>

                <div style="margin-top:16px;">
                    <a href="admin.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
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