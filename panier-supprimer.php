<?php
// panier-supprimer.php - Supprime un produit du panier
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0 && isset($_SESSION['panier'][$id])) {
    unset($_SESSION['panier'][$id]);
}

header('Location: panier.php');
exit;