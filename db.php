<?php
// db.php pour Render (PostgreSQL)

// Récupérer l'URL de la base de données depuis les variables d'environnement Render
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Si Render injecte DATABASE_URL (recommandé)
    $db = parse_url($database_url);
    $host = $db['host'];
    $port = $db['port'] ?? 5432;
    $dbname = ltrim($db['path'], '/');
    $username = $db['user'];
    $password = $db['pass'];
} else {
    // Fallback : informations en dur (pour les tests)
    $host = 'dpg-d961g8eq1p3s73fj7teg-a'; // Remplace par ton hostname
    $port = 5432;
    $dbname = 'easypick_db';
    $username = 'easypick_db_user';
    $password = 'nmalnIW2aHCaMV1mpBN0gc5XknIYSZtQ'; // Mets le mot de passe que tu as récupéré
}

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optionnel : définir le fuseau horaire
    $pdo->exec("SET timezone TO 'Europe/Paris'");
    
} catch (PDOException $e) {
    die('Erreur de connexion PostgreSQL : ' . $e->getMessage());
}
?>