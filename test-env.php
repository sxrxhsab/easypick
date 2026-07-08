<?php

// ===== MULTILANGUE =====
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'fr';
$_SESSION['lang'] = $lang;
$translations = [];
if (file_exists(__DIR__ . '/lang/' . $lang . '.php')) {
    $translations = require_once __DIR__ . '/lang/' . $lang . '.php';
}
function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

?><?php
echo "DB_HOST = " . getenv('DB_HOST') . "<br>";
echo "DB_PORT = " . getenv('DB_PORT') . "<br>";
echo "DB_NAME = " . getenv('DB_NAME') . "<br>";
echo "DB_USER = " . getenv('DB_USER') . "<br>";
echo "DB_PASSWORD = " . getenv('DB_PASSWORD') . "<br>";
?>
