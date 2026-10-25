-- =========================================================================
-- SCHEMA — niveau 1
-- =========================================================================

-- Nettoyage (ordre des FK)
DROP TABLE IF EXISTS plat_du_jour CASCADE;
DROP TABLE IF EXISTS ligne_commande CASCADE;
DROP TABLE IF EXISTS commande CASCADE;
DROP TABLE IF EXISTS plat CASCADE;
DROP TABLE IF EXISTS categorie CASCADE;

-- Catégories
CREATE TABLE categorie (
    id      SERIAL PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

-- Plats
CREATE TABLE plat (
    id           SERIAL PRIMARY KEY,
    nom          VARCHAR(100) NOT NULL UNIQUE,
    prix         NUMERIC(10,2) NOT NULL CHECK (prix > 0),
    id_categorie INT NOT NULL REFERENCES categorie(id),
    description  VARCHAR(255),
    disponible   BOOLEAN NOT NULL DEFAULT true
);

-- Plat du jour : 1 seul par date grâce à la PRIMARY KEY sur date_jour
CREATE TABLE plat_du_jour (
    date_jour DATE PRIMARY KEY,
    id_plat   INT NOT NULL REFERENCES plat(id)
);

-- Commandes
CREATE TABLE commande (
    id        SERIAL PRIMARY KEY,
    date_commande TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total     NUMERIC(10,2) NOT NULL DEFAULT 0
);

CREATE TABLE ligne_commande (
    id            SERIAL PRIMARY KEY,
    id_commande   INT NOT NULL REFERENCES commande(id) ON DELETE CASCADE,
    id_plat       INT NOT NULL REFERENCES plat(id),
    quantite      INT NOT NULL CHECK (quantite > 0),
    prix_unitaire NUMERIC(10,2) NOT NULL CHECK (prix_unitaire > 0)
);