<?php
session_start();
require_once 'db.php';

$reference = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : 'N/A';

// Récupérer la commande pour afficher les détails
$stmt = $pdo->prepare('SELECT * FROM commandes WHERE reference = ?');
$stmt->execute([$reference]);
$commande = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<!-- ... le reste de ta page ... -->
<div class="confirmation-box">
    <div class="icon"><i class="fas fa-check-circle"></i></div>
    <h1>Commande <span>confirmée</span></h1>
    <p>Merci <strong><?= htmlspecialchars($commande['prenom']) ?></strong> !</p>
    <p>Votre commande a été enregistrée avec succès.</p>

    <div class="order-number">
        #<?= htmlspecialchars($reference) ?>
    </div>

    <p style="font-size:14px; color:rgba(255,255,255,0.3);">
        Un <?= __('email') ?> de confirmation a été envoyé à <strong><?= htmlspecialchars($commande['email']) ?></strong>
    </p>

    <a href="index.php" class="btn-continue"><i class="fas fa-home"></i> <?= __('retour_accueil') ?></a>
</div>
