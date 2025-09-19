
<?php
session_start();
include './includes/card.php';

$produits = [
    ["nom" => "airpods", "prix" => 199, "description" => "AirPods Apple sans fil.", "stock" => 10],
    ["nom" => "iphone", "prix" => 999, "description" => "iPhone dernière génération.", "stock" => 5],
    ["nom" => "Macbook", "prix" => 1499, "description" => "Macbook Pro 16 pouces.", "stock" => 2],
];

// Gestion du panier
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    if (!isset($_SESSION['panier'][$nom])) {
        $_SESSION['panier'][$nom] = 1;
    } else {
        $_SESSION['panier'][$nom]++;
    }
}
if (isset($_POST['retirer'])) {
    $nom = $_POST['nom'];
    if (isset($_SESSION['panier'][$nom])) {
        $_SESSION['panier'][$nom]--;
        if ($_SESSION['panier'][$nom] <= 0) {
            unset($_SESSION['panier'][$nom]);
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Ma boutique en ligne</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Ma boutique en ligne</h1>
        <nav>
            <a href="#">Accueil</a>
            <a href="#panier">Panier (<?php echo array_sum($_SESSION['panier']); ?>)</a>
        </nav>
    </header>
    <main>
        <section class="produits">
            <h2>Nos produits</h2>
            <div class="card-container">
                <?php foreach ($produits as $p) {
                    echo '<form method="post">';
                    echo createCard($p["nom"], $p["prix"], $p["description"], $p["stock"]);
                    echo '<input type="hidden" name="nom" value="' . htmlspecialchars($p["nom"]) . '" />';
                    echo '<button type="submit" name="ajouter" class="card-btn">Ajouter au panier</button>';
                    echo '</form>';
                } ?>
            </div>
        </section>
        <section id="panier" class="panier">
            <h2>Votre panier</h2>
            <?php if (empty($_SESSION['panier'])): ?>
                <p>Votre panier est vide.</p>
            <?php else: ?>
                <ul>
                <?php foreach ($_SESSION['panier'] as $nom => $quantite): ?>
                    <li>
                        <?php echo htmlspecialchars($nom); ?> x <?php echo $quantite; ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="nom" value="<?php echo htmlspecialchars($nom); ?>" />
                            <button type="submit" name="retirer">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>