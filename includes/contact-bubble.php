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

    fetch('process_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Message envoyé avec succès!');
            form.reset();
            toggleContactForm();
        } else {
            alert('Une erreur est survenue. Veuillez réessayer.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue. Veuillez réessayer.');
    });
}
</script>