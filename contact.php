<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="contact-bubble" id="contactBubble">
        <div class="bubble-icon">
            <i class="fas fa-comments"></i>
        </div>
        <div class="contact-form-container">
            <div class="contact-header">
                <h2>Contactez-nous</h2>
                <button class="close-btn" id="closeContact">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nom = trim($_POST['nom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $destinataire = 'lucascharle43@gmail.com'; // <-- À personnaliser
                if ($nom && $email && $message) {
                    $sujet = "Nouveau message de contact Luc & Luk Shop";
                    $contenu = "Nom: $nom\nEmail: $email\nMessage:\n$message";
                    $headers = "From: $email\r\nReply-To: $email\r\n";
                    mail($destinataire, $sujet, $contenu, $headers);
                    echo '<div style="color:#28a745; background:#eafaf1; border:2px solid #28a745; padding:18px; border-radius:12px; margin:24px auto; max-width:600px; text-align:center; font-size:1.1em;">Votre message a bien été envoyé !</div>';
                }
            }
            ?>
            <form method="post">
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message :</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Envoyer
                </button>
            </form>
        </div>
    </div>

    <script>
        const contactBubble = document.getElementById('contactBubble');
        const closeBtn = document.getElementById('closeContact');
        
        contactBubble.addEventListener('click', function(e) {
            if (e.target.closest('.bubble-icon')) {
                this.classList.add('active');
            }
        });

        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            contactBubble.classList.remove('active');
        });

        // Empêcher la fermeture lors du clic sur le formulaire
        document.querySelector('.contact-form-container').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    </script>
</body>
</html>
