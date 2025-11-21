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
            session_start();
            require_once 'config.php';
            
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['user_id'])) {
                echo '<div style="color:#dc3545; background:#f8d7da; border:2px solid #dc3545; padding:18px; border-radius:12px; margin:24px auto; max-width:600px; text-align:center; font-size:1.1em;">
                        <i class="fas fa-exclamation-triangle"></i> Vous devez être connecté pour envoyer un message.
                        <br><a href="connexion.php" style="color:#007bff; text-decoration:underline; margin-top:10px; display:inline-block;">Se connecter</a>
                      </div>';
            } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nom = trim($_POST['nom'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $message = trim($_POST['message'] ?? '');
                
                if ($nom && $email && $message) {
                    // Sauvegarder le message en base de données
                    try {
                        $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, message) VALUES (?, ?, ?)");
                        $stmt->execute([$nom, $email, $message]);
                        echo '<div style="color:#28a745; background:#eafaf1; border:2px solid #28a745; padding:18px; border-radius:12px; margin:24px auto; max-width:600px; text-align:center; font-size:1.1em;">
                                <i class="fas fa-check-circle"></i> Votre message a été envoyé avec succès ! L\'administrateur le lira bientôt.
                              </div>';
                    } catch (Exception $e) {
                        echo '<div style="color:#dc3545; background:#f8d7da; border:2px solid #dc3545; padding:18px; border-radius:12px; margin:24px auto; max-width:600px; text-align:center; font-size:1.1em;">
                                <i class="fas fa-exclamation-triangle"></i> Erreur lors de l\'envoi: ' . $e->getMessage() . '
                              </div>';
                    }
                } else {
                    echo '<div style="color:#dc3545; background:#f8d7da; border:2px solid #dc3545; padding:18px; border-radius:12px; margin:24px auto; max-width:600-px; text-align:center; font-size:1.1em;">
                            <i class="fas fa-exclamation-triangle"></i> Veuillez remplir tous les champs correctement.
                          </div>';
                }
            }
            ?>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; margin: 20px 0;">
                    <h3 style="color: #495057;">Connexion requise</h3>
                    <p style="color: #6c757d;">Vous devez être connecté pour envoyer un message.</p>
                    <a href="connexion.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                </div>
            <?php else: ?>
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
            <?php endif; ?>
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
