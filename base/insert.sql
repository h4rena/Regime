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