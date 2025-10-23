<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API pour récupérer les villes en fonction du code postal depuis la base de données
if (!isset($_GET['code_postal']) || strlen($_GET['code_postal']) != 5) {
    echo json_encode(['error' => 'Code postal invalide']);
    exit;
}

$code_postal = $_GET['code_postal'];

// Inclure la configuration de la base de données
require_once 'config.php';

try {
    // Requête pour récupérer les villes correspondant au code postal
    $stmt = $pdo->prepare("SELECT nom_ville FROM villes WHERE code_postal = ? ORDER BY nom_ville");
    $stmt->execute([$code_postal]);
    $villes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($villes)) {
        echo json_encode([
            'success' => true,
            'code_postal' => $code_postal,
            'villes' => $villes
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Aucune ville trouvée pour ce code postal'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Erreur de base de données',
        'message' => $e->getMessage()
    ]);
}
?>
