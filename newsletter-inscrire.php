<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide.']);
        exit;
    }
    try {
        $stmt = $pdo->prepare('INSERT INTO newsletter (email) VALUES (?)');
        $stmt->execute([$email]);
        echo json_encode(['success' => true, 'message' => '✅ Inscription réussie !']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => '⚠️ Cet <?= __('email') ?> est déjà inscrit.']);
    }
}
