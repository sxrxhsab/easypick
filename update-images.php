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

// Ou pour tous les produits avec une image principale, ajouter une image supplémentaire
$stmt = $pdo->query('SELECT id, image FROM produits WHERE images IS NULL OR images = ""');
$produits = $stmt->fetchAll();

$compteur = 0;
foreach ($produits as $p) {
    if (!empty($p['image'])) {
        // Créer un tableau avec l'image principale et une autre générée
        $images = '["' . $p['image'] . '", "' . str_replace('.jpg', '_2.jpg', $p['image']) . '"]';
        $stmt_update = $pdo->prepare('UPDATE produits SET images = ? WHERE id = ?');
        $stmt_update->execute([$images, $p['id']]);
        $compteur++;
    }
}

echo "<hr>";
echo "<h2>✅ $compteur produits mis à jour avec des images multiples !</h2>";
?>