-- =========================================================================
-- DONNÉES — niveau 1
-- =========================================================================

-- Catégories
INSERT INTO categorie (libelle) VALUES
('Goûter'),
('Plat principal'),
('Boisson')
ON CONFLICT (libelle) DO NOTHING;

-- Plats (Goûter)
INSERT INTO plat (nom, prix, id_categorie, description, disponible) VALUES
('Mofo gasy',   10.50, (SELECT id FROM categorie WHERE libelle = 'Goûter'),
 'Classique malgache, croquant et moelleux.', true),
('Mokary',      12.00, (SELECT id FROM categorie WHERE libelle = 'Goûter'),
 'Galette de riz sucrée, petit-déjeuner ou goûter.', true),
('Koba',        15.00, (SELECT id FROM categorie WHERE libelle = 'Goûter'),
 'Gâteau de riz et cacahuètes enveloppé dans une feuille de bananier.', true),
('Godrogodro',  18.00, (SELECT id FROM categorie WHERE libelle = 'Goûter'),
 'Gâteau de semoule caramélisé au lait de coco.', true),
('Menakely',    20.00, (SELECT id FROM categorie WHERE libelle = 'Goûter'),
 'Beignet traditionnel en forme de couronne.', true)
ON CONFLICT (nom) DO NOTHING;

-- Plats principaux
INSERT INTO plat (nom, prix, id_categorie, description, disponible) VALUES
('Ravitoto sy henakisoa',  25.00, (SELECT id FROM categorie WHERE libelle = 'Plat principal'),
 'Feuilles de manioc pilées au porc.', true),
('Akoho sy voanio',        28.00, (SELECT id FROM categorie WHERE libelle = 'Plat principal'),
 'Poulet au lait de coco.', true),
('Romazava',               22.00, (SELECT id FROM categorie WHERE libelle = 'Plat principal'),
 'Bouillon de viande aux brèdes mafana.', true),
('Henakisoa sy tsaramaso', 24.00, (SELECT id FROM categorie WHERE libelle = 'Plat principal'),
 'Porc aux haricots blancs.', true),
('Soupe chinoise',         18.00, (SELECT id FROM categorie WHERE libelle = 'Plat principal'),
 'Soupe de nouilles et légumes.', true),
('Mi sao',                 15.00, (SELECT id FROM categorie WHERE libelle = 'Plat principal'),
 'Nouilles sautées malgaches.', true),
('Vary amin''anana',       14.00, (SELECT id FROM categorie WHERE libelle = 'Plat principal'),
 'Riz aux brèdes.', true)
ON CONFLICT (nom) DO NOTHING;

-- Un plat épuisé (pour tester l'interface)
UPDATE plat SET disponible = false WHERE nom = 'Soupe chinoise';

-- =========================================================================
-- PLAT DU JOUR — 7 jours (aujourd'hui + 6 précédents)
-- =========================================================================
DELETE FROM plat_du_jour
WHERE date_jour BETWEEN CURRENT_DATE - INTERVAL '6 days' AND CURRENT_DATE;

INSERT INTO plat_du_jour (date_jour, id_plat) VALUES
(CURRENT_DATE - INTERVAL '6 days', (SELECT id FROM plat WHERE nom = 'Ravitoto sy henakisoa')),
(CURRENT_DATE - INTERVAL '5 days', (SELECT id FROM plat WHERE nom = 'Akoho sy voanio')),
(CURRENT_DATE - INTERVAL '4 days', (SELECT id FROM plat WHERE nom = 'Romazava')),
(CURRENT_DATE - INTERVAL '3 days', (SELECT id FROM plat WHERE nom = 'Henakisoa sy tsaramaso')),
(CURRENT_DATE - INTERVAL '2 days', (SELECT id FROM plat WHERE nom = 'Soupe chinoise')),
(CURRENT_DATE - INTERVAL '1 day',  (SELECT id FROM plat WHERE nom = 'Mi sao')),
(CURRENT_DATE,                     (SELECT id FROM plat WHERE nom = 'Vary amin''anana'));

-- =========================================================================
-- PREUVES DES CONTRAINTES (ces requêtes doivent ÉCHOUER — c'est attendu)
-- =========================================================================

-- 1) Prix à 0 → viole check_prix_positif
INSERT INTO plat (nom, prix, id_categorie, description)
VALUES ('Test 0 ar', 0.00,
        (SELECT id FROM categorie WHERE libelle = 'Goûter'),
        'Devrait échouer');
-- ERROR: new row for relation "plat" violates check constraint "plat_prix_check"

-- 2) Nom en double → viole UNIQUE(nom)
INSERT INTO plat (nom, prix, id_categorie, description)
VALUES ('Ravitoto sy henakisoa', 25.00,
        (SELECT id FROM categorie WHERE libelle = 'Goûter'),
        'Devrait échouer');
-- ERROR: duplicate key value violates unique constraint "plat_nom_key"

-- 3) Deux plats du jour à la même date → viole PK(date_jour)
INSERT INTO plat_du_jour (date_jour, id_plat)
VALUES (CURRENT_DATE, (SELECT id FROM plat WHERE nom = 'Mofo gasy'));
-- ERROR: duplicate key value violates unique constraint "plat_du_jour_pkey"