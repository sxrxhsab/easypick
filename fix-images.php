<?php
require_once 'db.php';

echo "<h1>🔧 Correction des images</h1>";

// Récupérer les produits sans images multiples
$stmt = $pdo->query("SELECT id, image FROM produits WHERE images IS NULL OR images = '' OR images = '[]'");
$produits = $stmt->fetchAll();

$compteur = 0;
foreach ($produits as $p) {
    if (!empty($p['image'])) {
        // Ajouter l'image principale comme image unique
        $images_array = [$p['image']];
        $images_json = json_encode($images_array);
        
        $stmt_update = $pdo->prepare('UPDATE produits SET images = ? WHERE id = ?');
        $stmt_update->execute([$images_json, $p['id']]);
        $compteur++;
        echo "✅ Produit ID " . $p['id'] . " - images ajoutées<br>";
    }
}

echo "<hr>";
echo "<h2>✅ $compteur produits corrigés !</h2>";
echo "<br><a href='admin-produits.php'>Voir les produits</a>";
?>