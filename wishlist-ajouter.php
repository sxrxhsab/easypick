<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$produit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($produit_id) {
    try {
        $stmt = $pdo->prepare('INSERT INTO wishlist (utilisateur_id, produit_id) VALUES (?, ?)');
        $stmt->execute([$_SESSION['user_id'], $produit_id]);
        $_SESSION['success'] = '✅ Produit ajouté aux favoris !';
    } catch (PDOException $e) {
        $_SESSION['error'] = '⚠️ Déjà dans vos favoris.';
    }
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '<?= __('boutique') ?>.php'));
exit;
