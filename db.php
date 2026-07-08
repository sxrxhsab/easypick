<?php
// db.php - Version avec certificat CA

$host = getenv('DB_HOST') ?: 'easypick-db-sabeursamy66-2547.a.aivencloud.com';
$port = getenv('DB_PORT') ?: 26003;
$dbname = getenv('DB_NAME') ?: 'defaultdb';
$username = getenv('DB_USER') ?: 'avnadmin';
$password = getenv('DB_PASSWORD') ?: 'AVNS_JLGOdhJG2I8e9xkhs99';

// Télécharger le certificat depuis Aiven
$ca_cert = __DIR__ . '/ca.pem';  // Mets le fichier ca.pem dans le même dossier

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 30,
            PDO::MYSQL_ATTR_SSL_CA => $ca_cert,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion MySQL : ' . $e->getMessage());
}
?>