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
    </style>
</head>
<body>
    <div class="inscription-container">
        <h1>Inscription client</h1>
        <form>
            <div>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom">
            </div>
            <div>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email">
            </div>
            <div>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password">
            </div>
            <button type="submit">S'inscrire</button>
        </form>
        <div class="links">
            <a href="connexion.php">Déjà inscrit ?</a>
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
