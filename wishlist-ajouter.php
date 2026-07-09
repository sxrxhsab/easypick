<?php
ob_start();
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$produit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($produit_id > 0) {
    try {
        // Vérifier si déjà dans la wishlist
        $stmt = $pdo->prepare('SELECT id FROM wishlist WHERE utilisateur_id = ? AND produit_id = ?');
        $stmt->execute([$_SESSION['user_id'], $produit_id]);
        
        if (!$stmt->fetch()) {
            // Ajouter à la wishlist
            $stmt = $pdo->prepare('INSERT INTO wishlist (utilisateur_id, produit_id) VALUES (?, ?)');
            $stmt->execute([$_SESSION['user_id'], $produit_id]);
            $_SESSION['message_wishlist'] = '✅ Produit ajouté à votre wishlist !';
        } else {
            $_SESSION['message_wishlist'] = '⚠️ Ce produit est déjà dans votre wishlist.';
        }
    } catch (PDOException $e) {
        $_SESSION['message_wishlist'] = '❌ Erreur : ' . $e->getMessage();
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'boutique.php'));
exit;
?>