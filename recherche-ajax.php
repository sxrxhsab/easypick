<?php
session_start();
require_once 'db.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$produits = [];

if (!empty($search)) {
    $stmt = $pdo->prepare('SELECT * FROM produits WHERE nom LIKE ? OR description LIKE ? LIMIT 20');
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $produits = $stmt->fetchAll();
}
?>
<?php if (empty($produits)): ?>
    <p style="text-align:center; padding:40px 0; color:rgba(255,255,255,0.4);">
        <i class="fas fa-box-open" style="font-size:48px; display:block; margin-bottom:16px;"></i>
        Aucun produit trouvé pour "<?= htmlspecialchars($search) ?>".
    </p>
<?php else: ?>
    <?php foreach ($produits as $index => $produit): ?>
    <div class="product-card fade-up delay-<?= ($index % 4) + 1 ?>">
        <a href="produit.php?id=<?= $produit['id'] ?>" class="product-link-overlay" aria-label="Voir le produit"></a>
        <div class="product-image-wrap">
            <img src="<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" />
            <div class="badges">
                <?php if ($produit['est_promo'] && $produit['prix_old']): ?>
                    <span class="badge promo">-<?= round((1 - $produit['prix'] / $produit['prix_old']) * 100) ?>%</span>
                <?php endif; ?>
                <?php if ($produit['est_nouveau']): ?>
                    <span class="badge new">Nouveau</span>
                <?php endif; ?>
                <?php if ($produit['est_top']): ?>
                    <span class="badge top">Top vente</span>
                <?php endif; ?>
            </div>
            <div class="action-buttons">
                <button class="fav-btn" onclick="event.stopPropagation(); toggleFav(this)"><i class="far fa-heart"></i></button>
                <button onclick="event.stopPropagation(); quickView(this)"><i class="fas fa-eye"></i></button>
            </div>
        </div>
        <div class="product-name"><?= htmlspecialchars($produit['nom']) ?></div>
        <div class="product-rating">
            <span class="stars">
                <?php
                $note = round($produit['note'] * 2) / 2;
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $note) echo '<i class="fas fa-star"></i>';
                    elseif ($i - 0.5 <= $note) echo '<i class="fas fa-star-half-alt"></i>';
                    else echo '<i class="fas fa-star grey"></i>';
                }
                ?>
            </span>
            <span class="count">(<?= $produit['nb_avis'] ?> avis)</span>
        </div>
        <div class="product-price">
            <span class="current"><?= number_format($produit['prix'], 2, ',', ' ') ?> €</span>
            <?php if ($produit['prix_old']): ?>
                <span class="old"><?= number_format($produit['prix_old'], 2, ',', ' ') ?> €</span>
            <?php endif; ?>
        </div>
        <div class="card-actions">
            <a href="panier-ajouter.php?id=<?= $produit['id'] ?>&qte=1" class="btn-add">Ajouter au panier</a>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>