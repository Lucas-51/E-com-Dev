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

require_once 'includes/panier_db.php';

// Récupérer le rôle de l'utilisateur si connecté
$userRole = null;
$unreadMessages = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $userRole = $user['role'] ?? null;
    
    // Si admin, compter les messages non lus
    if ($userRole === 'admin') {
        $stmt = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE lu = FALSE");
        $unreadMessages = $stmt->fetchColumn();
    }
}

// --- Panier ---
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = isset($_SESSION['user_id']) ? chargerPanier($pdo, $_SESSION['user_id']) : [];
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
            
            // Sauvegarder le panier si l'utilisateur est connecté
            if (isset($_SESSION['user_id'])) {
                sauvegarderPanier($pdo, $_SESSION['user_id'], $_SESSION['panier']);
            }
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
        
        // Sauvegarder le panier si l'utilisateur est connecté
        if (isset($_SESSION['user_id'])) {
            sauvegarderPanier($pdo, $_SESSION['user_id'], $_SESSION['panier']);
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
<header class="main-header">
    <div class="header-container">
        <h1 class="brand-name">Luc & Luk Shop</h1>
        <nav class="main-nav">
            <ul class="nav-list">
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
                <li><a href="panier.php">Panier (<?= $panierCount ?>)</a></li>
            </ul>
        </nav>
        <div class="user-section">
            <?php if (empty($_SESSION['user_id'])): ?>
                <div class="login-container">
                    <a href="connexion.php" class="sign-in-btn">Connexion</a>
                </div>
            <?php else: ?>
                <div class="user-info">
                    <div class="dropdown" id="user-dropdown">
                        <span class="user-greeting dropdown-trigger">Bonjour, <?= htmlspecialchars($_SESSION['user_nom']) ?> ▼</span>
                        <ul class="dropdown-menu user-menu">
                            <li><a href="mon_compte.php">Mon compte</a></li>
                            <li><a href="historique.php">Historique</a></li>
                            <?php if ($userRole === 'admin'): ?>
                                <li><a href="admin_messages.php" style="color: #007bff; font-weight: bold;">
                                    <i class="fas fa-cog"></i> Administration 
                                    <?php if ($unreadMessages > 0): ?>
                                        <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: 5px;">
                                            <?= $unreadMessages ?>
                                        </span>
                                    <?php endif; ?>
                                </a></li>
                            <?php endif; ?>
                            <li><a href="deconnexion.php" class="logout-link">Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
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

    // Gestion du menu déroulant utilisateur
    const userDropdown = document.getElementById('user-dropdown');
    if (userDropdown) {
        const trigger = userDropdown.querySelector('.dropdown-trigger');
        trigger && trigger.addEventListener('click', (e) => {
            e.preventDefault();
            userDropdown.classList.toggle('open');
        });
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#user-dropdown')) {
                userDropdown.classList.remove('open');
            }
        });
    }

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
                <!-- Bouton Offres Étudiants supprimé -->
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
</main>
<?php include 'includes/contact-bubble.php'; ?>
</body>
</html>