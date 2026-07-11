<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

$nb_articles = isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0;
$user_connecte = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EasyPick – Accueil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <style>
        /* ---- TOUS TES STYLES EXISTANTS ---- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #0d0d0d; color: #fff; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 20px; }

        .top-bar { background: #0a0a0a; height: 62px; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding: 0 20px; }
        .top-bar-container { max-width: 1280px; width: 100%; display: grid; grid-template-columns: repeat(5, 1fr); gap: 0; align-items: center; }
        .top-item { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 8px 0; border-right: 1px solid rgba(255,255,255,0.08); transition: background 0.3s; cursor: default; }
        .top-item:last-child { border-right: none; }
        .top-item:hover { background: rgba(255,106,0,0.08); }
        .top-item i { font-size: 20px; color: #ff6a00; transition: filter 0.3s, transform 0.3s; }
        .top-item:hover i { filter: brightness(1.5); transform: scale(1.05); }
        .top-text { display: flex; flex-direction: column; line-height: 1.2; }
        .top-text strong { font-weight: 700; font-size: 14px; color: #fff; letter-spacing: 0.3px; }
        .top-text span { font-weight: 300; font-size: 11px; color: #aaa; }

        .navbar-wrapper { display: flex; justify-content: center; margin-top: 0; position: relative; z-index: 100; padding: 0 20px; }
        .navbar { width: 90%; max-width: 1400px; height: 68px; background: linear-gradient(135deg, rgba(180,60,0,0.85), rgba(255,106,0,0.75)); backdrop-filter: blur(8px); border-radius: 20px; box-shadow: 0 12px 35px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; }
        .logo { display: flex; align-items: center; gap: 10px; }
        .logo img { height: 60px; width: auto; filter: drop-shadow(0 0 12px rgba(255,106,0,0.35)); transition: transform 0.3s; object-fit: contain; }
        .logo img:hover { transform: scale(1.05); }
        .logo-text { display: flex; flex-direction: column; line-height: 1.1; }
        .logo-text .brand { font-weight: 900; font-size: 22px; letter-spacing: 1.5px; text-shadow: 0 2px 8px rgba(0,0,0,0.3); }
        .logo-text .brand .easy { color: #ff6a00; }
        .logo-text .brand .pick { color: #ffffff; }
        .logo-text .sub { font-weight: 300; font-size: 10px; color: rgba(255,255,255,0.7); letter-spacing: 0.5px; margin-top: -2px; }

        .nav-menu { display: flex; align-items: center; gap: 28px; list-style: none; }
        .nav-menu li a { font-weight: 700; font-size: 16px; color: #fff; padding: 4px 0; position: relative; transition: color 0.3s; }
        .nav-menu li a::after { content: ''; position: absolute; left: 0; bottom: -2px; width: 0; height: 2.5px; background: #ff6a00; border-radius: 10px; transition: width 0.3s; }
        .nav-menu li a:hover { color: #ff6a00; }
        .nav-menu li a:hover::after { width: 100%; }
        .nav-menu li a.active { color: #ff6a00; }
        .nav-menu li a.active::after { width: 100%; }

        .nav-icons { display: flex; align-items: center; gap: 22px; }
        .nav-icons a { color: #fff; font-size: 20px; transition: color 0.3s ease, transform 0.2s ease; position: relative; }
        .nav-icons a:hover { color: #ff6a00; transform: scale(1.08); }
        .cart-badge { position: absolute; top: -8px; right: -10px; background-color: #ff6a00; color: #fff; font-size: 10px; font-weight: 700; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 12px rgba(255,106,0,0.5); }
        .hamburger { display: none; flex-direction: column; gap: 4px; cursor: pointer; background: none; border: none; padding: 4px; }
        .hamburger span { display: block; width: 26px; height: 2.5px; background: #fff; border-radius: 10px; transition: 0.3s ease; }

        /* ===== BARRE DE RECHERCHE DANS LA NAVBAR ===== */
        .search-container {
            display: none;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.08);
            border-radius: 50px;
            padding: 4px 4px 4px 18px;
            border: 1px solid rgba(255,255,255,0.06);
            transition: all 0.3s ease;
        }
        .search-container.active {
            display: flex;
        }
        .search-container input {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            width: 200px;
            padding: 8px 0;
        }
        .search-container input::placeholder {
            color: rgba(255,255,255,0.3);
        }
        .search-container button {
            background: #ff6a00;
            border: none;
            color: #fff;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            white-space: nowrap;
        }
        .search-container button:hover {
            background: #ff8833;
            transform: scale(1.02);
        }
        .search-toggle {
            cursor: pointer;
        }

        /* ===== RESULTATS LIVE SEARCH ===== */
        .search-results {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            left: 0;
            background: #1A1A1A;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.06);
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 200;
            box-shadow: 0 20px 60px rgba(0,0,0,0.8);
            padding: 10px 0;
        }
        .search-results .result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            transition: background 0.3s;
            cursor: pointer;
        }
        .search-results .result-item:hover {
            background: rgba(255,106,0,0.08);
        }
        .search-results .result-item img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            background: #0d0d0d;
            border-radius: 8px;
            padding: 4px;
        }
        .search-results .result-item .info {
            flex: 1;
        }
        .search-results .result-item .info .name {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
        }
        .search-results .result-item .info .desc {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
        }
        .search-results .result-item .price {
            font-size: 14px;
            font-weight: 700;
            color: #ff6a00;
        }
        .search-results .no-result {
            text-align: center;
            padding: 30px 20px;
            color: rgba(255,255,255,0.3);
        }

        /* ===== HERO ===== */
        .hero { position: relative; min-height: 620px; height: 85vh; max-height: 750px; display: flex; align-items: center; margin-top: -68px; padding: 0 60px; background: #0d0d0d; overflow: hidden; }
        .hero-bg { position: absolute; inset: 0; background: url('hero.png') center/cover no-repeat; filter: brightness(0.45) saturate(1.1); z-index: 0; }
        .hero-overlay { position: absolute; inset: 0; background: radial-gradient(circle at 80% 30%, rgba(255,106,0,0.15) 0%, transparent 60%), linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%); z-index: 1; }
        .hero-content { position: relative; z-index: 2; max-width: 1280px; width: 100%; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 30px; }
        .hero-text { flex: 1 1 50%; max-width: 580px; }
        .hero-text h1 { font-weight: 900; font-size: clamp(48px,6vw,80px); line-height: 1.05; letter-spacing: -1.5px; text-transform: uppercase; }
        .hero-text h1 .line1, .hero-text h1 .line2 { color: #fff; display: block; }
        .hero-text h1 .line3 { color: #ff6a00; display: block; text-shadow: 0 0 30px rgba(255,106,0,0.25); }
        .hero-text p { font-size: clamp(16px,1.6vw,22px); font-weight: 300; color: rgba(255,255,255,0.8); margin: 22px 0 32px; max-width: 500px; line-height: 1.6; }
        .hero-buttons { display: flex; flex-wrap: wrap; gap: 20px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0 34px; height: 56px; font-size: 16px; font-weight: 700; border-radius: 50px; border: none; cursor: pointer; transition: all 0.3s; text-transform: uppercase; letter-spacing: 0.4px; min-width: 180px; }
        .btn-primary { background: #ff6a00; color: #fff; box-shadow: 0 8px 25px rgba(255,106,0,0.3); }
        .btn-primary:hover { background: #ff8833; transform: scale(1.04); box-shadow: 0 12px 35px rgba(255,106,0,0.5); }
        .btn-secondary { background: transparent; color: #fff; border: 2px solid #fff; backdrop-filter: blur(4px); }
        .btn-secondary:hover { background: rgba(255,255,255,0.12); color: #ff6a00; border-color: #ff6a00; transform: scale(1.04); }
        .hero-visual { flex: 1 1 50%; display: flex; justify-content: center; align-items: center; position: relative; min-height: 300px; }
        .hero-visual .glow-circle { width: 320px; height: 320px; background: radial-gradient(circle, rgba(255,106,0,0.15) 0%, transparent 70%); border-radius: 50%; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); filter: blur(40px); animation: pulse 4s infinite alternate; }
        @keyframes pulse { 0% { transform: translate(-50%,-50%) scale(0.9); opacity: 0.6; } 100% { transform: translate(-50%,-50%) scale(1.2); opacity: 1; } }

        /* ===== NOTRE HISTOIRE ===== */
        .brand-story { padding: 90px 0; background: linear-gradient(135deg, #0d0d0d 0%, #1a0a00 100%); border-bottom: 1px solid rgba(255,106,0,0.1); border-top: 1px solid rgba(255,106,0,0.05); }
        .story-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .story-text .badge { color: #ff6a00; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; font-size: 14px; margin-bottom: 10px; display: inline-block; border-left: 3px solid #ff6a00; padding-left: 15px; }
        .story-text h2 { font-size: 40px; font-weight: 700; line-height: 1.2; margin-bottom: 20px; }
        .story-text h2 span { color: #ff6a00; }
        .story-text p { color: rgba(255,255,255,0.75); font-size: 17px; line-height: 1.8; margin-bottom: 16px; }
        .story-text .signature { margin-top: 25px; font-size: 18px; font-weight: 600; color: #fff; }
        .story-text .signature span { color: #ff6a00; }
        .story-visual { display: flex; justify-content: center; align-items: center; position: relative; min-height: 300px; }
        .story-visual .year-badge { font-size: 130px; font-weight: 900; color: rgba(255,106,0,0.06); position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); z-index: 0; letter-spacing: -8px; user-select: none; white-space: nowrap; }
        .story-visual .founder-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); padding: 40px 35px; border-radius: 24px; border: 1px solid rgba(255,106,0,0.2); text-align: center; z-index: 1; max-width: 350px; width: 100%; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .founder-card .avatar-placeholder { width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #ff6a00, #cc5500); margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; font-size: 38px; color: #fff; font-weight: 700; box-shadow: 0 0 30px rgba(255,106,0,0.2); }
        .founder-card h4 { font-size: 22px; font-weight: 700; margin-bottom: 2px; }
        .founder-card .title { color: #ff6a00; font-weight: 600; font-size: 14px; letter-spacing: 1px; text-transform: uppercase; }
        .founder-card .divider { width: 40px; height: 2px; background: #ff6a00; margin: 12px auto; }
        .founder-card .quote { font-style: italic; color: rgba(255,255,255,0.75); font-size: 15px; line-height: 1.6; }
        .founder-card .quote i { color: #ff6a00; opacity: 0.6; }

        /* ===== CATEGORIES ===== */
        .categories { padding: 80px 0 60px; background: #0d0d0d; }
        .section-title { font-size: 36px; font-weight: 700; text-align: center; margin-bottom: 12px; }
        .section-title span { color: #ff6a00; }
        .section-sub { text-align: center; color: rgba(255,255,255,0.6); font-size: 18px; margin-bottom: 50px; }
        .categories-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .category-card { position: relative; border-radius: 20px; overflow: hidden; background: #1a1a1a; transition: transform 0.4s, box-shadow 0.4s; cursor: pointer; height: 280px; display: flex; align-items: flex-end; justify-content: center; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.4); }
        .category-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 20px 50px rgba(255,106,0,0.2); }
        .category-card img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; filter: brightness(0.6); transition: filter 0.4s; }
        .category-card:hover img { filter: brightness(0.8); }
        .category-card .cat-content { position: relative; z-index: 2; text-align: center; }
        .category-card .cat-content h3 { font-size: 24px; font-weight: 700; color: #fff; text-shadow: 0 4px 20px rgba(0,0,0,0.8); }
        .category-card .cat-content .btn-cat { display: inline-block; margin-top: 15px; padding: 10px 25px; border-radius: 50px; background: #ff6a00; color: #fff; font-weight: 600; font-size: 14px; opacity: 0; transform: translateY(10px); transition: all 0.3s; }
        .category-card:hover .btn-cat { opacity: 1; transform: translateY(0); }

        /* ===== PRODUITS ===== */
        .products { padding: 60px 0 80px; background: #111; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 30px; }
        .product-card { background: #1a1a1a; border-radius: 16px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; box-shadow: 0 8px 25px rgba(0,0,0,0.3); padding: 20px 20px 25px; text-align: center; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(255,106,0,0.15); }
        .product-card img { width: 100%; height: 200px; object-fit: contain; background: #0d0d0d; border-radius: 12px; margin-bottom: 15px; }
        .product-card .product-name { font-size: 18px; font-weight: 600; margin-bottom: 6px; }
        .product-card .product-price { font-size: 22px; font-weight: 700; color: #ff6a00; }
        .product-card .product-price .old { font-size: 16px; color: #999; text-decoration: line-through; margin-right: 10px; font-weight: 400; }
        .product-card .stars { color: #ffb800; margin: 6px 0 12px; font-size: 14px; }
        .product-card .btn-add { background: transparent; border: 2px solid #ff6a00; color: #ff6a00; padding: 8px 20px; border-radius: 50px; font-weight: 600; transition: all 0.3s; cursor: pointer; font-size: 14px; }
        .product-card .btn-add:hover { background: #ff6a00; color: #fff; box-shadow: 0 8px 20px rgba(255,106,0,0.3); }

        /* ===== PROMO ===== */
        .promo-banner { padding: 80px 0; background: linear-gradient(135deg, #1a0a00, #2d0a00, #0d0d0d); border-top: 1px solid rgba(255,106,0,0.2); border-bottom: 1px solid rgba(255,106,0,0.2); }
        .promo-content { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 20px; }
        .promo-content h2 { font-size: 44px; font-weight: 900; text-transform: uppercase; }
        .promo-content h2 span { color: #ff6a00; }
        .promo-content p { font-size: 20px; color: rgba(255,255,255,0.7); max-width: 600px; }
        .promo-content .btn-promo { background: #ff6a00; color: #fff; padding: 16px 50px; border-radius: 60px; font-weight: 700; font-size: 20px; transition: all 0.3s; box-shadow: 0 8px 30px rgba(255,106,0,0.3); text-transform: uppercase; letter-spacing: 1px; }
        .promo-content .btn-promo:hover { background: #ff8833; transform: scale(1.05); box-shadow: 0 12px 40px rgba(255,106,0,0.5); }

        /* ===== AVIS ===== */
        .testimonials { padding: 80px 0; background: #0d0d0d; }
        .testimonials-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px; }
        .testimonial-card { background: #1a1a1a; border-radius: 20px; padding: 30px 25px; box-shadow: 0 8px 30px rgba(0,0,0,0.4); transition: transform 0.3s; }
        .testimonial-card:hover { transform: translateY(-5px); }
        .testimonial-card .avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #ff6a00; margin-bottom: 15px; }
        .testimonial-card .name { font-weight: 700; font-size: 18px; }
        .testimonial-card .role { color: #aaa; font-size: 14px; margin-bottom: 12px; }
        .testimonial-card .stars { color: #ffb800; margin-bottom: 12px; }
        .testimonial-card .comment { color: rgba(255,255,255,0.8); font-size: 15px; line-height: 1.6; font-style: italic; }

        /* ===== NEWSLETTER ===== */
        .newsletter { padding: 80px 0; background: #111; border-top: 1px solid rgba(255,255,255,0.05); }
        .newsletter-content { max-width: 600px; margin: 0 auto; text-align: center; }
        .newsletter-content h2 { font-size: 32px; font-weight: 700; margin-bottom: 10px; }
        .newsletter-content p { color: rgba(255,255,255,0.6); margin-bottom: 30px; }
        .newsletter-form { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
        .newsletter-form input { flex: 1; min-width: 220px; padding: 16px 24px; border-radius: 60px; border: 1px solid #333; background: #0d0d0d; color: #fff; font-size: 16px; outline: none; transition: border 0.3s; }
        .newsletter-form input:focus { border-color: #ff6a00; }
        .newsletter-form button { background: #ff6a00; color: #fff; border: none; padding: 16px 40px; border-radius: 60px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s; }
        .newsletter-form button:hover { background: #ff8833; transform: scale(1.04); box-shadow: 0 8px 25px rgba(255,106,0,0.4); }

        /* ===== FOOTER ===== */
        .footer { background: #0a0a0a; padding: 50px 0 30px; border-top: 1px solid rgba(255,255,255,0.05); }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 40px; margin-bottom: 40px; }
        .footer-col h4 { font-size: 18px; font-weight: 700; margin-bottom: 18px; color: #fff; }
        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { color: rgba(255,255,255,0.6); transition: color 0.3s; font-size: 15px; }
        .footer-col ul li a:hover { color: #ff6a00; }
        .footer-social { display: flex; gap: 15px; margin-top: 15px; }
        .footer-social a { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: #1a1a1a; color: #fff; font-size: 18px; transition: all 0.3s; }
        .footer-social a:hover { background: #ff6a00; transform: translateY(-4px); }
        .footer-payments { display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px; }
        .footer-payments i { font-size: 32px; color: rgba(255,255,255,0.4); transition: color 0.3s; }
        .footer-payments i:hover { color: #fff; }
        .footer-bottom { text-align: center; padding-top: 25px; border-top: 1px solid rgba(255,255,255,0.05); color: rgba(255,255,255,0.4); font-size: 14px; }
        .footer-bottom a { color: #ff6a00; }

        @media (max-width:1024px) {
            .navbar { padding: 0 18px; height: 68px; }
            .nav-menu { gap: 18px; }
            .nav-menu li a { font-size: 15px; }
            .hero { padding: 0 30px; min-height: 560px; margin-top: -68px; }
            .logo img { height: 54px; }
            .story-grid { grid-template-columns: 1fr; gap: 40px; text-align: center; }
            .story-text .badge { border-left: none; padding-left: 0; }
            .story-text h2 { font-size: 32px; }
            .story-visual .year-badge { font-size: 80px; }
            .story-visual .founder-card { max-width: 100%; padding: 30px 20px; }
        }
        @media (max-width:768px) {
            .top-bar { height: auto; padding: 10px 12px; }
            .top-bar-container { grid-template-columns: repeat(3,1fr); gap: 6px; }
            .top-item { border-right: none; justify-content: flex-start; padding: 4px 0; gap: 8px; }
            .top-item i { font-size: 17px; }
            .top-text strong { font-size: 12px; }
            .top-text span { font-size: 10px; }
            .top-item:nth-child(4), .top-item:nth-child(5) { display: none; }
            .navbar { height: 62px; padding: 0 14px; border-radius: 16px; width: 95%; }
            .logo img { height: 48px; }
            .logo-text .brand { font-size: 18px; }
            .logo-text .sub { font-size: 9px; }
            .nav-menu { display: none; flex-direction: column; position: absolute; top: 72px; left: 0; width: 100%; background: rgba(10,10,10,0.96); backdrop-filter: blur(12px); padding: 24px 20px; gap: 14px; border-radius: 0 0 20px 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.7); }
            .nav-menu.open { display: flex; }
            .nav-menu li a { font-size: 18px; }
            .hamburger { display: flex; }
            .nav-icons { gap: 14px; }
            .nav-icons a { font-size: 17px; }
            .cart-badge { width: 16px; height: 16px; font-size: 9px; top: -6px; right: -8px; }
            .hero { flex-direction: column; justify-content: center; padding: 0 20px; min-height: 480px; height: auto; max-height: none; margin-top: -62px; padding-top: 40px; padding-bottom: 60px; }
            .hero-content { flex-direction: column; text-align: center; gap: 10px; }
            .hero-text { max-width: 100%; }
            .hero-text p { max-width: 100%; margin: 16px 0 24px; font-size: 16px; }
            .hero-buttons { justify-content: center; }
            .btn { height: 48px; font-size: 14px; min-width: 150px; padding: 0 24px; }
            .hero-visual { display: none; }
            .categories-grid { grid-template-columns: repeat(2,1fr); }
            .products-grid { grid-template-columns: repeat(2,1fr); }
            .testimonials-grid { grid-template-columns: 1fr; }
            .promo-content h2 { font-size: 30px; }
            .story-text h2 { font-size: 28px; }
            .search-container input { width: 120px; }
            .search-container button { font-size: 12px; padding: 6px 12px; }
        }
        @media (max-width:480px) {
            .top-bar-container { grid-template-columns: repeat(2,1fr); }
            .top-item:nth-child(3) { display: none; }
            .hero-text h1 { font-size: clamp(32px,10vw,42px); }
            .hero-text p { font-size: 14px; }
            .btn { height: 44px; font-size: 13px; min-width: 130px; padding: 0 18px; }
            .navbar { height: 56px; }
            .logo img { height: 40px; }
            .nav-icons a { font-size: 15px; }
            .nav-icons { gap: 10px; }
            .hero { margin-top: -56px; }
            .story-text h2 { font-size: 24px; }
            .story-text p { font-size: 15px; }
            .story-visual .year-badge { font-size: 60px; }
            .founder-card .avatar-placeholder { width: 70px; height: 70px; font-size: 28px; }
            .categories-grid { grid-template-columns: 1fr; }
            .products-grid { grid-template-columns: 1fr; }
            .promo-content h2 { font-size: 24px; }
            .promo-content .btn-promo { padding: 14px 30px; font-size: 16px; }
            .newsletter-form input { min-width: 100%; }
            .newsletter-form button { width: 100%; }
            .search-container input { width: 80px; font-size: 12px; }
            .search-container button { font-size: 11px; padding: 4px 10px; }
        }
    </style>
</head>
<body>

    <!-- ===== TOP BAR ===== -->
    <div class="top-bar">
        <div class="top-bar-container">
            <div class="top-item"><i class="fas fa-truck"></i><div class="top-text"><strong>Livraison</strong><span>Internationale</span></div></div>
            <div class="top-item"><i class="fas fa-shield-alt"></i><div class="top-text"><strong>Paiement</strong><span>100% sécurisé</span></div></div>
            <div class="top-item"><i class="fas fa-star"></i><div class="top-text"><strong>Produits</strong><span>Premium</span></div></div>
            <div class="top-item"><i class="fas fa-headset"></i><div class="top-text"><strong>Support</strong><span>24/7</span></div></div>
            <div class="top-item"><i class="fas fa-undo-alt"></i><div class="top-text"><strong>Retours</strong><span>30 jours</span></div></div>
        </div>
    </div>

    <!-- ===== NAVBAR AVEC BARRE DE RECHERCHE ===== -->
    <div class="navbar-wrapper">
        <nav class="navbar">
            <div class="logo">
                <img src="logoeasy.png" alt="EasyPick Logo" />
                <div class="logo-text">
                    <span class="brand"><span class="easy">EASY</span><span class="pick">PICK</span></span>
                    <span class="sub">By Samy Sabeur</span>
                </div>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" class="active">Accueil</a></li>
                <li><a href="boutique.php">Boutique</a></li>
                <li><a href="nouveautes.php">Nouveautés</a></li>
                <li><a href="promotions.php">Promotions</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
           <div class="nav-icons">
    <!-- ===== LOUPE POUR OUVRIR LA RECHERCHE ===== -->
    <a href="#" class="search-toggle" id="searchToggle" aria-label="Rechercher">
        <i class="fas fa-search"></i>
    </a>
    <a href="wishlist.php" style="color:#ff6a00;"><i class="fas fa-heart"></i></a>
    <?php if ($user_connecte): ?>
        <a href="mon-compte.php"><i class="fas fa-user"></i></a>
    <?php else: ?>
        <a href="login.php"><i class="fas fa-user"></i></a>
    <?php endif; ?>
    <a href="panier.php" style="position:relative;">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-badge"><?= $nb_articles ?></span>
    </a>
    <?php if ($user_connecte && $user_role === 'admin'): ?>
        <a href="admin.php"><i class="fas fa-cog"></i></a>
    <?php endif; ?>
    <button class="hamburger" id="hamburger"><span></span><span></span><span></span></button>
</div>
        </nav>
    </div>

   
    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-text">
                <h1><span class="line1">LA</span><span class="line2">TECHNOLOGIE</span><span class="line3">SIMPLIFIÉE.</span></h1>
                <p>Découvrez les meilleurs accessoires tech sélectionnés avec soin pour améliorer votre quotidien.</p>
                <div class="hero-buttons">
                    <a href="boutique.php" class="btn btn-primary">Découvrir nos produits</a>
                    <a href="#" class="btn btn-secondary">Voir les offres</a>
                </div>
            </div>
            <div class="hero-visual"><div class="glow-circle"></div></div>
        </div>
    </section>

    <!-- ===== NOTRE HISTOIRE ===== -->
    <section class="brand-story">
        <div class="container">
            <div class="story-grid">
                <div class="story-text">
                    <span class="badge">Notre histoire</span>
                    <h2>Une passion pour la tech, <br />une mission : <span>simplifier vos choix</span>.</h2>
                    <p>Fondée en <strong>2025</strong> par <strong>Samy Sabeur</strong>, EasyPick est bien plus qu'une simple boutique en ligne.</p>
                    <div class="signature">— <span>EasyPick</span>, votre guide tech depuis 2025.</div>
                </div>
                <div class="story-visual">
                    <div class="year-badge">2025</div>
                    <div class="founder-card">
                        <div class="avatar-placeholder"><i class="fas fa-user"></i></div>
                        <h4>Samy Sabeur</h4>
                        <div class="title">Fondateur & CEO</div>
                        <div class="divider"></div>
                        <div class="quote"><i class="fas fa-quote-left"></i> Rendre la tech accessible et désirable, c'est notre ADN. <i class="fas fa-quote-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CATEGORIES AVEC IMAGES SPÉCIFIQUES ===== -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Shop par <span>Catégorie</span></h2>
            <p class="section-sub">Trouvez l'accessoire parfait pour votre setup</p>
            <div class="categories-grid">
                <!-- Audio & Casques -->
                <div class="category-card">
                    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=400&fit=crop&crop=center" alt="Casque audio" />
                    <div class="cat-content">
                        <h3>Audio & Casques</h3>
                        <a href="boutique.php" class="btn-cat">Découvrir</a>
                    </div>
                </div>
                <!-- Claviers & Souris -->
                <div class="category-card">
                    <img src="https://images.unsplash.com/photo-1618384887929-16ec33abef8a?w=600&h=400&fit=crop&crop=center" alt="Clavier mécanique et souris" />
                    <div class="cat-content">
                        <h3>Claviers & Souris</h3>
                        <a href="boutique.php" class="btn-cat">Découvrir</a>
                    </div>
                </div>
                <!-- Chargeurs & Batteries -->
                <div class="category-card">
                    <img src="https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=600&h=400&fit=crop&crop=center" alt="Chargeur et batterie externe" />
                    <div class="cat-content">
                        <h3>Chargeurs & Batteries</h3>
                        <a href="boutique.php" class="btn-cat">Découvrir</a>
                    </div>
                </div>
                <!-- Enceintes & Son -->
                <div class="category-card">
                    <img src="https://images.unsplash.com/photo-1545454675-3531b543be5d?w=600&h=400&fit=crop&crop=center" alt="Enceinte Bluetooth" />
                    <div class="cat-content">
                        <h3>Enceintes & Son</h3>
                        <a href="boutique.php" class="btn-cat">Découvrir</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PRODUITS VEDETTES ===== -->
    <section class="products">
        <div class="container">
            <h2 class="section-title">Nos <span>Best Sellers</span></h2>
            <p class="section-sub">Les produits préférés de notre communauté</p>
            <div class="products-grid">
                <?php
                $stmt = $pdo->query('SELECT * FROM produits ORDER BY id DESC LIMIT 4');
                $produits = $stmt->fetchAll();
                if (empty($produits)): ?>
                    <p style="color:rgba(255,255,255,0.4); text-align:center; grid-column:1/-1; padding:40px 0;">Aucun produit disponible.</p>
                <?php else: foreach ($produits as $p): ?>
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($p['image'] ?? 'https://picsum.photos/seed/' . $p['id'] . '/300/200') ?>" alt="<?= htmlspecialchars($p['nom']) ?>" />
                        <div class="product-name"><?= htmlspecialchars($p['nom']) ?></div>
                        <div class="product-price">
                            <?php if ($p['prix_old']): ?><span class="old"><?= number_format($p['prix_old'], 2, ',', ' ') ?> €</span><?php endif; ?>
                            <?= number_format($p['prix'], 2, ',', ' ') ?> €
                        </div>
                        <button class="btn-add">Ajouter au panier</button>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- ===== PROMO ===== -->
    <section class="promo-banner">
        <div class="container">
            <div class="promo-content">
                <h2>Jusqu'à <span>-30%</span> sur votre première commande</h2>
                <p>Code : <strong>EASYPICK30</strong></p>
                <a href="boutique.php" class="btn-promo">Je profite de l'offre</a>
            </div>
        </div>
    </section>

    <!-- ===== AVIS ===== -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">Ils nous <span>font confiance</span></h2>
            <p class="section-sub">Ce que nos clients pensent de nous</p>
            <div class="testimonials-grid">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT a.*, u.nom as utilisateur_nom, u.prenom as utilisateur_prenom FROM avis a JOIN utilisateurs u ON a.utilisateur_id = u.id WHERE a.note >= 4 ORDER BY a.created_at DESC LIMIT 3");
                    $stmt->execute();
                    $avis = $stmt->fetchAll();
                    if (!empty($avis)): foreach ($avis as $avis_item):
                        $initials = strtoupper(substr($avis_item['utilisateur_prenom'] ?? 'U', 0, 1) . substr($avis_item['utilisateur_nom'] ?? 'N', 0, 1));
                    ?>
                        <div class="testimonial-card">
                            <div class="avatar"><span style="display:flex; align-items:center; justify-content:center; width:60px; height:60px; border-radius:50%; background:#ff6a00; color:#fff; font-weight:700; font-size:20px;"><?= $initials ?></span></div>
                            <div class="name"><?= htmlspecialchars($avis_item['utilisateur_prenom'] . ' ' . $avis_item['utilisateur_nom']) ?></div>
                            <div class="role">Client EasyPick</div>
                            <div class="stars"><?php for ($i = 1; $i <= 5; $i++): ?><?= $i <= $avis_item['note'] ? '<i class="fas fa-star"></i>' : '<i class="fas fa-star grey"></i>' ?><?php endfor; ?></div>
                            <div class="comment">"<?= htmlspecialchars($avis_item['commentaire']) ?>"</div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="testimonial-card" style="grid-column:1/-1; text-align:center; color:rgba(255,255,255,0.4); padding:40px;"><p>Pas encore d'avis. Soyez le premier !</p></div>
                    <?php endif; } catch (PDOException $e) { ?>
                    <div class="testimonial-card" style="grid-column:1/-1; text-align:center; color:rgba(255,255,255,0.4); padding:40px;"><p>Pas encore d'avis.</p></div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- ===== NEWSLETTER ===== -->
    <section class="newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2>Ne ratez aucune <span style="color:#ff6a00;">offre</span></h2>
                <p>Inscrivez-vous à notre newsletter.</p>
                <form class="newsletter-form" onsubmit="return inscrireNewsletter(event)">
                    <input type="email" id="newsletterEmail" placeholder="Votre adresse email" required />
                    <button type="submit">S'abonner</button>
                </form>
                <div id="newsletterMessage" style="margin-top:10px; text-align:center;"></div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col"><h4>EasyPick</h4><ul><li><a href="a-propos.php">À propos</a></li><li><a href="blog.php">Blog</a></li><li><a href="carrieres.php">Carrières</a></li><li><a href="contact.php">Contact</a></li></ul></div>
                <div class="footer-col"><h4>Aide</h4><ul><li><a href="#">Centre d'aide</a></li><li><a href="#">Suivi de commande</a></li><li><a href="#">Retours</a></li><li><a href="#">FAQ</a></li></ul></div>
                <div class="footer-col"><h4>Légal</h4><ul><li><a href="cgv.php">CGV</a></li><li><a href="confidentialite.php">Politique de confidentialité</a></li><li><a href="cookies.php">Cookies</a></li><li><a href="mentions-legales.php">Mentions légales</a></li></ul></div>
                <div class="footer-col"><h4>Suivez-nous</h4><div class="footer-social"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-twitter"></i></a><a href="#"><i class="fab fa-youtube"></i></a></div><div class="footer-payments"><i class="fab fa-cc-visa"></i><i class="fab fa-cc-mastercard"></i><i class="fab fa-cc-paypal"></i><i class="fab fa-cc-apple-pay"></i></div></div>
            </div>
            <div class="footer-bottom">&copy; 2026 EasyPick – Tous droits réservés. Design par <a href="#">Samy Sabeur</a>.</div>
        </div>
    </footer>

    <!-- ===== SCRIPTS ===== -->
    <script>
        // ===== HAMBURGER =====
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        hamburger.addEventListener('click', () => navMenu.classList.toggle('open'));
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) navMenu.classList.remove('open');
            });
        });

        // ===== NEWSLETTER =====
        function inscrireNewsletter(e) {
            e.preventDefault();
            const email = document.getElementById('newsletterEmail').value;
            const msg = document.getElementById('newsletterMessage');
            fetch('newsletter-inscrire.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(r => r.json())
            .then(data => {
                msg.textContent = data.message;
                msg.style.color = data.success ? '#00b894' : '#ff4444';
                if (data.success) document.getElementById('newsletterEmail').value = '';
                setTimeout(() => msg.textContent = '', 4000);
            })
            .catch(() => {
                msg.textContent = '⚠️ Une erreur est survenue.';
                msg.style.color = '#ff4444';
            });
        }

        // ===== BARRE DE RECHERCHE =====
        const searchToggle = document.getElementById('searchToggle');
        const searchContainer = document.getElementById('searchContainer');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const searchButton = document.getElementById('searchButton');
        let searchTimeout;

        // Ouvrir/fermer la barre de recherche
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            searchContainer.classList.toggle('active');
            if (searchContainer.classList.contains('active')) {
                searchInput.focus();
            } else {
                searchResults.style.display = 'none';
            }
        });

        // Recherche en temps réel
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchTimeout);

            if (query.length === 0) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`recherche-live.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.text())
                    .then(html => {
                        if (html.trim()) {
                            searchResults.innerHTML = html;
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.style.display = 'none';
                        }
                    })
                    .catch(() => {
                        searchResults.style.display = 'none';
                    });
            }, 300);
        });

        // Fermer les résultats en cliquant ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container') && !e.target.closest('.search-toggle')) {
                searchResults.style.display = 'none';
            }
        });

        // Recherche via le bouton
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query.length > 0) {
                window.location.href = `boutique.php?search=${encodeURIComponent(query)}`;
            }
        });

        // Recherche via la touche Entrée
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query.length > 0) {
                    window.location.href = `boutique.php?search=${encodeURIComponent(query)}`;
                }
            }
        });
    </script>
</body>
</html>