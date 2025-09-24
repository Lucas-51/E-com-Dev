<?php
session_start();
require_once 'config.php';
require_once 'includes/panier_db.php';

// Sauvegarder le panier avant la déconnexion
if (isset($_SESSION['user_id']) && !empty($_SESSION['panier'])) {
    sauvegarderPanier($pdo, $_SESSION['user_id'], $_SESSION['panier']);
}

// Détruire la session
session_unset();
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit;
