-- Script SQL pour créer les tables nécessaires à l'historique de commandes
-- À exécuter dans votre base de données 'ecom'

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    date_commande DATETIME NOT NULL,
    nom VARCHAR(150) NOT NULL,
    prenom VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL,
    adresse TEXT NOT NULL,
    code_postal VARCHAR(20) NOT NULL,
    tel VARCHAR(50) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des items de commande
CREATE TABLE IF NOT EXISTS commande_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commande_id INT UNSIGNED NOT NULL,
    produit_nom VARCHAR(255) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    quantite INT UNSIGNED NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vérifier que les tables nécessaires existent
-- Table users (devrait déjà exister)
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table produits (devrait déjà exister)
CREATE TABLE IF NOT EXISTS produits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    description TEXT,
    stock INT UNSIGNED DEFAULT 0,
    categorie VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table paniers (devrait déjà exister)
CREATE TABLE IF NOT EXISTS paniers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    produit_nom VARCHAR(255) NOT NULL,
    quantite INT UNSIGNED NOT NULL,
    INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
