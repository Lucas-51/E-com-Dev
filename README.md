# A faire 

- débeug le contactez-nous

# E-com-Dev

Site e‑commerce de démonstration "Luc & Luk Shop" développé en PHP + PDO (MySQL) et HTML/CSS.

## Description

Une petite boutique en ligne montrant :
- Pages : accueil, catégories, produit, panier, connexion/inscription, contact.
- Panier en session et synchronisation en base pour les utilisateurs connectés.
- Validation de commande avec saisie des informations d'expédition et enregistrement d'un historique de commandes en session.
- Contrôle de stock côté panier et lors de la validation.
- Composant de contact (bulle) sur la page d'accueil.

## Prérequis

- MAMP / PHP 
- MySQL

## Installation

1. Cloner le dépôt ou copier les fichiers dans votre dossier htdocs de MAMP, par exemple :
   /Applications/MAMP/htdocs/E-com-Dev
2. Créer la base de données et les tables (exemples ci‑dessous).
3. Mettre à jour `config.php` si nécessaire (identifiants de la BDD).
4. Lancer MAMP et ouvrir `http://localhost:8888/E-com-Dev/` (port selon votre config MAMP).

## Structure du projet (fichiers importants)

- `index.php` : page d'accueil et affichage des produits en vedette.
- `categorie.php` : liste des catégories et affichage des produits filtrés.
- `produit.php` : page produit (détails).
- `panier.php` : affichage et modification du panier.
- `valider_panier.php` : formulaire de validation (informations d'envoi) et récapitulatif.
- `connexion.php`, `inscription.php`, `deconnexion.php` : gestion utilisateur basique.
- `contact.php` : page de contact (formulaire d'envoi mail).
- `includes/card.php` : génération des cartes produit.
- `includes/contact-bubble.php` : composant bulle de contact (présent sur l'accueil).
- `includes/panier_db.php` : helpers pour charger/sauvegarder le panier en base (si présents).
- `style.css` : styles du site.
- `config.php` : configuration de la connexion PDO à la BDD.

## Base de données (exemples)

Exemple minimal de tables MySQL à créer (adapter types/contraintes selon besoin) :

```sql
CREATE DATABASE ecom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecom;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE produits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL UNIQUE,
  description TEXT,
  prix DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  categorie VARCHAR(80) DEFAULT NULL
);

-- Optionnel : panier persistant par utilisateur
CREATE TABLE paniers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  produit_nom VARCHAR(150) NOT NULL,
  quantite INT NOT NULL DEFAULT 1,
  UNIQUE KEY(user_id, produit_nom),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

Importer quelques produits de test (exemple) :

```sql
INSERT INTO produits (nom, description, prix, stock, categorie) VALUES
('iphone', 'iPhone dernière génération.', 999.00, 5, 'iphone'),
('ipad', 'iPad 10,9 pouces dernière génération.', 599.00, 0, 'ipad'),
('airpods', 'AirPods Apple sans fil.', 199.00, 10, 'airpods'),
('macbook', 'Macbook Pro 16 pouces.', 1499.00, 2, 'macbook');
```

## Configuration importante

- `config.php` contient la connexion PDO. Vérifier host, db, user, pass.
- L'adresse destinataire du formulaire de contact par défaut est définie dans `contact.php` (variable `$destinataire`). Remplacer par l'email souhaité.
- Le fuseau horaire pour l'horodatage des commandes est défini sur `Europe/Paris` dans `valider_panier.php`.

## Fonctionnalités implémentées

- Ajout/suppression/modification de quantité depuis la page catégorie et panier.
- Vérification du stock avant ajout et avant validation (message d'erreur affiché si insuffisant).
- Validation de commande : saisie des informations client (nom, prénom, email, adresse, téléphone) puis affichage d'un récapitulatif.
- Enregistrement d'un historique simple en session (possibilité d'étendre pour stocker en base).
- Panier sauvegardé en base pour les utilisateurs connectés (fonctions utilitaires dans `includes/panier_db.php`).

## Personnalisation

- Pour changer le style, modifier `style.css`.
- Pour ajouter des images, placer les fichiers dans le dossier `images/` et nommer selon `nom.jpg` attendu par `includes/card.php`.
- Pour activer l'envoi d'email en environnement local, configurer un SMTP ou utiliser un outil comme MailHog / mhsendmail avec MAMP.

## Sécurité & améliorations possibles

- Hacher les mots de passe (déjà réalisé avec `password_hash` lors de l'inscription).
- Validation et nettoyage plus stricts des données côté serveur.
- Utiliser des jetons CSRF pour les formulaires.
- Stocker l'historique des commandes en base plutôt qu'en session.
- Ajouter pagination, recherche et filtrage avancé des produits.

## Tests rapides

- Vérifier la page d'accueil : `index.php`.
- Tester l'ajout de produits au panier et la persistance après connexion.
- Tester la validation et le récapitulatif (horodatage en heure Paris).
- Tester le formulaire de contact et configurer `$destinataire`.

## Contact

Pour toute question sur ce projet, répondre ou ouvrir une issue dans le dépôt contenant ces fichiers.

---

README généré automatiquement à partir de l'état actuel du projet.
