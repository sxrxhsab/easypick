<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin-commandes.php');
    exit;
}

// Récupérer la commande
$stmt = $pdo->prepare('SELECT * FROM commandes WHERE id = ?');
$stmt->execute([$id]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: admin-commandes.php');
    exit;
}

// Récupérer les lignes de commande
$stmt = $pdo->prepare('SELECT lc.*, p.nom as produit_nom, p.image 
    FROM lignes_commandes lc 
    JOIN produits p ON lc.produit_id = p.id 
    WHERE lc.commande_id = ?');
$stmt->execute([$id]);
$lignes = $stmt->fetchAll();

// Statistiques
$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Détail commande</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* (reprendre les styles de admin.php) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .navbar-simple { width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; position: sticky; top: 0; z-index: 1000; padding: 0 30px; }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .nav-icons { display: flex; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .admin-hero { padding: 30px 0 20px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); border-bottom: 1px solid rgba(255,255,255,0.04); }
        .admin-hero h1 { font-size: 34px; font-weight: 900; }
        .admin-hero h1 span { color: #ff6a00; }
        .admin-hero p { color: rgba(255,255,255,0.4); font-size: 15px; margin-top: 4px; }
        .section { padding: 30px 0 60px; }

        .detail-card { background: #1A1A1A; border-radius: 16px; padding: 30px; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 30px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .detail-grid .info { color: rgba(255,255,255,0.6); font-size: 14px; }
        .detail-grid .info strong { color: #fff; }

        .statut-select { padding: 8px 16px; background: #0F0F0F; color: #fff; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; font-family: 'Poppins', sans-serif; cursor: pointer; }
        .statut-select:focus { border-color: #ff6a00; }

        .btn-save { padding: 8px 24px; background: #ff6a00; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-save:hover { background: #ff8833; transform: scale(1.02); }

        .back-link { display: inline-block; margin-top: 16px; color: rgba(255,255,255,0.3); transition: color 0.3s; }
        .back-link:hover { color: #fff; }

        .statut { padding: 4px 12px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .statut.en_attente { background: rgba(255,193,7,0.15); color: #ffc107; }
        .statut.payee { background: rgba(0,184,148,0.15); color: #00b894; }
        .statut.expediee { background: rgba(13,202,240,0.15); color: #0dcaf0; }
        .statut.livree { background: rgba(40,167,69,0.15); color: #28a745; }
        .statut.annulee { background: rgba(255,68,68,0.15); color: #ff4444; }

        @media (max-width: 768px) {
            .navbar-simple { height: 60px; padding: 0 16px; }
            .navbar-simple .logo-text { font-size: 18px; }
            .navbar-simple .hamburger { display: flex; }
            .detail-grid { grid-template-columns: 1fr; }
            .admin-hero h1 { font-size: 28px; }
        }
    </style>
</head>
<body>

    <?php
    $nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
    $user_connecte = isset($_SESSION['user_id']);
    $user_role = $_SESSION['user_role'] ?? '';
    include 'header.php';
    ?>

    <section class="admin-hero">
        <div class="container">
            <h1>Détail de la <span>commande</span></h1>
            <p>#<?= htmlspecialchars($commande['reference']) ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <div class="detail-card">
                <div class="detail-grid">
                    <div class="info">
                        <strong>Référence</strong><br>
                        #<?= htmlspecialchars($commande['reference']) ?>
                    </div>
                    <div class="info">
                        <strong>Date</strong><br>
                        <?= date('d/m/Y H:i', strtotime($commande['created_at'])) ?>
                    </div>
                    <div class="info">
                        <strong>Client</strong><br>
                        <?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?><br>
                        <?= htmlspecialchars($commande['email']) ?><br>
                        <?= htmlspecialchars($commande['telephone']) ?>
                    </div>
                    <div class="info">
                        <strong>Adresse de livraison</strong><br>
                        <?= htmlspecialchars($commande['adresse']) ?><br>
                        <?= htmlspecialchars($commande['code_postal'] . ' ' . $commande['ville']) ?><br>
                        <?= htmlspecialchars($commande['pays']) ?>
                    </div>
                    <div class="info">
                        <strong>Total</strong><br>
                        <span style="font-size:24px; font-weight:900; color:#ff6a00;"><?= number_format($commande['total'], 2, ',', ' ') ?> €</span>
                    </div>
                    <div class="info">
                        <strong>Statut</strong><br>
                        <form method="POST" action="admin-commande-statut.php" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                            <input type="hidden" name="id" value="<?= $commande['id'] ?>" />
                            <select name="statut" class="statut-select">
                                <option value="en_attente" <?= $commande['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                                <option value="payee" <?= $commande['statut'] === 'payee' ? 'selected' : '' ?>>Payée</option>
                                <option value="expediee" <?= $commande['statut'] === 'expediee' ? 'selected' : '' ?>>Expédiée</option>
                                <option value="livree" <?= $commande['statut'] === 'livree' ? 'selected' : '' ?>>Livrée</option>
                                <option value="annulee" <?= $commande['statut'] === 'annulee' ? 'selected' : '' ?>>Annulée</option>
                            </select>
                            <button type="submit" class="btn-save">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>

            <h3 style="font-size:20px; font-weight:700; margin-bottom:16px;">🛒 Articles commandés</h3>

            <div class="table-wrap" style="background:#1A1A1A; border-radius:16px; border:1px solid rgba(255,255,255,0.06); padding:10px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:12px 16px; color:rgba(255,255,255,0.3); font-weight:600; font-size:13px; text-transform:uppercase;">Produit</th>
                            <th style="text-align:center; padding:12px 16px; color:rgba(255,255,255,0.3); font-weight:600; font-size:13px; text-transform:uppercase;">Quantité</th>
                            <th style="text-align:right; padding:12px 16px; color:rgba(255,255,255,0.3); font-weight:600; font-size:13px; text-transform:uppercase;">Prix unitaire</th>
                            <th style="text-align:right; padding:12px 16px; color:rgba(255,255,255,0.3); font-weight:600; font-size:13px; text-transform:uppercase;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lignes as $ligne): ?>
                        <tr>
                            <td style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.04);">
                                <?= htmlspecialchars($ligne['produit_nom']) ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.04); text-align:center;">
                                <?= $ligne['quantite'] ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.04); text-align:right;">
                                <?= number_format($ligne['prix_unitaire'], 2, ',', ' ') ?> €
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.04); text-align:right; font-weight:600;">
                                <?= number_format($ligne['prix_unitaire'] * $ligne['quantite'], 2, ',', ' ') ?> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right; padding:12px 16px; font-weight:700; font-size:18px; border-top:1px solid rgba(255,255,255,0.06);">Total</td>
                            <td style="text-align:right; padding:12px 16px; font-weight:700; font-size:18px; color:#ff6a00; border-top:1px solid rgba(255,255,255,0.06);">
                                <?= number_format($commande['total'], 2, ',', ' ') ?> €
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <a href="admin-commandes.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la liste des commandes</a>

        </div>
    </section>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
    </script>

</body>
</html>