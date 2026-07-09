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
        $stmt = $pdo->prepare('DELETE FROM wishlist WHERE utilisateur_id = ? AND produit_id = ?');
        $stmt->execute([$_SESSION['user_id'], $produit_id]);
        $_SESSION['message_wishlist'] = '✅ Produit retiré de votre wishlist.';
    } catch (PDOException $e) {
        $_SESSION['message_wishlist'] = '❌ Erreur : ' . $e->getMessage();
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'boutique.php'));
exit;
?>