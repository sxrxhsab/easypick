<?php
session_start();
require_once 'db.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Forcer l'écriture de la session avant la redirection
            session_write_close();

            if ($user['role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: boutique.php');
            }
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .login-box { background: #1A1A1A; padding: 50px 40px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.06); width: 100%; max-width: 420px; }
        .login-box h1 { font-size: 28px; font-weight: 900; text-align: center; margin-bottom: 8px; }
        .login-box h1 span { color: #ff6a00; }
        .login-box .sub { text-align: center; color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 30px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: rgba(255,255,255,0.7); }
        .form-group input { width: 100%; padding: 14px 18px; background: #0F0F0F; border: 1px solid rgba(255,255,255,0.06); border-radius: 14px; color: #fff; font-size: 15px; font-family: 'Poppins', sans-serif; outline: none; transition: border-color 0.3s; }
        .form-group input:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.06); }
        .btn-login { width: 100%; padding: 16px 0; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 16px; font-weight: 700; font-size: 18px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-login:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        .error { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff4444; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
        .links { text-align: center; margin-top: 18px; color: rgba(255,255,255,0.3); font-size: 14px; }
        .links a { color: #ff6a00; transition: color 0.3s; }
        .links a:hover { color: #ff8833; }
        .back-home { display: inline-block; margin-top: 20px; color: rgba(255,255,255,0.2); font-size: 13px; transition: color 0.3s; }
        .back-home:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1><span>EASY</span>PICK</h1>
        <div class="sub">Connectez-vous à votre compte</div>

        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="vous@exemple.com" />
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••" />
            </div>
            <button type="submit" class="btn-login">Se connecter</button>
        </form>

        <div class="links">
            Pas encore de compte ? <a href="register.php">Créer un compte</a>
        </div>
        <div style="text-align:center;">
            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>