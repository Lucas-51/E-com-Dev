<?php
session_start();
require_once 'config.php';
include './includes/card.php';

// --- Récupération des catégories depuis la BDD ---
try {
    $stmt = $pdo->query("SELECT DISTINCT categorie FROM produits ORDER BY categorie");
    $categories = [];
    while ($row = $stmt->fetch()) {
        $cat = $row['categorie'];
        $categories[$cat] = ucfirst($cat); // Met la première lettre en majuscule
    }
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $categories = [];
}

// Récupération des produits depuis la BDD
try {
    $stmt = $pdo->query("SELECT * FROM produits ORDER BY categorie");
    $produits = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $produits = [];
}

// --- Gestion panier (même logique que index.php) ---
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $_SESSION['panier'][$nom] = ($_SESSION['panier'][$nom] ?? 0) + 1;
}
if (isset($_POST['retirer'])) {
    $nom = $_POST['nom'];
    if (isset($_SESSION['panier'][$nom])) {
        $_SESSION['panier'][$nom]--;
        if ($_SESSION['panier'][$nom] <= 0) unset($_SESSION['panier'][$nom]);
    }
}

// --- Vérifie si on a cliqué sur une catégorie ---
$catKey   = isset($_GET['cat']) ? strtolower($_GET['cat']) : null;
$catLabel = $catKey && isset($categories[$catKey]) ? $categories[$catKey] : null;

// --- Filtre les produits si une catégorie est sélectionnée ---
$produitsFiltres = $catLabel
    ? array_filter($produits, fn($p) => $p['categorie'] === $catKey)
    : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1 style="text-align:center; margin-top: 0;">Catégories de produits</h1>
        <nav class="main-nav" style="display: flex; justify-content: center; gap: 40px; margin: 0 0 16px 0;">
            <a href="index.php" style="font-weight:bold; color:#fff; text-decoration:none;">Accueil</a>
            <div class="dropdown">
                <a href="categorie.php" style="font-weight:bold; color:#fff; text-decoration:none;">Catégories</a>
                <div class="dropdown-menu">
                    <a href="categorie.php?cat=iphone">iPhone</a>
                    <a href="categorie.php?cat=macbook">Macbook</a>
                    <a href="categorie.php?cat=airpods">AirPods</a>
                    <a href="categorie.php?cat=ipad">iPad</a>
                </div>
            </div>
            <a href="produit.php" style="font-weight:bold; color:#fff; text-decoration:none;">Produit</a>
            <a href="contact.php" style="font-weight:bold; color:#fff; text-decoration:none;">Contact</a>
            <a href="panier.php" style="font-weight:bold; color:#fff; text-decoration:none;">Panier (<?php echo array_sum($_SESSION['panier']); ?>)</a>
        </nav>
    </header>

    <main>
        <!-- Fil d'Ariane -->
        <div class="breadcrumb">
            <a href="index.php">Accueil</a> &gt; 
            <a href="categorie.php">Catégories</a>
            <?php if ($catLabel): ?> &gt; <strong><?php echo htmlspecialchars($catLabel); ?></strong><?php endif; ?>
        </div>

        <?php if (!$catLabel): ?>
            <!-- Affiche la liste des catégories si aucune sélection -->
            <section>
                <h2 style="text-align:center;">Choisissez une catégorie</h2>
                <div class="cats-grid">
                    <?php foreach ($categories as $key => $label): ?>
                        <div class="cat-card">
                            <a href="categorie.php?cat=<?php echo $key; ?>">
                                <?php echo $label; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <!-- Affiche les produits filtrés -->
            <section class="produits">
                <h2 style="text-align:center;">Catégorie : <?php echo htmlspecialchars($catLabel); ?></h2>
                <div class="card-container">
                    <?php if (empty($produitsFiltres)): ?>
                        <p style="text-align:center;">Aucun produit dans cette catégorie.</p>
                    <?php else: ?>
                        <?php foreach ($produitsFiltres as $p): ?>
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <form method="post" style="width:100%; display:flex; flex-direction:column; align-items:center;">
                                    <?php echo createCard($p["nom"], $p["prix"], $p["description"], $p["stock"]); ?>
                                    <input type="hidden" name="nom" value="<?php echo htmlspecialchars($p["nom"]); ?>">
                                    <button type="submit" name="ajouter" class="card-btn" style="margin-top:16px; width:80%; max-width:220px;">Ajouter au panier</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>