<?php
session_start();
require_once 'db.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Vérifier que le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: boutique.php');
    exit;
}

$produit_id = isset($_POST['produit_id']) ? (int)$_POST['produit_id'] : 0;
$note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
$commentaire = trim($_POST['commentaire']);

// Validation
if ($produit_id <= 0 || $note < 1 || $note > 5 || empty($commentaire)) {
    $_SESSION['erreur_avis'] = 'Veuillez remplir tous les champs correctement.';
    header('Location: produit.php?id=' . $produit_id);
    exit;
}

// Vérifier que le produit existe
$stmt = $pdo->prepare('SELECT id FROM produits WHERE id = ?');
$stmt->execute([$produit_id]);
if (!$stmt->fetch()) {
    $_SESSION['erreur_avis'] = 'Produit introuvable.';
    header('Location: boutique.php');
    exit;
}

// Insérer l'avis
$stmt = $pdo->prepare('INSERT INTO avis (produit_id, utilisateur_id, nom, note, commentaire) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([
    $produit_id,
    $_SESSION['user_id'],
    $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'],
    $note,
    $commentaire
]);

// Mettre à jour la note moyenne du produit
$stmt = $pdo->prepare('SELECT AVG(note) as avg_note, COUNT(*) as nb FROM avis WHERE produit_id = ?');
$stmt->execute([$produit_id]);
$result = $stmt->fetch();

$stmt = $pdo->prepare('UPDATE produits SET note = ?, nb_avis = ? WHERE id = ?');
$stmt->execute([round($result['avg_note'], 1), $result['nb'], $produit_id]);

$_SESSION['succes_avis'] = 'Merci pour votre avis !';
header('Location: produit.php?id=' . $produit_id);
exit;