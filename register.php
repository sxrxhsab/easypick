<?php
ob_start();
session_start();
require_once 'db.php';
$erreur = '';
$succes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 1. Validations
    if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif ($password !== $password_confirm) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $erreur = 'Le mot de passe doit faire au moins 6 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'L\'adresse email est invalide.';
    } else {
        // 2. Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = 'Cet email est déjà utilisé.';
        } else {
            // 3. Hash du mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // 4. Insertion
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (prenom, nom, email, password, role) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$prenom, $nom, $email, $hash, 'user']);

            $succes = 'Compte créé avec succès ! Vous pouvez vous connecter.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Inscription</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
        .register-box { background: #1A1A1A; padding: 40px 35px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.06); width: 100%; max-width: 440px; }
        .register-box h1 { font-size: 28px; font-weight: 900; text-align: center; margin-bottom: 6px; }
        .register-box h1 .easy { color: #ff6a00; }
        .register-box h1 .pick { color: #fff; }
        .register-box .sub { text-align: center; color: rgba(255,255,255,0.4); font-size: 14px; margin-bottom: 25px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px; color: rgba(255,255,255,0.7); }
        .form-group input { width: 100%; padding: 12px 16px; background: #0F0F0F; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; color: #fff; font-size: 14px; font-family: 'Poppins', sans-serif; outline: none; transition: border-color 0.3s; }
        .form-group input:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.06); }
        .btn-register { width: 100%; padding: 14px 0; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 14px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-register:hover { transform: scale(1.02); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        .error { background: rgba(255,68,68,0.1); border: 1px solid rgba(255,68,68,0.2); color: #ff4444; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        .success { background: rgba(0,184,148,0.1); border: 1px solid rgba(0,184,148,0.2); color: #00b894; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
        .links { text-align: center; margin-top: 16px; color: rgba(255,255,255,0.3); font-size: 14px; }
        .links a { color: #ff6a00; transition: color 0.3s; }
        .links a:hover { color: #ff8833; }
        .back-home { display: inline-block; margin-top: 16px; color: rgba(255,255,255,0.2); font-size: 13px; transition: color 0.3s; text-align: center; width: 100%; }
        .back-home:hover { color: #fff; }
        .footer { background: transparent; padding: 20px 0 10px; text-align: center; color: rgba(255,255,255,0.12); font-size: 12px; width: 100%; max-width: 440px; margin-top: 20px; }
        .footer a { color: #ff6a00; }
    </style>
</head>
<body>
    <div class="register-box">
        <h1><span class="easy">EASY</span><span class="pick">PICK</span></h1>
        <div class="sub">Créez votre compte</div>

        <?php if ($erreur): ?>
            <div class="error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        <?php if ($succes): ?>
            <div class="success"><?= htmlspecialchars($succes) ?></div>
        <?php endif; ?>

        <?php if (!$succes): ?>
        <form method="POST">
            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="prenom" required placeholder="Jean" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" />
            </div>
            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="nom" required placeholder="Dupont" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" />
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="vous@exemple.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="Min. 6 caractères" />
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="password_confirm" required placeholder="Répétez le mot de passe" />
            </div>
            <button type="submit" class="btn-register">Créer mon compte</button>
        </form>
        <?php endif; ?>

        <div class="links">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </div>
        <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
    </div>

    <footer class="footer">
        &copy; 2026 EasyPick – Tous droits réservés. Design par <a href="#">Samy Sabeur</a>.
    </footer>
</body>
</html>