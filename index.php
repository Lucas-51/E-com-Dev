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
        // Trouver le stock actuel du produit
        $stmt = $pdo->prepare("SELECT stock FROM produits WHERE nom = ?");
        $stmt->execute([$nom]);
        $produit = $stmt->fetch();
        
        if ($produit && ($produit['stock'] > ($_SESSION['panier'][$nom] ?? 0))) {
            $_SESSION['panier'][$nom] = ($_SESSION['panier'][$nom] ?? 0) + 1;
        }
    }
}
if (isset($_POST['retirer'])) {
    $nom = $_POST['nom'] ?? '';
    if (isset($_SESSION['panier'][$nom]) && $_SESSION['panier'][$nom] > 0) {
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h2>Découvrez l'Excellence Technologique</h2>
            <p>Les derniers produits Apple à prix compétitifs</p>
            <div class="cta-buttons">
                <a href="categorie.php" class="primary-button">
                    <span>Explorer la Collection</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="offre_etudiants.php" class="secondary-button">
                    <span>Offres Étudiants</span>
                    <i class="fas fa-graduation-cap"></i>
                </a>
            </div>
        </div>
        <div class="hero-image">
            <img src="images/Macbook.jpg" alt="MacBook Pro" class="floating">
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <h3>Produits Populaires</h3>
        <div class="product-grid">
            <?php
            $featured = array_slice($produits, 0, 3);
            foreach ($featured as $produit) {
                echo createCard($produit['nom'], $produit['prix'], $produit['description'], $produit['stock']);
            }
            ?>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="benefit-card">
            <i class="fas fa-truck"></i>
            <h4>Livraison Gratuite</h4>
            <p>Pour toute commande de plus de 1000€</p>
        </div>
        <div class="benefit-card">
            <i class="fas fa-shield-alt"></i>
            <h4>Garantie 2 Ans</h4>
            <p>Sur tous nos produits</p>
        </div>
        <div class="benefit-card">
            <i class="fas fa-sync"></i>
            <h4>Retours Faciles</h4>
            <p>Sous 30 jours</p>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="newsletter-content">
            <h3>Restez Informé</h3>
            <p>Recevez nos dernières offres et nouveautés en avant-première</p>
            <form class="newsletter-form" action="newsletter-signup.php" method="POST">
                <input type="email" name="email" placeholder="Votre adresse email" required>
                <button type="submit" class="primary-button">
                    <span>S'inscrire</span>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </section>
</main>
<?php include 'includes/contact-bubble.php'; ?>
</body>
</html>