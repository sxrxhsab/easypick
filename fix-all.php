<?php
// fix-all.php - Corrige tous les fichiers PHP

$files = [
    'index.php', 'boutique.php', 'promotions.php', 'nouveautes.php',
    'header.php', 'footer.php', 'panier.php', 'login.php',
    'register.php', 'mon-compte.php', 'contact.php', 'wishlist.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Supprimer le BOM si présent
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        
        // Ajouter ob_start() après <?php si absent
        if (strpos($content, 'ob_start()') === false && strpos($content, '<?php') !== false) {
            $content = preg_replace('/<\?php\s*/', '<?php' . "\nob_start();\n", $content, 1);
        }
        
        // S'assurer que session_start() est présent
        if (strpos($content, 'session_start()') === false && strpos($content, 'session_status()') === false) {
            $content = str_replace('ob_start();', "ob_start();\nsession_start();", $content);
        }
        
        file_put_contents($file, $content);
        echo "✅ Corrigé : $file\n";
    } else {
        echo "⚠️ Fichier manquant : $file\n";
    }
}
echo "\n✅ Correction terminée !\n";