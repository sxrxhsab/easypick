<?php
require_once 'db.php';
$query = $pdo->query('SELECT * FROM produits LIMIT 5');
$produits = $query->fetchAll();
echo '<pre>';
print_r($<?= __('produits') ?>);
echo '</pre>';
?>
