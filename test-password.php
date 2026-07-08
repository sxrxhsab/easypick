<?php
ob_start();
require_once 'db.php';

$email = 'admin@easypick.com';
$password = 'password';

try {
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Test mot de passe</title>
        <style>
            body { font-family: Arial, sans-serif; background: #151515; color: #fff; padding: 40px; }
            .box { background: #1A1A1A; padding: 30px; border-radius: 12px; max-width: 600px; margin: 0 auto; }
            h1 { color: #ff6a00; }
            .success { color: #00b894; }
            .error { color: #ff4444; }
            .info { color: rgba(255,255,255,0.6); }
            pre { background: #0F0F0F; padding: 15px; border-radius: 8px; overflow: auto; }
            hr { border-color: rgba(255,255,255,0.06); }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>🔑 Test du mot de passe</h1>';

    if ($user) {
        echo '<p class="success">✅ Utilisateur trouvé : <strong>' . htmlspecialchars($user['email']) . '</strong></p>';
        echo '<hr>';
        echo '<p class="info">📧 Email : <strong>' . htmlspecialchars($user['email']) . '</strong></p>';
        echo '<p class="info">🔑 Hash stocké : <code>' . htmlspecialchars($user['password']) . '</code></p>';
        echo '<p class="info">🔑 Mot de passe testé : <strong>' . htmlspecialchars($password) . '</strong></p>';
        echo '<hr>';
        
        if (password_verify($password, $user['password'])) {
            echo '<p class="success">✅ Le mot de passe est CORRECT !</p>';
            echo '<p class="success">🔓 Tu peux te connecter avec :</p>';
            echo '<ul>';
            echo '<li>📧 Email : <strong>admin@easypick.com</strong></li>';
            echo '<li>🔑 Mot de passe : <strong>password</strong></li>';
            echo '</ul>';
            echo '<br><a href="login.php" style="display:inline-block; padding:12px 30px; background:#ff6a00; color:#fff; border-radius:12px; text-decoration:none;">Se connecter</a>';
        } else {
            echo '<p class="error">❌ Le mot de passe ne correspond pas au hash.</p>';
            echo '<p class="info">💡 Suggestions :</p>';
            echo '<ul class="info">';
            echo '<li>Vérifie que le mot de passe est bien : <strong>password</strong></li>';
            echo '<li>Recrée un compte admin avec <a href="create-admin.php" style="color:#ff6a00;">create-admin.php</a></li>';
            echo '</ul>';
        }
    } else {
        echo '<p class="error">❌ Utilisateur non trouvé.</p>';
        echo '<p class="info">💡 Crée un compte admin avec <a href="create-admin.php" style="color:#ff6a00;">create-admin.php</a></p>';
    }

    echo '</div>
    </body>
    </html>';

} catch (PDOException $e) {
    echo '<div class="box">';
    echo '<h1 style="color:#ff4444;">❌ Erreur</h1>';
    echo '<p style="color:#ff4444;">' . $e->getMessage() . '</p>';
    echo '</div>';
}
?>