<?php
// db.php - Connexion à PostgreSQL sur Neon

$host = getenv('DB_HOST') ?: 'ep-lingering-glade-atuudk2x.c-9.us-east-1.aws.neon.tech';
$port = getenv('DB_PORT') ?: 5432;
$dbname = getenv('DB_NAME') ?: 'neondb';
$username = getenv('DB_USER') ?: 'neondb_owner';
$password = getenv('DB_PASSWORD') ?: 'npg_CYJQH82shmin';

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 30
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion PostgreSQL : ' . $e->getMessage());
}
?>