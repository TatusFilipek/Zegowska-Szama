<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama - Offers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
</head>
<body class="vh-100 d-flex flex-column">
<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/db.php';

$offers = [];
try {
    $stmt = $conn->query('SELECT id, name, price FROM offers ORDER BY created_at DESC');
    $offers = $stmt->fetchAll();
} catch (Exception $e) {
    $offers = [];
}

function getOfferProducts($conn, $offerId) {
    try {
        $stmt = $conn->prepare('
            SELECT p.name, op.quantity, p.price_cents
            FROM offer_products op
            JOIN products p ON op.product_id = p.id
            WHERE op.offer_id = ?
            ORDER BY p.name
        ');
        $stmt->execute([$offerId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function calculateDiscount($conn, $offerId, $offerPrice) {
    $products = getOfferProducts($conn, $offerId);
    if (empty($products)) {
        return 0;
    }
    
    $totalProductPrice = 0;
    foreach ($products as $product) {
        $totalProductPrice += $product['price_cents'] * $product['quantity'];
    }
    
    if ($totalProductPrice == 0) {
        return 0;
    }
    
    $discount = (($totalProductPrice - $offerPrice) / $totalProductPrice) * 100;
    return round($discount, 0);
}
?>

    <div class="flex-fill container-fluid px-4 py-3 overflow-auto">
        <h3 class="display-6 fw-bold mb-4 border-bottom pb-2" style="color: #2e3d52; border-color: #4a5568 !important;">Your offers</h3>

        <div class="row">
            <?php if (!empty($offers)): ?>
                <?php foreach ($offers as $offer): ?>
                    <?php 
                        $offerProducts = getOfferProducts($conn, $offer['id']);
                        $discountPercent = calculateDiscount($conn, $offer['id'], $offer['price']);
                    ?>
            <div class="col-md-6 mb-5">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                        <div class="fw-bold fs-4 text-truncate" style="color: #2e3d52; max-width: 100%; text-transform: uppercase;">
                            <?= htmlspecialchars($offer['name']) ?>
                        </div>
                    </div>

                    <div class="text-start mb-2" style="display: grid; grid-template-columns: 3fr 7fr; gap: 1rem; color: #a0a0b0; text-transform: uppercase;">
                        <div class="fs-5 fw-bold text-start text-truncate" style="color: #2e3d52; max-width: 220px;">
                            $<?= number_format($offer['price'] / 100, 2) ?>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-end fs-6 fw-medium pb-1">
                            <span>Produkt</span>
                            <span>Ilość</span>
                        </div>
                    </div>

                    <div class="text-center" style="display: grid; grid-template-columns: 3fr 7fr; gap: 1rem; padding-top: 0">
                        <div class="rounded d-flex flex-column justify-content-end w-100" style="height: 220px; max-width: 220px; background-color: #3b4257 !important;">
                            <div class="mx-auto mb-2 py-1 rounded text-truncate" style="background-color: #86A77F; width: 85%; font-size: 0.85rem; font-weight: bold; color: #fff; px-1;">
                                <?= htmlspecialchars($discountPercent) ?>% OFF
                            </div>
                        </div>

                        <div class="fs-4 text-start d-flex flex-column justify-content-start gap-2" style="max-height: 220px; overflow-y: auto;">
                            <?php foreach ($offerProducts as $product): ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-1" style="border-color: rgba(255, 255, 255, 0.15) !important;">
                                    <span class="text-truncate" style="max-width: 75%;" title="<?= htmlspecialchars($product['name']) ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </span>
                                    <span class="fw-bold" style="color: #86A77F; white-space: nowrap;">
                                        <?= htmlspecialchars($product['quantity']) ?>x
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    Brak dostępnych ofert.
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="skrypt.js"></script>
</body>
</html>
