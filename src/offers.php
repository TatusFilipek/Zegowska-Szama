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
                    <div class="text-center" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; padding: 1rem 0; color: #a0a0b0; font-size: 1.2rem; font-weight: 500; text-transform: uppercase; padding-bottom: 0">
                        <div><?= htmlspecialchars($offer['name']) ?></div>
                        <div>$<?= number_format($offer['price'] / 100, 2) ?></div>
                        <div>count</div>
                        <div></div>
                    </div>

                    <div class="text-center" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; padding: 0.75rem 0; padding-top: 0">
                        <div class="rounded d-flex flex-column justify-content-end" style="width: 100%; aspect-ratio: 1/1; background-color: #3b4257 !important;">
                            <div class="mx-auto" style="background-color: #86A77F; width: 60%;"><?= htmlspecialchars($discountPercent) ?>% OFF</div>
                        </div>

                        <div class="fs-4">
                            <?php foreach ($offerProducts as $product): ?>
                            <div><?= htmlspecialchars($product['name']) ?></div>
                            <?php endforeach; ?>
                        </div>

                        <div class="fs-4" style="color: #86A77F;">
                            <?php foreach ($offerProducts as $product): ?>
                            <div><?= htmlspecialchars($product['quantity']) ?>x</div>
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
