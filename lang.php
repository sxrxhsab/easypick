<?php
// lang.php - Gestion du multilangue (centralisé)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'fr';
$_SESSION['lang'] = $lang;

$translations = [];
$langFile = __DIR__ . '/lang/' . $lang . '.php';
if (file_exists($langFile)) {
    $translations = require_once $langFile;
}

if (!function_exists('__')) {
    function __($key) {
        global $translations;
        return $translations[$key] ?? $key;
    }
}
?>