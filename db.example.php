<?php
// db.example.php - Modèle pour la connexion à la base de données
// Copiez ce fichier en db.php et modifiez les valeurs

$host = 'localhost';
$port = 3306;
$dbname = 'easypick';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    // ...
} catch (PDOException $e) {
    die('Erreur de connexion MySQL : ' . $e->getMessage());
}
?>