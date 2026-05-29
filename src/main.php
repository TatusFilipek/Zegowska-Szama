<?php
require_once __DIR__ . '/db.php';
session_start();
$loggedIn = !empty($_SESSION['user_id']);

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
<?php require_once __DIR__ . '/header.php'; ?>
    <!-- mobile -->
    <div class="flex-fill container-fluid px-4 py-3 overflow-auto d-md-none d-inline">
        
        <div class="d-flex gap-3 mb-4">
            <div class="text-capitalize px-4 pe-5 py-2 text-start fw-bolder fs-4 darkColor rounded-2" style="border: 0px;">Name</div>
            <input type="text" class="darkColor px-3 py-2 text-start fs-4 rounded-2" placeholder="Search..." style="border: 0px; width: 90%;">
        </div>

        <div class="mb-5">
            <div id="mobile-categories"></div>
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

        <div class="mb-5 px-4">
            <div id="desktop-categories"></div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="product.js"></script>
    <script src="products-render.js"></script>
    <script src="skrypt.js"></script>
</body>
</html>