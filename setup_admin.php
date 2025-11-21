<?php
require_once 'config.php';

try {
    // Ajouter le champ role à la table users si il n'existe pas déjà
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user'");
        echo "Champ 'role' ajouté à la table users\n";
    } else {
        echo "Champ 'role' existe déjà\n";
    }
    
    // Créer la table messages_contact
    $sql = "CREATE TABLE IF NOT EXISTS messages_contact (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        message TEXT NOT NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        lu BOOLEAN DEFAULT FALSE,
        INDEX idx_date (date_creation),
        INDEX idx_lu (lu)
    )";
    
    $pdo->exec($sql);
    echo "Table 'messages_contact' créée\n";
    
    // Créer un utilisateur admin par défaut si aucun admin n'existe
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        $adminEmail = 'admin@luklukshop.com';
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $adminNom = 'Administrateur';
        
        $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$adminNom, $adminEmail, $adminPassword]);
        
        echo "Compte administrateur créé:\n";
        echo "Email: $adminEmail\n";
        echo "Mot de passe: admin123\n";
        echo "CHANGEZ CE MOT DE PASSE après la première connexion!\n";
    } else {
        echo "Un compte administrateur existe déjà\n";
    }
    
    echo "\nBase de données mise à jour avec succès!\n";
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
