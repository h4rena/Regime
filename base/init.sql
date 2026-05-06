CREATE DATABASE regime;
USE regime;

CREATE TABLE genre(
id INT PRIMARY KEY AUTO_INCREMENT,
nom VARCHAR(50) --masculin, feminin, autre
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    genre_id INT,
    wallet_balance DECIMAL(10,2) DEFAULT 0,
    is_gold BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (genre_id) REFERENCES genre(id)
);


CREATE TABLE objectif(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) --augmenter_poids, reduire_poids, imc_ideal
);

CREATE TABLE sante (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    taille DECIMAL(5,2),
    poids DECIMAL(5,2),
    imc DECIMAL(5,2),
    id_objectif INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (id_objectif) REFERENCES objectif(id)
);

CREATE TABLE aliment(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150),
    pourcentage INT 
);

CREATE TABLE regime (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150),
    description TEXT,
    id_aliment INT,
    variation_poids DECIMAL(5,2),
    duree_jours INT,
    prix DECIMAL(10,2),
    FOREIGN KEY (id_aliment) REFERENCES aliment(id)
);

CREATE TABLE activite_sportive (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150),
    description TEXT,
    calories_par_heure INT,
    duree_recommandee_min INT
);

CREATE TABLE wallet_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE,
    montant DECIMAL(10,2),
    is_used BOOLEAN DEFAULT FALSE,
    used_by INT NULL,
    used_at DATETIME NULL,
    FOREIGN KEY (used_by) REFERENCES users(id)
);

CREATE TABLE wallet_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    montant DECIMAL(10,2),
    type ENUM('credit', 'debit'),
    description VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE statut(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) --actif, termine, abandonne
);

CREATE TABLE user_regime (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    regime_id INT,
    prix_paye DECIMAL(10,2),
    date_debut DATE,
    date_fin DATE,
    statut_id INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (regime_id) REFERENCES regime(id),
    FOREIGN KEY (statut_id) REFERENCES statut(id)
);

CREATE TABLE user_regime_activite (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_regime_id INT,
    activite_id INT,
    FOREIGN KEY (user_regime_id) REFERENCES user_regime(id),
    FOREIGN KEY (activite_id) REFERENCES activite_sportive(id)
);