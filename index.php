<?php
// index.php
session_start();
require_once 'config.php';
include './includes/card.php'; // doit définir function createCard($nom,$prix,$desc,$stock): string

// Récupération des produits depuis la BDD
try {
    $stmt = $pdo->query("SELECT * FROM produits ORDER BY categorie");
    $produits = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $produits = [];
}

// --- Panier ---
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
$panierCount = array_sum($_SESSION['panier']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma boutique en ligne</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
    <div style="display: flex; flex-direction: column; align-items: center; position: relative;">
        <!-- Suppression du logo dans le header -->
        <h1 style="text-align: center; width: 100%; margin: 0;">Ma boutique en ligne</h1>
        <?php if (empty($_SESSION['user_id'])): ?>
            <div id="login-btn-container" style="position: absolute; right: 32px; top: 0;">
                <a href="connexion.php" class="sign-in-btn">Connexion</a>
            </div>
        <?php else: ?>
            <div style="position: absolute; right: 32px; top: 0; display: flex; align-items: center; gap: 10px;">
                <span style="color: #007bff; font-weight: 600;">Bonjour, <?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                <a href="deconnexion.php" class="sign-in-btn" style="background:#dc3545;">Déconnexion</a>
            </div>
        <?php endif; ?>
        <nav class="main-nav" style="width: 100%; margin-top: 20px;">
            <ul style="display: flex; justify-content: center; align-items: center; gap: 40px; margin: 0; padding: 0; list-style: none; width: 100%;">
                <li><a href="index.php">Accueil</a></li>
                <li class="dropdown" id="cat-dropdown">
                    <a href="categorie.php">Catégories</a>
                    <ul class="dropdown-menu">
                        <li><a href="categorie.php?cat=iphone">iPhone</a></li>
                        <li><a href="categorie.php?cat=ipad">iPad</a></li>
                        <li><a href="categorie.php?cat=macbook">Macbook</a></li>
                        <li><a href="categorie.php?cat=airpods">AirPods</a></li>
                    </ul>
                </li>
                <li><a href="produit.php">Produit</a></li>
                <li><a href="panier.php">Panier (<?= $panierCount ?>)</a></li>
            </ul>
        </nav>
    </div>
</header>

<script>
    // Ouvrir/fermer le dropdown au clic (mobile)
    const dd = document.getElementById('cat-dropdown');
    dd && dd.addEventListener('click', (e) => {
        if (e.target.closest('.dropdown > a')) { e.preventDefault(); dd.classList.toggle('open'); }
    });
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#cat-dropdown')) dd && dd.classList.remove('open');
    });

    // Afficher le lien S'inscrire sous le bouton Connexion
    const showLoginBtn = document.getElementById('show-login');
    const registerLink = document.getElementById('register-link');
    if (showLoginBtn && registerLink) {
        showLoginBtn.addEventListener('click', () => {
            registerLink.style.display = registerLink.style.display === 'none' ? 'block' : 'none';
        });
    }
</script>

<main>
    <div style="display: flex; flex-direction: column; align-items: center; margin-top: 60px;">
        <div style="background: #fff; border-radius: 32px; box-shadow: 0 8px 32px rgba(0,0,0,0.10); padding: 32px 48px 24px 48px; display: flex; flex-direction: column; align-items: center;">
            <img src="images/logo e-commerce.jpg" alt="Logo Lukluk & Lucas" style="max-width: 320px; width: 100%; height: auto; display: block;">
        </div>
    </div>
</main>
<?php include 'includes/contact-bubble.php'; ?>
</body>
</html>