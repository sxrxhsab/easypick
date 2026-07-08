<?php
// db.php - Version avec fallback

// Essayer d'abord avec le nom d'hôte, puis avec l'IP directe
$hosts = [
    getenv('DB_HOST') ?: 'easypick-db-sabeursamy66-2547.a.aivencloud.com',
    'XXX.XXX.XXX.XXX'  // ← REMPLACE PAR L'IP TROUVÉE
];

$port = getenv('DB_PORT') ?: 26003;
$dbname = getenv('DB_NAME') ?: 'defaultdb';
$username = getenv('DB_USER') ?: 'avnadmin';
$password = getenv('DB_PASSWORD') ?: 'AVNS_JLGOdhJG2I8e9xkhs99';

$pdo = null;
$lastError = null;

foreach ($hosts as $host) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 30
            ]
        );
        break; // Connexion réussie
    } catch (PDOException $e) {
        $lastError = $e->getMessage();
        continue; // Essayer le prochain host
    }
}

if (!$pdo) {
    die('Erreur de connexion MySQL : ' . $lastError);
}
?>