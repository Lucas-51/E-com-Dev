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
                            <input type="number" name="qte" value="<?php echo htmlspecialchars($prod['qte']); ?>" min="1" style="width:60px; text-align:center;">
                            <input type="hidden" name="nom" value="<?php echo htmlspecialchars($prod['nom']); ?>">
                        </td>
                        <td><?php echo $prod['prix'] * $prod['qte']; ?>€</td>
                        <td style="white-space:nowrap;">
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
            <form method="post" action="<?php echo isset($_SESSION['user_id']) ? 'valider_panier.php' : 'connexion.php?redirect=panier.php'; ?>" style="text-align:center; margin-top:24px;">
                <button type="submit" class="card-btn" style="background:#28a745;color:#fff;border:none;padding:12px 32px;border-radius:8px;font-size:1.2em;cursor:pointer;">Valider mon panier</button>
            </form>
        <?php endif; ?>
        <div class="links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qteInputs = document.querySelectorAll('input[name="qte"]');
    const stock = { 'iphone': 5 }; // Stock pour chaque produit
    const validerBtn = document.querySelector('button.card-btn');
    // Ajoute un message d'erreur sous le tableau si besoin
    let errorMsg = document.createElement('div');
    errorMsg.id = 'stock-error';
    errorMsg.style = 'color:#dc3545; background:#ffeaea; border:1px solid #dc3545; padding:10px; border-radius:6px; margin:18px 0; text-align:center; display:none;';
    document.querySelector('.panier-container').appendChild(errorMsg);

    function checkStock(input) {
        const tr = input.closest('tr');
        const nom = tr.querySelector('td:first-child').textContent.trim().toLowerCase();
        let quantite = parseInt(input.value) || 1;
        if (stock[nom] !== undefined && quantite > stock[nom]) {
            errorMsg.textContent = `Pas assez de stock pour le produit '${nom}' (stock disponible : ${stock[nom]}).`;
            errorMsg.style.display = 'block';
            if (validerBtn) validerBtn.disabled = true;
            return false;
        } else {
            errorMsg.style.display = 'none';
            if (validerBtn) validerBtn.disabled = false;
            return true;
        }
    }

    qteInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            // Vérifie le stock
            checkStock(input);
            // Récupère la ligne du produit
            const tr = input.closest('tr');
            // Récupère le prix
            const prixTd = tr.querySelector('td:nth-child(2)');
            let prix = parseFloat(prixTd.textContent.replace('€', '').replace(',', '.'));
            let quantite = parseInt(input.value) || 1;
            // Calcule le total de la ligne
            let total = prix * quantite;
            // Met à jour le total de la ligne
            tr.querySelector('td:nth-child(4)').textContent = total + '€';
            // Met à jour le total général
            let totalGeneral = 0;
            document.querySelectorAll('table.panier-table tr').forEach(function(row, idx) {
                if (idx > 0 && row.querySelector('td:nth-child(4)')) {
                    let val = row.querySelector('td:nth-child(4)').textContent.replace('€', '').replace(',', '.');
                    totalGeneral += parseFloat(val) || 0;
                }
            });
            // Affiche le total général
            let totalCell = document.querySelector('table.panier-table tr:last-child th[colspan="2"]');
            if (totalCell) totalCell.textContent = totalGeneral + '€';
        });
        // Vérifie le stock au chargement
        checkStock(input);
    });
});
</script>
</body>
</html>
