<?php
// panier-maj.php - Met à jour les quantités dans le panier
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantites'])) {
    foreach ($_POST['quantites'] as $id => $qte) {
        $id = (int)$id;
        $qte = max(1, (int)$qte);
        if ($qte <= 0) {
            unset($_SESSION['<?= __('panier') ?>'][$id]);
        } else {
            $_SESSION['<?= __('panier') ?>'][$id] = $qte;
        }
    }
}

// Rediriger vers le <?= __('panier') ?>
header('Location: <?= __('panier') ?>.php');
exit;
