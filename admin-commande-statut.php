<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $statut = $_POST['statut'];

    $stmt = $pdo->prepare('UPDATE commandes SET statut = ? WHERE id = ?');
    $stmt->execute([$statut, $id]);
}

header('Location: admin-commande-detail.php?id=' . $id);
exit;
