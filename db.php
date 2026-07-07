<?php
// db.php pour Render (PostgreSQL avec SSL)

// Récupérer l'URL de la base de données depuis les variables d'environnement
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Parse l'URL PostgreSQL
    $db = parse_url($database_url);
    $host = $db['host'];
    $port = $db['port'] ?? 5432;
    $dbname = ltrim($db['path'], '/');
    $username = $db['user'];
    $password = $db['pass'];
} else {
    // Fallback local
    $host = 'localhost';
    $port = 5432;
    $dbname = 'easypick';
    $username = 'postgres';
    $password = '';
}

try {
    // 🔥 AJOUT : sslmode=require pour Render
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur de connexion PostgreSQL : ' . $e->getMessage());
}
?>