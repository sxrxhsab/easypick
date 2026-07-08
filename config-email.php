<?php
// config-email.php - Configuration SMTP pour l'envoi d'emails
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerEmail($destinataire, $prenom, $reference, $total, $articles, $sujet = '', $corps_personnalise = '') {
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('EMAIL_USER') ?: 'sabeursarah66@gmail.com';
        $mail->Password   = getenv('EMAIL_PASSWORD') ?: 'ton_mot_de_passe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('Easypick75@gmail.com', 'EasyPick');
        $mail->addAddress($destinataire, $prenom);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = $sujet ?: '✅ Confirmation de votre commande EasyPick';

        if ($corps_personnalise) {
            $mail->Body = $corps_personnalise;
            $mail->AltBody = strip_tags($corps_personnalise);
        } else {
            // Corps par défaut (commande)
            $corps = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
                    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                    .header { text-align: center; border-bottom: 2px solid #ff6a00; padding-bottom: 20px; margin-bottom: 20px; }
                    .header h1 { color: #ff6a00; font-size: 28px; }
                    .header h1 span { color: #000; }
                    .order-details { background: #f8f8f8; border-radius: 12px; padding: 15px 20px; margin: 15px 0; }
                    .order-details .ref { font-size: 18px; font-weight: 700; color: #ff6a00; }
                    .table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                    .table th { background: #ff6a00; color: #fff; padding: 10px; text-align: left; }
                    .table td { padding: 10px; border-bottom: 1px solid #ddd; }
                    .total { font-size: 20px; font-weight: 700; text-align: right; color: #ff6a00; padding-top: 10px; border-top: 2px solid #ddd; }
                    .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; }
                    .btn { display: inline-block; padding: 12px 30px; background: #ff6a00; color: #fff; border-radius: 50px; text-decoration: none; font-weight: 700; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1><span>EASY</span>PICK</h1>
                        <p style='color:#666;'>Merci pour votre commande !</p>
                    </div>
                    <p>Bonjour <strong>" . htmlspecialchars($prenom) . "</strong>,</p>
                    <p>Nous vous remercions pour votre commande. Voici le récapitulatif :</p>
                    <div class='order-details'>
                        <p><strong>Référence :</strong> <span class='ref'>#" . htmlspecialchars($reference) . "</span></p>
                        <p><strong>Date :</strong> " . date('d/m/Y H:i') . "</p>
                    </div>
                    <table class='table'>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th style='text-align:center;'>Quantité</th>
                                <th style='text-align:right;'>Prix</th>
                            </tr>
                        </thead>
                        <tbody>";

            foreach ($articles as $article) {
                $corps .= "
                            <tr>
                                <td>" . htmlspecialchars($article['nom']) . "</td>
                                <td style='text-align:center;'>" . $article['quantite'] . "</td>
                                <td style='text-align:right;'>" . number_format($article['prix'] * $article['quantite'], 2, ',', ' ') . " €</td>
                            </tr>";
            }

            $corps .= "
                        </tbody>
                    </table>
                    <div class='total'>
                        Total : " . number_format($total, 2, ',', ' ') . " €
                    </div>
                    <p style='margin: 25px 0; text-align:center;'>
                        <a href='https://easypick.onrender.com/mon-compte.php' class='btn'>Suivre ma commande</a>
                    </p>
                    <div class='footer'>
                        <p>EasyPick – Votre guide tech depuis 2025</p>
                        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $mail->Body = $corps;
            $mail->AltBody = "Merci pour votre commande #" . $reference . " Total : " . number_format($total, 2, ',', ' ') . " €";
        }

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email : " . $mail->ErrorInfo);
        return false;
    }
}
?>