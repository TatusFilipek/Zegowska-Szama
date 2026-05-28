<?php
require_once __DIR__ . '/db.php';

// Fetch products
try {
    $stmt = $conn->query('SELECT id, name, category, picture, price_cents, COALESCE(discount_percent,0) AS discount_percent FROM products ORDER BY category, name');
    $products = $stmt->fetchAll();
    $productsByCategory = [];
    foreach ($products as $product) {
        $category = $product['category'] ?: 'Brak kategorii';
        $productsByCategory[$category][] = $product;
    }
} catch (Exception $e) {
    $products = [];
    $productsByCategory = [];
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zegowska Szama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
</head>
<body class="vh-100 d-flex flex-column">
    <div class="header text-lowercase fs-4 d-flex align-items-center gap-4 dropdown p-2 ps-3">
        <div class="my-auto">
            <a href="main.php"><img src="../design/photos/zeg-icon.png" style="width: 55px;"></a>
        </div>
        <div class="my-auto flex-fill fs-3">
            <a href="main.php">Szama</a>
        </div>

        <div class="d-none d-md-flex gap-5 my-auto fs-3 me-3">
            <a class="text-capitalize" href="profile.html">Profile</a>
            <a class="text-capitalize" href="checkout.html">Checkout</a>
            <a class="text-capitalize" href="notifications.html">Notifications</a>
            <a class="text-capitalize" href="offers.html">Offers</a>
            <a class="text-capitalize" href="manage.html">Manage</a>
        </div>
        
        <div class="my-auto d-md-none" data-bs-toggle="dropdown" aria-expanded="false">
            <img class="btn bi bi-list m-0 p-0" src="../design/photos/burger.png" style="width: 55px;">
        </div>
        <ul class="row dropdown-menu custom-dropdown-menu w-100 mt-1 p-0 d-md-none">
            <li><a class="dropdown-item custom-item" href="profile.html">Profile</a></li>
            <li><a class="dropdown-item custom-item" href="checkout.html">Checkout</a></li>
            <li><a class="dropdown-item custom-item" href="notifications.html">Notifications</a></li>
            <li><a class="dropdown-item custom-item" href="offers.html">Offers</a></li>
            <li><a class="dropdown-item custom-item" href="manage.html">Manage</a></li>
        </ul>
    </div>

    <!-- mobile -->
    <div class="flex-fill container-fluid px-4 py-3 overflow-auto d-md-none d-inline">
        
        <div class="d-flex gap-3 mb-4">
            <div class="text-capitalize px-4 pe-5 py-2 text-start fw-bolder fs-4 darkColor rounded-2" style="border: 0px;">Name</div>
            <input type="text" class="darkColor px-3 py-2 text-start fs-4 rounded-2" placeholder="Search..." style="border: 0px; width: 90%;">
        </div>

        <div class="mb-5 position-relative category-section">
            <div class="position-relative">
                <div class="d-flex flex-column gap-4">
<?php foreach ($productsByCategory as $category => $items): ?>
                    <div>
                        <div class="display-6 border-bottom pb-2 mb-3" style="color: #4a5568; border-color: #3b4257 !important;"><?= htmlspecialchars($category) ?></div>
                        <div class="d-flex overflow-x-auto flex-nowrap gap-3 scroll-container pe-5">
<?php foreach ($items as $p): ?>
                            <div style="min-width: 120px;">
                                <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['name']) ?></div>
                                <small class="d-block text-success mb-2" style="font-size: 0.75rem;"><?= (int)$p['discount_percent'] ?>% OFF!</small>
<?php if (!empty($p['picture']) && file_exists(__DIR__ . '/../design/photos/' . $p['picture'])): ?>
                                <img src="../design/photos/<?= rawurlencode($p['picture']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded" style="width:100%; aspect-ratio:1/1; object-fit:cover; background-color:#3b4257;">
<?php else: ?>
                                <div class="rounded bg-secondary" style="width: 100%; aspect-ratio: 1/1; background-color: #3b4257 !important;"></div>
<?php endif; ?>
                                <div class="mt-2 text-muted text-center fw-semibold"><?= number_format($p['price_cents']/100, 2, '.', '') ?>$</div>
                            </div>
<?php endforeach; ?>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>

                <div class="scroll-arrow-left d-none position-absolute start-0 d-flex align-items-center h-100 top-0 ps-2" style="cursor: pointer; background: linear-gradient(270deg, transparent 0%, white 40%); z-index: 5; padding-right: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-left text-dark fw-bold" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </div>

                <div class="scroll-arrow-right d-none position-absolute end-0 d-flex align-items-center h-100 top-0 pe-2" style="cursor: pointer; background: linear-gradient(90deg, transparent 0%, white 40%); z-index: 5; padding-left: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-right text-dark fw-bold" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- normal -->
    <div class="flex-fill container-fluid overflow-auto d-none d-md-block m-0 p-0">
        <div class="promo-banner overflow-hidden position-relative">
            <div class="text-center mx-auto" style="width: 100%;">
                <span class="bubble bubble-dark" style="left: -50px; top: -30px; width: 160px; opacity: 1;"></span>
                <span class="bubble bubble-purple" style="left: 22%; top: -20px; width: 60px; opacity: 0.7;"></span>
                <span class="bubble bubble-purple" style="right: 18%; bottom: -30px; width: 110px; z-index: 3; opacity: 0.8;"></span>
                <span class="bubble bubble-dark" style="right: -30px; top: -20px; width: 90px; opacity: 0.6;"></span>
                <span class="bubble bubble-dark" style="right: 40%; top: 10px; width: 70px; opacity: 0.8;"></span>

                <h1 class="display-4 fw-bold promo-text py-4 m-0">
                    What’s in <span class="accent-text">store</span> today?
                </h1>
            </div>

            <div class="d-flex gap-3 mb-4 mx-auto" style="width: 70%;">
                <div class="text-capitalize px-4 pe-5 py-2 text-start fw-bolder fs-4 darkColor rounded-2" style="border: 0px;">Name</div>
                <input type="text" class="darkColor px-3 py-2 text-start fs-4 rounded-2" placeholder="Search..." style="border: 0px; width: 90%;">
            </div>
        </div>

        <div class="mb-5 position-relative category-section px-4">
            <div class="position-relative pt-3">
                <div class="d-flex flex-column gap-5">
<?php foreach ($productsByCategory as $category => $items): ?>
                    <div>
                        <div class="display-6 border-bottom pb-2 mb-4" style="color: #4a5568; border-color: #3b4257 !important;"><?= htmlspecialchars($category) ?></div>
                        <div class="d-flex overflow-x-auto flex-nowrap gap-5 scroll-container pe-5">
<?php foreach ($items as $p): ?>
                            <div class="d-flex gap-3" style="min-width: 240px; min-height: 120px;">
                                <?php if (!empty($p['picture']) && file_exists(__DIR__ . '/../design/photos/' . $p['picture'])): ?>
                                    <img src="../design/photos/<?= rawurlencode($p['picture']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded" style="width: 120px; height: 120px; object-fit: cover; background-color: #3b4257;">
                                <?php else: ?>
                                    <div class="rounded bg-secondary" style="width: 120px; height: 120px; background-color: #3b4257 !important;"></div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark mb-0 fs-3"><?= htmlspecialchars($p['name']) ?></div>
                                    <small class="d-block text-success mb-2 fs-6"><?= (int)$p['discount_percent'] ?>% OFF!</small>
                                    <div class="mt-2 text-muted fw-semibold fs-3"><?= number_format($p['price_cents']/100, 2, '.', '') ?>$</div>
                                </div>
                            </div>
<?php endforeach; ?>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>
                

                <div class="scroll-arrow-left d-none position-absolute start-0 d-flex align-items-center h-100 top-0 ps-2" style="cursor: pointer; background: linear-gradient(270deg, transparent 0%, white 40%); z-index: 5; padding-right: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-left text-dark fw-bold" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </div>

                <div class="scroll-arrow-right d-none position-absolute end-0 d-flex align-items-center h-100 top-0 pe-2" style="cursor: pointer; background: linear-gradient(90deg, transparent 0%, white 40%); z-index: 5; padding-left: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-chevron-right text-dark fw-bold" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="skrypt.js"></script>
</body>
</html>
