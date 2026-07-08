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

// Empêcher un admin de se supprimer lui-même
if ($id > 0 && $id != $_SESSION['user_id']) {
    // Vérifier si l'utilisateur existe avant de le supprimer
    $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->fetch()) {
        // Supprimer l'utilisateur
        $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
        $stmt->execute([$id]);
    }
}

// Redirection vers la liste des utilisateurs
header('Location: admin-utilisateurs.php');
exit;
?>