<?php
session_start();
require_once 'config.php';

// Si non connecté, rediriger vers la connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=mon_compte.php');
    exit;
}

// Récupérer les informations de l'utilisateur
try {
    $stmt = $pdo->prepare("SELECT nom, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $user = null;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="compte-container" style="max-width:600px;margin:36px auto;padding:24px;">
    <h1>Mon compte</h1>

    <?php if ($user): ?>
        <div class="user-infos" style="background:#f8f9fa;padding:24px;border-radius:12px;border:1px solid #e6e6e6;">
            <h3 style="margin-top:0;">Mes informations personnelles</h3>
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <strong>Nom :</strong>
                    <span style="margin-left:12px;"><?= htmlspecialchars($user['nom']) ?></span>
                </div>
                <div>
                    <strong>Email :</strong>
                    <span style="margin-left:12px;"><?= htmlspecialchars($user['email']) ?></span>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="message" style="padding:18px;background:#fff4f4;border:2px solid #b71c1c;border-radius:12px;">
            Erreur lors du chargement des informations utilisateur.
        </div>
    <?php endif; ?>

    <div style="margin-top:32px;text-align:center;">
        <a href="index.php" style="display:inline-block;background:#007bff;color:#fff;text-decoration:none;padding:14px 32px;border-radius:12px;font-weight:600;font-size:1.1em;transition:background-color 0.3s ease;box-shadow:0 4px 12px rgba(0,123,255,0.2);">
            ← Retour à l'accueil
        </a>
    </div>
</div>
</body>
</html>
