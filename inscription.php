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
                $message = "Inscription réussie. <a href='connexion.php'>Connectez-vous</a>";
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
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .inscription-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .inscription-container h1 {
            text-align: center;
            font-size: 2.2em;
            margin-bottom: 28px;
        }
        .inscription-container form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .inscription-container label {
            font-weight: 500;
            margin-bottom: 6px;
        }
        .inscription-container input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .inscription-container button {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .inscription-container button:hover {
            background: #0056b3;
        }
        .inscription-container .links {
            text-align: center;
            margin-top: 18px;
        }
        .inscription-container .links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            margin: 0 8px;
        }
        .inscription-container .links a:hover {
            text-decoration: underline;
        }
        .inscription-container .message {
            text-align: center;
            margin-top: 16px;
            color: #d32f2f;
        }
    </style>
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
        <div class="links">
            <a href="connexion.php">Déjà inscrit ?</a>
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
