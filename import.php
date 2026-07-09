<?php
// import-cj-produits.php - Importe les produits CJ dans EasyPick

require_once 'db.php';

echo "<h1>📦 Importation des produits CJ Dropshipping</h1>";

$fichier = 'produits_cj.csv';

if (!file_exists($fichier)) {
    die("❌ Fichier CSV introuvable. <br>
         📁 Télécharge le fichier depuis CJ Dropshipping et mets-le dans le dossier du site avec le nom <strong>produits_cj.csv</strong>");
}

$handle = fopen($fichier, 'r');
if (!$handle) {
    die("❌ Impossible d'ouvrir le fichier.");
}

$entetes = fgetcsv($handle);

echo "<p>📋 Colonnes trouvées : " . implode(' → ', $entetes) . "</p>";
echo "<hr>";

$compteur = 0;
$erreurs = 0;

while (($ligne = fgetcsv($handle)) !== false) {
    // ✅ LECTURE DES COLONNES
    $nom = trim($ligne[0] ?? 'Produit CJ');
    $image = trim($ligne[1] ?? '');
    $sku = trim($ligne[2] ?? '');                 // ✅ SKU
    $couleur = trim($ligne[3] ?? '');             // ✅ Couleur
    $entrepot = trim($ligne[4] ?? '');            // ✅ Entrepôt
    $inventaire = (int) ($ligne[5] ?? 0);
    $prix_base = (float) ($ligne[14] ?? 0);
    $frais_livraison = (float) ($ligne[15] ?? 0);
    $stock = (int) ($ligne[5] ?? 10);
    
    // ============================================================
    // ✅ CALCUL DES PRIX
    // ============================================================
    $prix_achat = $prix_base + $frais_livraison;
    $prix_vente = $prix_achat; // Tu modifieras toi-même
    
    $prix_achat = round($prix_achat, 2);
    $prix_vente = round($prix_vente, 2);
    
    // ============================================================
    // ✅ CONSTRUCTION DU NOM ET DE LA DESCRIPTION
    // ============================================================
    
    // Nom avec couleur
    if (!empty($couleur)) {
        $nom_complet = $nom . ' - ' . $couleur;
    } else {
        $nom_complet = $nom;
    }
    
    // ✅ DESCRIPTION COMPLÈTE AVEC SKU, ENTREPÔT, COULEUR, LIVRAISON
    $description = "SKU: $sku | Entrepôt: $entrepot | Couleur: $couleur | Livraison: " . number_format($frais_livraison, 2) . " €";
    
    // Vérifier que le prix est valide
    if ($prix_base <= 0) {
        $prix_base = 9.99;
        $prix_achat = $prix_base + $frais_livraison;
        $prix_vente = $prix_achat;
    }
    
    // Vérifier que le nom n'est pas vide
    if (empty($nom)) {
        $erreurs++;
        continue;
    }
    
    // ✅ Insérer dans la base
    try {
        $stmt = $pdo->prepare('INSERT INTO produits 
            (nom, description, prix, prix_achat, stock, image, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())');
        
        $stmt->execute([
            $nom_complet,           // Nom du produit
            $description,           // ✅ Description avec SKU
            $prix_vente,            // Prix de vente
            $prix_achat,            // Prix d'achat (produit + livraison)
            $stock,                 // Stock
            $image                  // Image
        ]);
        
        $compteur++;
        
        // Affichage du résultat
        echo "✅ Produit importé : <strong>$nom_complet</strong><br>";
        echo "&nbsp;&nbsp;&nbsp;📦 Prix base: " . number_format($prix_base, 2) . " € | ";
        echo "🚚 Livraison: " . number_format($frais_livraison, 2) . " € | ";
        echo "💰 Prix achat: " . number_format($prix_achat, 2) . " €<br>";
        echo "&nbsp;&nbsp;&nbsp;🔑 SKU: <strong>$sku</strong> | 📍 Entrepôt: $entrepot<br><br>";
        
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