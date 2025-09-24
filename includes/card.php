<?php
function createCard($nom, $prix, $description, $stock, $withQuantity = false) {
    $currentStock = isset($_SESSION['panier_temp'][$nom]) ? $stock - $_SESSION['panier_temp'][$nom] : $stock;
    $stockClass = $currentStock <= 5 ? ($currentStock <= 0 ? 'stock-empty' : 'stock-low') : 'stock-available';
    
    $quantityBlock = '';
    if ($withQuantity) {
        $qte = isset($_SESSION['panier_temp'][$nom]) ? $_SESSION['panier_temp'][$nom] : 1;
        $quantityBlock = '<div class="card-actions">
            <div class="quantity-form" style="display:flex;align-items:center;gap:10px;">
                <button type="button" class="quantity-btn" onclick="updateQuantity(this, -1)" ' . ($qte <= 1 ? 'disabled' : '') . '>-
                </button>
                <input type="number" name="qte" value="' . $qte . '" min="1" max="' . $currentStock . '" style="width:50px;text-align:center;" readonly>
                <button type="button" class="quantity-btn" onclick="updateQuantity(this, 1)" ' . ($qte >= $currentStock ? 'disabled' : '') . '>+
                </button>
            </div>
        </div>';
    }
    
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
                ' . $quantityBlock . '
            </div>
        </div>
    ';
}
?>
<script>
function updateQuantity(btn, delta) {
    const input = btn.parentNode.querySelector('input[name="qte"]');
    let val = parseInt(input.value) + delta;
    const min = parseInt(input.min);
    const max = parseInt(input.max);
    if (val < min) val = min;
    if (val > max) val = max;
    input.value = val;
    // Désactive les boutons si limite atteinte
    btn.parentNode.querySelectorAll('button').forEach(b => b.disabled = false);
    if (val === min) btn.parentNode.querySelector('button:first-child').disabled = true;
    if (val === max) btn.parentNode.querySelector('button:last-child').disabled = true;
    // Met à jour l'input hidden du formulaire parent
    const form = btn.closest('form');
    if (form) {
        const hiddenQte = form.querySelector('input.hidden-qte');
        if (hiddenQte) hiddenQte.value = val;
    }
}
</script>