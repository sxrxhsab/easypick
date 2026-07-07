<?php
// panier-ajouter.php - Ajoute un produit au panier
session_start();

// Initialiser le panier si inexistant
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Récupérer les paramètres
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quantite = isset($_GET['qte']) ? max(1, (int)$_GET['qte']) : 1;

if ($id <= 0) {
    header('Location: boutique.php');
    exit;
}

// Si le produit existe déjà dans le panier, augmenter la quantité
if (isset($_SESSION['panier'][$id])) {
    $_SESSION['panier'][$id] += $quantite;
} else {
    $_SESSION['panier'][$id] = $quantite;
}

// Rediriger vers la page précédente ou la boutique
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'boutique.php';
header('Location: ' . $referer);
exit;