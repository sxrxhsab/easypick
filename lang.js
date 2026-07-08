// lang.js - Traduction JavaScript ultra-simple
var translations = {
    'fr': {
        'accueil': 'Accueil',
        'boutique': 'Boutique',
        'nouveautes': 'Nouveautés',
        'promotions': 'Promotions',
        'contact': 'Contact',
        'mon_compte': 'Mon compte',
        'connexion': 'Connexion',
        'inscription': 'Inscription',
        'deconnexion': 'Déconnexion',
        'panier': 'Panier',
        'rechercher': 'Rechercher un produit...',
        'ajouter_au_panier': 'Ajouter au panier',
        'acheter_maintenant': 'Acheter maintenant',
        'total': 'Total',
        'livraison': 'Livraison',
        'paiement': 'Paiement',
        'avis': 'Avis',
        'donner_votre_avis': 'Donnez votre avis',
        'publier': 'Publier',
        'a_propos': 'À propos',
        'blog': 'Blog',
        'carrieres': 'Carrières',
        'cgv': 'CGV',
        'confidentialite': 'Politique de confidentialité',
        'cookies': 'Cookies',
        'mentions_legales': 'Mentions légales',
        'suivez_nous': 'Suivez-nous',
        'tous_droits_reserves': 'Tous droits réservés',
        'design_par': 'Design par',
        'aide': 'Aide',
        'legal': 'Légal',
        'decouvrir': 'Découvrir',
        'voir_les_offres': 'Voir les offres',
        'shop_par_categorie': 'Shop par Catégorie',
        'best_sellers': 'Nos Best Sellers',
        'profitez_offre': 'Je profite de l\'offre',
        'jusqua_30': 'Jusqu\'à -30%',
        'premiere_commande': 'sur votre première commande',
        'code_promo': 'Code : EASYPICK30',
        'ils_nous_font_confiance': 'Ils nous font confiance',
        'ce_que_clients_pensent': 'Ce que nos clients pensent de nous',
        'ne_ratez_aucune_offre': 'Ne ratez aucune offre',
        'inscrivez_newsletter': 'Inscrivez-vous à notre newsletter',
        's_abonner': 'S\'abonner',
        'categorie': 'Catégorie',
        'marque': 'Marque',
        'prix': 'Prix',
        'note_minimale': 'Note minimale',
        'toutes_les_notes': 'Toutes les notes',
        'appliquer_filtres': 'Appliquer les filtres',
        'decouvrir_nos_produits': 'Découvrir nos produits',
        'nos_produits': 'Nos produits',
        'description_hero': 'Découvrez les meilleurs accessoires tech sélectionnés avec soin pour améliorer votre quotidien, votre bureau et votre expérience numérique.',
        'notre_boutique': 'Notre Boutique'
    },
    'en': {
        'accueil': 'Home',
        'boutique': 'Shop',
        'nouveautes': 'New arrivals',
        'promotions': 'Promotions',
        'contact': 'Contact',
        'mon_compte': 'My account',
        'connexion': 'Login',
        'inscription': 'Register',
        'deconnexion': 'Logout',
        'panier': 'Cart',
        'rechercher': 'Search products...',
        'ajouter_au_panier': 'Add to cart',
        'acheter_maintenant': 'Buy now',
        'total': 'Total',
        'livraison': 'Delivery',
        'paiement': 'Payment',
        'avis': 'Reviews',
        'donner_votre_avis': 'Give your review',
        'publier': 'Publish',
        'a_propos': 'About us',
        'blog': 'Blog',
        'carrieres': 'Careers',
        'cgv': 'Terms',
        'confidentialite': 'Privacy policy',
        'cookies': 'Cookies',
        'mentions_legales': 'Legal notices',
        'suivez_nous': 'Follow us',
        'tous_droits_reserves': 'All rights reserved',
        'design_par': 'Design by',
        'aide': 'Help',
        'legal': 'Legal',
        'decouvrir': 'Discover',
        'voir_les_offres': 'View offers',
        'shop_par_categorie': 'Shop by Category',
        'best_sellers': 'Our Best Sellers',
        'profitez_offre': 'Take advantage',
        'jusqua_30': 'Up to -30%',
        'premiere_commande': 'on your first order',
        'code_promo': 'Code: EASYPICK30',
        'ils_nous_font_confiance': 'They trust us',
        'ce_que_clients_pensent': 'What our customers think',
        'ne_ratez_aucune_offre': 'Don\'t miss any offer',
        'inscrivez_newsletter': 'Subscribe to our newsletter',
        's_abonner': 'Subscribe',
        'categorie': 'Category',
        'marque': 'Brand',
        'prix': 'Price',
        'note_minimale': 'Minimum rating',
        'toutes_les_notes': 'All ratings',
        'appliquer_filtres': 'Apply filters',
        'decouvrir_nos_produits': 'Discover our products',
        'nos_produits': 'Our products',
        'description_hero': 'Discover the best tech accessories carefully selected to improve your daily life, your workspace and your digital experience.',
        'notre_boutique': 'Our Shop'
    }
};

// Fonction pour traduire la page
function translatePage(lang) {
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
            el.textContent = translations[lang][key];
        }
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.getAttribute('data-i18n-placeholder');
        if (translations[lang] && translations[lang][key]) {
            el.setAttribute('placeholder', translations[lang][key]);
        }
    });
    document.querySelectorAll('[data-i18n-title]').forEach(el => {
        const key = el.getAttribute('data-i18n-title');
        if (translations[lang] && translations[lang][key]) {
            el.setAttribute('title', translations[lang][key]);
        }
    });
    localStorage.setItem('lang', lang);
}

// Charger la langue
var lang = localStorage.getItem('lang') || 'fr';
if (window.location.search.includes('lang=')) {
    lang = window.location.search.split('lang=')[1].split('&')[0];
    localStorage.setItem('lang', lang);
}
document.documentElement.lang = lang;

// Attendre que le DOM soit chargé pour traduire
document.addEventListener('DOMContentLoaded', function() {
    translatePage(lang);
});

// Gestion des clics sur les sélecteurs de langue
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('lang-selector')) {
        e.preventDefault();
        var newLang = e.target.getAttribute('data-lang');
        translatePage(newLang);
        var url = new URL(window.location);
        url.searchParams.set('lang', newLang);
        window.history.pushState({}, '', url);
    }
});

// Traduire aussi après un changement de page AJAX (si besoin)
window.addEventListener('popstate', function() {
    var currentLang = localStorage.getItem('lang') || 'fr';
    translatePage(currentLang);
});