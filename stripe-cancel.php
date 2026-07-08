<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Paiement annulé</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        a { text-decoration: none; color: inherit; }
        .cancel-box { background: #1A1A1A; border-radius: 24px; padding: 50px 40px; text-align: center; border: 1px solid rgba(255,255,255,0.06); max-width: 480px; width: 100%; }
        .cancel-box .icon { font-size: 72px; color: #ff6a00; margin-bottom: 16px; }
        .cancel-box h1 { font-size: 32px; font-weight: 900; margin-bottom: 8px; }
        .cancel-box h1 span { color: #ff6a00; }
        .cancel-box p { color: rgba(255,255,255,0.5); font-size: 16px; line-height: 1.7; }
        .cancel-box .btn-retry { display: inline-block; padding: 14px 44px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border-radius: 60px; font-weight: 700; font-size: 16px; transition: all 0.3s; margin-top: 20px; }
        .cancel-box .btn-retry:hover { transform: scale(1.05); box-shadow: 0 12px 35px rgba(255,106,0,0.25); }
        @media (max-width: 768px) { .cancel-box { padding: 30px 20px; } .cancel-box h1 { font-size: 26px; } }
    </style>
</head>
<body>
    <div class="cancel-box">
        <div class="icon"><i class="fas fa-times-circle"></i></div>
        <h1>Paiement <span>annulé</span></h1>
        <p>Vous avez annulé le processus de paiement.</p>
        <p style="font-size:14px; color:rgba(255,255,255,0.3); margin-top:6px;">Aucun montant n'a été prélevé.</p>
        <a href="panier.php" class="btn-retry"><i class="fas fa-arrow-left"></i> Retour au <?= __('panier') ?></a>
    </div>
</body>
</html>
