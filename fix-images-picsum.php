<?php
require_once 'db.php';

echo "<h1>🖼️ Correction des images avec picsum.photos</h1>";

// Récupérer tous les produits
$stmt = $pdo->query("SELECT id, nom, image, sku FROM produits");
$produits = $stmt->fetchAll();

$compteur = 0;

foreach ($produits as $p) {
    $images = [];
    
    // 1. Image principale
    if (!empty($p['image']) && filter_var($p['image'], FILTER_VALIDATE_URL)) {
        $images[] = $p['image'];
    }
    
    // 2. Générer des images picsum
    $seed = !empty($p['sku']) ? $p['sku'] : 'product_' . $p['id'];
    for ($i = 1; $i <= 4; $i++) {
        $images[] = 'https://picsum.photos/seed/' . md5($seed . '_' . $i) . '/600/400';
    }
    
    // Garder seulement les images uniques
    $images = array_unique($images);
    $images_json = json_encode(array_values($images));
    
    $stmt_update = $pdo->prepare('UPDATE produits SET images = ? WHERE id = ?');
    $stmt_update->execute([$images_json, $p['id']]);
    
    $compteur++;
    echo "✅ Produit ID " . $p['id'] . " - " . htmlspecialchars($p['nom']) . " - " . count($images) . " images<br>";
}

echo "<hr>";
echo "<h2>✅ $compteur produits corrigés !</h2>";
echo "<br><a href='admin-produits.php'>Voir les produits</a>";
?>
