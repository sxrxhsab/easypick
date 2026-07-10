<?php
require_once 'db.php';

echo "<h1>🖼️ Ajout des photos multiples</h1>";

// Exemple pour la batterie externe (ID 1192)
$images = '[
    "https://cf.cjdropshipping.com/20200714/1887360430737.jpg",
    "https://cf.cjdropshipping.com/20200714/1887360430738.jpg",
    "https://cf.cjdropshipping.com/20200714/1887360430739.jpg"
]';

$stmt = $pdo->prepare('UPDATE produits SET images = ? WHERE id = ?');
$stmt->execute([$images, 1192]);
echo "✅ Images ajoutées pour le produit ID 1192<br>";

// ✅ CORRECTION : Utiliser IS NULL et COALESCE
$stmt = $pdo->query("SELECT id, image FROM produits WHERE images IS NULL OR images = '' OR images = '[]'");
$produits = $stmt->fetchAll();

$compteur = 0;
foreach ($produits as $p) {
    if (!empty($p['image'])) {
        // Créer un tableau avec l'image principale
        $images_array = [$p['image']];
        
        // Ajouter une image alternative (générée depuis la même source)
        $alt_image = str_replace('.jpg', '_2.jpg', $p['image']);
        if ($alt_image != $p['image']) {
            $images_array[] = $alt_image;
        }
        
        $images_json = json_encode($images_array);
        $stmt_update = $pdo->prepare('UPDATE produits SET images = ? WHERE id = ?');
        $stmt_update->execute([$images_json, $p['id']]);
        $compteur++;
        echo "✅ Images multiples ajoutées pour le produit ID " . $p['id'] . "<br>";
    }
}

echo "<hr>";
echo "<h2>✅ $compteur produits mis à jour avec des images multiples !</h2>";
echo "<br><a href='admin-produits.php'>Voir les produits</a>";
?>