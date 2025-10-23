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
$notifAjout = false;
if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $qte = isset($_POST['qte']) ? max(1, (int)$_POST['qte']) : 1;
    $_SESSION['panier'][$nom] = ($_SESSION['panier'][$nom] ?? 0) + $qte;
    $notifAjout = true;
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
            <a href="panier.php" style="font-weight:bold; color:#fff; text-decoration:none;">Panier (<?php echo array_sum($_SESSION['panier']); ?>)</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <div class="dropdown" id="user-dropdown-cat" style="position:relative;display:inline-block;">
                    <span class="dropdown-trigger" style="cursor:pointer;color:#fff;font-weight:bold;padding:8px 12px;border-radius:8px;"><?= htmlspecialchars($_SESSION['user_nom']) ?> ▼</span>
                    <ul class="dropdown-menu user-menu" style="position:absolute;right:0;top:calc(100% + 8px);min-width:180px;background:#fff;border:1px solid #e0e0e0;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,0.15);padding:8px;display:none;z-index:1000;list-style:none;margin:0;">
                        <li><a href="mon_compte.php" style="display:block;padding:12px 16px;color:#333;text-decoration:none;border-radius:8px;font-weight:500;">Mon compte</a></li>
                        <li><a href="historique.php" style="display:block;padding:12px 16px;color:#333;text-decoration:none;border-radius:8px;font-weight:500;">Historique</a></li>
                        <li><a href="deconnexion.php" style="display:block;padding:12px 16px;color:#dc3545;text-decoration:none;border-radius:8px;font-weight:500;">Déconnexion</a></li>
                    </ul>
                </div>
            <?php endif; ?>
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
                                    <?php 
                                    $withQuantity = in_array(strtolower($p["nom"]), ["iphone", "macbook", "airpods", "ipad"]);
                                    echo createCard($p["nom"], $p["prix"], $p["description"], $p["stock"], $withQuantity); 
                                    ?>
                                    <input type="hidden" name="nom" value="<?php echo htmlspecialchars($p["nom"]); ?>">
                                    <?php if ($withQuantity): ?>
                                        <input type="hidden" name="qte" value="1" class="hidden-qte">
                                    <?php endif; ?>
                                    <button type="submit" name="ajouter" class="card-btn" style="margin-top:16px; margin-bottom:20px; width:80%; max-width:220px;">Ajouter au panier</button>
                                    
                                    <!-- Ajout des spécifications techniques -->
                                    <div class="specs-container" style="width: 100%; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                                        <h3 style="text-align: center; margin-bottom: 20px; color: #333;">Caractéristiques techniques</h3>
                                        <div style="display: flex; flex-direction: column; gap: 15px;">
                                            <?php
                                            // Définition des spécifications selon le type de produit
                                            $specs = [];
                                            if (stripos($p["nom"], 'iphone') !== false) {
                                                $specs = [
                                                    "Écran" => "6.7\" Super Retina XDR OLED",
                                                    "Processeur" => "Puce A17 Pro",
                                                    "RAM" => "8 Go",
                                                    "Stockage" => "256 Go",
                                                    "Appareil photo" => "48 Mpx principal",
                                                    "Batterie" => "4422 mAh"
                                                ];
                                            } elseif (stripos($p["nom"], 'macbook') !== false) {
                                                $specs = [
                                                    "Écran" => "14.2\" Liquid Retina XDR",
                                                    "Processeur" => "Puce M3 Pro",
                                                    "RAM" => "16 Go",
                                                    "Stockage" => "512 Go SSD",
                                                    "Graphique" => "GPU 14 cœurs",
                                                    "Autonomie" => "Jusqu'à 18h"
                                                ];
                                            } elseif (stripos($p["nom"], 'ipad') !== false) {
                                                $specs = [
                                                    "Écran" => "11\" Liquid Retina",
                                                    "Processeur" => "Puce M2",
                                                    "RAM" => "8 Go",
                                                    "Stockage" => "128 Go",
                                                    "Appareil photo" => "12 Mpx",
                                                    "Connectivité" => "Wi-Fi 6E"
                                                ];
                                            } elseif (stripos($p["nom"], 'airpods') !== false) {
                                                $specs = [
                                                    "Type" => "Écouteurs sans fil",
                                                    "Puce" => "H2",
                                                    "Autonomie" => "6h d'écoute",
                                                    "Charge" => "Boîtier MagSafe",
                                                    "Audio" => "Audio spatial",
                                                    "Résistance" => "IPX4"
                                                ];
                                            }

                                            // Affichage des spécifications
                                            foreach ($specs as $key => $value):
                                            ?>
                                                <div style="display: flex; align-items: center; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px;">
                                                    <span style="font-weight: 500; color: #666; width: 120px;"><?php echo htmlspecialchars($key); ?></span>
                                                    <span style="color: #333;"><?php echo htmlspecialchars($value); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>
<div id="notif-toast" style="display:none;position:fixed;top:32px;right:32px;z-index:9999;background:#28a745;color:#fff;padding:18px 32px;border-radius:8px;box-shadow:0 2px 12px rgba(40,167,69,0.15);font-size:1.15em;font-weight:500;transition:opacity 0.3s;">Produit ajouté au panier !</div>
<script>
// Affiche la notification si le produit a été ajouté (côté serveur)
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($notifAjout): ?>
    const toast = document.getElementById('notif-toast');
    if (toast) {
        toast.style.display = 'block';
        toast.style.opacity = '1';
        setTimeout(function() {
            toast.style.opacity = '0';
            setTimeout(function() { toast.style.display = 'none'; }, 400);
        }, 5000);
    }
    <?php endif; ?>
    
    // Gestion du menu déroulant utilisateur
    const userDropdown = document.getElementById('user-dropdown-cat');
    if (userDropdown) {
        const trigger = userDropdown.querySelector('.dropdown-trigger');
        const menu = userDropdown.querySelector('.user-menu');
        
        trigger && trigger.addEventListener('click', (e) => {
            e.preventDefault();
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#user-dropdown-cat')) {
                menu.style.display = 'none';
            }
        });
        
        // Hover effects
        const menuLinks = menu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('mouseenter', () => {
                link.style.backgroundColor = link.textContent.trim() === 'Déconnexion' ? '#ffeaea' : '#f8f9fa';
                link.style.color = link.textContent.trim() === 'Déconnexion' ? '#dc3545' : '#007bff';
            });
            link.addEventListener('mouseleave', () => {
                link.style.backgroundColor = 'transparent';
                link.style.color = link.textContent.trim() === 'Déconnexion' ? '#dc3545' : '#333';
            });
        });
    }
});
</script>
</body>
</html>