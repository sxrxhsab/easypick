<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$confirmation = isset($_GET['confirm']) ? $_GET['confirm'] : '';

if ($id > 0) {
    // Récupérer les infos du produit
    $stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
    $stmt->execute([$id]);
    $produit = $stmt->fetch();
    
    if (!$produit) {
        header('Location: admin-produits.php?erreur=produit_introuvable');
        exit;
    }
    
    // Si confirmation reçue, supprimer
    if ($confirmation === 'oui') {
        try {
            $pdo->beginTransaction();
            
            // 1. Supprimer les lignes de commandes
            $stmt = $pdo->prepare('DELETE FROM lignes_commandes WHERE produit_id = ?');
            $stmt->execute([$id]);
            
            // 2. Supprimer les avis
            try {
                $stmt = $pdo->prepare('DELETE FROM avis WHERE produit_id = ?');
                $stmt->execute([$id]);
            } catch (PDOException $e) {}
            
            // 3. Supprimer l'image du dossier uploads
            if (!empty($produit['image']) && file_exists($produit['image'])) {
                unlink($produit['image']);
            }
            
            // 4. Supprimer le produit
            $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
            $stmt->execute([$id]);
            
            $pdo->commit();
            header('Location: admin-produits.php?succes=produit_supprime');
            exit;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            die('Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
} else {
    header('Location: admin-produits.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Confirmer la suppression</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .confirm-box { background: #1A1A1A; padding: 40px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.06); max-width: 500px; width: 100%; text-align: center; }
        .confirm-box .icon { font-size: 60px; color: #ff4444; margin-bottom: 16px; }
        .confirm-box h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .confirm-box h1 span { color: #ff4444; }
        .confirm-box p { color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 25px; }
        .confirm-box .product-name { color: #fff; font-weight: 600; }
        .confirm-box .warning { background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.2); color: #ffc107; padding: 10px 14px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; }
        .btn-group { display: flex; gap: 12px; justify-content: center; }
        .btn-cancel { padding: 12px 30px; background: transparent; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; font-weight: 600; transition: all 0.3s; cursor: pointer; font-family: 'Poppins', sans-serif; text-decoration: none; }
        .btn-cancel:hover { background: rgba(255,255,255,0.05); }
        .btn-danger { padding: 12px 30px; background: #ff4444; border: none; border-radius: 12px; color: #fff; font-weight: 700; transition: all 0.3s; cursor: pointer; font-family: 'Poppins', sans-serif; text-decoration: none; }
        .btn-danger:hover { background: #ff6666; transform: scale(1.02); }
    </style>
</head>
<body>
    <div class="confirm-box">
        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h1>Confirmer la <span>suppression</span></h1>
        <div class="warning">
            ⚠️ Ce produit a peut-être des commandes associées. 
            <br>Les lignes de commandes seront également supprimées.
        </div>
        <p>
            Êtes-vous sûr de vouloir supprimer le produit 
            <span class="product-name">"<?= htmlspecialchars($produit['nom']) ?>"</span> ?
            <br /><br />
            <strong style="color:#ff4444;">Cette action est irréversible.</strong>
        </p>
        <div class="btn-group">
            <a href="admin-produits.php" class="btn-cancel"><i class="fas fa-times"></i> Annuler</a>
            <a href="admin-produit-supprimer.php?id=<?= $id ?>&confirm=oui" class="btn-danger"><i class="fas fa-trash"></i> Supprimer</a>
        </div>
    </div>
</body>
</html>