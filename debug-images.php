<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1176;

$stmt = $pdo->prepare('SELECT id, nom, image, images FROM produits WHERE id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();

echo "<h1>🔍 Debug images</h1>";
echo "<p>ID: " . $p['id'] . "</p>";
echo "<p>Nom: " . htmlspecialchars($p['nom']) . "</p>";
echo "<p>Image principale: " . htmlspecialchars($p['image']) . "</p>";
echo "<p>Images (JSON): <pre>" . htmlspecialchars($p['images']) . "</pre></p>";

if (!empty($p['images'])) {
    $images = json_decode($p['images'], true);
    echo "<p>Images décodées: <pre>";
    print_r($images);
    echo "</pre></p>";
} else {
    echo "<p style='color:red;'>❌ La colonne images est vide !</p>";
}

echo "<br><a href='produit.php?id=" . $p['id'] . "'>Voir le produit</a>";
?>