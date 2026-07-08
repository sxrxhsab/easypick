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

if ($id > 0) {
    // Vérifier si le produit existe avant de le supprimer
    $stmt = $pdo->prepare('SELECT id FROM produits WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        // Supprimer le produit
        $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
        $stmt->execute([$id]);
        
        // Optionnel : supprimer aussi l'image du dossier uploads
        // $stmt = $pdo->prepare('SELECT image FROM produits WHERE id = ?');
        // $stmt->execute([$id]);
        // $produit = $stmt->fetch();
        // if ($produit && file_exists($produit['image'])) {
        //     unlink($produit['image']);
        // }
    }
}

// Redirection vers la liste des produits
header('Location: admin-produits.php');
exit;
?>