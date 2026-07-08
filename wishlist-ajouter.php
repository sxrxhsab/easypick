<?php
ob_start();
require_once 'db.php';

$email = 'admin@easypick.com';
$password = 'password';

echo '<h2>🔑 Test mot de passe</h2>';

try {
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        echo '✅ Utilisateur trouvé : ' . htmlspecialchars($user['email']) . '<br>';
        echo 'Hash stocké : ' . htmlspecialchars($user['password']) . '<br>';
        echo 'Mot de passe testé : ' . htmlspecialchars($password) . '<br>';
        
        if (password_verify($password, $user['password'])) {
            echo '✅ Le mot de passe est CORRECT !<br>';
            echo '🔓 Connecte-toi avec admin@easypick.com / password';
        } else {
            echo '❌ Le mot de passe ne correspond pas au hash.';
        }
    } else {
        echo '❌ Utilisateur non trouvé.';
        echo '<br><a href="create-admin.php">Créer un compte admin</a>';
    }
} catch (PDOException $e) {
    echo '❌ Erreur : ' . $e->getMessage();
}
?>