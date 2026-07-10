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
    $sku = trim($ligne[2] ?? '');
    $couleur = trim($ligne[3] ?? '');
    $entrepot = trim($ligne[4] ?? '');
    $inventaire = (int) ($ligne[5] ?? 0);
    $prix_base = (float) ($ligne[14] ?? 0);
    $frais_livraison = (float) ($ligne[15] ?? 0);
    $stock = (int) ($ligne[5] ?? 10);
    
    // ✅ Récupérer la description depuis le CSV (colonne 6 ou 1)
    $description_csv = trim($ligne[6] ?? '');
    if (empty($description_csv)) {
        $description_csv = trim($ligne[1] ?? '');
    }
    
    // ============================================================
    // ✅ CALCUL DES PRIX
    // ============================================================
    $prix_achat = $prix_base + $frais_livraison;
    $prix_vente = $prix_achat * 2.2; // Marge de 120% (tu peux ajuster)
    
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
    
    // ✅ DESCRIPTION COURTE (avec SKU)
    $description = "SKU: $sku | Entrepôt: $entrepot | Couleur: $couleur | Livraison: " . number_format($frais_livraison, 2) . " €";
    
    // ✅ DESCRIPTION LONGUE (complète pour la page produit)
    if (!empty($description_csv)) {
        // Utiliser la description du CSV
        $description_longue = $description_csv;
    } else {
        // Générer une description complète
        $description_longue = "🔹 $nom_complet\n\n";
        $description_longue .= "📦 **Caractéristiques techniques :**\n";
        $description_longue .= "• Référence : $sku\n";
        $description_longue .= "• Entrepôt : $entrepot\n";
        $description_longue .= "• Couleur : $couleur\n";
        $description_longue .= "• Prix base : " . number_format($prix_base, 2) . " €\n";
        $description_longue .= "• Frais de livraison : " . number_format($frais_livraison, 2) . " €\n\n";
        $description_longue .= "✅ **Description :**\n";
        $description_longue .= "Ce produit de qualité est soigneusement sélectionné par EasyPick pour vous offrir le meilleur rapport qualité-prix.\n\n";
        $description_longue .= "📦 **Contenu du colis :**\n";
        $description_longue .= "• Produit × 1\n";
        $description_longue .= "• Emballage d'origine\n\n";
        $description_longue .= "🛡️ **Garantie EasyPick :**\n";
        $description_longue .= "• Livraison offerte\n";
        $description_longue .= "• Retours sous 30 jours\n";
        $description_longue .= "• Garantie 2 ans";
    }
    
    // Vérifier que le prix est valide
    if ($prix_base <= 0) {
        $prix_base = 9.99;
        $prix_achat = $prix_base + $frais_livraison;
        $prix_vente = $prix_achat * 2.2;
    }
    
    // Vérifier que le nom n'est pas vide
    if (empty($nom)) {
        $erreurs++;
        continue;
    }
    
    // ✅ Insérer dans la base
    try {
        $stmt = $pdo->prepare('INSERT INTO produits 
            (nom, description, description_longue, sku, prix, prix_achat, stock, image, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        
        $stmt->execute([
            $nom_complet,           // Nom du produit
            $description,           // Description courte (avec SKU)
            $description_longue,    // ✅ Description longue
            $sku,                   // ✅ SKU
            $prix_vente,            // Prix de vente (avec marge)
            $prix_achat,            // Prix d'achat (produit + livraison)
            $stock,                 // Stock
            $image                  // Image
        ]);
        
        $compteur++;
        
        // Affichage du résultat
        echo "✅ Produit importé : <strong>$nom_complet</strong><br>";
        echo "&nbsp;&nbsp;&nbsp;📦 Prix base: " . number_format($prix_base, 2) . " € | ";
        echo "🚚 Livraison: " . number_format($frais_livraison, 2) . " € | ";
        echo "💰 Prix achat: " . number_format($prix_achat, 2) . " € | ";
        echo "💲 Prix vente: " . number_format($prix_vente, 2) . " €<br>";
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