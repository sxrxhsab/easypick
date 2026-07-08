<?php
// newsletter-notification.php - Envoyer une notification aux inscrits

function envoyerNotificationNouveauxProduits($nb_produits_ajoutes) {
    global $pdo;
    
    // Vérifier si la table newsletter existe
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'newsletter'");
        if ($stmt->rowCount() == 0) {
            return; // La table n'existe pas, on sort
        }
    } catch (PDOException $e) {
        return;
    }
    
    // Récupérer les emails des inscrits
    try {
        $stmt = $pdo->query('SELECT email FROM newsletter');
        $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        return;
    }
    
    if (empty($emails)) return;

    // Lire le compteur actuel depuis un fichier
    $fichier_compteur = __DIR__ . '/compteur_ajouts.txt';
    $total_ajouts = 0;
    if (file_exists($fichier_compteur)) {
        $total_ajouts = (int) file_get_contents($fichier_compteur);
    }
    
    $total_ajouts += $nb_produits_ajoutes;
    
    // Si on a atteint 10 nouveaux produits
    if ($total_ajouts >= 10) {
        $sujet = '🚀 Nouveaux produits sur EasyPick !';
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background: #151515; color: #fff; }
                .container { max-width: 600px; margin: 0 auto; padding: 40px; background: #1A1A1A; border-radius: 20px; }
                h1 { color: #ff6a00; font-size: 28px; }
                .btn { display: inline-block; padding: 14px 35px; background: #ff6a00; color: #fff; border-radius: 50px; text-decoration: none; font-weight: 700; }
                .footer { margin-top: 30px; color: rgba(255,255,255,0.3); font-size: 12px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>🚀 10 nouveaux produits !</h1>
                <p style='font-size: 16px; color: rgba(255,255,255,0.7);'>Découvrez les dernières nouveautés tech sélectionnées pour vous.</p>
                <br />
                <a href='https://easypick.onrender.com/nouveautes.php' class='btn'>Voir les nouveautés</a>
                <div class='footer'>EasyPick - Votre guide tech depuis 2025</div>
            </div>
        </body>
        </html>
        ";
        
        // Envoyer l'email à tous les inscrits
        require_once 'config-email.php';
        if (function_exists('envoyerEmail')) {
            foreach ($emails as $email) {
                envoyerEmail($email, 'Client EasyPick', 'notification', 0, [], $sujet, $message);
            }
        }
        
        // Réinitialiser le compteur
        $total_ajouts = 0;
    }
    
    // Stocker le compteur
    file_put_contents($fichier_compteur, $total_ajouts);
}
?>