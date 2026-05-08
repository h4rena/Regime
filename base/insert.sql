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

INSERT INTO regime (nom, variation_poids, duree_jours, prix) VALUES
('Régime Équilibré Marin', -3.00, 30, 33000.00),
('Régime Léger Actif', -1.50, 14, 21000.00),
('Régime Protéiné Force', 2.00, 21, 26000.00),
('Détox Marin Intensif', -2.50, 14, 23000.00),
('Équilibre Total', 0.00, 30, 18000.00),
('Programme Gold Premium', -4.00, 45, 75000.00);