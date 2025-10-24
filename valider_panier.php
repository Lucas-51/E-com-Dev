<?php
session_start();

date_default_timezone_set('Europe/Paris');

// Redirige si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=valider_panier.php');
    exit;
}

require_once 'config.php';

// Charger le panier
$panier = $_SESSION['panier'] ?? [];
try {
    $stmt = $pdo->query("SELECT * FROM produits");
    $produits = $stmt->fetchAll();
} catch(PDOException $e) {
    $produits = [];
}

// Contrôle du stock
$messageStock = '';
foreach ($panier as $nom => $qte) {
    foreach ($produits as $p) {
        if ($p['nom'] === $nom && $qte > $p['stock']) {
            $messageStock .= "Pas assez de stock pour le produit '$nom' (stock disponible : {$p['stock']}).<br>";
        }
    }
}
if (!empty($messageStock)) {
    echo '<div style="color:#b71c1c; background:#fff4f4; border:2px solid #b71c1c; padding:32px 24px; border-radius:18px; margin:48px auto; max-width:700px; text-align:center; font-size:1.35em; box-shadow:0 2px 16px rgba(183,28,28,0.08);">
        <span style="display:block; margin-bottom:18px; font-weight:600; letter-spacing:0.5px;">' . $messageStock . '</span>
        <a href="panier.php" style="display:inline-block; background:#b71c1c; color:#fff; font-weight:600; text-decoration:none; padding:12px 32px; border-radius:10px; font-size:1.1em; box-shadow:0 2px 8px rgba(183,28,28,0.10); transition:background 0.2s;">Retour au panier</a>
    </div>';
    exit;
}

// Si le formulaire n'est pas soumis, afficher le formulaire
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Validation commande</title><link rel="stylesheet" href="style.css">
    <style>
        .ville-suggestions {
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            position: absolute;
            width: 100%;
            z-index: 1000;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .ville-suggestion {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        .ville-suggestion:hover {
            background-color: #f0f0f0;
        }
        .ville-suggestion:last-child {
            border-bottom: none;
        }
        .field-container {
            position: relative;
        }
    </style>
    </head><body>';
    echo '<div class="panier-container" style="max-width:600px;">';
    echo '<h2>Informations de livraison</h2>';
    echo '<form method="post" style="display:flex;flex-direction:column;gap:18px;">';
    echo '<input type="text" name="nom" placeholder="Nom" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="prenom" placeholder="Prénom" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="email" name="email" placeholder="Adresse mail" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="adresse" placeholder="Adresse d\'envoi" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" id="code_postal" name="code_postal" placeholder="Code postal" pattern="[0-9]{5}" maxlength="5" required style="padding:10px;font-size:1.1em;">';
    echo '<div class="field-container" style="margin-bottom:10px;">';
    echo '<select id="ville" name="ville" required style="padding:10px;font-size:1.1em;width:100%;box-sizing:border-box;border-radius:4px;border:1px solid #ddd;" disabled>';
    echo '<option value="">Sélectionnez une ville</option>';
    echo '</select>';
    echo '</div>';
    echo '<input type="tel" name="tel" placeholder="Numéro de téléphone" required style="padding:10px;font-size:1.1em;">';
    echo '<button type="submit" style="background:#28a745;color:#fff;border:none;padding:12px 32px;border-radius:8px;font-size:1.2em;cursor:pointer;">Enregistrer</button>';
    echo '</form>';
    echo '<script>
        const codePostalInput = document.getElementById("code_postal");
        const villeSelect = document.getElementById("ville");
        let timeoutId = null;

        async function chargerVilles(codePostal) {
            try {
                if (codePostal.length !== 5) {
                    villeSelect.innerHTML = "<option value=\'\'>Sélectionnez une ville</option>";
                    villeSelect.disabled = true;
                    return;
                }

                villeSelect.innerHTML = "<option value=\'\'>Chargement...</option>";
                villeSelect.disabled = true;

                const response = await fetch(`api_villes.php?code_postal=${codePostal}`);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || "Erreur lors de la requête");
                }

                villeSelect.innerHTML = "<option value=\'\'>Sélectionnez une ville</option>";

                if (data.success && Array.isArray(data.villes) && data.villes.length > 0) {
                    data.villes.forEach(ville => {
                        const option = document.createElement("option");
                        option.value = ville;
                        option.textContent = ville;
                        villeSelect.appendChild(option);
                    });
                    villeSelect.disabled = false;

                    const previousValue = "' . htmlspecialchars($ville) . '";
                    if (previousValue && data.villes.includes(previousValue)) {
                        villeSelect.value = previousValue;
                    }
                } else {
                    villeSelect.innerHTML = "<option value=\'\'>Aucune ville trouvée</option>";
                    villeSelect.disabled = true;
                }
            } catch (error) {
                console.error("Erreur:", error);
                villeSelect.innerHTML = "<option value=\'\'>Erreur de chargement</option>";
                villeSelect.disabled = true;
            }
        }

        // Utiliser un debounce pour éviter trop d\'appels API
        function debounce(codePostal) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => chargerVilles(codePostal), 300);
        }

        codePostalInput.addEventListener("input", (e) => debounce(e.target.value));

        // Charger les villes au chargement si un code postal est déjà présent
        if (codePostalInput.value.length === 5) {
            chargerVilles(codePostalInput.value);
        }
    </script>';
    echo '</form>';
    echo '<script>
        const codePostalInput = document.getElementById("code_postal");
        const villeSelect = document.getElementById("ville");

        function chargerVilles(codePostal) {
            if (codePostal.length === 5) {
                // Réinitialiser et désactiver le select pendant le chargement
                villeSelect.innerHTML = "<option value=\'\'>Chargement...</option>";
                villeSelect.disabled = true;

                fetch(`api_villes.php?code_postal=${codePostal}`)
                    .then(response => response.json())
                    .then(data => {
                        // Réinitialiser le select
                        villeSelect.innerHTML = "<option value=\'\'>Sélectionnez une ville</option>";
                        
                        if (data.success && data.villes && data.villes.length > 0) {
                            // Ajouter les options
                            data.villes.forEach(ville => {
                                const option = document.createElement("option");
                                option.value = ville;
                                option.textContent = ville;
                                villeSelect.appendChild(option);
                            });
                            villeSelect.disabled = false;
                        } else {
                            villeSelect.innerHTML = "<option value=\'\'>Aucune ville trouvée</option>";
                            villeSelect.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error("Erreur:", error);
                        villeSelect.innerHTML = "<option value=\'\'>Erreur de chargement</option>";
                        villeSelect.disabled = true;
                    });
            } else {
                // Réinitialiser si le code postal n\'est pas complet
                villeSelect.innerHTML = "<option value=\'\'>Sélectionnez une ville</option>";
                villeSelect.disabled = true;
            }
        }

        // Écouter les changements du code postal
        codePostalInput.addEventListener("input", function() {
            chargerVilles(this.value);
        });

        // Charger les villes au chargement si un code postal est déjà présent
        if (codePostalInput.value.length === 5) {
            chargerVilles(codePostalInput.value);
        }
    </script>';
    echo '</div></body></html>';
    exit;
}

// Vérification des champs
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$adresse = trim($_POST['adresse'] ?? '');
$ville = trim($_POST['ville'] ?? '');
$tel = trim($_POST['tel'] ?? '');
 $code_postal = trim($_POST['code_postal'] ?? '');
if (!$nom || !$prenom || !$email || !$adresse || !$ville || !$code_postal || !$tel) {
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Validation commande</title><link rel="stylesheet" href="style.css">
    <style>
        .ville-suggestions {
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            position: absolute;
            width: 100%;
            z-index: 1000;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .ville-suggestion {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        .ville-suggestion:hover {
            background-color: #f0f0f0;
        }
        .ville-suggestion:last-child {
            border-bottom: none;
        }
        .field-container {
            position: relative;
        }
    </style>
    </head><body>';
    echo '<div class="panier-container" style="max-width:600px;">';
    echo '<h2>Informations de livraison</h2>';
    echo '<form method="post" style="display:flex;flex-direction:column;gap:18px;">';
    echo '<input type="text" name="nom" placeholder="Nom" value="' . htmlspecialchars($nom) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="prenom" placeholder="Prénom" value="' . htmlspecialchars($prenom) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="email" name="email" placeholder="Adresse mail" value="' . htmlspecialchars($email) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" name="adresse" placeholder="Adresse d\'envoi" value="' . htmlspecialchars($adresse) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<input type="text" id="code_postal" name="code_postal" placeholder="Code postal" pattern="[0-9]{5}" maxlength="5" value="' . htmlspecialchars($code_postal) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<div class="field-container">';
    echo '<select id="ville" name="ville" required style="padding:10px;font-size:1.1em;width:100%;box-sizing:border-box;" disabled>';
    echo '<option value="">Sélectionnez une ville</option>';
    echo '</select>';
    echo '</div>';
    echo '<input type="tel" name="tel" placeholder="Numéro de téléphone" value="' . htmlspecialchars($tel) . '" required style="padding:10px;font-size:1.1em;">';
    echo '<div style="color:#222; background:#fff4f4; border:2px solid #b71c1c; padding:18px; border-radius:12px; margin:12px 0; text-align:center; font-size:1.1em;">Veuillez remplir tous les champs.</div>';
    echo '<button type="submit" style="background:#28a745;color:#fff;border:none;padding:12px 32px;border-radius:8px;font-size:1.2em;cursor:pointer;">Enregistrer</button>';
    echo '</form>';
    echo '<script>
        const codePostalInput = document.getElementById("code_postal");
        const villeSelect = document.getElementById("ville");

        function chargerVilles(codePostal) {
            if (codePostal.length === 5) {
                // Réinitialiser et désactiver le select pendant le chargement
                villeSelect.innerHTML = "<option value=\'\'>Chargement...</option>";
                villeSelect.disabled = true;

                fetch(`api_villes.php?code_postal=${codePostal}`)
                    .then(response => {
                        console.log("Statut de la réponse:", response.status);
                        return response.text();
                    })
                    .then(text => {
                        console.log("Réponse brute:", text);
                        const data = JSON.parse(text);
                        console.log("Données analysées:", data);
                        
                        // Réinitialiser le select
                        villeSelect.innerHTML = "<option value=\'\'>Sélectionnez une ville</option>";
                        
                        if (data.success && data.villes && data.villes.length > 0) {
                            // Ajouter les options
                            data.villes.forEach(ville => {
                                const option = document.createElement("option");
                                option.value = ville;
                                option.textContent = ville;
                                villeSelect.appendChild(option);
                            });
                            villeSelect.disabled = false;
                            
                            // Si une ville était précédemment sélectionnée, la resélectionner
                            const previousValue = "' . htmlspecialchars($ville) . '";
                            if (previousValue && data.villes.includes(previousValue)) {
                                villeSelect.value = previousValue;
                            }
                        } else {
                            villeSelect.innerHTML = "<option value=\'\'>Aucune ville trouvée</option>";
                            villeSelect.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error("Erreur détaillée:", error);
                        villeSelect.innerHTML = "<option value=\'\'>Erreur de chargement</option>";
                        villeSelect.disabled = true;
                    });
            } else {
                // Réinitialiser si le code postal n\'est pas complet
                villeSelect.innerHTML = "<option value=\'\'>Sélectionnez une ville</option>";
                villeSelect.disabled = true;
            }
        }

        // Écouter les changements du code postal
        codePostalInput.addEventListener("input", function() {
            chargerVilles(this.value);
        });

        // Charger les villes au chargement si un code postal est déjà présent
        if (codePostalInput.value.length === 5) {
            chargerVilles(codePostalInput.value);
        }
    </script>';
    echo '</div></body></html>';
    exit;
}

// Générer l'achat
$achat = [];
$total = 0;
foreach ($panier as $nomProd => $qte) {
    foreach ($produits as $p) {
        if ($p['nom'] === $nomProd) {
            $achat[] = [
                'nom' => $p['nom'],
                'prix' => $p['prix'],
                'qte' => $qte
            ];
            $total += $p['prix'] * $qte;
            // Ne pas décrémenter le stock ici : on le fera dans la transaction
        }
    }
}

// Insérer la commande en base (transactionnelle)
try {
    $pdo->beginTransaction();

    // Verrouiller et décrémenter le stock pour chaque produit
    $stockStmt = $pdo->prepare("SELECT stock FROM produits WHERE nom = ? FOR UPDATE");
    $updateStock = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE nom = ? AND stock >= ?");
    foreach ($achat as $item) {
        $stockStmt->execute([$item['nom']]);
        $row = $stockStmt->fetch();
        if (!$row || $row['stock'] < $item['qte']) {
            throw new Exception("Pas assez de stock pour le produit '" . $item['nom'] . "'.");
        }
        $updateStock->execute([$item['qte'], $item['nom'], $item['qte']]);
    }

    // Insérer la commande principale
    $stmtOrder = $pdo->prepare("INSERT INTO commandes (user_id, date_commande, nom, prenom, email, adresse, code_postal, ville, tel, total) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtOrder->execute([$_SESSION['user_id'], $nom, $prenom, $email, $adresse, $code_postal, $ville, $tel, $total]);
    $commande_id = $pdo->lastInsertId();

    // Insérer les items de la commande
    $stmtItem = $pdo->prepare("INSERT INTO commande_items (commande_id, produit_nom, prix, quantite) VALUES (?, ?, ?, ?)");
    foreach ($achat as $item) {
        $stmtItem->execute([$commande_id, $item['nom'], $item['prix'], $item['qte']]);
    }

    $pdo->commit();
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur commande</title><link rel="stylesheet" href="style.css"></head><body>';
    echo '<div class="panier-container" style="max-width:700px;">';
    echo '<h2>Erreur lors de la validation de la commande</h2>';
    echo '<div style="color:#b71c1c; background:#fff4f4; border:2px solid #b71c1c; padding:18px; border-radius:12px;">' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '<div class="links" style="margin-top:24px;"><a href="panier.php">Retour au panier</a></div>';
    echo '</div></body></html>';
    exit;
}

// Vider le panier
$_SESSION['panier'] = [];

// Affichage récapitulatif
echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Récapitulatif commande</title><link rel="stylesheet" href="style.css"></head><body>';
echo '<div class="panier-container" style="max-width:700px;">';
echo '<h2>Merci pour votre commande !</h2>';
echo '<h3>Informations client</h3>';
echo '<ul style="font-size:1.15em;line-height:2;">';
echo '<li><strong>Nom :</strong> ' . htmlspecialchars($nom) . '</li>';
echo '<li><strong>Prénom :</strong> ' . htmlspecialchars($prenom) . '</li>';
echo '<li><strong>Email :</strong> ' . htmlspecialchars($email) . '</li>';
echo '<li><strong>Adresse d\'envoi :</strong> ' . htmlspecialchars($adresse) . '</li>';
echo '<li><strong>Code postal :</strong> ' . htmlspecialchars($code_postal) . '</li>';
echo '<li><strong>Ville :</strong> ' . htmlspecialchars($ville) . '</li>';
echo '<li><strong>Téléphone :</strong> ' . htmlspecialchars($tel) . '</li>';
echo '</ul>';
echo '<h3>Votre commande</h3>';
echo '<ul style="font-size:1.15em;line-height:2;">';
foreach ($achat as $prod) {
    echo '<li>' . htmlspecialchars($prod['nom']) . ' x ' . $prod['qte'] . ' — ' . ($prod['prix'] * $prod['qte']) . '€</li>';
}
echo '</ul>';
echo '<div style="font-size:1.3em;font-weight:600;margin-top:18px;">Total : ' . $total . '€</div>';
echo '<div class="links" style="margin-top:32px;text-align:center;"><a href="index.php" style="display:inline-block;background:#007bff;color:#fff;text-decoration:none;padding:14px 32px;border-radius:12px;font-weight:600;font-size:1.1em;transition:background-color 0.3s ease;box-shadow:0 4px 12px rgba(0,123,255,0.2);">← Retour à l\'accueil</a></div>';
echo '</div>';
?>
</body>
</html>
