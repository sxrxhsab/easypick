# ============================================================
# Script : Remplacement des textes statiques par __()
# Utilisation : .\remplace-textes.ps1
# ============================================================

$replacements = @{
    'Accueil' = 'accueil'
    'Boutique' = 'boutique'
    'Nouveautés' = 'nouveautes'
    'Promotions' = 'promotions'
    'Contact' = 'contact'
    'Mon compte' = 'mon_compte'
    'Connexion' = 'connexion'
    'Inscription' = 'inscription'
    'Déconnexion' = 'deconnexion'
    'Panier' = 'panier'
    'Rechercher' = 'rechercher'
    'Ajouter au panier' = 'ajouter_au_panier'
    'Acheter maintenant' = 'acheter_maintenant'
    'Voir le produit' = 'voir_le_produit'
    'En stock' = 'en_stock'
    'Rupture de stock' = 'rupture'
    'Avis' = 'avis'
    'Donnez votre avis' = 'donner_votre_avis'
    'Publier' = 'publier'
    'Total' = 'total'
    'Sous-total' = 'sous_total'
    'Livraison' = 'livraison'
    'Livraison offerte' = 'livraison_offerte'
    'Passer la commande' = 'passer_commande'
    'Continuer mes achats' = 'continuer_achats'
    'Votre panier est vide' = 'panier_vide'
    'Quantité' = 'quantite'
    'Supprimer' = 'supprimer'
    'Mettre à jour' = 'mettre_a_jour'
    'Email' = 'email'
    'Mot de passe' = 'mot_de_passe'
    'Confirmer le mot de passe' = 'confirmer_mot_de_passe'
    'Prénom' = 'prenom'
    'Nom' = 'nom'
    'Se connecter' = 'se_connecter'
    'Créer un compte' = 'creer_un_compte'
    'Déjà un compte ?' = 'deja_compte'
    'Pas encore de compte ?' = 'pas_encore_compte'
    'Retour à l\'accueil' = 'retour_accueil'
    'Tableau de bord' = 'tableau_de_bord'
    'Produits' = 'produits'
    'Commandes' = 'commandes'
    'Utilisateurs' = 'utilisateurs'
    'Catégories' = 'categories'
    'Ajouter' = 'ajouter'
    'Modifier' = 'modifier'
    'Supprimer' = 'supprimer'
    'Enregistrer' = 'enregistrer'
    'Annuler' = 'annuler'
    'À propos' = 'a_propos'
    'Blog' = 'blog'
    'Carrières' = 'carrieres'
    'CGV' = 'cgv'
    'Politique de confidentialité' = 'confidentialite'
    'Cookies' = 'cookies'
    'Mentions légales' = 'mentions_legales'
    'Suivez-nous' = 'suivez_nous'
    'Tous droits réservés' = 'tous_droits_reserves'
    'Design par' = 'design_par'
}

$excludeDirs = @('vendor', 'uploads', 'lang', 'suppliers', 'node_modules')
$files = Get-ChildItem -Recurse -Filter *.php | Where-Object {
    $path = $_.DirectoryName -replace [regex]::Escape((Get-Location).Path), ''
    $exclude = $false
    foreach ($dir in $excludeDirs) {
        if ($path -match "\\$dir\\") { $exclude = $true; break }
    }
    -not $exclude
}

Write-Host "🔍 Fichiers trouvés : $($files.Count)" -ForegroundColor Cyan

function Update-FileContent {
    param(
        [string]$FilePath,
        [hashtable]$Replacements
    )
    $content = Get-Content -Path $FilePath -Raw -Encoding UTF8
    $modified = $false
    
    foreach ($key in $Replacements.Keys) {
        $value = $Replacements[$key]
        $pattern = "(?<!<\?= __\('$value'\) )$key(?![^<]*>)"
        if ($content -match $pattern) {
            $content = $content -replace $pattern, "<?= __('$value') ?>"
            $modified = $true
        }
        $pattern = "(value|placeholder|title|alt|aria-label)=['""]($key)['""]"
        if ($content -match $pattern) {
            $content = $content -replace $pattern, "`$1='<?= __('$value') ?>'"
            $modified = $true
        }
    }
    
    if ($modified) {
        Set-Content -Path $FilePath -Value $content -Encoding UTF8 -NoNewline
        Write-Host "✅ Modifié : $FilePath" -ForegroundColor Green
    } else {
        Write-Host "⏭️ Aucun changement : $FilePath" -ForegroundColor Gray
    }
}

foreach ($file in $files) {
    Update-FileContent -FilePath $file.FullName -Replacements $replacements
}

Write-Host "`n🎉 Terminé !" -ForegroundColor Yellow
Write-Host "⚠️  Vérifie les fichiers avant de committer." -ForegroundColor Red
