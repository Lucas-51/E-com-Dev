<?php
function createCard($nom, $prix, $description, $stock) {
    $currentStock = isset($_SESSION['panier'][$nom]) ? $stock - $_SESSION['panier'][$nom] : $stock;
    $stockClass = $currentStock <= 5 ? ($currentStock <= 0 ? 'stock-empty' : 'stock-low') : 'stock-available';
    
    return '
        <div class="card">
            <img src="' . htmlspecialchars('./images/' . $nom . '.jpg') . '" alt="' . htmlspecialchars($nom) . '" class="card-img">
            <div class="card-body" style="display: flex; flex-direction: column; align-items: center;">
                <h2 class="card-title" style="text-align: center; width: 100%;">' . htmlspecialchars($nom) . '</h2>
                <div style="font-size:1.1em; color:#007bff; font-weight:bold; margin-bottom:8px;">À partir de ' . htmlspecialchars($prix) . '€</div>
                <p class="card-desc" style="text-align: center; width: 100%;">' . htmlspecialchars($description) . '</p>
                <div class="stock-info ' . $stockClass . '">
                    ' . ($currentStock > 0 ? $currentStock . ' unités disponibles' : 'Rupture de stock') . '
                </div>
                <div class="card-actions">
                    <form method="post" class="quantity-form">
                        <input type="hidden" name="nom" value="' . htmlspecialchars($nom) . '">
                        <button type="submit" name="retirer" class="quantity-btn" ' . ($currentStock >= $stock ? 'disabled' : '') . '>
                            <span>-</span>
                        </button>
                        <span class="quantity">' . (isset($_SESSION['panier'][$nom]) ? $_SESSION['panier'][$nom] : 0) . '</span>
                        <button type="submit" name="ajouter" class="quantity-btn" ' . ($currentStock <= 0 ? 'disabled' : '') . '>
                            <span>+</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    ';
}
?>