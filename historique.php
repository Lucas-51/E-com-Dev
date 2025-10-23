<?php
session_start();
require_once 'config.php';

// Si non connecté, rediriger vers la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=historique.php');
    exit;
}

// Récupérer les commandes de l'utilisateur
try {
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE user_id = ? ORDER BY date_commande DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $commandes = $stmt->fetchAll();
} catch (PDOException $e) {
    $commandes = [];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des commandes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="historique-container" style="max-width:900px;margin:36px auto;">
    <h1>Mon historique de commandes</h1>

    <?php if (empty($commandes)): ?>
        <div class="message" style="padding:18px;background:#fff4f4;border:2px solid #b71c1c;border-radius:12px;">Vous n'avez aucune commande enregistrée.</div>
        <div style="margin-top:32px;text-align:center;">
            <a href="index.php" style="display:inline-block;background:#007bff;color:#fff;text-decoration:none;padding:14px 32px;border-radius:12px;font-weight:600;font-size:1.1em;transition:background-color 0.3s ease;box-shadow:0 4px 12px rgba(0,123,255,0.2);">
                ← Retour à l'accueil
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($commandes as $commande): ?>
            <div class="commande" style="border:1px solid #e6e6e6;padding:18px;border-radius:12px;margin-bottom:18px;background:#fff;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <strong>Commande #<?= htmlspecialchars($commande['id']) ?></strong>
                        <div style="color:#666;font-size:0.95em;">Passée le <?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($commande['date_commande']))) ?></div>
                    </div>
                    <div style="font-weight:600;">Total : <?= number_format($commande['total'], 2, ',', ' ') ?> €</div>
                </div>

                <div style="margin-top:12px;">
                    <strong>Livraison :</strong>
                    <div style="font-size:0.95em;line-height:1.6;">
                        <?= htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']) ?><br>
                        <?= htmlspecialchars($commande['adresse']) ?><br>
                        <?= htmlspecialchars($commande['code_postal']) ?><br>
                        <?= htmlspecialchars($commande['tel']) ?><br>
                        <?= htmlspecialchars($commande['email']) ?><br>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <strong>Produits :</strong>
                    <ul style="margin-top:8px;">
                        <?php
                        $stmtItem = $pdo->prepare("SELECT produit_nom, prix, quantite FROM commande_items WHERE commande_id = ?");
                        $stmtItem->execute([$commande['id']]);
                        $items = $stmtItem->fetchAll();
                        foreach ($items as $it):
                        ?>
                            <li><?= htmlspecialchars($it['produit_nom']) ?> x <?= (int)$it['quantite'] ?> — <?= number_format($it['prix'] * $it['quantite'], 2, ',', ' ') ?> €</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
        <div style="margin-top:32px;text-align:center;">
            <a href="index.php" style="display:inline-block;background:#007bff;color:#fff;text-decoration:none;padding:14px 32px;border-radius:12px;font-weight:600;font-size:1.1em;transition:background-color 0.3s ease;box-shadow:0 4px 12px rgba(0,123,255,0.2);">
                ← Retour à l'accueil
            </a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
