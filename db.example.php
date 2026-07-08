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
// db.example.php - Modèle pour la connexion à la base de données
// Copiez ce fichier en db.php et modifiez les valeurs

$host = 'localhost';
$port = 3306;
$dbname = 'easypick';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    // ...
} catch (PDOException $e) {
    die('Erreur de connexion MySQL : ' . $e->getMessage());
}
?>
