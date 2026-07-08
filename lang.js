// lang.js - Système de traduction FR/EN complet et robuste

// ===== DICTIONNAIRE DE TRADUCTION =====
const translations = {
    'fr': {
        // Navigation
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
        'admin': 'Admin',
        
        // Recherche & Filtres
        'rechercher': 'Rechercher un produit...',
        'trier_par': 'Trier par',
        'pertinence': 'Pertinence',
        'prix_croissant': 'Prix croissant',
        'prix_decroissant': 'Prix décroissant',
        'meilleures_notes': 'Meilleures notes',
        'categories': 'Catégories',
        'marques': 'Marques',
        'prix': 'Prix',
        'note_minimale': 'Note minimale',
        '5_etoiles': '5 étoiles',
        '4_etoiles': '4+ étoiles',
        '3_etoiles': '3+ étoiles',
        'toutes_les_notes': 'Toutes les notes',
        'appliquer_filtres': 'Appliquer les filtres',
        'aucun_produit': 'Aucun produit ne correspond à vos critères.',
        'voir_tous_produits': 'Voir tous les produits',
        'notre_boutique': 'Notre Boutique',
        'description_boutique': 'Découvrez notre sélection d\'accessoires technologiques premium.',
        
        // Produits
        'ajouter_au_panier': 'Ajouter au panier',
        'acheter_maintenant': 'Acheter maintenant',
        'avis': 'avis',
        'donner_votre_avis': 'Donnez votre avis',
        'publier': 'Publier',
        'decouvrir': 'Découvrir',
        'voir_les_offres': 'Voir les offres',
        'nos_produits': 'Nos produits',
        'best_sellers': 'Nos Best Sellers',
        'shop_par_categorie': 'Shop par Catégorie',
        'trouvez_accessoire': 'Trouvez l\'accessoire parfait pour votre setup',
        
        // Promotions
        'profitez_offre': 'Je profite de l\'offre',
        'jusqua_30': 'Jusqu\'à -30%',
        'premiere_commande': 'sur votre première commande',
        'code_promo': 'Code : EASYPICK30',
        
        // Avis
        'ils_nous_font_confiance': 'Ils nous font confiance',
        'ce_que_clients_pensent': 'Ce que nos clients pensent de nous',
        
        // Newsletter
        'ne_ratez_aucune_offre': 'Ne ratez aucune offre',
        'inscrivez_newsletter': 'Inscrivez-vous à notre newsletter et recevez en avant-première nos promotions exclusives.',
        's_abonner': 'S\'abonner',
        'email_placeholder': 'Votre adresse email',
        
        // Footer
        'a_propos': 'À propos',
        'blog': 'Blog',
        'carrieres': 'Carrières',
        'aide': 'Aide',
        'legal': 'Légal',
        'cgv': 'CGV',
        'confidentialite': 'Politique de confidentialité',
        'cookies': 'Cookies',
        'mentions_legales': 'Mentions légales',
        'suivez_nous': 'Suivez-nous',
        'tous_droits_reserves': 'Tous droits réservés',
        'design_par': 'Design par',
        
        // Panier
        'total': 'Total',
        'livraison': 'Livraison',
        'paiement': 'Paiement',
        'continuer_achats': 'Continuer mes achats',
        'valider_commande': 'Valider la commande',
        'panier_vide': 'Votre panier est vide',
        
        // Hero
        'decouvrir_nos_produits': 'Découvrir nos produits',
        'description_hero': 'Découvrez les meilleurs accessoires tech sélectionnés avec soin pour améliorer votre quotidien, votre bureau et votre expérience numérique.',
        'la_technologie_simplifiee': 'LA TECHNOLOGIE SIMPLIFIÉE.',
        
        // Contact
        'contactez_nous': 'Contactez-nous',
        'votre_nom': 'Votre nom',
        'votre_email': 'Votre email',
        'sujet': 'Sujet',
        'votre_message': 'Votre message',
        'envoyer': 'Envoyer',
        'message_envoye': 'Votre message a été envoyé avec succès !',
        'erreur_envoi': 'Une erreur est survenue. Veuillez réessayer.',
        'une_question': 'Une question ? Un projet ? N\'hésitez pas à nous écrire.',
        'notre_equipe': 'Notre équipe est là pour répondre à toutes vos questions.',
        'email_contact': 'contact@easypick.com',
        'telephone': 'Téléphone',
        'adresse': 'Adresse',
        'horaires': 'Horaires',
        'lundi_vendredi': 'Lundi - Vendredi : 9h - 18h',
        
        // Compte
        'bienvenue': 'Bienvenue',
        'modifier_profil': 'Modifier mon profil',
        'mes_commandes': 'Mes commandes',
        'mes_avis': 'Mes avis',
        'ma_wishlist': 'Ma wishlist',
        
        // Produit
        'description': 'Description',
        'caracteristiques': 'Caractéristiques',
        'en_stock': 'En stock',
        'rupture_stock': 'Rupture de stock',
        'quantite': 'Quantité',
        'ajouter': 'Ajouter',
        
        // Erreurs
        'erreur_404': 'Page non trouvée',
        'erreur_serveur': 'Erreur serveur',
        'retour_accueil': 'Retour à l\'accueil'
    },
    
    'en': {
        // Navigation
        'accueil': 'Home',
        'boutique': 'Shop',
        'nouveautes': 'New Arrivals',
        'promotions': 'Promotions',
        'contact': 'Contact',
        'mon_compte': 'My Account',
        'connexion': 'Login',
        'inscription': 'Register',
        'deconnexion': 'Logout',
        'panier': 'Cart',
        'admin': 'Admin',
        
        // Search & Filters
        'rechercher': 'Search products...',
        'trier_par': 'Sort by',
        'pertinence': 'Relevance',
        'prix_croissant': 'Price: Low to High',
        'prix_decroissant': 'Price: High to Low',
        'meilleures_notes': 'Best Rated',
        'categories': 'Categories',
        'marques': 'Brands',
        'prix': 'Price',
        'note_minimale': 'Minimum Rating',
        '5_etoiles': '5 stars',
        '4_etoiles': '4+ stars',
        '3_etoiles': '3+ stars',
        'toutes_les_notes': 'All Ratings',
        'appliquer_filtres': 'Apply Filters',
        'aucun_produit': 'No products match your criteria.',
        'voir_tous_produits': 'View all products',
        'notre_boutique': 'Our Shop',
        'description_boutique': 'Discover our selection of premium tech accessories.',
        
        // Products
        'ajouter_au_panier': 'Add to Cart',
        'acheter_maintenant': 'Buy Now',
        'avis': 'reviews',
        'donner_votre_avis': 'Give your review',
        'publier': 'Publish',
        'decouvrir': 'Discover',
        'voir_les_offres': 'View Offers',
        'nos_produits': 'Our Products',
        'best_sellers': 'Our Best Sellers',
        'shop_par_categorie': 'Shop by Category',
        'trouvez_accessoire': 'Find the perfect accessory for your setup',
        
        // Promotions
        'profitez_offre': 'Take advantage',
        'jusqua_30': 'Up to -30%',
        'premiere_commande': 'on your first order',
        'code_promo': 'Code: EASYPICK30',
        
        // Reviews
        'ils_nous_font_confiance': 'They trust us',
        'ce_que_clients_pensent': 'What our customers think',
        
        // Newsletter
        'ne_ratez_aucune_offre': 'Don\'t miss any offer',
        'inscrivez_newsletter': 'Subscribe to our newsletter and receive our exclusive promotions in advance.',
        's_abonner': 'Subscribe',
        'email_placeholder': 'Your email address',
        
        // Footer
        'a_propos': 'About Us',
        'blog': 'Blog',
        'carrieres': 'Careers',
        'aide': 'Help',
        'legal': 'Legal',
        'cgv': 'Terms',
        'confidentialite': 'Privacy Policy',
        'cookies': 'Cookies',
        'mentions_legales': 'Legal Notices',
        'suivez_nous': 'Follow Us',
        'tous_droits_reserves': 'All rights reserved',
        'design_par': 'Design by',
        
        // Cart
        'total': 'Total',
        'livraison': 'Delivery',
        'paiement': 'Payment',
        'continuer_achats': 'Continue Shopping',
        'valider_commande': 'Checkout',
        'panier_vide': 'Your cart is empty',
        
        // Hero
        'decouvrir_nos_produits': 'Discover our products',
        'description_hero': 'Discover the best tech accessories carefully selected to improve your daily life, your workspace and your digital experience.',
        'la_technologie_simplifiee': 'TECHNOLOGY SIMPLIFIED.',
        
        // Contact
        'contactez_nous': 'Contact Us',
        'votre_nom': 'Your name',
        'votre_email': 'Your email',
        'sujet': 'Subject',
        'votre_message': 'Your message',
        'envoyer': 'Send',
        'message_envoye': 'Your message has been sent successfully!',
        'erreur_envoi': 'An error occurred. Please try again.',
        'une_question': 'A question? A project? Feel free to write to us.',
        'notre_equipe': 'Our team is here to answer all your questions.',
        'email_contact': 'contact@easypick.com',
        'telephone': 'Phone',
        'adresse': 'Address',
        'horaires': 'Opening hours',
        'lundi_vendredi': 'Monday - Friday: 9am - 6pm',
        
        // Account
        'bienvenue': 'Welcome',
        'modifier_profil': 'Edit profile',
        'mes_commandes': 'My orders',
        'mes_avis': 'My reviews',
        'ma_wishlist': 'My wishlist',
        
        // Product
        'description': 'Description',
        'caracteristiques': 'Features',
        'en_stock': 'In stock',
        'rupture_stock': 'Out of stock',
        'quantite': 'Quantity',
        'ajouter': 'Add',
        
        // Errors
        'erreur_404': 'Page not found',
        'erreur_serveur': 'Server error',
        'retour_accueil': 'Back to home'
    }
};

// ===== FONCTION PRINCIPALE =====
function translatePage(lang) {
    const t = translations[lang] || translations['fr'];
    
    // Traduire le texte des éléments
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (t[key] !== undefined) {
            el.textContent = t[key];
        }
    });
    
    // Traduire les placeholders
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
        const key = el.getAttribute('data-i18n-placeholder');
        if (t[key] !== undefined) {
            el.setAttribute('placeholder', t[key]);
        }
    });
    
    // Traduire les titres
    document.querySelectorAll('[data-i18n-title]').forEach(el => {
        const key = el.getAttribute('data-i18n-title');
        if (t[key] !== undefined) {
            el.setAttribute('title', t[key]);
        }
    });
    
    // Traduire les valeurs (pour les boutons, etc.)
    document.querySelectorAll('[data-i18n-value]').forEach(el => {
        const key = el.getAttribute('data-i18n-value');
        if (t[key] !== undefined) {
            el.value = t[key];
        }
    });
    
    // Traduire les attributs alt des images
    document.querySelectorAll('[data-i18n-alt]').forEach(el => {
        const key = el.getAttribute('data-i18n-alt');
        if (t[key] !== undefined) {
            el.setAttribute('alt', t[key]);
        }
    });
    
    // Mettre à jour l'attribut lang du document
    document.documentElement.lang = lang;
    
    // Sauvegarder la langue
    localStorage.setItem('lang', lang);
}

// ===== CHARGEMENT DE LA LANGUE =====
function getCurrentLang() {
    // Vérifier dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('lang')) {
        const lang = urlParams.get('lang');
        if (translations[lang]) {
            localStorage.setItem('lang', lang);
            return lang;
        }
    }
    
    // Vérifier dans localStorage
    const storedLang = localStorage.getItem('lang');
    if (storedLang && translations[storedLang]) {
        return storedLang;
    }
    
    // Vérifier la langue du navigateur
    const browserLang = navigator.language.split('-')[0];
    if (translations[browserLang]) {
        return browserLang;
    }
    
    // Par défaut : français
    return 'fr';
}

// ===== INITIALISATION =====
document.addEventListener('DOMContentLoaded', function() {
    const lang = getCurrentLang();
    translatePage(lang);
    
    // Mettre à jour l'apparence des sélecteurs de langue
    document.querySelectorAll('.lang-selector').forEach(btn => {
        const btnLang = btn.getAttribute('data-lang');
        if (btnLang === lang) {
            btn.style.color = '#ff6a00';
            btn.style.fontWeight = '700';
        } else {
            btn.style.color = 'rgba(255,255,255,0.4)';
            btn.style.fontWeight = '400';
        }
    });
});

// ===== GESTION DES CLICS SUR LES SÉLECTEURS =====
document.addEventListener('click', function(e) {
    const target = e.target.closest('.lang-selector');
    if (target) {
        e.preventDefault();
        const newLang = target.getAttribute('data-lang');
        if (translations[newLang]) {
            translatePage(newLang);
            
            // Mettre à jour l'URL avec le paramètre lang
            const url = new URL(window.location);
            url.searchParams.set('lang', newLang);
            window.history.pushState({ lang: newLang }, '', url);
            
            // Mettre à jour l'apparence des boutons
            document.querySelectorAll('.lang-selector').forEach(btn => {
                const btnLang = btn.getAttribute('data-lang');
                if (btnLang === newLang) {
                    btn.style.color = '#ff6a00';
                    btn.style.fontWeight = '700';
                } else {
                    btn.style.color = 'rgba(255,255,255,0.4)';
                    btn.style.fontWeight = '400';
                }
            });
        }
    }
});

// ===== GESTION DU RETOUR ARRIÈRE =====
window.addEventListener('popstate', function(event) {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('lang')) {
        const lang = urlParams.get('lang');
        if (translations[lang]) {
            translatePage(lang);
        }
    }
});

// ===== FONCTION UTILITAIRE POUR PHP =====
// Cette fonction peut être appelée depuis PHP pour obtenir la langue courante
function getLangForPHP() {
    return getCurrentLang();
}

// ===== EXPOSER LES FONCTIONS GLOBALEMENT =====
window.translatePage = translatePage;
window.getCurrentLang = getCurrentLang;

// ===== RECHARGER LA TRADUCTION APRÈS UN AJAX =====
function reloadTranslations() {
    const lang = getCurrentLang();
    translatePage(lang);
}

// Exposer la fonction de rechargement
window.reloadTranslations = reloadTranslations;

console.log('✅ lang.js chargé avec succès !');
console.log('🌍 Langue actuelle :', getCurrentLang());