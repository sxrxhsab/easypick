<?php
ob_start();
require_once 'db.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Base de Données</title>
    <style>
        body { font-family: Arial, sans-serif; background: #151515; color: #fff; padding: 40px; }
        .box { background: #1A1A1A; padding: 30px; border-radius: 12px; max-width: 900px; margin: 0 auto; }
        h1 { color: #ff6a00; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); }
        th { color: #ff6a00; }
        .success { color: #00b894; }
        .error { color: #ff4444; }
        pre { background: #0F0F0F; padding: 15px; border-radius: 8px; overflow: auto; max-height: 400px; }
    </style>
</head>
<body>
    <div class="box">';

try {
    // 1. Tester la connexion
    echo '<h1>🔍 Test de la Base de Données</h1>';
    
    // 2. Compter les produits
    $count = $pdo->query('SELECT COUNT(*) FROM produits')->fetchColumn();
    echo '<p><strong>📦 Nombre total de produits :</strong> <span class="success">' . $count . '</span></p>';
    
    // 3. Récupérer les 5 premiers produits
    $query = $pdo->query('SELECT * FROM produits LIMIT 5');
    $produits = $query->fetchAll();
    
    if (count($produits) > 0) {
        echo '<h2>📋 Derniers produits (5 premiers) :</h2>';
        echo '<table>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prix (€)</th>
                    <th>Stock</th>
                    <th>Image</th>
                </tr>';
        
        foreach ($produits as $p) {
            echo '<tr>
                    <td>' . $p['id'] . '</td>
                    <td>' . htmlspecialchars($p['nom']) . '</td>
                    <td>' . number_format($p['prix'], 2, ',', ' ') . '</td>
                    <td>' . $p['stock'] . '</td>
                    <td>' . htmlspecialchars($p['image'] ?? 'Aucune') . '</td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo '<p class="error">⚠️ Aucun produit trouvé dans la base.</p>';
    }
    
    // 4. Afficher les colonnes de la table
    $columns = $pdo->query('DESCRIBE produits')->fetchAll();
    echo '<h2>📊 Structure de la table "produits" :</h2>';
    echo '<table>';
    echo '<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Défaut</th></tr>';
    foreach ($columns as $col) {
        echo '<tr>
                <td>' . $col['Field'] . '</td>
                <td>' . $col['Type'] . '</td>
                <td>' . $col['Null'] . '</td>
                <td>' . ($col['Default'] ?? 'NULL') . '</td>
              </tr>';
    }
    echo '</table>';
    
    // 5. Afficher les catégories
    $categories = $pdo->query('SELECT * FROM categories')->fetchAll();
    echo '<h2>📂 Catégories :</h2>';
    if (count($categories) > 0) {
        echo '<ul>';
        foreach ($categories as $cat) {
            echo '<li>#' . $cat['id'] . ' - ' . htmlspecialchars($cat['nom']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="error">⚠️ Aucune catégorie trouvée.</p>';
    }
    
    echo '<hr>';
    echo '<p class="success">✅ Connexion à la base de données OK !</p>';
    
} catch (PDOException $e) {
    echo '<h2 class="error">❌ Erreur :</h2>';
    echo '<p class="error">' . $e->getMessage() . '</p>';
    echo '<p>Vérifie les identifiants dans <strong>db.php</strong></p>';
}

echo '</div>
</body>
</html>';
?>