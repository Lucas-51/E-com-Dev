<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produit</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .produit-container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 28px rgba(0,0,0,0.08);
            padding: 40px 32px 32px 32px;
        }
        .produit-container h1 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 24px;
            color: #222;
        }
        .produit-container p {
            text-align: center;
            font-size: 1.2em;
            color: #222;
        }
        .produit-container .links {
            text-align: center;
            margin-top: 24px;
        }
        .produit-container .links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .produit-container .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="produit-container">
        <h1>Page Produit</h1>
        <p>Détails du produit sélectionné.</p>
        <div class="links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
