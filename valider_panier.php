<?php
session_start();

// Redirige si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=valider_panier.php');
    exit;
}

// Charger les infos utilisateur depuis la session
$prenom = $_SESSION['user_nom'] ?? '';
$email = '';

require_once 'config.php';
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    if ($row) $email = $row['email'];
}

// Simuler une "base de données" d'historique d'achat en session
if (!isset($_SESSION['historique'])) {
    $_SESSION['historique'] = [];
}

// Simuler des infos utilisateur (à remplacer par un vrai système d'auth plus tard)
if (!isset($_SESSION['prenom'])) $_SESSION['prenom'] = 'PrénomTest';
if (!isset($_SESSION['email'])) $_SESSION['email'] = 'test@email.com';

// Récupérer le panier actuel
$panier = $_SESSION['panier'] ?? [];

// Charger les produits
require_once 'config.php';
try {
    $stmt = $pdo->query("SELECT * FROM produits");
    $produits = $stmt->fetchAll();
} catch(PDOException $e) {
    $produits = [];
}

// Générer l'achat et vider le panier
$achat = [];
$total = 0;
foreach ($panier as $nom => $qte) {
    foreach ($produits as $p) {
        if ($p['nom'] === $nom) {
            $achat[] = [
                'nom' => $p['nom'],
                'prix' => $p['prix'],
                'qte' => $qte
            ];
            $total += $p['prix'] * $qte;
            // Décrémenter le stock en base de données
            $updateStock = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE nom = ? AND stock >= ?");
            $updateStock->execute([$qte, $nom, $qte]);
        }
    }
}
if (!empty($achat)) {
    $_SESSION['historique'][] = [
        'date' => date('d/m/Y H:i'),
        'produits' => $achat,
        'total' => $total
    ];
    $_SESSION['panier'] = [];
}

// Affichage de l'historique
$historique = $_SESSION['historique'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique d'achat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="panier-container">
        <h2>Merci pour votre commande, <?php echo htmlspecialchars($prenom); ?> !</h2>
        <p>Un récapitulatif a été envoyé à : <strong><?php echo htmlspecialchars($email); ?></strong></p>
        <h3>Votre historique d'achat :</h3>
        <?php if (empty($historique)): ?>
            <p>Aucun achat effectué pour le moment.</p>
        <?php else: ?>
            <?php foreach (array_reverse($historique) as $commande): ?>
                <div class="historique-block" style="margin-bottom:24px;">
                    <div style="font-weight:bold;">Commande du <?php echo $commande['date']; ?></div>
                    <ul>
                        <?php foreach ($commande['produits'] as $prod): ?>
                            <li><?php echo htmlspecialchars($prod['nom']); ?> x <?php echo $prod['qte']; ?> — <?php echo $prod['prix'] * $prod['qte']; ?>€</li>
                        <?php endforeach; ?>
                    </ul>
                    <div>Total : <strong><?php echo $commande['total']; ?>€</strong></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
