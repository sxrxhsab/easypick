<?php
// db.php pour Render (PostgreSQL avec SSL)

$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Utiliser l'URL complète avec les paramètres (sslmode=require déjà inclus)
    try {
        $pdo = new PDO($database_url);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Erreur de connexion PostgreSQL : ' . $e->getMessage());
    }
} else {
    // Fallback pour le développement local (sans SSL)
    $host = 'localhost';
    $port = 5432;
    $dbname = 'easypick';
    $username = 'postgres';
    $password = '';
    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Erreur de connexion PostgreSQL locale : ' . $e->getMessage());
    }
}
?>