<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sprawdzenie logowania użytkownika
$loggedIn = !empty($_SESSION['user_id']);
$userId = $loggedIn ? (int)$_SESSION['user_id'] : null;

// Dane połączenia z bazą danych
$host = 'localhost';
$db = 'szama';
$user = 'root';
$pass = '';

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Błąd połączenia z bazą.']);
    exit;
}

// --- NOWA OBSŁUGA ZAPYTANIA GET: SPRAWDZANIE STATUSU ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check_status') {
    header('Content-Type: application/json');
    
    $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
    if ($orderId <= 0 || !$userId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nieprawidłowe żądanie.']);
        exit;
    }

    // Pobieramy aktualny status zamówienia przypisanego do użytkownika
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if ($order) {
        echo json_encode(['success' => true, 'status' => (int)$order['status']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nie znaleziono zamówienia.']);
    }
    exit;
}

// Obsługa zapytań AJAX POST (składanie zamówienia)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'place_order') {
    header('Content-Type: application/json');
    
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Musisz być zalogowany.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $cart = !empty($input['cart']) ? $input['cart'] : [];

    if (empty($cart)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Koszyk jest pusty.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Dodanie zamówienia (Krok 1: status = 1)
        $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, status) VALUES (?, 1)");
        $stmtOrder->execute([$userId]);
        $orderId = $pdo->lastInsertId();

        $stmtProductFetch = $pdo->prepare("SELECT price_cents, COALESCE(discount_percent, 0) AS discount FROM products WHERE id = ?");
        $stmtOfferFetch   = $pdo->prepare("SELECT price FROM offers WHERE id = ?");
        
        $stmtInsertItem  = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price_snapshot, discount_percent_snapshot) VALUES (?, ?, ?, ?, ?)");
        $stmtInsertOffer = $pdo->prepare("INSERT INTO order_offers (order_id, offer_id, quantity, unit_price_snapshot) VALUES (?, ?, ?, ?)");

        foreach ($cart as $item) {
            $itemId = $item['id'];
            $quantity = (int)$item['quantity'];

            if (is_string($itemId) && strpos($itemId, 'offer_') === 0) {
                $offerId = (int)str_replace('offer_', '', $itemId);
                $stmtOfferFetch->execute([$offerId]);
                $offerData = $stmtOfferFetch->fetch();
                if ($offerData) {
                    $stmtInsertOffer->execute([$orderId, $offerId, $quantity, (int)$offerData['price']]);
                }
            } else {
                $productId = (int)$itemId;
                $stmtProductFetch->execute([$productId]);
                $productData = $stmtProductFetch->fetch();
                if ($productData) {
                    $stmtInsertItem->execute([$orderId, $productId, $quantity, (int)$productData['price_cents'], (int)$productData['discount']]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'order_id' => $orderId]);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zegowska Szama - Koszyk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
</head>
<body class="vh-100 d-flex flex-column">
<?php require_once __DIR__ . '/header.php'; ?>

    <div class="flex-fill container-fluid px-4 py-3 overflow-auto d-flex flex-column align-items-center">
        <div class="mb-2 w-100" style="max-width: 600px;">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #2e3d52;">Koszyk</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #5a8e7a; color: white; font-weight: bold;">1</div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center" style="margin: 0 1rem; margin-top: 0.7rem;">
                    <div style="height: 2px; width: 100%; background-color: #5a8e7a;"></div>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #2e3d52;">Płatność</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #37645D; color: white; font-weight: bold;">2</div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center" style="margin: 0 1rem; margin-top: 0.7rem;">
                    <div style="height: 2px; width: 100%; background-color: #5a8e7a;"></div>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #a0a0b0;">Przetwarzanie</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #3b4257; color: #a2a2bd; font-weight: bold;">3</div>
                </div>
                <div class="flex-grow-1 d-flex align-items-center" style="margin: 0 1rem; margin-top: 0.7rem;">
                    <div style="height: 2px; width: 100%; background-color: #c9a3a3;"></div>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <small style="color: #a0a0b0;">Odbiór</small>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px; background-color: #7a7a8e; color: white; font-weight: bold;">4</div>
                </div>
            </div>
        </div>

        <div id="checkout-content" class="content-section w-100 d-flex flex-column align-items-center" style="max-width: 600px;">
            <h2 class="display-6 fw-bold mb-4 w-100 text-start" style="color: #2e3d52;">Koszyk</h2>
            <div class="order-table-container w-100 mb-4"></div>
            
            <div id="checkout-actions-container" class="d-flex gap-3 mb-4 w-100 d-none">
                <button class="btn btn-cancel fw-semibold py-3 px-4 rounded-3 fs-5" style="background-color: #cbd5e1; color: #475569; border: none; flex: 1;">Cancel</button>
                <button id="btn-submit-order" class="btn fw-semibold py-3 px-4 rounded-3 fs-5 text-white" style="background-color: #2e3d52; border: none; flex: 2;" disabled>0.00$</button>
            </div>
        </div>

        <div id="payment-content" class="content-section w-100 d-none flex-column align-items-center" style="max-width: 600px;">
            <h2 class="display-6 fw-bold mb-4 w-100 text-start" style="color: #2e3d52;">Płatność</h2>
            <div class="fs-4 mb-3" style="color:#2e3d52;">Kwota całkowita do zapłaty przy kasie</div>
            <div class="fw-bold fs-2 text-center my-5" id="payment-total-amount">0.00$</div>
            <div class="alert alert-info text-center w-100">Oczekiwanie na potwierdzenie płatności przez kasjera...</div>
        </div>

        <div id="proccessing-content" class="content-section w-100 d-none flex-column align-items-center" style="max-width: 600px;">
            <h2 class="display-6 fw-bold mb-4 w-100 text-start" style="color: #2e3d52;">Przetwarzanie</h2>
            <div class="fw-bold fs-2 text-center my-5">Przygotowywanie twojego jedzenia...</div>
            <div class="spinner-border text-secondary mb-3" role="status"></div>
            <p style="color: #2e3d52;">Twoje zamówienie jest w trakcie przygotowywania przez kuchnię.</p>
        </div>

        <div id="collect-content" class="content-section w-100 d-none flex-column align-items-center" style="max-width: 600px;">
            <h2 class="display-6 fw-bold mb-4 w-100 text-start" style="color: #2e3d52;">Odbiór</h2>
            <div class="fw-bold fs-2 text-center my-5" style="color: #5a8e7a;">Twoje jedzenie jest gotowe do odbioru!</div>
            <div class="fs-4 mb-5 text-center">Podejdź do lady i podaj swój numer zamówienia.</div>
            <a href="main.php" class="btn fw-semibold py-3 px-5 rounded-3 fs-5 text-white text-decoration-none" style="background-color: #2e3d52;">Wróć do strony głównej</a>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">Szkolna strona</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="product.js"></script>
    <script src="offer.js"></script>
    <script src="checkout.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const actionsContainer = document.getElementById('checkout-actions-container');
        const submitBtn = document.getElementById('btn-submit-order');
        
        // Funkcja do pobierania asynchronicznego pełnych danych o cenach z bazy (tak jak w checkout.js)
        async function updateOrderButtonPrice() {
            try {
                const stored = localStorage.getItem('zegowskaCart');
                let cart = [];
                try {
                    if (stored) cart = JSON.parse(stored) || [];
                } catch(e) { cart = []; }

                if (!cart || cart.length === 0) {
                    if (actionsContainer) actionsContainer.classList.add('d-none');
                    if (submitBtn) {
                        submitBtn.setAttribute('disabled', 'true');
                        submitBtn.textContent = "0.00$";
                    }
                    return;
                }

                // Pobieramy dane cen z bazy
                const [resProducts, resOffers] = await Promise.all([
                    fetch('api.php'),
                    fetch('offers-api.php')
                ]);

                if (!resProducts.ok || !resOffers.ok) return;

                const allProducts = await resProducts.ok ? await resProducts.json() : [];
                const allOffers = await resOffers.ok ? await resOffers.json() : [];

                let totalCents = 0;

                cart.forEach(item => {
                    const quantity = parseInt(item.quantity, 10) || 0;
                    if (typeof item.id === 'string' && item.id.startsWith('offer_')) {
                        const offerId = parseInt(item.id.replace('offer_', ''), 10);
                        const dbOffer = allOffers.find(o => parseInt(o.id, 10) === offerId);
                        if (dbOffer) {
                            totalCents += (parseInt(dbOffer.price, 10) || 0) * quantity;
                        }
                    } else {
                        const productId = parseInt(item.id, 10);
                        const dbProd = allProducts.find(p => parseInt(p.id, 10) === productId);
                        if (dbProd) {
                            const priceCents = parseInt(dbProd.price_cents, 10) || 0;
                            const discount = parseInt(dbProd.discount_percent, 10) || 0;
                            const finalUnitPrice = discount > 0 ? Math.round(priceCents * (1 - discount / 100)) : priceCents;
                            totalCents += finalUnitPrice * quantity;
                        }
                    }
                });

                const formattedPrice = (totalCents / 100).toFixed(2) + '$';

                // Aktualizacja przycisku i widoczności
                if (actionsContainer) actionsContainer.classList.remove('d-none');
                if (submitBtn) {
                    submitBtn.removeAttribute('disabled');
                    submitBtn.textContent = formattedPrice;
                }
            } catch (err) {
                console.error("Błąd aktualizacji ceny na guziku:", err);
            }
        }

        // Pierwsze wywołanie
        updateOrderButtonPrice();

        // Nasłuchiwanie zmian ilości w koszyku (przyciski plus/minus generowane przez checkout.js)
        document.addEventListener('click', (e) => {
            if (e.target && (e.target.classList.contains('action-btn-plus') || e.target.classList.contains('action-btn-minus') || e.target.classList.contains('btn-cancel'))) {
                setTimeout(updateOrderButtonPrice, 50);
            }
        });
    });
    </script>
</body>
</html>