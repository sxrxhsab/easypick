<?php
// header.php - Version simplifiée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>