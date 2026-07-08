<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // Mettre la catégorie à NULL pour les produits avant de supprimer
    $pdo->prepare('UPDATE produits SET categorie_id = NULL WHERE categorie_id = ?')->execute([$id]);
    $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
}

header('Location: admin-categories.php');
exit;