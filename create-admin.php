<?php
require_once 'db.php';

$email = 'admin@easypick.com';
$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);
$prenom = 'Admin';
$nom = 'EasyPick';

try {
    $stmt = $pdo->prepare('INSERT INTO utilisateurs (email, password, prenom, nom, role) VALUES (?, ?, ?, ?, "admin")');
    $stmt->execute([$<?= __('email') ?>, $hash, $pre<?= __('nom') ?>, $<?= __('nom') ?>]);
    echo "✅ Compte admin créé avec succès !<br>";
    echo "<?= __('email') ?> : admin@easypick.com<br>";
    echo "Mot de passe : password";
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "❌ Ce compte existe déjà. Supprime-le d'abord ou connecte-toi avec les identifiants existants.";
    } else {
        echo "❌ Erreur : " . $e->getMessage();
    }
}
