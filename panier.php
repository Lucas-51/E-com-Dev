<?php
session_start();

// --- Affichage du panier ---
$produits = [
    ["nom" => "airpods", "prix" => 199,  "description" => "AirPods Apple sans fil",         "stock" => 10, "categorie" => "airpods"],
    ["nom" => "iphone",  "prix" => 999,  "description" => "iPhone dernière génération",      "stock" => 5,  "categorie" => "iphone"],
    ["nom" => "Macbook", "prix" => 1499, "description" => "Macbook Pro 16 pouces",           "stock" => 2,  "categorie" => "macbook"],
    ["nom" => "ipad",    "prix" => 599,  "description" => "iPad 10,9 pouces dernière génération", "stock" => 7, "categorie" => "ipad"],
];

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
if (isset($_POST['update_qte'])) {
    $nom = $_POST['nom'] ?? '';
    $qte = max(1, (int)($_POST['qte'] ?? 1));
    if ($nom !== '' && isset($_SESSION['panier'][$nom])) {
        $_SESSION['panier'][$nom] = $qte;
    }
    // Rafraîchir pour éviter le repost
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
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .panier-container {
            max-width: 700px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            padding: 48px 32px 40px 32px;
        }
        .panier-container h2 {
            text-align: center;
            font-size: 2.4em;
            margin-bottom: 32px;
            color: #222;
        }
        .panier-container p {
            text-align: center;
            font-size: 1.7em;
            color: #222;
        }
        .panier-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .panier-table th, .panier-table td {
            border: 1px solid #e0e0e0;
            padding: 12px 8px;
            text-align: center;
        }
        .panier-table th {
            background: #f5f5f5;
        }
        .panier-container .links {
            text-align: center;
            margin-top: 24px;
        }
        .panier-container .links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            margin: 0 8px;
        }
        .panier-container .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="panier-container">
        <h2>Votre panier</h2>
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
                        <td>
                            <button type="submit" name="update_qte" style="background:#007bff;color:#fff;border:none;padding:6px 12px;border-radius:4px;cursor:pointer;">Mettre à jour</button>
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
        <?php endif; ?>
        <div class="links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
