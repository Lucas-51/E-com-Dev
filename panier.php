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
        <p>Votre panier est vide.</p>
        <div class="links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
