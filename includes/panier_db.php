<?php

function sauvegarderPanier($pdo, $user_id, $panier) {
    // Supprimer l'ancien panier
    $stmt = $pdo->prepare("DELETE FROM paniers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Insérer le nouveau panier
    if (!empty($panier)) {
        $stmt = $pdo->prepare("INSERT INTO paniers (user_id, produit_nom, quantite) VALUES (?, ?, ?)");
        foreach ($panier as $produit => $quantite) {
            $stmt->execute([$user_id, $produit, $quantite]);
        }
    }
}

function chargerPanier($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT produit_nom, quantite FROM paniers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $panier = [];
    
    while ($row = $stmt->fetch()) {
        $panier[$row['produit_nom']] = $row['quantite'];
    }
    
    return $panier;
}

