<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="contact-container">
        <h1>Formulaire de contact</h1>
        <form>
            <div>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom">
            </div>
            <div>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email">
            </div>
            <div>
                <label for="message">Message :</label>
                <textarea id="message" name="message"></textarea>
            </div>
            <button type="submit">Envoyer</button>
        </form>
        <div class="links">
            <a href="index.php">Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
