<?php
session_start();
require_once 'db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($query) < 1) {
    exit;
}

// Rechercher dans les produits (nom, description, référence)
$stmt = $pdo->prepare("
    SELECT id, nom, description, prix, prix_old, image, 'produit' as type 
    FROM produits 
    WHERE nom LIKE ? OR description LIKE ? 
    LIMIT 10
");
$searchTerm = '%' . $query . '%';
$stmt->execute([$searchTerm, $searchTerm]);
$produits = $stmt->fetchAll();

// Rechercher dans les catégories
$stmt = $pdo->prepare("
    SELECT id, nom, NULL as description, NULL as prix, NULL as prix_old, NULL as image, 'categorie' as type 
    FROM categories 
    WHERE nom LIKE ? 
    LIMIT 5
");
$stmt->execute([$searchTerm]);
$categories = $stmt->fetchAll();

// Rechercher dans les marques
$stmt = $pdo->prepare("
    SELECT id, nom, NULL as description, NULL as prix, NULL as prix_old, NULL as image, 'marque' as type 
    FROM marques 
    WHERE nom LIKE ? 
    LIMIT 5
");
$stmt->execute([$searchTerm]);
$marques = $stmt->fetchAll();

// Fusionner les résultats
$results = array_merge($produits, $categories, $marques);

if (empty($results)) {
    echo '<div class="no-result"><i class="fas fa-search" style="font-size:24px; display:block; margin-bottom:8px;"></i>Aucun résultat trouvé pour "<strong>' . htmlspecialchars($query) . '</strong>"</div>';
    exit;
}

foreach ($results as $item) {
    $icon = 'fa-box';
    $link = 'boutique.php?search=' . urlencode($item['nom']);
    if ($item['type'] === 'categorie') {
        $icon = 'fa-tags';
        $link = 'boutique.php?categorie=' . $item['id'];
    } elseif ($item['type'] === 'marque') {
        $icon = 'fa-tag';
        $link = 'boutique.php?marque=' . $item['id'];
    }
    ?>
    <a href="<?= $link ?>" class="result-item">
        <?php if ($item['image']): ?>
            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['nom']) ?>" />
        <?php else: ?>
            <div style="width:40px; height:40px; background:#0d0d0d; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#ff6a00; font-size:18px;">
                <i class="fas <?= $icon ?>"></i>
            </div>
        <?php endif; ?>
        <div class="info">
            <div class="name"><?= htmlspecialchars($item['nom']) ?></div>
            <div class="desc">
                <?php if ($item['type'] === 'produit'): ?>
                    <?= htmlspecialchars(substr($item['description'] ?? '', 0, 60)) ?>...
                <?php elseif ($item['type'] === 'categorie'): ?>
                    Catégorie
                <?php else: ?>
                    Marque
                <?php endif; ?>
            </div>
        </div>
        <?php if ($item['prix']): ?>
            <div class="price"><?= number_format($item['prix'], 2, ',', ' ') ?> €</div>
        <?php endif; ?>
    </a>
    <?php
}
?>