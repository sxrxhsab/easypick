<?php
// config-stripe.php
require_once __DIR__ . '/vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51TqGgdAmISMqsuSTB0s5kdtg7gBQdlS4gDOZJR5FDdiUebb471CzS00sEd7c28Lrluygp3isKkW4EMeQsYMyNufA00gpQNX9qU');

// Configuration des URLs (à adapter selon ton environnement)
define('STRIPE_SUCCESS_URL', 'http://localhost/EasyPick-PHP/stripe-success.php');
define('STRIPE_CANCEL_URL', 'http://localhost/EasyPick-PHP/stripe-cancel.php');
define('STRIPE_WEBHOOK_URL', 'http://localhost/EasyPick-PHP/stripe-webhook.php');
?>