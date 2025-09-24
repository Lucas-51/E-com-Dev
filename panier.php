<?php
session_start();
require_once 'config.php';

// --- Affichage du panier ---
try {
    $stmt = $pdo->query("SELECT * FROM produits");
    $produits = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $produits = [];
}

// Affichage du panier
$panier = $_SESSION['panier'] ?? [];
$produitsPanier = [];
foreach ($panier as $nom => $qte) {
    foreach ($produits as $p) {
        if ($p['nom'] === $nom) {
            $produitsPanier[] = [
                'nom' => $p['nom'],
                'prix' => $p['prix'],
                'description' => $p['description'],
                'qte' => $qte
            ];
        }
    }
}

// --- Gestion modification du panier ---
$messageStock = '';
if (isset($_POST['update_qte'])) {
    $nom = $_POST['nom'] ?? '';
    $qte = max(1, (int)($_POST['qte'] ?? 1));
    // Chercher le stock du produit
    $stock = null;
    foreach ($produits as $p) {
        if ($p['nom'] === $nom) {
            $stock = $p['stock'];
            break;
        }
    }
    if ($nom !== '' && isset($_SESSION['panier'][$nom])) {
        if ($stock !== null && $qte > $stock) {
            $messageStock = "Pas assez de stock pour le produit '$nom' (stock disponible : $stock).";
        } else {
            $_SESSION['panier'][$nom] = $qte;
            header('Location: panier.php');
            exit;
        }
    }
}
// --- Suppression d'un produit du panier ---
if (isset($_POST['delete_prod'])) {
    $nom = $_POST['nom'] ?? '';
    if ($nom !== '' && isset($_SESSION['panier'][$nom])) {
        unset($_SESSION['panier'][$nom]);
    }
    header('Location: panier.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="panier-container">
        <h2>Votre panier</h2>
        <?php if (!empty($messageStock)): ?>
            <div style="color:#dc3545; background:#ffeaea; border:1px solid #dc3545; padding:10px; border-radius:6px; margin-bottom:18px; text-align:center;">
                <?php echo htmlspecialchars($messageStock); ?>
            </div>
        <?php endif; ?>
        <?php if (empty($produitsPanier)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <form method="post">
            <table class="panier-table">
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                <?php $total = 0; ?>
                <?php foreach ($produitsPanier as $prod): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prod['nom']); ?></td>
                        <td><?php echo htmlspecialchars($prod['prix']); ?>€</td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="number" name="qte" value="<?php echo htmlspecialchars($prod['qte']); ?>" min="1" style="width:60px; text-align:center;">
                                <input type="hidden" name="nom" value="<?php echo htmlspecialchars($prod['nom']); ?>">
                        </td>
                        <td><?php echo $prod['prix'] * $prod['qte']; ?>€</td>
                        <td style="white-space:nowrap;">
                                <button type="submit" name="update_qte" style="background:#007bff;color:#fff;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;">Mettre à jour</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="nom" value="<?php echo htmlspecialchars($prod['nom']); ?>">
                                <button type="submit" name="delete_prod" style="background:#dc3545;color:#fff;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;margin-left:5px;">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total += $prod['prix'] * $prod['qte']; ?>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3">Total</th>
                    <th colspan="2"><?php echo $total; ?>€</th>
                </tr>
            </table>
            </form>
            <form method="post" action="<?php echo isset($_SESSION['user_id']) ? 'valider_panier.php' : 'connexion.php?redirect=valider_panier.php'; ?>" style="text-align:center; margin-top:24px;">
                <button type="submit" class="card-btn" style="background:#28a745;color:#fff;border:none;padding:12px 32px;border-radius:8px;font-size:1.2em;cursor:pointer;">Valider mon panier</button>
            </form>
        <?php endif; ?>
        <div class="links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
