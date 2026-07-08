<?php
// supprimer-bom.php - Supprime les BOM UTF-8 de tous les fichiers .php

function removeBOM($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getPathname();
            $content = file_get_contents($path);
            // Vérifier si le fichier commence par BOM (EF BB BF)
            if (strpos($content, "\xEF\xBB\xBF") === 0) {
                $content = substr($content, 3);
                file_put_contents($path, $content);
                echo "✅ BOM supprimé : $path\n";
            }
        }
    }
}

removeBOM(__DIR__);
echo "🎉 Terminé !\n";
?>