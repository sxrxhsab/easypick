<?php
// db.php - Neon PostgreSQL

$host = 'ep-lingering-glade-atuudk2x.c-9.us-east-1.aws.neon.tech';
$port = 5432;
$dbname = 'neondb';
$username = 'neondb_owner';
$password = 'npg_CYJQH82shmin';

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