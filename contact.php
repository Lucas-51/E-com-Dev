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
            <form>
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
