<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categorie = isset($_GET['categorie']) ? (int)$_GET['categorie'] : null;
$prix_max = isset($_GET['prix_max']) ? (int)$_GET['prix_max'] : null;
$marque = isset($_GET['marque']) ? (int)$_GET['marque'] : null;
$note_min = isset($_GET['note_min']) ? (int)$_GET['note_min'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';

// Fonction de traduction si elle n'existe pas
if (!function_exists('__')) {
    function __($text) {
        return $text;
    }
}

// Requête SQL de base
$sql = 'SELECT * FROM produits WHERE 1=1';
$params = [];

if (!empty($search)) {
    $sql .= ' AND (nom LIKE ? OR description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}
if ($categorie) {
    $sql .= ' AND categorie_id = ?';
    $params[] = $categorie;
}
if ($prix_max && $prix_max > 0) {
    $sql .= ' AND prix <= ?';
    $params[] = $prix_max;
}
if ($marque) {
    $sql .= ' AND marque_id = ?';
    $params[] = $marque;
}
if ($note_min && $note_min > 0) {
    $sql .= ' AND note >= ?';
    $params[] = $note_min;
}

switch ($sort) {
    case 'price-asc':
        $sql .= ' ORDER BY prix ASC';
        break;
    case 'price-desc':
        $sql .= ' ORDER BY prix DESC';
        break;
    case 'rating':
        $sql .= ' ORDER BY note DESC, nb_avis DESC';
        break;
    case 'newest':
        $sql .= ' ORDER BY created_at DESC';
        break;
    default:
        $sql .= ' ORDER BY id ASC';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produits = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPick – Boutique</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Langue JS -->
    <script src="lang.js"></script>

    <style>
        /* ---------- TOUS LES STYLES EXISTANTS ---------- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #151515; color: #fff; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }
        .container { max-width: 1500px; margin: 0 auto; padding: 0 30px; }
        .fade-up { opacity: 0; transform: translateY(40px); transition: opacity 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94), transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
        .fade-up.delay-1 { transition-delay: 0.1s; }
        .fade-up.delay-2 { transition-delay: 0.2s; }
        .fade-up.delay-3 { transition-delay: 0.3s; }
        .fade-up.delay-4 { transition-delay: 0.4s; }
        .bg-halo { position: absolute; pointer-events: none; border-radius: 50%; filter: blur(100px); z-index: 0; }
        .bg-halo.orange-1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,106,0,0.05) 0%, transparent 70%); top: 5%; right: -5%; }
        .bg-halo.orange-2 { width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,106,0,0.03) 0%, transparent 70%); bottom: 15%; left: -5%; }

        .navbar-simple { width: 100%; height: 68px; background: #181818; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; position: sticky; top: 0; z-index: 1000; padding: 0 30px; }
        .navbar-simple .nav-container { max-width: 1500px; width: 100%; display: flex; align-items: center; justify-content: space-between; }
        .navbar-simple .logo-text { font-weight: 900; font-size: 22px; letter-spacing: 1px; }
        .navbar-simple .logo-text .easy { color: #ff6a00; }
        .navbar-simple .logo-text .pick { color: #fff; }
        .navbar-simple .nav-menu { display: flex; align-items: center; gap: 30px; list-style: none; }
        .navbar-simple .nav-menu li a { font-weight: 500; font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.3s; letter-spacing: 0.3px; padding: 4px 0; position: relative; }
        .navbar-simple .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .navbar-simple .nav-menu li a:hover { color: #fff; }
        .navbar-simple .nav-menu li a:hover::after { width: 100%; }
        .navbar-simple .nav-menu li a.active { color: #fff; }
        .navbar-simple .nav-menu li a.active::after { width: 100%; }
        .navbar-simple .nav-icons { display: flex; align-items: center; gap: 20px; }
        .navbar-simple .nav-icons a { color: rgba(255,255,255,0.6); font-size: 18px; transition: color 0.3s; position: relative; }
        .navbar-simple .nav-icons a:hover { color: #ff6a00; }
        .navbar-simple .nav-icons .cart-badge { position: absolute; top: -6px; right: -8px; background: #ff6a00; color: #fff; font-size: 9px; font-weight: 700; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 12px rgba(255,106,0,0.4); }
        .navbar-simple .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .navbar-simple .hamburger span { display: block; width: 24px; height: 2px; background: #fff; border-radius: 10px; transition: 0.3s; }

        .hero-shop { position: relative; padding: 30px 0 25px; min-height: 200px; background: linear-gradient(135deg, #0D0D0D 0%, #1A1A1A 60%, #252525 100%); overflow: hidden; display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .hero-shop .halo-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 400px; height: 200px; background: radial-gradient(ellipse, rgba(255,106,0,0.06) 0%, transparent 70%); z-index: 0; filter: blur(50px); animation: pulseHalo 6s infinite alternate; }
        @keyframes pulseHalo { 0% { transform: translate(-50%,-50%) scale(0.9); opacity: 0.6; } 100% { transform: translate(-50%,-50%) scale(1.2); opacity: 1; } }
        .hero-shop .light-lines { position: absolute; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
        .hero-shop .light-lines .line { position: absolute; height: 1px; background: linear-gradient(90deg, transparent, rgba(255,106,0,0.06), transparent); width: 40%; left: 30%; border-radius: 100%; }
        .hero-shop .light-lines .line:nth-child(1) { top: 30%; animation: lineSlide 8s infinite linear; }
        .hero-shop .light-lines .line:nth-child(2) { top: 60%; animation: lineSlide 12s infinite linear reverse; }
        .hero-shop .light-lines .line:nth-child(3) { top: 80%; animation: lineSlide 10s infinite linear; }
        @keyframes lineSlide { 0% { transform: translateX(-30%); opacity: 0; } 50% { opacity: 1; } 100% { transform: translateX(30%); opacity: 0; } }
        .hero-shop .container { position: relative; z-index: 1; text-align: center; }
        .hero-shop h1 { font-size: clamp(32px,5vw,52px); font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; line-height: 1.1; }
        .hero-shop h1 .white { color: #fff; }
        .hero-shop h1 .orange { color: #ff6a00; text-shadow: 0 0 60px rgba(255,106,0,0.15); }
        .hero-shop p { color: rgba(255,255,255,0.5); font-size: 15px; font-weight: 300; max-width: 500px; margin: 6px auto 0; line-height: 1.6; }

        .shop-section { position: relative; padding: 30px 0 100px; background: #151515; overflow: hidden; }
        .search-toolbar { display: flex; align-items: center; gap: 16px; margin-bottom: 30px; background: #1A1A1A; border-radius: 16px; padding: 6px 6px 6px 20px; border: 1px solid rgba(255,255,255,0.06); transition: border-color 0.3s, box-shadow 0.3s; }
        .search-toolbar:focus-within { border-color: rgba(255,106,0,0.3); box-shadow: 0 0 0 3px rgba(255,106,0,0.05); }
        .search-toolbar .search-icon { color: rgba(255,255,255,0.2); font-size: 18px; flex-shrink: 0; }
        .search-toolbar input { flex: 1; background: transparent; border: none; padding: 14px 0; color: #fff; font-size: 15px; font-family: 'Poppins', sans-serif; outline: none; min-width: 0; }
        .search-toolbar input::placeholder { color: rgba(255,255,255,0.25); font-weight: 300; }
        .search-toolbar .search-divider { width: 1px; height: 30px; background: rgba(255,255,255,0.06); flex-shrink: 0; }
        .search-toolbar .sort-wrap { display: flex; align-items: center; gap: 10px; padding-right: 6px; flex-shrink: 0; }
        .search-toolbar .sort-wrap label { color: rgba(255,255,255,0.3); font-size: 13px; font-weight: 400; }
        .search-toolbar .sort-wrap select { padding: 8px 36px 8px 16px; background: #151515; color: #fff; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; font-size: 13px; font-family: 'Poppins', sans-serif; cursor: pointer; outline: none; transition: border-color 0.3s, box-shadow 0.3s; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ff6a00' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 12px center; background-size: 14px; min-width: 130px; }
        .search-toolbar .sort-wrap select:focus { border-color: #ff6a00; box-shadow: 0 0 0 3px rgba(255,106,0,0.08); }
        .search-toolbar .sort-wrap select option { background: #1A1A1A; color: #fff; }
        .search-toolbar .btn-search { padding: 10px 24px; background: #ff6a00; color: #fff; border: none; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; white-space: nowrap; }
        .search-toolbar .btn-search:hover { background: #ff7d1a; transform: scale(1.02); box-shadow: 0 8px 25px rgba(255,106,0,0.25); }

        .shop-layout { display: grid; grid-template-columns: 300px 1fr; gap: 45px; align-items: start; }
        .shop-sidebar { position: sticky; top: 90px; align-self: start; }
        .filter-block { background: #1A1A1A; border-radius: 20px; padding: 22px 22px; margin-bottom: 18px; border: 1px solid rgba(255,255,255,0.06); transition: border-color 0.3s, background 0.3s; }
        .filter-block:hover { border-color: rgba(255,106,0,0.12); background: #1E1E1E; }
        .filter-block.active-filter { border-color: rgba(255,106,0,0.15); background: rgba(255,106,0,0.04); }
        .filter-block h4 { font-size: 14px; font-weight: 700; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; color: #fff; letter-spacing: 0.3px; user-select: none; }
        .filter-block h4 i { color: #ff6a00; font-size: 12px; transition: transform 0.4s; }
        .filter-block h4.open i { transform: rotate(180deg); }
        .filter-block .filter-content { display: flex; flex-direction: column; gap: 8px; max-height: 500px; overflow: hidden; opacity: 1; transition: max-height 0.5s cubic-bezier(0.25,0.46,0.45,0.94), opacity 0.4s, margin 0.4s; }
        .filter-block .filter-content.collapsed { max-height: 0; opacity: 0; margin-top: -8px; }
        .filter-block ul { list-style: none; }
        .filter-block ul li { margin-bottom: 6px; }
        .filter-block ul li label { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,0.6); font-size: 13px; cursor: pointer; transition: color 0.3s; padding: 4px 8px; margin: 0 -8px; border-radius: 6px; }
        .filter-block ul li label:hover { color: #fff; background: rgba(255,106,0,0.05); }
        .filter-block ul li label input[type="checkbox"], .filter-block ul li label input[type="radio"] { appearance: none; -webkit-appearance: none; width: 17px; height: 17px; border: 2px solid rgba(255,255,255,0.12); border-radius: 5px; cursor: pointer; position: relative; transition: all 0.3s; flex-shrink: 0; }
        .filter-block ul li label input[type="radio"] { border-radius: 50%; }
        .filter-block ul li label input[type="checkbox"]:checked, .filter-block ul li label input[type="radio"]:checked { background: #ff6a00; border-color: #ff6a00; box-shadow: 0 0 18px rgba(255,106,0,0.2); }
        .filter-block ul li label input[type="checkbox"]:checked::after { content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); color: #fff; font-size: 9px; }
        .filter-block ul li label input[type="radio"]:checked::after { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 7px; height: 7px; background: #fff; border-radius: 50%; }
        .filter-block ul li label .count { color: rgba(255,255,255,0.15); font-size: 11px; margin-left: auto; }
        .price-range { display: flex; flex-direction: column; gap: 12px; padding: 4px 0; }
        .price-range input[type="range"] { width: 100%; height: 4px; -webkit-appearance: none; appearance: none; background: rgba(255,255,255,0.06); border-radius: 10px; outline: none; }
        .price-range input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 18px; height: 18px; border-radius: 50%; background: #ff6a00; cursor: pointer; box-shadow: 0 0 20px rgba(255,106,0,0.25); transition: all 0.2s; }
        .price-range input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.12); box-shadow: 0 0 30px rgba(255,106,0,0.4); }
        .price-range .price-labels { display: flex; justify-content: space-between; color: rgba(255,255,255,0.35); font-size: 12px; }
        .price-range .price-labels span:last-child { color: #ff6a00; font-weight: 600; }
        .filter-stars { display: flex; align-items: center; gap: 5px; color: #ffb800; font-size: 12px; }
        .filter-stars .grey { color: #444; }
        .filter-stars span { color: rgba(255,255,255,0.3); font-size: 11px; margin-left: 3px; }
        .btn-apply-filter { width: 100%; padding: 14px; background: #ff6a00; color: #fff; border: none; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.4s; font-family: 'Poppins', sans-serif; letter-spacing: 0.3px; background: linear-gradient(135deg, #ff6a00, #ff7d1a); box-shadow: 0 6px 25px rgba(255,106,0,0.15); }
        .btn-apply-filter:hover { transform: scale(1.02); box-shadow: 0 10px 35px rgba(255,106,0,0.3); background: linear-gradient(135deg, #ff7d1a, #ff8c33); }

        .products-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 35px; }
        .product-card { background: #202020; border-radius: 20px; overflow: hidden; padding: 24px 24px 28px; border: 1px solid rgba(255,255,255,0.06); transition: transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94), box-shadow 0.4s cubic-bezier(0.25,0.46,0.45,0.94), border-color 0.4s; position: relative; display: flex; flex-direction: column; cursor: pointer; }
        .product-card .product-link-overlay { position: absolute; inset: 0; z-index: 1; border-radius: 20px; }
        .product-card .action-buttons, .product-card .badges, .product-card .card-actions .btn-add { position: relative; z-index: 2; }
        .product-card .product-image-wrap { position: relative; overflow: hidden; border-radius: 16px; background: #151515; margin-bottom: 16px; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; }
        .product-card .product-image-wrap img { width: 100%; height: 100%; object-fit: contain; padding: 20px; transition: transform 0.5s cubic-bezier(0.25,0.46,0.45,0.94); }
        .product-card:hover .product-image-wrap img { transform: scale(1.08); }
        .product-card:hover { transform: translateY(-12px); box-shadow: 0 30px 70px rgba(255,106,0,0.12); border-color: rgba(255,106,0,0.12); }
        .product-card .badges { position: absolute; top: 14px; left: 14px; display: flex; flex-direction: column; gap: 6px; z-index: 2; }
        .product-card .badges .badge { padding: 4px 14px; border-radius: 30px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .product-card .badges .badge.promo { background: #ff6a00; color: #fff; }
        .product-card .badges .badge.new { background: #00b894; color: #fff; }
        .product-card .badges .badge.top { background: #fdcb6e; color: #151515; }
        .product-card .action-buttons { position: absolute; top: 14px; right: 14px; display: flex; flex-direction: column; gap: 8px; z-index: 2; opacity: 0; transform: translateX(12px); transition: all 0.4s cubic-bezier(0.25,0.46,0.45,0.94); }
        .product-card:hover .action-buttons { opacity: 1; transform: translateX(0); }
        .product-card .action-buttons button { width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(21,21,21,0.85); backdrop-filter: blur(8px); color: #fff; font-size: 16px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.06); z-index: 3; }
        .product-card .action-buttons button:hover { background: #ff6a00; transform: scale(1.1); box-shadow: 0 8px 30px rgba(255,106,0,0.25); border-color: #ff6a00; }
        .product-card .action-buttons button.fav-active { color: #ff6a00; }
        .product-card .action-buttons button.fav-active i { font-weight: 900; }
        .product-card .product-name { font-size: 16px; font-weight: 600; margin-bottom: 4px; line-height: 1.3; color: #fff; position: relative; z-index: 1; }
        .product-card .product-rating { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; position: relative; z-index: 1; }
        .product-card .product-rating .stars { color: #ffb800; font-size: 13px; }
        .product-card .product-rating .stars .grey { color: #444; }
        .product-card .product-rating .count { color: rgba(255,255,255,0.2); font-size: 12px; }
        .product-card .product-price { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; position: relative; z-index: 1; }
        .product-card .product-price .current { font-size: 32px; font-weight: 900; color: #ff6a00; letter-spacing: -0.5px; }
        .product-card .product-price .old { font-size: 16px; color: rgba(255,255,255,0.2); text-decoration: line-through; font-weight: 400; }
        .product-card .card-actions { display: flex; gap: 10px; margin-top: auto; position: relative; z-index: 2; }
        .product-card .card-actions .btn-add { width: 100%; padding: 14px 0; background: linear-gradient(135deg, #ff6a00, #ff7d1a); color: #fff; border: none; border-radius: 16px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.4s; font-family: 'Poppins', sans-serif; letter-spacing: 0.3px; box-shadow: 0 6px 25px rgba(255,106,0,0.12); text-align: center; display: inline-block; }
        .product-card .card-actions .btn-add:hover { transform: scale(1.03); box-shadow: 0 10px 35px rgba(255,106,0,0.25); background: linear-gradient(135deg, #ff7d1a, #ff8c33); }

        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 55px; flex-wrap: wrap; }
        .pagination a, .pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 44px; height: 44px; padding: 0 16px; border-radius: 14px; background: #1A1A1A; color: rgba(255,255,255,0.4); font-weight: 600; font-size: 14px; transition: all 0.3s; border: 1px solid rgba(255,255,255,0.06); }
        .pagination a:hover { background: #ff6a00; color: #fff; border-color: #ff6a00; transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255,106,0,0.15); }
        .pagination .active { background: #ff6a00; color: #fff; border-color: #ff6a00; }
        .pagination .dots { background: transparent; border: none; color: rgba(255,255,255,0.06); }

        .footer { position: relative; background: #0F0F0F; padding: 50px 0 30px; border-top: 1px solid rgba(255,255,255,0.04); overflow: hidden; }
        .footer::before { content: ''; position: absolute; bottom: -100px; right: -100px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,106,0,0.03) 0%, transparent 70%); border-radius: 50%; filter: blur(80px); pointer-events: none; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 40px; margin-bottom: 35px; position: relative; z-index: 1; }
        .footer-col h4 { font-size: 15px; font-weight: 700; margin-bottom: 14px; color: #fff; }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 8px; }
        .footer-col ul li a { color: rgba(255,255,255,0.3); transition: color 0.3s; font-size: 13px; }
        .footer-col ul li a:hover { color: #ff6a00; }
        .footer-social { display: flex; gap: 10px; margin-top: 10px; }
        .footer-social a { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,0.04); color: #fff; font-size: 16px; transition: all 0.3s; }
        .footer-social a:hover { background: #ff6a00; transform: translateY(-4px); box-shadow: 0 8px 25px rgba(255,106,0,0.12); }
        .footer-payments { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .footer-payments i { font-size: 28px; color: rgba(255,255,255,0.12); transition: color 0.3s; }
        .footer-payments i:hover { color: #fff; }
        .footer-bottom { text-align: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.04); color: rgba(255,255,255,0.12); font-size: 12px; position: relative; z-index: 1; }
        .footer-bottom a { color: #ff6a00; }

        @media (max-width:1200px) { .products-grid { grid-template-columns: repeat(3,1fr); gap: 30px; } .shop-layout { grid-template-columns: 270px 1fr; gap: 35px; } }
        @media (max-width:992px) { .products-grid { grid-template-columns: repeat(2,1fr); gap: 28px; } .shop-layout { grid-template-columns: 1fr; gap: 30px; } .shop-sidebar { position: relative; top: 0; display: grid; grid-template-columns: repeat(3,1fr); gap: 15px; } .filter-block { margin-bottom: 0; } .search-toolbar { flex-wrap: wrap; padding: 10px 16px; } .search-toolbar .sort-wrap { padding-right: 0; width: 100%; } .search-toolbar .sort-wrap select { flex: 1; min-width: 0; } .search-toolbar .btn-search { width: 100%; } .search-toolbar .search-divider { display: none; } }
        @media (max-width:768px) { .container { padding: 0 16px; } .navbar-simple { height: 60px; padding: 0 16px; } .navbar-simple .logo-text { font-size: 18px; } .navbar-simple .nav-menu { display: none; flex-direction: column; position: absolute; top: 60px; left: 0; width: 100%; background: #181818; padding: 24px 20px; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 20px 40px rgba(0,0,0,0.5); } .navbar-simple .nav-menu.open { display: flex; } .navbar-simple .nav-menu li a { font-size: 16px; color: rgba(255,255,255,0.7); } .navbar-simple .hamburger { display: flex; } .navbar-simple .nav-icons { gap: 14px; } .navbar-simple .nav-icons a { font-size: 16px; } .hero-shop { min-height: 160px; padding: 25px 0 20px; } .hero-shop h1 { font-size: 28px; } .shop-section { padding: 25px 0 70px; } .shop-sidebar { grid-template-columns: 1fr 1fr; gap: 12px; } .products-grid { grid-template-columns: 1fr 1fr; gap: 20px; } .product-card { padding: 16px 16px 20px; } .product-card .product-image-wrap img { padding: 14px; } .product-card .product-name { font-size: 14px; } .product-card .product-price .current { font-size: 26px; } .product-card .product-price .old { font-size: 14px; } .product-card .card-actions .btn-add { font-size: 12px; padding: 10px 0; } .product-card .action-buttons { opacity: 1; transform: none; top: 10px; right: 10px; } .product-card .action-buttons button { width: 34px; height: 34px; font-size: 14px; } .product-card .badges .badge { font-size: 9px; padding: 3px 10px; } .search-toolbar { flex-direction: column; padding: 12px 16px; gap: 12px; } .search-toolbar .sort-wrap { width: 100%; flex-wrap: wrap; } .search-toolbar .sort-wrap select { flex: 1; min-width: 0; width: 100%; } .search-toolbar .btn-search { width: 100%; } .search-toolbar .search-divider { display: none; } .pagination a, .pagination span { min-width: 38px; height: 38px; font-size: 13px; padding: 0 12px; } }
        @media (max-width:480px) { .navbar-simple { height: 54px; } .navbar-simple .logo-text { font-size: 16px; } .navbar-simple .nav-icons a { font-size: 14px; } .navbar-simple .nav-icons { gap: 12px; } .hero-shop h1 { font-size: 24px; } .shop-sidebar { grid-template-columns: 1fr; } .products-grid { grid-template-columns: 1fr; gap: 24px; } .product-card .product-image-wrap img { padding: 16px; } .product-card .product-price .current { font-size: 28px; } }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <!-- ===== HERO ===== -->
    <section class="hero-shop">
        <div class="halo-text"></div>
        <div class="light-lines">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <div class="container">
            <h1><span class="white">NOTRE</span> <span class="orange" data-i18n="notre_boutique">Boutique</span></h1>
            <p data-i18n="description_boutique">Découvrez notre sélection d'accessoires technologiques premium.</p>
        </div>
    </section>

    <!-- ===== SECTION BOUTIQUE ===== -->
    <section class="shop-section">
        <div class="bg-halo orange-1"></div>
        <div class="bg-halo orange-2"></div>

        <div class="container">

            <!-- ===== BARRE DE RECHERCHE + TRI ===== -->
            <div class="search-toolbar">
                <span class="search-icon"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" data-i18n-placeholder="rechercher" placeholder="Rechercher un produit..." autocomplete="off" />
                <div class="search-divider"></div>
                <div class="sort-wrap">
                    <label for="sortSelect" data-i18n="trier_par">Trier par</label>
                    <select id="sortSelect" onchange="applyFilters()">
                        <option value="relevance" <?= $sort === 'relevance' ? 'selected' : '' ?> data-i18n="pertinence">Pertinence</option>
                        <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?> data-i18n="prix_croissant">Prix croissant</option>
                        <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?> data-i18n="prix_decroissant">Prix décroissant</option>
                        <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?> data-i18n="meilleures_notes">Meilleures notes</option>
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?> data-i18n="nouveautes">Nouveautés</option>
                    </select>
                </div>
            </div>

            <!-- ===== FILTRES + GRILLE ===== -->
            <div class="shop-layout">

                <!-- SIDEBAR FILTRES -->
                <aside class="shop-sidebar">
                    <form method="GET" action="" id="filterForm">
                        <!-- Paramètres de recherche et tri (pour rechargement) -->
                        <?php if (!empty($search)): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>
                        <?php if ($sort !== 'relevance'): ?>
                            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                        <?php endif; ?>

                        <div class="filter-block active-filter">
                            <h4 onclick="toggleFilter(this)" data-i18n="categories">Catégories <i class="fas fa-chevron-down"></i></h4>
                            <div class="filter-content">
                                <ul>
                                    <?php
                                    $categories = $pdo->query('SELECT * FROM categories')->fetchAll();
                                    foreach ($categories as $cat):
                                        $checked = ($categorie == $cat['id']) ? 'checked' : '';
                                    ?>
                                        <li><label><input type="checkbox" name="categorie" value="<?= $cat['id'] ?>" <?= $checked ?> onchange="this.form.submit()"> <?= htmlspecialchars($cat['nom']) ?> <span class="count">(<?= $pdo->query('SELECT COUNT(*) FROM produits WHERE categorie_id = ' . $cat['id'])->fetchColumn() ?>)</span></label></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="filter-block">
                            <h4 onclick="toggleFilter(this)" data-i18n="prix">Prix <i class="fas fa-chevron-down"></i></h4>
                            <div class="filter-content">
                                <div class="price-range">
                                    <input type="range" name="prix_max" min="0" max="300" value="<?= $prix_max ?: 150 ?>" onchange="this.form.submit()">
                                    <div class="price-labels">
                                        <span>0 €</span>
                                        <span id="priceDisplay"><?= $prix_max ?: 150 ?> €</span>
                                        <span>300 €+</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-block">
                            <h4 onclick="toggleFilter(this)" data-i18n="marques">Marques <i class="fas fa-chevron-down"></i></h4>
                            <div class="filter-content">
                                <ul>
                                    <?php
                                    $marques = $pdo->query('SELECT * FROM marques')->fetchAll();
                                    foreach ($marques as $m):
                                        $checked = ($marque == $m['id']) ? 'checked' : '';
                                    ?>
                                        <li><label><input type="checkbox" name="marque" value="<?= $m['id'] ?>" <?= $checked ?> onchange="this.form.submit()"> <?= htmlspecialchars($m['nom']) ?> <span class="count">(<?= $pdo->query('SELECT COUNT(*) FROM produits WHERE marque_id = ' . $m['id'])->fetchColumn() ?>)</span></label></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="filter-block">
                            <h4 onclick="toggleFilter(this)" data-i18n="note_minimale">Note minimale <i class="fas fa-chevron-down"></i></h4>
                            <div class="filter-content">
                                <ul>
                                    <li><label><input type="radio" name="note_min" value="5" <?= $note_min == 5 ? 'checked' : '' ?> onchange="this.form.submit()"> <span class="filter-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i> <span data-i18n="5_etoiles">5 étoiles</span></span></label></li>
                                    <li><label><input type="radio" name="note_min" value="4" <?= $note_min == 4 ? 'checked' : '' ?> onchange="this.form.submit()"> <span class="filter-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star grey"></i> <span data-i18n="4_etoiles">4+ étoiles</span></span></label></li>
                                    <li><label><input type="radio" name="note_min" value="3" <?= $note_min == 3 ? 'checked' : '' ?> onchange="this.form.submit()"> <span class="filter-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star grey"></i><i class="fas fa-star grey"></i> <span data-i18n="3_etoiles">3+ étoiles</span></span></label></li>
                                    <li><label><input type="radio" name="note_min" value="" <?= !$note_min ? 'checked' : '' ?> onchange="this.form.submit()"> <span style="color:rgba(255,255,255,0.4);" data-i18n="toutes_les_notes">Toutes les notes</span></label></li>
                                </ul>
                            </div>
                        </div>

                        <button type="submit" class="btn-apply-filter" data-i18n="appliquer_filtres">Appliquer les filtres</button>
                    </form>
                </aside>

                <!-- GRILLE PRODUITS -->
                <div>
                    <div id="productGrid" class="products-grid">
                        <?php if (empty($produits)): ?>
                            <div style="text-align:center; padding:60px 0; color:rgba(255,255,255,0.4); grid-column:1 / -1;">
                                <i class="fas fa-box-open" style="font-size:48px; margin-bottom:16px;"></i>
                                <p data-i18n="aucun_produit">Aucun produit ne correspond à vos critères.</p>
                                <a href="boutique.php" style="color:#ff6a00; font-weight:600;" data-i18n="voir_tous_produits">Voir tous les produits</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($produits as $index => $produit):
                                // Vérification des favoris - AVEC GESTION D'ERREUR
                                $est_favori = false;
                                if (isset($_SESSION['user_id'])) {
                                    try {
                                        $stmt = $pdo->prepare('SELECT produit_id FROM wishlist WHERE utilisateur_id = ? AND produit_id = ?');
                                        $stmt->execute([$_SESSION['user_id'], $produit['id']]);
                                        $est_favori = $stmt->fetch();
                                    } catch (PDOException $e) {
                                        // Table wishlist n'existe pas, on ignore
                                        $est_favori = false;
                                    }
                                }
                            ?>
                            <div class="product-card fade-up delay-<?= ($index % 4) + 1 ?>">
                                <a href="produit.php?id=<?= $produit['id'] ?>" class="product-link-overlay"></a>
                                <div class="product-image-wrap">
                                    <img src="<?= htmlspecialchars($produit['image'] ?? 'https://picsum.photos/seed/' . $produit['id'] . '/300/200') ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
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
                                    <span class="count">(<?= $produit['nb_avis'] ?> <span data-i18n="avis">avis</span>)</span>
                                </div>
                                <div class="product-price">
                                    <span class="current"><?= number_format($produit['prix'], 2, ',', ' ') ?> €</span>
                                    <?php if (isset($produit['prix_old']) && $produit['prix_old']): ?>
                                        <span class="old"><?= number_format($produit['prix_old'], 2, ',', ' ') ?> €</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-actions">
                                    <a href="panier-ajouter.php?id=<?= $produit['id'] ?>&qte=1" class="btn-add" data-i18n="ajouter_au_panier">Ajouter au panier</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination">
                        <a href="#"><i class="fas fa-chevron-left"></i></a>
                        <a href="#" class="active">1</a>
                        <a href="#">2</a>
                        <a href="#">3</a>
                        <span class="dots">…</span>
                        <a href="#">8</a>
                        <a href="#"><i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col"><h4>EasyPick</h4><ul><li><a href="a-propos.php" data-i18n="a_propos">À propos</a></li><li><a href="blog.php" data-i18n="blog">Blog</a></li><li><a href="carrieres.php" data-i18n="carrieres">Carrières</a></li><li><a href="contact.php" data-i18n="contact">Contact</a></li></ul></div>
                <div class="footer-col"><h4 data-i18n="aide">Aide</h4><ul><li><a href="#">Centre d'aide</a></li><li><a href="#">Suivi de commande</a></li><li><a href="#">Retours</a></li><li><a href="#">FAQ</a></li></ul></div>
                <div class="footer-col"><h4 data-i18n="legal">Légal</h4><ul><li><a href="cgv.php" data-i18n="cgv">CGV</a></li><li><a href="confidentialite.php" data-i18n="confidentialite">Politique de confidentialité</a></li><li><a href="cookies.php" data-i18n="cookies">Cookies</a></li><li><a href="mentions-legales.php" data-i18n="mentions_legales">Mentions légales</a></li></ul></div>
                <div class="footer-col"><h4 data-i18n="suivez_nous">Suivez-nous</h4><div class="footer-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-twitter"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div><div class="footer-payments"><i class="fab fa-cc-visa"></i><i class="fab fa-cc-mastercard"></i><i class="fab fa-cc-paypal"></i><i class="fab fa-cc-apple-pay"></i></div></div>
            </div>
            <div class="footer-bottom">&copy; 2026 EasyPick – <span data-i18n="tous_droits_reserves">Tous droits réservés</span>. <span data-i18n="design_par">Design par</span> <a href="#">Samy Sabeur</a>.</div>
        </div>
    </footer>

    <script>
        // ===== HAMBURGER =====
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        if (hamburger && navMenu) {
            hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
        }

        // ===== FILTRES (accordéon) =====
        function toggleFilter(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('i');
            content.classList.toggle('collapsed');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
            header.classList.toggle('open');
        }

        // ===== FAVORIS =====
       // ===== FAVORIS =====
function toggleFav(productId, btn) {
    const icon = btn.querySelector('i');
    if (icon.classList.contains('fas')) {
        window.location.href = 'wishlist-supprimer.php?id=' + productId;
    } else {
        window.location.href = 'wishlist-ajouter.php?id=' + productId;
    }
}

        // ===== APERÇU RAPIDE =====
        function quickView(btn) {
            const card = btn.closest('.product-card');
            const name = card.querySelector('.product-name').textContent;
            alert('🖥️ Aperçu rapide : ' + name);
        }

        // ===== LIVE SEARCH (AJAX) =====
        const searchInput = document.getElementById('searchInput');
        const productGrid = document.getElementById('productGrid');
        const sortSelect = document.getElementById('sortSelect');
        let searchTimeout;

        function loadProducts(query, sort) {
            const url = 'recherche-ajax.php?q=' + encodeURIComponent(query) + '&sort=' + encodeURIComponent(sort);
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    productGrid.innerHTML = html;
                    document.querySelectorAll('.fade-up').forEach(el => el.classList.add('visible'));
                })
                .catch(err => console.error('Erreur:', err));
        }

        function applyFilters() {
            const q = searchInput.value.trim();
            const sort = sortSelect.value;
            if (q.length > 0) {
                loadProducts(q, sort);
            } else {
                window.location.href = window.location.pathname + '?sort=' + encodeURIComponent(sort);
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const q = this.value.trim();
                const sort = sortSelect.value;
                searchTimeout = setTimeout(() => {
                    if (q.length > 0) {
                        loadProducts(q, sort);
                    } else {
                        window.location.href = window.location.pathname + '?sort=' + encodeURIComponent(sort);
                    }
                }, 400);
            });
        }

        // ===== FADE-UP =====
        const fadeElements = document.querySelectorAll('.fade-up');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        fadeElements.forEach(el => observer.observe(el));
    </script>

</body>
</html>