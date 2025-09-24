<?php
// produit.php
session_start();
require_once 'config.php';
include './includes/card.php';

// --- Gestion panier (logique identique à index.php) ---
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'] ?? '';
    if ($nom !== '') {
        $_SESSION['panier'][$nom] = ($_SESSION['panier'][$nom] ?? 0) + 1;
    }
}
if (isset($_POST['retirer'])) {
    $nom = $_POST['nom'] ?? '';
    if (isset($_SESSION['panier'][$nom])) {
        $_SESSION['panier'][$nom]--;
        if ($_SESSION['panier'][$nom] <= 0) {
            unset($_SESSION['panier'][$nom]);
        }
    }
}

// Récupération des produits depuis la BDD
try {
    $stmt = $pdo->query("SELECT * FROM produits ORDER BY categorie");
    $produits = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $produits = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div style="display: flex; flex-direction: column; align-items: center; position: relative;">
        <h1 style="text-align: center; width: 100%; margin: 0;">Nos produits</h1>
    </div>
</header>
<main>
    <div class="card-container">
        <?php foreach ($produits as $p): ?>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <?php echo createCard($p["nom"], $p["prix"], $p["description"], $p["stock"]); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center; margin-top:32px;">
        <a href="index.php" style="color:#007bff; text-decoration:none; font-weight:500; font-size:1.1em; background:#f5f5f5; border-radius:8px; padding:10px 22px; transition:background 0.2s; border:1px solid #e0e0e0;">Retour à l'accueil</a>
    </div>
</main>
</body>
</html>
