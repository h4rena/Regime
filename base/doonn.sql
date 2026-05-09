-- Genres
INSERT INTO genre (nom) VALUES 
('Homme'),
('Femme');

-- Objectifs
INSERT INTO objectif (nom) VALUES
('Perte de poids'),
('Prise de masse'),
('Maintien du poids');

-- Statuts
INSERT INTO statut (nom) VALUES
('En cours'),
('Terminé'),
('Annulé');

-- Utilisateurs
INSERT INTO users (nom, prenom, email, password, genre_id, Date_naissance, wallet_balance, is_gold) VALUES
('Rakoto', 'Jean', 'jean.rakoto@example.com', '$2y$10$hashDuMotDePasse', 1, '1990-05-12', 100.00, TRUE),
('Randria', 'Marie', 'marie.randria@example.com', '$2y$10$hashDuMotDePasse', 2, '1995-08-20', 50.00, FALSE);

-- Santé
INSERT INTO sante (user_id, taille, poids, imc, id_objectif) VALUES
(1, 175, 80, 26.1, 1),
(2, 160, 55, 21.5, 2);

-- Aliments
INSERT INTO aliment (nom, pourcentage) VALUES
('Riz', 40.00),
('Poulet', 30.00),
('Légumes', 30.00);

-- Régimes
INSERT INTO regime (nom, variation_poids, duree_jours, prix) VALUES
('Régime minceur', -5.0, 30, 200.00),
('Régime prise de masse', 4.0, 45, 300.00);

-- Régime-Aliment
INSERT INTO regime_aliment (regime_id, aliment_id) VALUES
(1, 1),
(1, 3),
(2, 2);

-- Activités sportives
INSERT INTO activite_sportive (nom, calories_par_heure, duree_recommandee_min) VALUES
('Course à pied', 600, 30),
('Natation', 500, 45),
('Musculation', 400, 60);

-- Wallet codes
INSERT INTO wallet_codes (code, montant, is_used, used_by, used_at) VALUES
('CODE123', 50.00, FALSE, NULL, NULL),
('BONUS2026', 100.00, TRUE, 1, NOW());

-- Transactions
INSERT INTO wallet_transactions (user_id, montant, type) VALUES
(1, 50.00, 'credit'),
(1, 20.00, 'debit'),
(3, 100.00, 'credit');

-- User Régime
INSERT INTO user_regime (user_id, regime_id, prix_paye, date_debut, date_fin, statut_id) VALUES
(1, 1, 200.00, '2026-05-01', '2026-05-31', 1),
(2, 2, 300.00, '2026-05-01', '2026-06-15', 1);

-- User Régime Activité
INSERT INTO user_regime_activite (user_regime_id, activite_id) VALUES
(1, 1),
(1, 3),
(2, 2);

-- Wallet pour l'utilisateur 1
INSERT INTO wallet (user_id, type, montant)
VALUES (1, 'golde', 150.00);

-- Wallet pour l'utilisateur 2
INSERT INTO wallet (user_id, type, montant)
VALUES (3, 'normal', 75.50);

