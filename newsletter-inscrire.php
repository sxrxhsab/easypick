<?php
ob_start();
session_start();
require_once 'db.php';

// Définir l'en-tête JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    // Vérifier si l'email est valide
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide.']);
        exit;
    }
    
    try {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT id FROM newsletter WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà inscrit.']);
            exit;
        }
        
        // Insérer l'email
        $stmt = $pdo->prepare('INSERT INTO newsletter (email, date_inscription) VALUES (?, NOW())');
        $stmt->execute([$email]);
        
        echo json_encode(['success' => true, 'message' => '✅ Inscription réussie !']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
?>