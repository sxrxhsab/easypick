<?php
// panier-supprimer.php - Supprime un produit du panier
ob_start();
session_start();
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier que l'ID est valide
if ($id <= 0) {
    $_SESSION['erreur_panier'] = 'ID de produit invalide.';
    header('Location: panier.php');
    exit;
}

// Vérifier que le produit existe dans le panier
if (!isset($_SESSION['panier'][$id])) {
    $_SESSION['erreur_panier'] = 'Ce produit n\'est pas dans votre panier.';
    header('Location: panier.php');
    exit;
}

// Supprimer le produit
unset($_SESSION['panier'][$id]);

// Message de confirmation
$_SESSION['succes_panier'] = 'Produit supprimé du panier avec succès !';

header('Location: panier.php');
exit;
?>