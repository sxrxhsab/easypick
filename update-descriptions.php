<?php
require_once 'db.php';

echo "<h1>🔄 Mise à jour des descriptions</h1>";

// Récupérer tous les produits
$stmt = $pdo->query('SELECT id, nom, description, sku FROM produits');
$produits = $stmt->fetchAll();

$compteur = 0;

foreach ($produits as $p) {
    // Extraire le SKU de la description
    $sku = $p['sku'] ?? '';
    if (empty($sku)) {
        preg_match('/SKU: ([^\|]+)/', $p['description'], $matches);
        $sku = $matches[1] ?? 'Non défini';
    }
    
    // Générer une description longue
    $description_longue = "🔹 " . $p['nom'] . "\n\n";
    $description_longue .= "📦 **Caractéristiques techniques :**\n";
    $description_longue .= "• Référence : " . $sku . "\n";
    $description_longue .= "• Catégorie : Accessoires tech\n";
    $description_longue .= "• Qualité : Premium\n\n";
    $description_longue .= "✅ **Description :**\n";
    $description_longue .= "Ce produit de qualité est soigneusement sélectionné par EasyPick pour vous offrir le meilleur rapport qualité-prix.\n\n";
    $description_longue .= "📦 **Contenu du colis :**\n";
    $description_longue .= "• Produit × 1\n";
    $description_longue .= "• Emballage d'origine\n\n";
    $description_longue .= "🛡️ **Garantie EasyPick :**\n";
    $description_longue .= "• Livraison offerte\n";
    $description_longue .= "• Retours sous 30 jours\n";
    $description_longue .= "• Garantie 2 ans";
    
    // Mettre à jour
    $stmt_update = $pdo->prepare('UPDATE produits SET description_longue = ? WHERE id = ?');
    $stmt_update->execute([$description_longue, $p['id']]);
    $compteur++;
    echo "✅ Produit ID " . $p['id'] . " mis à jour<br>";
}

echo "<hr>";
echo "<h2>✅ $compteur produits mis à jour !</h2>";
echo "<br><a href='admin-produits.php'>Voir les produits</a>";
?>