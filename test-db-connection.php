<?php
echo "<h2>🔍 Diagnostic connexion MySQL</h2>";

$host = getenv('DB_HOST') ?: 'easypick-db-sabeursamy66-2547.a.aivencloud.com';
$port = getenv('DB_PORT') ?: 2547;
$dbname = getenv('DB_NAME') ?: 'easypick';
$username = getenv('DB_USER') ?: 'avnadmin';
$password = getenv('DB_PASSWORD') ?: '';

echo "<p>Host: $host</p>";
echo "<p>Port: $port</p>";
echo "<p>DB: $dbname</p>";
echo "<p>User: $username</p>";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    echo "<p style='color:green;'>✅ Connexion réussie !</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>