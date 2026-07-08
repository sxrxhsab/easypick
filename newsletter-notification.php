<?php
// newsletter-notification.php - Envoyer une notification aux inscrits

function envoyerNotificationNouveauxProduits($nb_produits_ajoutes) {
    global $pdo;
    
    // Récupérer les emails des inscrits
    $stmt = $pdo->query('SELECT email FROM newsletter');
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($emails)) return;

    // Nombre total de produits (pour vérifier si on a atteint 10 nouveaux)
    static $total_ajouts = 0;
    $total_ajouts += $nb_produits_ajoutes;
    
    // Si on a atteint 10 nouveaux produits
    if ($total_ajouts >= 10) {
        $sujet = '🚀 Nouveaux <?= __('produits') ?> sur EasyPick !';
        $message = "
        <html>
        <body>
            <h2>10 nouveaux <?= __('produits') ?> viennent d'arriver !</h2>
            <p>Découvrez les dernières <?= __('nouveautes') ?> tech sélectionnées pour vous.</p>
            <a href='https://easypick.onrender.com/nouveautes.php' style='display:inline-block; padding:12px 30px; background:#ff6a00; color:#fff; border-radius:50px; text-decoration:none;'>Voir les <?= __('nouveautes') ?></a>
        </body>
        </html>
        ";
        
        require_once 'config-email.php';
        foreach ($emails as $email) {
            envoyerEmail($email, 'Client EasyPick', 'notification', 0, [], $sujet, $message);
        }
        
        // Réinitialiser le compteur
        $total_ajouts = 0;
    }
    
    // Stocker le compteur en session ou en base (pour persistance)
    // Ici on utilise un fichier simple
    file_put_contents('compteur_ajouts.txt', $total_ajouts);
}
?>
