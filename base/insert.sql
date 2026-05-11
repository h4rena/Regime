INSERT INTO role (nom) VALUES
('Admin'),
('Utilisateur');

INSERT INTO genre (nom) VALUES
('Femme'),
('Homme'),
('Autre');

INSERT INTO objectif (nom) VALUES
('Perte de poids'),
('Prise de poids'),
('atteindre imc');

INSERT INTO parametres (cle, libelle, valeur, type, description) VALUES
('gold_price', 'Prix de l\'option Gold', '50000', 'number', 'Paiement unique pour activer la remise de 15%'),
('gold_discount', 'Remise Gold', '15', 'number', 'Pourcentage de réduction sur les régimes'),
('wallet_bonus_rate', 'Bonus portefeuille', '1', 'number', 'Taux de crédit appliqué aux codes portefeuille');

INSERT INTO regime (nom, variation_poids, duree_jours, prix, pourcentage_viande, pourcentage_poisson, pourcentage_volaille) VALUES
('Régime Équilibré Marin', -3.00, 30, 33000.00, 25, 40, 35),
('Régime Léger Actif', -1.50, 14, 21000.00, 20, 45, 35),
('Régime Protéiné Force', 2.00, 21, 26000.00, 50, 25, 25),
('Détox Marin Intensif', -2.50, 14, 23000.00, 15, 50, 35),
('Équilibre Total', 0.00, 30, 18000.00, 30, 35, 35),
('Programme Gold Premium', -4.00, 45, 75000.00, 25, 45, 30);

-- password: test1234567
INSERT INTO users (id_role, nom, prenom, email, password, genre_id, Date_naissance, wallet_balance, is_gold) VALUES
(1, 'Admin', 'Test', 'admin@regime.local', '$2y$10$rUKxnBrNAu6O9ScC9JGM.O/zi3/pZ.EZrkU7yLtevVG6cOArfJmTC', 1, '1990-01-01', 250000, 1),
(2, 'Jean', 'Rakoto', 'jean.rakoto@regime.local', '$2y$10$rUKxnBrNAu6O9ScC9JGM.O/zi3/pZ.EZrkU7yLtevVG6cOArfJmTC.', 2, '1995-06-15', 125000, 0),
(2, 'Mina', 'Rasoa', 'mina.rasoa@regime.local', '$2y$10$rUKxnBrNAu6O9ScC9JGM.O/zi3/pZ.EZrkU7yLtevVG6cOArfJmTC.', 3, '1998-11-20', 80000, 0);

INSERT INTO wallet (user_id, type, montant) VALUES
(1, 'gold', 250000),
(2, 'normal', 125000),
(3, 'normal', 80000);

INSERT INTO wallet_codes (code, montant, is_used, used_by, used_at) VALUES
('TEST5000', 5000, FALSE, NULL, NULL),
('TEST25000', 25000, FALSE, NULL, NULL),
('TEST50000', 50000, FALSE, NULL, NULL),
('TEST100000', 100000, FALSE, NULL, NULL);

INSERT INTO wallet_transactions (user_id, montant, type) VALUES
(2, 25000, 'credit'),
(2, 50000, 'credit'),
(3, 100000, 'credit');

-- Activités sportives (5 activités requises)
INSERT INTO activite_sportive (nom, calories_par_heure, duree_recommandee_min) VALUES
('Course à pied', 600, 30),
('Natation', 500, 30),
('Vélo', 450, 40),
('Musculation', 400, 45),
('Yoga', 200, 60);

-- Codes portefeuille supplémentaires (pour totaliser 15 codes)
INSERT INTO wallet_codes (code, montant, is_used, used_by, used_at) VALUES
('BONUS1000', 1000, FALSE, NULL, NULL),
('BONUS2000', 2000, FALSE, NULL, NULL),
('WELCOME500', 500, FALSE, NULL, NULL),
('PREMIUM3000', 3000, FALSE, NULL, NULL),
('GOLD5000', 5000, FALSE, NULL, NULL),
('LUCKY10000', 10000, FALSE, NULL, NULL),
('SUPER15000', 15000, FALSE, NULL, NULL),
('MEGA20000', 20000, FALSE, NULL, NULL),
('ELITE25000', 25000, FALSE, NULL, NULL),
('VIP50000', 50000, FALSE, NULL, NULL),
('SPECIAL75000', 75000, FALSE, NULL, NULL);

-- ═══════════════════════════════════════════════════════════════════════════
-- DONNÉES SUPPLÉMENTAIRES POUR LE DASHBOARD
-- ═══════════════════════════════════════════════════════════════════════════

-- Statuts des souscriptions
INSERT INTO statut (nom) VALUES
('Actif'),
('Terminé'),
('Annulé'),
('En attente');

-- Données de santé (IMC distribution) - 3 utilisateurs
INSERT INTO sante (user_id, taille, poids, imc, id_objectif) VALUES
(2, 1.75, 78, 25.5, 1),  -- Jean: surpoids (25-29.9), objectif: perte de poids
(3, 1.62, 58, 22.1, 1),  -- Mina: normal (18.5-24.9), objectif: perte de poids
(1, 1.80, 75, 23.1, 3);  -- Admin: normal, objectif: atteindre imc

-- Souscriptions régimes (multiple souscriptions par utilisateur)
INSERT INTO user_regime (user_id, regime_id, prix_paye, date_debut, date_fin, statut_id) VALUES
-- Jean Rakoto (user_id=2) - 3 souscriptions
(2, 1, 28050, '2026-01-15', '2026-02-14', 2),     -- Régime Équilibré Marin (remise 15% Gold)
(2, 2, 21000, '2026-02-15', '2026-02-28', 1),     -- Régime Léger Actif
(2, 3, 26000, '2026-03-01', '2026-03-21', 1),     -- Régime Protéiné Force

-- Mina Rasoa (user_id=3) - 3 souscriptions
(3, 4, 23000, '2026-01-20', '2026-02-03', 2),     -- Détox Marin Intensif
(3, 2, 21000, '2026-02-10', '2026-02-23', 1),     -- Régime Léger Actif
(3, 5, 18000, '2026-03-05', '2026-04-04', 1),     -- Équilibre Total

-- Admin (user_id=1) - 2 souscriptions
(1, 6, 75000, '2025-12-01', '2026-01-15', 2),     -- Programme Gold Premium
(1, 1, 33000, '2026-02-01', '2026-03-02', 1);     -- Régime Équilibré Marin

-- Associations régimes-activités (lier les activités aux souscriptions)
INSERT INTO user_regime_activite (user_regime_id, activite_id) VALUES
-- Souscription 1 (Jean - Régime Équilibré Marin)
(1, 1), (1, 2), (1, 5),
-- Souscription 2 (Jean - Régime Léger Actif)
(2, 1), (2, 5),
-- Souscription 3 (Jean - Régime Protéiné Force)
(3, 4), (3, 1),
-- Souscription 4 (Mina - Détox Marin Intensif)
(4, 2), (4, 5),
-- Souscription 5 (Mina - Régime Léger Actif)
(5, 1), (5, 3),
-- Souscription 6 (Mina - Équilibre Total)
(6, 5), (6, 1),
-- Souscription 7 (Admin - Programme Gold Premium)
(7, 1), (7, 2), (7, 4),
-- Souscription 8 (Admin - Régime Équilibré Marin)
(8, 2), (8, 3);