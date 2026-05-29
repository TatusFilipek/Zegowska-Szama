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
    <div class="flex-fill container-fluid px-4 py-3 overflow-auto d-md-none d-inline">
        
        <div class="d-flex gap-3 mb-4">
            <div class="text-capitalize px-4 pe-5 py-2 text-center fw-bolder fs-4 darkColor rounded-2" style="border: 0px;">Nazwa</div>
            <input type="text" id="mobile-search" class="darkColor px-3 py-2 text-start fs-4 rounded-2" placeholder="Wyszukaj..." style="border: 0px; width: 90%;">
        </div>

        <div class="mb-5">
            <div id="mobile-categories"></div>
        </div>
    </div>

    <div class="flex-fill container-fluid overflow-auto d-none d-md-block m-0 p-0">
        <div class="promo-banner overflow-hidden position-relative">
            <div class="text-center mx-auto" style="width: 100%;">
                <span class="bubble bubble-dark" style="left: -50px; top: -30px; width: 160px; opacity: 1;"></span>
                <span class="bubble bubble-purple" style="left: 22%; top: -20px; width: 60px; opacity: 0.7;"></span>
                <span class="bubble bubble-purple" style="right: 18%; bottom: -30px; width: 110px; z-index: 3; opacity: 0.8;"></span>
                <span class="bubble bubble-dark" style="right: -30px; top: -20px; width: 90px; opacity: 0.6;"></span>
                <span class="bubble bubble-dark" style="right: 40%; top: 10px; width: 70px; opacity: 0.8;"></span>

                <h1 class="display-4 fw-bold promo-text py-4 m-0">
                    Co jest w <span class="accent-text">sklepie</span> dzisiaj?
                </h1>
            </div>

            <div class="d-flex gap-3 mb-4 mx-auto" style="width: 70%;">
                <div class="text-capitalize px-4 pe-5 py-2 text-center fw-bolder fs-4 darkColor rounded-2" style="border: 0px;">Nazwa</div>
                <input type="text" id="desktop-search" class="darkColor px-3 py-2 text-start fs-4 rounded-2" placeholder="Wyszukaj..." style="border: 0px; width: 90%;">
            </div>
        </div>

        <div class="mb-5 px-4">
            <div id="desktop-categories"></div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">Szkolna strona</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Przekazujemy stan sesji do skryptu JS
        window.isUserLoggedIn = <?php echo $loggedIn ? 'true' : 'false'; ?>;

        (function() {
            // Surowe dane pobrane z bazy PHP
            const rawProducts = <?php echo json_encode($products); ?>;

            function performSearch(queryText) {
                const query = queryText.toLowerCase().trim();

                // 1. Filtrowanie surowej bazy danych na podstawie wpisanej frazy
                const filteredRaw = rawProducts.filter(p => {
                    const nameMatch = p.name ? p.name.toLowerCase().includes(query) : false;
                    const categoryMatch = p.category ? p.category.toLowerCase().includes(query) : false;
                    return nameMatch || categoryMatch;
                });

                // 2. Bezpieczna weryfikacja czy wyeksponowane z pliku js funkcje są już gotowe
                if (typeof window.Product === 'function' && typeof window.groupByCategory === 'function') {
                    
                    // Konwersja surowych obiektów na pełnoprawne instancje klasy Product
                    const productInstances = filteredRaw.map(r => new window.Product(r));
                    
                    // Grupowanie produktów przy użyciu oryginalnej logiki mapowania kategorii
                    const groupedMap = window.groupByCategory(productInstances);

                    // 3. Renderowanie widoków dokładnie w tym samym formacie
                    if (typeof window.renderDesktop === 'function') {
                        window.renderDesktop(groupedMap);
                    }
                    if (typeof window.renderMobile === 'function') {
                        window.renderMobile(groupedMap);
                    }

                    // Przywrócenie działania strzałek przewijania (jeżeli funkcja istnieje w skrypt.js)
                    if (typeof window.initializeScrollButtons === 'function') {
                        window.initializeScrollButtons();
                    }

                    // Obsługa braku wyników wyszukiwania
                    if (filteredRaw.length === 0) {
                        const noResultsHtml = '<div class="text-center py-5 fs-4 text-muted">No products found matching your search.</div>';
                        const dContainer = document.getElementById('desktop-categories');
                        const mContainer = document.getElementById('mobile-categories');
                        if (dContainer) dContainer.innerHTML = noResultsHtml;
                        if (mContainer) mContainer.innerHTML = noResultsHtml;
                    }
                }
            }

            // Inicjalizacja nasłuchiwania pól tekstowych po załadowaniu drzewa DOM
            document.addEventListener('DOMContentLoaded', () => {
                const desktopSearch = document.getElementById('desktop-search');
                const mobileSearch = document.getElementById('mobile-search');

                if (desktopSearch) {
                    desktopSearch.addEventListener('input', (e) => {
                        const val = e.target.value;
                        if (mobileSearch) mobileSearch.value = val; // Synchronizacja z mobilnym inputem
                        performSearch(val);
                    });
                }

                if (mobileSearch) {
                    mobileSearch.addEventListener('input', (e) => {
                        const val = e.target.value;
                        if (desktopSearch) desktopSearch.value = val; // Synchronizacja z desktopowym inputem
                        performSearch(val);
                    });
                }
            });
        })();
    </script>

    <script src="product.js"></script>
    <script src="products-render.js"></script>
    <script src="skrypt.js"></script>
</body>
</html>