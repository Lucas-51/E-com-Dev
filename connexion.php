<?php
session_start();
require_once 'config.php';
$message = '';
// Redirige vers l'accueil si déjà connecté
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT id, nom, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            header('Location: index.php');
            exit;
        } else {
            $message = "Email ou mot de passe incorrect.";
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
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .connexion-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .connexion-container h1 {
            text-align: center;
            font-size: 2.2em;
            margin-bottom: 28px;
        }
        .connexion-container form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .connexion-container label {
            font-weight: 500;
            margin-bottom: 6px;
        }
        .connexion-container input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .connexion-container button {
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
        .connexion-container button:hover {
            background: #0056b3;
        }
        .connexion-container .links {
            text-align: center;
            margin-top: 18px;
        }
        .connexion-container .links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            margin: 0 8px;
        }
        .connexion-container .links a:hover {
            text-decoration: underline;
        }
        .connexion-container .message {
            text-align: center;
            margin-top: 16px;
            color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="connexion-container">
        <h1>Connexion client</h1>
        <form method="post" action="">
            <div>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <div class="links">
            <?php if (empty($_SESSION['user_id'])): ?>
                <a href="inscription.php">S'inscrire</a>
            <?php endif; ?>
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
