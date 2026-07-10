<?php
require_once 'db.php';

echo "<h1>🔧 FORCE l'ajout des images</h1>";

// Récupérer tous les produits
$stmt = $pdo->query("SELECT id, image FROM produits");
$produits = $stmt->fetchAll();

$compteur = 0;

foreach ($produits as $p) {
    if (empty($p['image'])) {
        continue;
    }
    
    // Construire les URLs des images
    $base = $p['image'];
    $ext = pathinfo($base, PATHINFO_EXTENSION);
    $name = pathinfo($base, PATHINFO_FILENAME);
    
    $images = [
        $base,
        str_replace($name, $name . '_1', $base),
        str_replace($name, $name . '_2', $base),
        str_replace($name, $name . '_3', $base),
        str_replace($name, $name . '_4', $base)
    ];
    
    // Nettoyer les doublons
    $images = array_unique($images);
    $images_json = json_encode(array_values($images));
    
    // Mettre à jour
    $stmt_update = $pdo->prepare('UPDATE produits SET images = ? WHERE id = ?');
    $stmt_update->execute([$images_json, $p['id']]);
    
    $compteur++;
    echo "✅ Produit ID " . $p['id'] . " - " . count($images) . " images<br>";
}

echo "<hr>";
echo "<h2>✅ $compteur produits mis à jour !</h2>";
echo "<br><a href='produit.php?id=1176'>Voir le produit 1176</a>";
?>