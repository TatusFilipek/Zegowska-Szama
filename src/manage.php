<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role_id']) || (int)$_SESSION['role_id'] !== 1) {
    header("Location: main.php");
    exit();
}

$host = 'localhost';
$db = 'szama';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    try {
        if ($action === 'get_orders') {
            // ZAKTUALIZOWANE: Pobieramy o.status, aby dynamicznie kontrolować etap zamówienia
            $stmt = $pdo->query("SELECT o.id, o.status, u.name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
            echo json_encode($stmt->fetchAll());
            exit;
        }
        
        if ($action === 'get_products') {
            $stmt = $pdo->query("SELECT id, name, category, price_cents, discount_percent, stock, picture FROM products ORDER BY id DESC");
            echo json_encode($stmt->fetchAll());
            exit;
        }

        if ($action === 'get_offers') {
            $stmt = $pdo->query("SELECT id, name, price FROM offers ORDER BY id DESC");
            $offers = $stmt->fetchAll();

            foreach ($offers as &$offer) {
                $pStmt = $pdo->prepare("SELECT product_id, quantity FROM offer_products WHERE offer_id = ?");
                $pStmt->execute([$offer['id']]);
                $offer['products'] = $pStmt->fetchAll();
            }
            echo json_encode($offers);
            exit;
        }
        
        if ($action === 'get_users') {
            $stmt = $pdo->query("SELECT u.id, u.name, u.email, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id ASC");
            echo json_encode($stmt->fetchAll());
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            // --- NOWA OBSŁUGA ARCHIWIZACJI (STATUS = 0) ---
            if ($action === 'archive_order') {
                if (empty($input['id'])) {
                    echo json_encode(['success' => false, 'error' => 'Brak ID zamówienia.']);
                    exit;
                }
                
                $stmt = $pdo->prepare("UPDATE orders SET status = 0 WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true]);
                exit;
            }

            if ($action === 'update_order_status') {
                // Dotychczasowa progresja statusu (zwiększanie o 1 dla etapów 1 -> 2 -> 3)
                $stmt = $pdo->prepare("UPDATE orders SET status = status + 1 WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true]);
                exit;
            }

            if ($action === 'update_order_status') {
                // ZAKTUALIZOWANE: Status zwiększa się automatycznie o 1 (Progresja statusu)
                $stmt = $pdo->prepare("UPDATE orders SET status = status + 1 WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true]);
                exit;
            }

            if ($action === 'save_product') {
                if (!empty($input['id'])) {
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price_cents = ?, discount_percent = ?, stock = ?, picture = ? WHERE id = ?");
                    $stmt->execute([$input['name'], $input['category'], $input['price_cents'], $input['discount_percent'], $input['stock'], $input['picture'], $input['id']]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO products (name, category, price_cents, discount_percent, stock, picture) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$input['name'], $input['category'], $input['price_cents'], $input['discount_percent'], $input['stock'], $input['picture']]);
                }
                echo json_encode(['success' => true]);
                exit;
            }

            if ($action === 'save_offer') {
                $pdo->beginTransaction();
                try {
                    if (!empty($input['id'])) {
                        $offerId = $input['id'];
                        $stmt = $pdo->prepare("UPDATE offers SET name = ?, price = ? WHERE id = ?");
                        $stmt->execute([$input['name'], $input['price'], $offerId]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO offers (name, price) VALUES (?, ?)");
                        $stmt->execute([$input['name'], $input['price']]);
                        $offerId = $pdo->lastInsertId();
                    }

                    $deleteStmt = $pdo->prepare("DELETE FROM offer_products WHERE offer_id = ?");
                    $deleteStmt->execute([$offerId]);

                    if (!empty($input['items']) && is_array($input['items'])) {
                        $insertStmt = $pdo->prepare("INSERT INTO offer_products (offer_id, product_id, quantity) VALUES (?, ?, ?)");
                        foreach ($input['items'] as $item) {
                            if ((int)$item['quantity'] > 0) {
                                $insertStmt->execute([$offerId, $item['product_id'], $item['quantity']]);
                            }
                        }
                    }

                    $pdo->commit();
                    echo json_encode(['success' => true]);
                } catch (Exception $innerException) {
                    $pdo->rollBack();
                    throw $innerException;
                }
                exit;
            }

            if ($action === 'change_user_role') {
                $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
                $stmt->execute([$input['role_id'], $input['id']]);
                echo json_encode(['success' => true]);
                exit;
            }

            if ($action === 'send_notification') {
                $stmt = $pdo->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
                $stmt->execute([$input['title'], $input['content']]);
                echo json_encode(['success' => true]);
                exit;
            }
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zegowska Szama - Zarządzaj</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
    <style>
        .darkColor::placeholder {
            color: #a2a2bd !important;
            opacity: 1;
        }
        input::placeholder, textarea::placeholder {
            color: #a2a2bd !important;
            opacity: 1;
        }

        input:focus, textarea:focus {
            color: #ffffff !important;
            background-color: #434b63 !important;
            outline: none;
            box-shadow: 0 0 5px rgba(90, 142, 122, 0.5);
        }

        .tabs-container {
            display: flex;
            align-items: center;
            background-color: #92b18d;
            border-bottom: 2px solid #4a6b52;
            padding: 0;
            width: 100%;
        }

        .tab-btn {
            flex: 1;
            background: none;
            border: none;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #4a586e;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            outline: none;
        }
        
        /* Gdy ekran ma 768px szerokości lub mniej (tablety i smartfony) */
        @media (max-width: 768px) {
            .tab-btn {
                font-size: 0.95rem; /* Zmniejszony tekst */
                padding: 8px 0;    /* Przy okazji warto też zmniejszyć padding na małe ekrany */
            }
        }

        /* Gdy ekran ma 480px szerokości lub mniej (małe smartfony) */
        @media (max-width: 480px) {
            .tab-btn {
                font-size: 0.75rem; /* Jeszcze mniejszy tekst */
            }
        }

        .tab-btn:hover {
            color: #2e3d52;
        }

        .tab-divider {
            width: 1px;
            height: 30px;
            background-color: #4a586e;
            opacity: 0.7;
        }

        .tab-btn.active-tab {
            color: #2e3d52 !important;
            font-weight: 700 !important;
        }

        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        .custom-modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .custom-modal-content {
            background-color: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>
<body class="vh-100 d-flex flex-column">
<?php require_once __DIR__ . '/header.php'; ?>

    <div class="flex-fill overflow-auto">
        
        <div class="tabs-container mb-4">
            <button class="tab-btn" data-tab="orders">Zamówienia</button>
            <span class="tab-divider"></span>
            <button class="tab-btn" data-tab="products">Produkty</button>
            <span class="tab-divider"></span>
            <button class="tab-btn" data-tab="offers">Oferty</button>
            <span class="tab-divider"></span>
            <button class="tab-btn" data-tab="users">Urzytkownicy</button>
            <span class="tab-divider"></span>
            <button class="tab-btn" data-tab="mail">Powiadomienia</button>
        </div>

        <div class="px-4 py-3">
            <div id="orders" class="tab-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="display-6 fw-bold mb-0" style="color: #2e3d52;">Zamówienia</h3>
                    <button class="btn fw-semibold px-4 py-2 rounded-2" onclick="reloadOrders()" style="background-color: #3b4257; color: #a2a2bd; border: none;">Odświerz</button>
                </div>
                <div style="overflow-x: auto;">
                    <table class="table table-borderless">
                        <thead style="color: #a0a0b0;">
                            <tr>
                                <th>Urzytkownik</th>
                                <th>Numer</th>
                                <th>Status</th>
                                <th>Akcja</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body"></tbody>
                    </table>
                </div>
            </div>

            <div id="products" class="tab-content d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="display-6 fw-bold mb-0" style="color: #2e3d52;">Produkty</h3>
                    <button type="button" onclick="openNewProductModal()" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Nowy produkt</button>
                </div>
                <div style="overflow-x: auto;" class="mb-5">
                    <table class="table table-borderless">
                        <thead style="color: #a0a0b0;">
                            <tr>
                                <th>Nazwa</th>
                                <th>Kategoria</th>
                                <th>Ilość na stanie</th>
                                <th>Cena</th>
                                <th>Edytuj</th>
                            </tr>
                        </thead>
                        <tbody id="products-table-body"></tbody>
                    </table>
                </div>

                <div id="product-modal" class="custom-modal-overlay" onclick="closeProductModalOnOutsideClick(event)">
                    <div class="custom-modal-content">
                        <button type="button" onclick="closeProductModal()" style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 1.8rem; color: #3b4257; cursor: pointer; font-weight: bold;">&times;</button>
                        <form id="product-form">
                            <h5 class="fw-bold mb-4" style="color: #2e3d52;" id="form-product-title">Dodaj / Edytuj Produkt</h5>
                            <input type="hidden" id="prod-id">
                            
                            <div class="mb-3">
                                <label style="color: #2e3d52; font-weight: 600;">Nazwa zdjęcia</label>
                                <input type="text" id="prod-picture" class="form-control darkColor" placeholder="nazwa obrazka" style="background-color: #3b4257; color: #a2a2bd; border: none;" required>
                            </div>

                            <div class="mb-3">
                                <label style="color: #2e3d52; font-weight: 600;">Nazwa</label>
                                <input type="text" id="prod-name" class="form-control darkColor" placeholder="text..." style="background-color: #3b4257; color: #a2a2bd; border: none;" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">Kategoria</label>
                                    <input type="text" id="prod-category" class="form-control darkColor" placeholder="text..." style="background-color: #3b4257; color: #a2a2bd; border: none;" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">Ilość na stanie</label>
                                    <input type="number" id="prod-stock" class="form-control darkColor" placeholder="0" style="background-color: #3b4257; color: #a2a2bd; border: none;" required min="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">Cena (grosze)</label>
                                    <input type="number" id="prod-price" class="form-control darkColor" placeholder="0" style="background-color: #3b4257; color: #a2a2bd; border: none;" required min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">Przecena %</label>
                                    <input type="number" id="prod-discount" class="form-control darkColor" placeholder="0-100%" style="background-color: #3b4257; color: #a2a2bd; border: none;" min="0" max="100" value="0">
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4 justify-content-end">
                                <button type="button" onclick="closeProductModal()" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #8b8b9e; color: white; border: none;">Odrzuć</button>
                                <button type="submit" id="prod-submit-btn" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Stworz</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="offers" class="tab-content d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="display-6 fw-bold mb-0" style="color: #2e3d52;">Oferty</h3>
                    <button type="button" onclick="openNewOfferModal()" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Nowa oferta</button>
                </div>
                <div style="overflow-x: auto;" class="mb-5">
                    <table class="table table-borderless">
                        <thead style="color: #a0a0b0;">
                            <tr>
                                <th>ID</th>
                                <th>Nazwa</th>
                                <th>Cena</th>
                                <th>Edytuj</th>
                            </tr>
                        </thead>
                        <tbody id="offers-table-body"></tbody>
                    </table>
                </div>

                <div id="offer-modal" class="custom-modal-overlay" onclick="closeOfferModalOnOutsideClick(event)">
                    <div class="custom-modal-content" style="max-width: 650px;">
                        <button type="button" onclick="closeOfferModal()" style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 1.8rem; color: #3b4257; cursor: pointer; font-weight: bold;">&times;</button>
                        <form id="offer-form">
                            <h5 class="fw-bold mb-4" style="color: #2e3d52;" id="form-offer-title">Dodaj / Edytuj Oferte</h5>
                            <input type="hidden" id="offer-id">
                            
                            <div class="mb-3">
                                <label style="color: #2e3d52; font-weight: 600;">Nazwa oferty</label>
                                <input type="text" id="offer-name" class="form-control darkColor" placeholder="Zestaw..." style="background-color: #3b4257; color: #a2a2bd; border: none;" required>
                            </div>
                            <div class="mb-3">
                                <label style="color: #2e3d52; font-weight: 600;">Cena (grosze)</label>
                                <input type="number" id="offer-price" class="form-control darkColor" placeholder="0" style="background-color: #3b4257; color: #a2a2bd; border: none;" required min="1">
                            </div>

                            <div class="mb-3">
                                <label style="color: #2e3d52; font-weight: 600; display: block;" class="mb-2">Zawartość zestawu (ilość sztuk):</label>
                                <div id="offer-products-container" style="max-height: 250px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background: #f8fafc;">
                                    <div class="text-muted text-center py-2">Ładowanie listy produktów...</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4 justify-content-end">
                                <button type="button" onclick="closeOfferModal()" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #8b8b9e; color: white; border: none;">Odrzuć</button>
                                <button type="submit" id="offer-submit-btn" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Stwórz</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="users" class="tab-content d-none">
                <h3 class="display-6 fw-bold mb-4" style="color: #2e3d52;">Urzytkownicy</h3>
                <div style="overflow-x: auto;">
                    <table class="table table-borderless">
                        <thead style="color: #a0a0b0;">
                            <tr>
                                <th>Nazwa</th>
                                <th>Email</th>
                                <th>Rola</th>
                                <th>Zmień</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body"></tbody>
                    </table>
                </div>
                <div class="mt-5">
                    <div class="d-flex gap-3 mb-4" style="border-bottom: 1px solid #4a5568; padding-bottom: 1rem;">
                        <input type="text" id="user-search" class="form-control darkColor" placeholder="Wyszukaj..." style="background-color: #3b4257; color: #a2a2bd; border: none; max-width: 300px;">
                        <button class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Nazwa</button>
                    </div>
                </div>
            </div>

            <div id="mail" class="tab-content d-none">
                <form id="mail-form">
                    <h3 class="display-6 fw-bold mb-4" style="color: #2e3d52;">Mail</h3>
                    <div class="mb-4">
                        <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Tytuł</label>
                        <input type="text" id="mail-title" class="form-control darkColor" placeholder="uwaga !!!" style="background-color: #3b4257; color: #a2a2bd; border: none; font-size: 1rem;" required>
                    </div>
                    <div class="mb-4">
                        <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Wiadomość</label>
                        <textarea id="mail-content" class="form-control darkColor" placeholder="Wpisz treść komunikatu..." style="background-color: #3b4257; color: #a2a2bd; border: none; font-size: 1rem; min-height: 200px;" required></textarea>
                    </div>
                    <button type="submit" class="btn fw-semibold px-5 py-2 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Prześlij</button>
                </form>
            </div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">Szkolna strona</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="manage.js"></script>
</body>
</html>