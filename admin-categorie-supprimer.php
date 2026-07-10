<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$confirmation = isset($_GET['confirm']) ? $_GET['confirm'] : '';

if ($id > 0) {
    // Récupérer le nom de la catégorie
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $categorie = $stmt->fetch();
    
    if (!$categorie) {
        header('Location: admin-categories.php');
        exit;
    }
    
    // Si confirmation reçue, supprimer
    if ($confirmation === 'oui') {
        try {
            // Démarrer une transaction
            $pdo->beginTransaction();
            
            // 1. Mettre la catégorie à NULL pour les produits associés
            $stmt = $pdo->prepare('UPDATE produits SET categorie_id = NULL WHERE categorie_id = ?');
            $stmt->execute([$id]);
            
            // 2. Supprimer la catégorie
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$id]);
            
            // Valider la transaction
            $pdo->commit();
            
            $_SESSION['message'] = '✅ Catégorie supprimée avec succès !';
            header('Location: admin-categories.php');
            exit;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['message'] = '❌ Erreur lors de la suppression : ' . $e->getMessage();
            header('Location: admin-categories.php');
            exit;
        }
    }
} else {
    header('Location: admin-categories.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirmer la suppression</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .confirm-box { background: #1A1A1A; padding: 40px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.06); max-width: 500px; width: 100%; text-align: center; }
        .confirm-box .icon { font-size: 60px; color: #ff4444; margin-bottom: 16px; }
        .confirm-box h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .confirm-box h1 span { color: #ff4444; }
        .confirm-box p { color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 25px; }
        .confirm-box .categorie-name { color: #fff; font-weight: 600; }
        .confirm-box .info { background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.2); color: #ffc107; padding: 10px 14px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
        .btn-group { display: flex; gap: 12px; justify-content: center; }
        .btn-cancel { padding: 12px 30px; background: transparent; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; font-weight: 600; transition: all 0.3s; cursor: pointer; font-family: 'Poppins', sans-serif; text-decoration: none; }
        .btn-cancel:hover { background: rgba(255,255,255,0.05); }
        .btn-danger { padding: 12px 30px; background: #ff4444; border: none; border-radius: 12px; color: #fff; font-weight: 700; transition: all 0.3s; cursor: pointer; font-family: 'Poppins', sans-serif; text-decoration: none; }
        .btn-danger:hover { background: #ff6666; transform: scale(1.02); }
        .back-link { display: inline-block; margin-top: 16px; color: rgba(255,255,255,0.3); transition: color 0.3s; text-decoration: none; }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="confirm-box">
        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h1>Confirmer la <span>suppression</span></h1>
        <div class="info">
            ⚠️ Les produits associés à cette catégorie ne seront pas supprimés.<br>
            Ils seront simplement retirés de la catégorie.
        </div>
        <p>
            Êtes-vous sûr de vouloir supprimer la catégorie 
            <span class="categorie-name">"<?= htmlspecialchars($categorie['nom']) ?>"</span> ?
            <br /><br />
            <strong style="color:#ff4444;">Cette action est irréversible.</strong>
        </p>
        <div class="btn-group">
            <a href="admin-categories.php" class="btn-cancel"><i class="fas fa-times"></i> Annuler</a>
            <a href="admin-categorie-supprimer.php?id=<?= $id ?>&confirm=oui" class="btn-danger"><i class="fas fa-trash"></i> Supprimer</a>
        </div>
        <div style="margin-top:20px;">
            <a href="admin-categories.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour aux catégories</a>
        </div>
    </div>
</body>
</html>