<?php
require_once 'db.php';

$email = 'admin@easypick.com';
$password = 'password';

$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    echo "✅ Utilisateur trouvé : " . $user['<?= __('email') ?>'] . "<br>";
    echo "Hash stocké : " . $user['password'] . "<br>";
    echo "<?= __('mot_de_passe') ?> testé : " . $password . "<br>";
    if (password_verify($password, $user['password'])) {
        echo "✅ Le <?= __('mot_de_passe') ?> est CORRECT !<br>";
        echo "Tu peux te connecter avec admin@easypick.com / password";
    } else {
        echo "❌ Le mot de passe ne correspond pas au hash.";
    }
} else {
    echo "❌ Utilisateur non trouvé.";
}
?>
