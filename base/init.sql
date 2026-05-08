CREATE DATABASE regime;
USE regime;

CREATE TABLE role(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL
);

CREATE TABLE genre(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_role INT,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255),
    genre_id INT,
    Date_naissance Date ,
    wallet_balance DECIMAL(10,2) DEFAULT 0,
    is_gold BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (genre_id) REFERENCES genre(id),
    FOREIGN KEY (id_role) REFERENCES role(id)
);

CREATE TABLE objectif(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL
);

CREATE TABLE sante (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    taille DECIMAL(5,2),
    poids DECIMAL(5,2),
    imc DECIMAL(5,2),
    id_objectif INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (id_objectif) REFERENCES objectif(id)
);

CREATE TABLE aliment(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150),
    pourcentage DECIMAL(5,2)
);

CREATE TABLE regime (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150),
    variation_poids DECIMAL(5,2),
    duree_jours INT,
    prix DECIMAL(10,2)
);

CREATE TABLE regime_aliment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    regime_id INT,
    aliment_id INT,
    FOREIGN KEY (regime_id) REFERENCES regime(id),
    FOREIGN KEY (aliment_id) REFERENCES aliment(id)
);

CREATE TABLE activite_sportive (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150),
    calories_par_heure INT,
    duree_recommandee_min INT
);

CREATE TABLE wallet_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE,
    montant DECIMAL(10,2),
    is_used BOOLEAN DEFAULT FALSE,
    used_by INT NULL,
    used_at DATETIME NULL,
    FOREIGN KEY (used_by) REFERENCES users(id)
);

CREATE TABLE wallet_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    montant DECIMAL(10,2),
    type ENUM('credit', 'debit'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE wallet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE,
    type ENUM('gold', 'normal') DEFAULT 'normal',
    montant DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE statut(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50)
);

CREATE TABLE user_regime (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_regime_id INT,
    activite_id INT,
    FOREIGN KEY (user_regime_id) REFERENCES user_regime(id),
    FOREIGN KEY (activite_id) REFERENCES activite_sportive(id)
);