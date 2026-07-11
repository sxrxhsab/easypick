<?php
require_once 'db.php';

echo "<h1>🔧 Correction des images avec picsum.photos</h1>";

// Récupérer tous les produits
$stmt = $pdo->query("SELECT id, nom, image FROM produits");
$produits = $stmt->fetchAll();

$compteur = 0;

foreach ($produits as $p) {
    // Générer un seed unique pour chaque produit
    $seed = 'product_' . $p['id'] . '_' . md5($p['nom']);
    
    $images = [
        'https://picsum.photos/seed/' . $seed . '_1/600/400',
        'https://picsum.photos/seed/' . $seed . '_2/600/400',
        'https://picsum.photos/seed/' . $seed . '_3/600/400',
        'https://picsum.photos/seed/' . $seed . '_4/600/400'
    ];
    
    // Ajouter l'image principale si elle existe
    if (!empty($p['image'])) {
        array_unshift($images, $p['image']);
    }
    
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
echo "<br><a href='produit.php?id=1264'>Voir le produit 1264</a>";
?>