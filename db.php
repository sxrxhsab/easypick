<?php
// db.php - Connexion à la base de données
 // ← Inclusion centralisée

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: 3306;
$dbname = getenv('DB_NAME') ?: 'easypick';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'sarah';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4;sslmode=require",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur de connexion MySQL : ' . $e->getMessage());
}
?>