<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Fonction de traduction si elle n'existe pas
if (!function_exists('__')) {
    function __($text) {
        return $text;
    }
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
$produits = [];

if (!empty($search)) {
    $sql = 'SELECT * FROM produits WHERE nom LIKE ? OR description LIKE ?';
    $params = ['%' . $search . '%', '%' . $search . '%'];
    
    switch ($sort) {
        case 'price-asc': $sql .= ' ORDER BY prix ASC'; break;
        case 'price-desc': $sql .= ' ORDER BY prix DESC'; break;
        case 'newest': $sql .= ' ORDER BY created_at DESC'; break;
        case 'rating': $sql .= ' ORDER BY note DESC, nb_avis DESC'; break;
        default: $sql .= ' ORDER BY id DESC';
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $produits = $stmt->fetchAll();
}
?>
<?php if (empty($produits)): ?>
    <div style="text-align:center; padding:60px 0; color:rgba(255,255,255,0.4); grid-column:1 / -1;">
        <i class="fas fa-box-open" style="font-size:48px; display:block; margin-bottom:16px;"></i>
        <p>Aucun produit trouvé pour "<strong><?= htmlspecialchars($search) ?></strong>".</p>
        <a href="boutique.php" style="color:#ff6a00; font-weight:600;">Voir tous les produits</a>
    </div>
<?php else: ?>
    <?php foreach ($produits as $index => $produit): 
        // Vérification des favoris
        $est_favori = false;
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt_fav = $pdo->prepare('SELECT produit_id FROM wishlist WHERE utilisateur_id = ? AND produit_id = ?');
                $stmt_fav->execute([$_SESSION['user_id'], $produit['id']]);
                $est_favori = $stmt_fav->fetch();
            } catch (PDOException $e) {
                $est_favori = false;
            }
        }
    ?>
    <div class="product-card fade-up delay-<?= ($index % 4) + 1 ?>">
        <a href="produit.php?id=<?= $produit['id'] ?>" class="product-link-overlay"></a>
        <div class="product-image-wrap">
            <img src="<?= htmlspecialchars($produit['image'] ?? 'https://picsum.photos/seed/' . $produit['id'] . '/300/200') ?>" alt="<?= htmlspecialchars($produit['nom']) ?>" />
            <div class="badges">
                <?php if (isset($produit['est_promo']) && $produit['est_promo'] && isset($produit['prix_old']) && $produit['prix_old']): ?>
                    <span class="badge promo">-<?= round((1 - $produit['prix'] / $produit['prix_old']) * 100) ?>%</span>
                <?php endif; ?>
                <?php if (isset($produit['est_nouveau']) && $produit['est_nouveau']): ?>
                    <span class="badge new">Nouveau</span>
                <?php endif; ?>
                <?php if (isset($produit['est_top']) && $produit['est_top']): ?>
                    <span class="badge top">Top vente</span>
                <?php endif; ?>
            </div>
            <div class="action-buttons">
                <button class="fav-btn" onclick="event.stopPropagation(); toggleFav(<?= $produit['id'] ?>, this)">
                    <i class="<?= $est_favori ? 'fas' : 'far' ?> fa-heart"></i>
                </button>
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
            <?php if (isset($produit['prix_old']) && $produit['prix_old']): ?>
                <span class="old"><?= number_format($produit['prix_old'], 2, ',', ' ') ?> €</span>
            <?php endif; ?>
        </div>
        <div class="card-actions">
            <a href="panier-ajouter.php?id=<?= $produit['id'] ?>&qte=1" class="btn-add">Ajouter au panier</a>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>