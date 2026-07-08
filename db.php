<?php
// db.php - Connexion à la base de données

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔧 CORRECTION : Utiliser 127.0.0.1 au lieu de localhost
$host = getenv('DB_HOST') ?: '127.0.0.1';  // ← CHANGÉ
$port = getenv('DB_PORT') ?: 3306;
$dbname = getenv('DB_NAME') ?: 'easypick';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'sarah';  // ← VIDE pour WAMP

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur de connexion MySQL : ' . $e->getMessage());
}
?>