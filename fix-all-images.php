<?php
require_once 'db.php';

echo "<h1>🔄 Correction automatique des images</h1>";

// Récupérer tous les produits
$stmt = $pdo->query("SELECT id, image, sku FROM produits");
$produits = $stmt->fetchAll();

$compteur = 0;

foreach ($produits as $p) {
    $images = [];
    
    // Image principale
    if (!empty($p['image'])) {
        $images[] = $p['image'];
    }
    
    // Générer des variantes
    if (!empty($p['image'])) {
        $ext = pathinfo($p['image'], PATHINFO_EXTENSION);
        $base = pathinfo($p['image'], PATHINFO_FILENAME);
        for ($i = 1; $i <= 6; $i++) {
            $variant = str_replace($base, $base . '_' . $i, $p['image']);
            if (!in_array($variant, $images)) {
                $images[] = $variant;
            }
        }
    }
    
    // Si pas d'images, utiliser picsum
    if (count($images) < 3) {
        $seed = !empty($p['sku']) ? $p['sku'] : $p['id'];
        for ($i = 1; $i <= 4; $i++) {
            $images[] = 'https://picsum.photos/seed/' . $seed . '_' . $i . '/600/400';
        }
    }
    
    $images = array_unique($images);
    $images_json = json_encode(array_values($images));
    
    $stmt_update = $pdo->prepare('UPDATE produits SET images = ? WHERE id = ?');
    $stmt_update->execute([$images_json, $p['id']]);
    
    $compteur++;
    echo "✅ Produit ID " . $p['id'] . " - " . count($images) . " images<br>";
}

echo "<hr>";
echo "<h2>✅ $compteur produits corrigés !</h2>";
echo "<br><a href='admin-produits.php'>Voir les produits</a>";
?>