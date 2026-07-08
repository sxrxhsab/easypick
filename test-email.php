<?php
ob_start();
require_once 'config-email.php';

// Vérifier si la fonction existe
if (!function_exists('envoyerEmail')) {
    die('❌ La fonction envoyerEmail() n\'existe pas dans config-email.php');
}

// Tester l'envoi d'un email
$destinataire = 'sabeursarah66@gmail.com'; // Mets ton email pour tester
$prenom = 'Sarah';
$nom = 'Sabeur';
$reference = 'TEST-001';
$total = 0.00;
$articles = [
    ['nom' => 'Produit de test', 'quantite' => 1, 'prix' => 0.00]
];

$envoi = envoyerEmail($destinataire, $prenom, $reference, $total, $articles);

if ($envoi) {
    echo "✅ Email envoyé avec succès à $destinataire !<br>";
    echo "Vérifie ta boîte mail (et les spams).";
} else {
    echo "❌ Erreur lors de l'envoi de l'email. Vérifie la configuration SMTP.";
}
?>