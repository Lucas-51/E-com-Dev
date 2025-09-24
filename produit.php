<?php
// produit.php
session_start();
include './includes/card.php';

$produits = [
    ["nom" => "airpods", "prix" => 199,  "description" => "AirPods Apple sans fil",         "stock" => 10, "categorie" => "airpods"],
    ["nom" => "iphone",  "prix" => 999,  "description" => "iPhone dernière génération",      "stock" => 5,  "categorie" => "iphone"],
    ["nom" => "Macbook", "prix" => 1499, "description" => "Macbook Pro 16 pouces",           "stock" => 2,  "categorie" => "macbook"],
    ["nom" => "ipad",    "prix" => 599,  "description" => "iPad 10,9 pouces dernière génération", "stock" => 7, "categorie" => "ipad"],
];
?>
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
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            padding: 16px;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            margin: 16px;
            max-width: 300px;
            background: #fff;
        }
        .card-img {
            width: 100%;
            height: auto;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px;
        }
        .card-title {
            font-size: 1.5em;
            margin: 0 0 8px;
            text-align: center;
        }
        .card-desc {
            font-size: 1em;
            margin: 0 0 16px;
            text-align: center;
            display: block;
        }
    </style>
</head>
<body>
<header>
    <div style="display: flex; flex-direction: column; align-items: center; position: relative;">
        <h1 style="text-align: center; width: 100%; margin: 0;">Nos produits</h1>
    </div>
</header>
<main>
    <div class="card-container">
        <?php foreach ($produits as $p): ?>
            <div style="display: flex; flex-direction: column; align-items: center;">
                <?php echo createCard($p["nom"], $p["prix"], $p["description"], $p["stock"]); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="text-align:center; margin-top:32px;">
        <a href="index.php" style="color:#007bff; text-decoration:none; font-weight:500; font-size:1.1em; background:#f5f5f5; border-radius:8px; padding:10px 22px; transition:background 0.2s; border:1px solid #e0e0e0;">Retour à l'accueil</a>
    </div>
</main>
</body>
</html>
