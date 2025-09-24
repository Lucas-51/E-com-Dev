<?php
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($name && $email && $message) {
        $to = "lucascharle43@gmail.com"; // Remplacez par votre email
        $subject = "Nouveau message de contact de " . $name;
        $headers = "From: " . $email . "\r\n" .
                  "Reply-To: " . $email . "\r\n" .
                  "X-Mailer: PHP/" . phpversion();

        $emailBody = "Nom: " . $name . "\n" .
                    "Email: " . $email . "\n\n" .
                    "Message:\n" . $message;

        if (mail($to, $subject, $emailBody, $headers)) {
            $response['success'] = true;
            $response['message'] = 'Message envoyé avec succès!';
        } else {
            $response['message'] = "Une erreur est survenue lors de l'envoi du message.";
        }
    } else {
        $response['message'] = "Veuillez remplir tous les champs correctement.";
    }
} else {
    $response['message'] = "Méthode non autorisée.";
}

echo json_encode($response);