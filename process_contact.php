<?php
session_start();

// Désactiver l'affichage des erreurs pour éviter de casser le JSON
error_reporting(0);
ini_set('display_errors', 0);

// Nettoyer le buffer de sortie
if (ob_get_level()) {
    ob_clean();
}

header('Content-Type: application/json');
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Vous devez être connecté pour envoyer un message.';
    $response['redirect'] = 'connexion.php';
    echo json_encode($response);
    exit;
}

// Fonction pour sauvegarder le message en base de données
function saveMessageToDatabase($pdo, $name, $email, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $message]);
        return true;
    } catch (PDOException $e) {
        throw new Exception("Erreur base de données: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($name && $email && $message) {
        // Sauvegarder le message en base de données
        try {
            saveMessageToDatabase($pdo, $name, $email, $message);
            $response['success'] = true;
            $response['message'] = 'Message envoyé avec succès! L\'administrateur le lira bientôt.';
        } catch (Exception $e) {
            $response['message'] = "Erreur lors de l'envoi: " . $e->getMessage();
        }
    } else {
        $response['message'] = "Veuillez remplir tous les champs correctement.";
    }
} else {
    $response['message'] = "Méthode non autorisée.";
}

// S'assurer que la réponse est valide
try {
    $json = json_encode($response);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $json;
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur de formatage JSON']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}