<?php
function createCard($nom, $prix, $description, $stock) {
    return '
        <div class="card">
            <img src="' . htmlspecialchars('./images/' . $nom . '.jpg') . '" alt="' . htmlspecialchars($nom) . '" class="card-img">
            <div class="card-body" style="display: flex; flex-direction: column; align-items: center;">
                <h2 class="card-title" style="text-align: center; width: 100%;">' . htmlspecialchars($nom) . '</h2>
                <div style="font-size:1.1em; color:#007bff; font-weight:bold; margin-bottom:8px;">À partir de ' . htmlspecialchars($prix) . '€</div>
                <p class="card-desc" style="text-align: center; width: 100%;">' . htmlspecialchars($description) . '</p>
            </div>
        </div>
    ';
}
?>