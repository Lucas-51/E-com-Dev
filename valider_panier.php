<?php
session_start();

date_default_timezone_set('Europe/Paris');

// Redirige si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=valider_panier.php');
    exit;
}

require_once 'config.php';

// Charger le panier
$panier = $_SESSION['panier'] ?? [];
try {
    $stmt = $pdo->query("SELECT * FROM produits");
    $produits = $stmt->fetchAll();
} catch(PDOException $e) {
    $produits = [];
}

// Contrôle du stock
$messageStock = '';
foreach ($panier as $nom => $qte) {
    foreach ($produits as $p) {
        if ($p['nom'] === $nom && $qte > $p['stock']) {
            $messageStock .= "Pas assez de stock pour le produit '$nom' (stock disponible : {$p['stock']}).<br>";
        }
    }
}
if (!empty($messageStock)) {
    echo '<div style="color:#b71c1c; background:#fff4f4; border:2px solid #b71c1c; padding:32px 24px; border-radius:18px; margin:48px auto; max-width:700px; text-align:center; font-size:1.35em; box-shadow:0 2px 16px rgba(183,28,28,0.08);">
        <span style="display:block; margin-bottom:18px; font-weight:600; letter-spacing:0.5px;">' . $messageStock . '</span>
        <a href="panier.php" style="display:inline-block; background:#b71c1c; color:#fff; font-weight:600; text-decoration:none; padding:12px 32px; border-radius:10px; font-size:1.1em; box-shadow:0 2px 8px rgba(183,28,28,0.10); transition:background 0.2s;">Retour au panier</a>
    </div>';
    exit;
}

// Si le formulaire n'est pas soumis, afficher le formulaire
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Validation commande</title><link rel="stylesheet" href="style.css"></head><body>';
    echo '<div class="panier-container" style="max-width:600px;">';
    echo '<h2>Informations de livraison</h2>';
    echo '<form method="post" style="display:flex;flex-direction:column;gap:18px;">';
    echo '<input type="text" name="nom" placeholder="Nom" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="prenom" placeholder="Prénom" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="email" name="email" placeholder="Adresse mail" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="adresse" placeholder="Adresse d\'envoi" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="code_postal" placeholder="Code postal" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="tel" name="tel" placeholder="Numéro de téléphone" required style="padding:10px;font-size:1.1em;">';
    echo '<button type="submit" style="background:#28a745;color:#fff;border:none;padding:12px 32px;border-radius:8px;font-size:1.2em;cursor:pointer;">Enregistrer</button>';
    echo '</form>';
    echo '</div></body></html>';
    exit;
}

// Vérification des champs
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$adresse = trim($_POST['adresse'] ?? '');
$tel = trim($_POST['tel'] ?? '');
 $code_postal = trim($_POST['code_postal'] ?? '');
if (!$nom || !$prenom || !$email || !$adresse || !$code_postal || !$tel) {
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Validation commande</title><link rel="stylesheet" href="style.css"></head><body>';
    echo '<div class="panier-container" style="max-width:600px;">';
    echo '<h2>Informations de livraison</h2>';
    echo '<form method="post" style="display:flex;flex-direction:column;gap:18px;">';
    echo '<input type="text" name="nom" placeholder="Nom" value="' . htmlspecialchars($nom) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="prenom" placeholder="Prénom" value="' . htmlspecialchars($prenom) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="email" name="email" placeholder="Adresse mail" value="' . htmlspecialchars($email) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="adresse" placeholder="Adresse d\'envoi" value="' . htmlspecialchars($adresse) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="code_postal" placeholder="Code postal" value="' . htmlspecialchars($code_postal) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="tel" name="tel" placeholder="Numéro de téléphone" value="' . htmlspecialchars($tel) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<div style="color:#222; background:#fff4f4; border:2px solid #b71c1c; padding:18px; border-radius:12px; margin:12px 0; text-align:center; font-size:1.1em;">Veuillez remplir tous les champs.</div>';
    echo '<button type="submit" style="background:#28a745;color:#fff;border:none;padding:12px 32px;border-radius:8px;font-size:1.2em;cursor:pointer;">Enregistrer</button>';
    echo '</form>';
    echo '</div></body></html>';
    exit;
}

// Générer l'achat
$achat = [];
$total = 0;
foreach ($panier as $nomProd => $qte) {
    foreach ($produits as $p) {
        if ($p['nom'] === $nomProd) {
            $achat[] = [
                'nom' => $p['nom'],
                'prix' => $p['prix'],
                'qte' => $qte
            ];
            $total += $p['prix'] * $qte;
            // Décrémenter le stock
            $updateStock = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE nom = ? AND stock >= ?");
            $updateStock->execute([$qte, $nomProd, $qte]);
        }
    }
}
$_SESSION['historique'][] = [
    'date' => date('d/m/Y H:i:s'),
    'produits' => $achat,
    'total' => $total
];
$_SESSION['panier'] = [];

// Affichage récapitulatif
echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Récapitulatif commande</title><link rel="stylesheet" href="style.css"></head><body>';
echo '<div class="panier-container" style="max-width:700px;">';
echo '<h2>Merci pour votre commande !</h2>';
echo '<h3>Informations client</h3>';
echo '<ul style="font-size:1.15em;line-height:2;">';
echo '<li><strong>Nom :</strong> ' . htmlspecialchars($nom) . '</li>';
echo '<li><strong>Prénom :</strong> ' . htmlspecialchars($prenom) . '</li>';
echo '<li><strong>Email :</strong> ' . htmlspecialchars($email) . '</li>';
echo '<li><strong>Adresse d\'envoi :</strong> ' . htmlspecialchars($adresse) . '</li>';
echo '<li><strong>Code postal :</strong> ' . htmlspecialchars($code_postal) . '</li>';
echo '<li><strong>Téléphone :</strong> ' . htmlspecialchars($tel) . '</li>';
echo '</ul>';
echo '<h3>Votre commande</h3>';
echo '<ul style="font-size:1.15em;line-height:2;">';
foreach ($achat as $prod) {
    echo '<li>' . htmlspecialchars($prod['nom']) . ' x ' . $prod['qte'] . ' — ' . ($prod['prix'] * $prod['qte']) . '€</li>';
}
echo '</ul>';
echo '<div style="font-size:1.3em;font-weight:600;margin-top:18px;">Total : ' . $total . '€</div>';
echo '<div class="links" style="margin-top:32px;"><a href="index.php">Retour à l\'accueil</a></div>';
echo '</div>';
?>
</body>
</html>
