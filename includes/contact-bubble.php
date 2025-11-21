<div class="contact-bubble">
    <div class="bubble-icon" onclick="toggleContactForm()">
        <i class="fas fa-comment"></i>
    </div>
    <div class="contact-form-container">
        <div class="contact-header">
            <h2>Contactez-nous</h2>
            <button class="close-btn" onclick="toggleContactForm()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="contact-form" onsubmit="submitContactForm(event)">
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Envoyer
            </button>
        </form>
    </div>
</div>

<script>
function toggleContactForm() {
    const contactBubble = document.querySelector('.contact-bubble');
    contactBubble.classList.toggle('active');
}

function submitContactForm(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('.submit-btn');
    
    // Désactiver le bouton pendant l'envoi
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

    fetch('process_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau: ' + response.status);
        }
        // Vérifier si la réponse est du JSON valide
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Réponse reçue:', text);
                throw new Error('Réponse invalide du serveur');
            }
        });
    })
    .then(data => {
        if (data.success) {
            alert(data.message || 'Message envoyé avec succès!');
            form.reset();
            toggleContactForm();
        } else {
            if (data.redirect) {
                if (confirm(data.message + ' Voulez-vous vous connecter maintenant ?')) {
                    window.location.href = data.redirect;
                }
            } else {
                alert(data.message || 'Une erreur est survenue. Veuillez réessayer.');
            }
        }
    })
    .catch(error => {
        console.error('Erreur détaillée:', error);
        alert('Erreur de connexion: ' + error.message);
    })
    .finally(() => {
        // Réactiver le bouton
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer';
    });
}
</script>