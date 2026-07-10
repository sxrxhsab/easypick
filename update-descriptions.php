<?php
require_once 'db.php';

// Récupérer tous les produits sans description longue
$stmt = $pdo->query('SELECT id, nom, description FROM produits WHERE description_longue IS NULL OR description_longue = ""');
$produits = $stmt->fetchAll();

foreach ($produits as $p) {
    // Extraire le SKU de la description
    $desc = $p['description'];
    preg_match('/SKU: ([^\|]+)/', $desc, $matches);
    $sku = $matches[1] ?? '';
    
    // Générer une description longue
    $description_longue = "Le " . $p['nom'] . " est un produit de qualité sélectionné par EasyPick.\n\n";
    $description_longue .= "Caractéristiques :\n";
    $description_longue .= "- Référence : " . $sku . "\n";
    $description_longue .= "- Catégorie : Accessoires tech\n";
    $description_longue .= "- Qualité : Premium\n\n";
    $description_longue .= "Parfait pour votre quotidien, alliant performance et fiabilité.";
    
    $stmt = $pdo->prepare('UPDATE produits SET description_longue = ? WHERE id = ?');
    $stmt->execute([$description_longue, $p['id']]);
    echo "✅ Produit ID " . $p['id'] . " mis à jour<br>";
}

echo "✅ Tous les produits ont été mis à jour !";
?>