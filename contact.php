<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .contact-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .contact-container h1 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 28px;
        }
        .contact-container form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .contact-container label {
            font-weight: 500;
            margin-bottom: 6px;
        }
        .contact-container input,
        .contact-container textarea {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .contact-container textarea {
            min-height: 80px;
            resize: vertical;
        }
        .contact-container button {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .contact-container button:hover {
            background: #0056b3;
        }
        .contact-container .links {
            text-align: center;
            margin-top: 18px;
        }
        .contact-container .links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .contact-container .links a:hover {
            text-decoration: underline;
        }
    </style>
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
