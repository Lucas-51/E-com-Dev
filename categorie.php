<?php
session_start();
include './includes/card.php';

// --- Définition des catégories ---
$categories = [
    'iphone'  => 'iPhone',
    'macbook' => 'Macbook',
    'airpods' => 'AirPods',
];

// --- Définition des produits (à remplacer par BDD si besoin) ---
$produits = [
    ["nom"=>"airpods","prix"=>199,"description"=>"AirPods Apple sans fil.","stock"=>10,"categorie"=>"airpods"],
    ["nom"=>"iphone","prix"=>999,"description"=>"iPhone dernière génération.","stock"=>5,"categorie"=>"iphone"],
    ["nom"=>"Macbook","prix"=>1499,"description"=>"Macbook Pro 16 pouces.","stock"=>2,"categorie"=>"macbook"],
];

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
    <style>
        /* Petits styles pour les catégories */
        .cats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            max-width: 900px;
            margin: 24px auto;
            padding: 0 16px;
        }
        .cat-card {
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            text-align: center;
            padding: 18px 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .cat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }
        .cat-card a {
            text-decoration: none;
            color: #222;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .breadcrumb {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 16px;
            color: #666;
            font-size: 0.9rem;
        }
        .breadcrumb a { color: #007bff; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>Catégories de produits</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="categorie.php">Catégories</a>
            <a href="produit.php">Produit</a>
            <a href="contact.php">Contact</a>
            <a href="panier.php">Panier (<?php echo array_sum($_SESSION['panier']); ?>)</a>
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
                            <form method="post">
                                <?php echo createCard($p["nom"], $p["prix"], $p["description"], $p["stock"]); ?>
                                <input type="hidden" name="nom" value="<?php echo htmlspecialchars($p["nom"]); ?>">
                                <button type="submit" name="ajouter" class="card-btn">Ajouter au panier</button>
                            </form>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>