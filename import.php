<?php
// import-cj-produits.php - Importe les produits CJ dans EasyPick

require_once 'db.php';

echo "<h1>📦 Importation des produits CJ Dropshipping</h1>";

// 1. Vérifier que le fichier CSV existe
$fichier = 'produits_cj.csv';

if (!file_exists($fichier)) {
    die("❌ Fichier CSV introuvable. <br>
         📁 Télécharge le fichier depuis CJ Dropshipping et mets-le dans le dossier du site avec le nom <strong>produits_cj.csv</strong>");
}

// 2. Lire le CSV
$handle = fopen($fichier, 'r');
if (!$handle) {
    die("❌ Impossible d'ouvrir le fichier.");
}

// Lire la première ligne (en-têtes)
$entetes = fgetcsv($handle);

echo "<p>📋 Colonnes trouvées : " . implode(' → ', $entetes) . "</p>";
echo "<hr>";

$compteur = 0;
$erreurs = 0;

while (($ligne = fgetcsv($handle)) !== false) {
    // ✅ ADAPTATION POUR TON CSV
    $nom = trim($ligne[0] ?? 'Produit CJ');
    $image = trim($ligne[1] ?? '');
    $sku = trim($ligne[2] ?? '');
    $couleur = trim($ligne[3] ?? '');
    $entrepot = trim($ligne[4] ?? '');
    $inventaire = (int) ($ligne[5] ?? 0);
    $prix = (float) ($ligne[14] ?? 0); // Product Base Price ($)
    $frais_livraison = (float) ($ligne[15] ?? 0);
    $total_cost = (float) ($ligne[16] ?? $prix);
    $stock = (int) ($ligne[5] ?? 10); // Inventory
    
    // Construire le nom complet avec la couleur
    if (!empty($couleur)) {
        $nom_complet = $nom . ' - ' . $couleur;
    } else {
        $nom_complet = $nom;
    }
    
    // Description
    $description = "SKU: $sku | Entrepôt: $entrepot | Couleur: $couleur";
    
    // Vérifier que le prix est valide
    if ($prix <= 0) {
        $prix = 9.99;
    }
    
    // Vérifier que le nom n'est pas vide
    if (empty($nom)) {
        $erreurs++;
        continue;
    }
    
    // Insérer dans la base
    try {
        $stmt = $pdo->prepare('INSERT INTO produits (nom, description, prix, prix_achat, stock, image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
$stmt->execute([$nom_complet, 'Produit CJ Dropshipping', $prix * 2.2, $prix, $stock, $image]);
        $compteur++;
        echo "✅ Produit importé : <strong>$nom_complet</strong> - $prix €<br>";
    } catch (PDOException $e) {
        $erreurs++;
        echo "❌ Erreur pour <strong>$nom</strong> : " . $e->getMessage() . "<br>";
    }
}

fclose($handle);

echo "<hr>";
echo "<h2>📊 Résumé :</h2>";
echo "<ul>";
echo "<li>✅ Produits importés : <strong>$compteur</strong></li>";
echo "<li>❌ Erreurs : <strong>$erreurs</strong></li>";
echo "</ul>";
echo "<br><a href='admin-produits.php' style='background:#ff6a00; color:#fff; padding:12px 24px; border-radius:12px; text-decoration:none;'>Voir les produits dans l'admin</a>";
?>