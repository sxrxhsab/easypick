import psycopg2
import sys

# Utilise l'URL EXTERNE récupérée sur Render
DATABASE_URL = "postgresql://easypick_db_user:nmalnIW2aHCaMV1mpBN0gc5XknIYSZtQ@dpg-d961g8eq1p3s73fj7teg-a.virginia-postgres.render.com/easypick_db"

sql_script = """
-- Table categories
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table marques
CREATE TABLE IF NOT EXISTS marques (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    logo VARCHAR(255)
);

-- Table produits
CREATE TABLE IF NOT EXISTS produits (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    prix_old DECIMAL(10,2),
    stock INT NOT NULL DEFAULT 0,
    categorie_id INT REFERENCES categories(id) ON DELETE SET NULL,
    marque_id INT REFERENCES marques(id) ON DELETE SET NULL,
    image VARCHAR(255),
    images TEXT,
    est_promo SMALLINT DEFAULT 0,
    est_nouveau SMALLINT DEFAULT 0,
    est_top SMALLINT DEFAULT 0,
    note DECIMAL(2,1) DEFAULT 0.0,
    nb_avis INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT NULL
);

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    prenom VARCHAR(100),
    nom VARCHAR(100),
    telephone VARCHAR(20),
    adresse TEXT,
    code_postal VARCHAR(10),
    ville VARCHAR(100),
    pays VARCHAR(100) DEFAULT 'France',
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT NULL
);

-- Table commandes
CREATE TABLE IF NOT EXISTS commandes (
    id SERIAL PRIMARY KEY,
    utilisateur_id INT REFERENCES utilisateurs(id) ON DELETE SET NULL,
    email VARCHAR(255) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    adresse TEXT NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    pays VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    statut VARCHAR(20) DEFAULT 'en_attente',
    reference VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT NULL
);

-- Table lignes_commandes
CREATE TABLE IF NOT EXISTS lignes_commandes (
    id SERIAL PRIMARY KEY,
    commande_id INT REFERENCES commandes(id) ON DELETE CASCADE,
    produit_id INT REFERENCES produits(id),
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL
);

-- Table favoris
CREATE TABLE IF NOT EXISTS favoris (
    id SERIAL PRIMARY KEY,
    utilisateur_id INT REFERENCES utilisateurs(id) ON DELETE CASCADE,
    produit_id INT REFERENCES produits(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(utilisateur_id, produit_id)
);

-- Table avis
CREATE TABLE IF NOT EXISTS avis (
    id SERIAL PRIMARY KEY,
    produit_id INT REFERENCES produits(id) ON DELETE CASCADE,
    utilisateur_id INT REFERENCES utilisateurs(id) ON DELETE SET NULL,
    nom VARCHAR(100) NOT NULL,
    note SMALLINT NOT NULL,
    commentaire TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
"""

try:
    conn = psycopg2.connect(dsn=DATABASE_URL)
    cursor = conn.cursor()
    print("✅ Connexion réussie !")
    
    cursor.execute(sql_script)
    conn.commit()
    print("✅ Tables créées avec succès !")
    
    cursor.execute("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")
    tables = cursor.fetchall()
    print("\n📋 Tables créées :")
    for table in tables:
        print(f"  - {table[0]}")
    
except Exception as e:
    print(f"❌ Erreur : {e}")
    sys.exit(1)
finally:
    if 'cursor' in locals():
        cursor.close()
    if 'conn' in locals():
        conn.close()