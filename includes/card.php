<?php
function createCard($nom, $prix, $description, $stock) {
    return '
        <div class="card">
            <img src="' . htmlspecialchars('./images/' . $nom . '.jpg') . '" alt="' . htmlspecialchars($nom) . '" class="card-img">
            <div class="card-body">
                <h2 class="card-title">' . htmlspecialchars($nom) . '</h2>
                <p class="card-desc">' . htmlspecialchars($description) . '</p>
                <div class="card-footer">
                    <span class="card-price">' . htmlspecialchars($prix) . ' €</span>
                    <span class="card-stock">' . ($stock > 0 ? 'En stock' : 'Rupture de stock') . '</span>
                </div>
            </div>
        </div>
    ';
}
?>