<?php
require_once __DIR__ . '/vendor/autoload.php';

// Récupérer la clé depuis l'environnement (Render)
$stripe_secret = getenv('STRIPE_SECRET_KEY') ?: 'sk_test_...';
\Stripe\Stripe::setApiKey($stripe_secret);

// URLs de production
define('STRIPE_SUCCESS_URL', 'https://easypick.onrender.com/stripe-success.php');
define('STRIPE_CANCEL_URL', 'https://easypick.onrender.com/stripe-cancel.php');
define('STRIPE_WEBHOOK_URL', 'https://easypick.onrender.com/stripe-webhook.php');
?>
