<?php
require_once 'config.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($nom && $email && $password) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$nom, $email, $hash])) {
                $message = "Inscription réussie ! <a href='connexion.php' style='display:inline-block;background:#28a745;color:#fff;text-decoration:none;padding:8px 16px;border-radius:8px;font-weight:600;margin-left:8px;transition:background-color 0.3s ease;'>Connectez-vous</a>";
            } else {
                $message = "Erreur lors de l'inscription.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="inscription-container">
        <h1>Inscription client</h1>
        <form method="post" action="">
            <div>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">S'inscrire</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <div class="links" style="text-align:center;margin-top:24px;">
            <a href="connexion.php" style="display:inline-block;background:#007bff;color:#fff;text-decoration:none;padding:10px 20px;border-radius:8px;font-weight:600;margin:0 8px;transition:background-color 0.3s ease;">Déjà inscrit ?</a>
            <a href="index.php" style="display:inline-block;background:#6c757d;color:#fff;text-decoration:none;padding:10px 20px;border-radius:8px;font-weight:600;margin:0 8px;transition:background-color 0.3s ease;">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
