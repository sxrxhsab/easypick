<?php
ob_start();
require_once 'db.php';

$email = 'admin@easypick.com';
$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);
$prenom = 'Admin';
$nom = 'EasyPick';

try {
    $stmt = $pdo->prepare('INSERT INTO utilisateurs (email, password, prenom, nom, role) VALUES (?, ?, ?, ?, "admin")');
    $stmt->execute([$email, $hash, $prenom, $nom]);
    echo "✅ Compte admin créé avec succès !<br>";
    echo "Email : admin@easypick.com<br>";
    echo "Mot de passe : password";
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "❌ Ce compte existe déjà. Supprime-le d'abord ou connecte-toi avec les identifiants existants.";
    } else {
        echo "❌ Erreur : " . $e->getMessage();
    }
}
?>