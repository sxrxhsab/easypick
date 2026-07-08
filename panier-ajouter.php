<?php
// <?= __('panier') ?>-<?= __('ajouter') ?>.php - Ajoute un produit au <?= __('panier') ?>
session_start();

// Initialiser le <?= __('panier') ?> si inexistant
if (!isset($_SESSION['<?= __('panier') ?>'])) {
    $_SESSION['<?= __('panier') ?>'] = [];
}

// Récupérer les paramètres
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quantite = isset($_GET['qte']) ? max(1, (int)$_GET['qte']) : 1;

if ($id <= 0) {
    header('Location: <?= __('boutique') ?>.php');
    exit;
}

// Si le produit existe déjà dans le <?= __('panier') ?>, augmenter la <?= __('quantite') ?>
if (isset($_SESSION['<?= __('panier') ?>'][$id])) {
    $_SESSION['<?= __('panier') ?>'][$id] += $quantite;
} else {
    $_SESSION['<?= __('panier') ?>'][$id] = $quantite;
}

// Rediriger vers la page précédente ou la <?= __('boutique') ?>
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '<?= __('boutique') ?>.php';
header('Location: ' . $referer);
exit;
