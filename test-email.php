<?php
require_once 'config-email.php';

// Tester l'envoi d'un email
$destinataire = 'sabeursarah66@gmail.com'; // Mets ton email pour tester
$prenom = 'Sarah';
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