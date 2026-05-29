<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jeśli użytkownik nie jest adminem, przekieruj go na stronę główną
if (!isset($_SESSION['role_id']) || (int)$_SESSION['role_id'] !== 1) {
    header("Location: main.php");
    exit();
}

// Połączenie z bazą danych
$host = 'localhost';
$db   = 'szama';
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

// --- BACKEND API DLA OPERACJI ASYNCHRONICZNYCH (AJAX) ---
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    try {
        if ($action === 'get_orders') {
            $stmt = $pdo->query("SELECT o.id, o.status, u.name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
            echo json_encode($stmt->fetchAll());
            exit;
        }
        
        if ($action === 'get_products') {
            $stmt = $pdo->query("SELECT id, name, category, price_cents, discount_percent, stock FROM products ORDER BY id DESC");
            echo json_encode($stmt->fetchAll());
            exit;
        }
        
        if ($action === 'get_users') {
            $stmt = $pdo->query("SELECT u.id, u.name, u.email, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id ASC");
            echo json_encode($stmt->fetchAll());
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            if ($action === 'update_order_status') {
                $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->execute([$input['status'], $input['id']]);
                echo json_encode(['success' => true]);
                exit;
            }

            if ($action === 'save_product') {
                if (!empty($input['id'])) {
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price_cents = ?, discount_percent = ?, stock = ? WHERE id = ?");
                    $stmt->execute([$input['name'], $input['category'], $input['price_cents'], $input['discount_percent'], $input['stock'], $input['id']]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO products (name, category, price_cents, discount_percent, stock, picture) VALUES (?, ?, ?, ?, ?, 'default.png')");
                    $stmt->execute([$input['name'], $input['category'], $input['price_cents'], $input['discount_percent'], $input['stock']]);
                }
                echo json_encode(['success' => true]);
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
    <title>Zegowska Szama - Manage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styl.css">
    <style>
        /* Nadpisanie kolorów placeholderów */
        .darkColor::placeholder {
            color: #a2a2bd !important;
            opacity: 1;
        }
        input::placeholder, textarea::placeholder {
            color: #a2a2bd !important;
            opacity: 1;
        }

        /* STYLIZACJA OKNA MODALNEGO (MENU DODAWANIA NAD STRONĄ) */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.6); /* Przyciemnienie reszty strony */
            backdrop-filter: blur(3px); /* Delikatne rozmycie tła */
            z-index: 1050; /* Nad wszystkimi elementami */
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
        <div class="tabs-container p-0 m-0 mb-4">
            <button class="tab-btn" data-tab="orders">Orders</button>
            <span class="tab-divider"></span>
            <button class="tab-btn" data-tab="products">Products</button>
            <span class="tab-divider"></span>
            <button class="tab-btn" data-tab="users">Users</button>
            <span class="tab-divider"></span>
            <button class="tab-btn" data-tab="mail">Mail</button>
        </div>

        <div class="px-5 py-3">
            <div id="orders" class="tab-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="display-6 fw-bold mb-0" style="color: #2e3d52;">orders</h3>
                    <button class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Complete</button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="table table-borderless">
                        <thead style="color: #a0a0b0;">
                            <tr>
                                <th>User</th>
                                <th>Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body">
                        </tbody>
                    </table>
                </div>

                <div class="mt-5" style="color: #2e3d52;">
                    <h5>Authorize <span style="color: #5a8e7a;">collected orders</span></h5>
                </div>
            </div>

            <div id="products" class="tab-content d-none">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="display-6 fw-bold mb-0" style="color: #2e3d52;">products</h3>
                    <button type="button" onclick="openNewProductModal()" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">New Product</button>
                </div>

                <div style="overflow-x: auto;" class="mb-5">
                    <table class="table table-borderless">
                        <thead style="color: #a0a0b0;">
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody id="products-table-body">
                        </tbody>
                    </table>
                </div>

                <div id="product-modal" class="custom-modal-overlay" onclick="closeProductModalOnOutsideClick(event)">
                    <div class="custom-modal-content">
                        <button type="button" onclick="closeProductModal()" style="position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 1.8rem; color: #3b4257; cursor: pointer; font-weight: bold;">&times;</button>
                        
                        <form id="product-form">
                            <h5 class="fw-bold mb-4" style="color: #2e3d52;" id="form-product-title">Add / Edit Product</h5>
                            <input type="hidden" id="prod-id">
                            
                            <div style="width: 120px; height: 120px; background-color: #3b4257; border-radius: 8px; margin-bottom: 1rem;"></div>
                            <div class="mb-3">
                                <label style="color: #2e3d52; font-weight: 600;">Name</label>
                                <input type="text" id="prod-name" class="form-control darkColor" placeholder="text..." style="background-color: #3b4257; color: #a2a2bd; border: none;" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">Category</label>
                                    <input type="text" id="prod-category" class="form-control darkColor" placeholder="text..." style="background-color: #3b4257; color: #a2a2bd; border: none;" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">Stock</label>
                                    <input type="number" id="prod-stock" class="form-control darkColor" placeholder="0" style="background-color: #3b4257; color: #a2a2bd; border: none;" required min="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">Price (cents)</label>
                                    <input type="number" id="prod-price" class="form-control darkColor" placeholder="0" style="background-color: #3b4257; color: #a2a2bd; border: none;" required min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label style="color: #2e3d52; font-weight: 600;">discount %</label>
                                    <input type="number" id="prod-discount" class="form-control darkColor" placeholder="0-100%" style="background-color: #3b4257; color: #a2a2bd; border: none;" min="0" max="100" value="0">
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4 justify-content-end">
                                <button type="button" onclick="closeProductModal()" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #8b8b9e; color: white; border: none;">Cancel</button>
                                <button type="submit" id="prod-submit-btn" class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="users" class="tab-content d-none">
                <h3 class="display-6 fw-bold mb-4" style="color: #2e3d52;">Users</h3>
                <div style="overflow-x: auto;">
                    <table class="table table-borderless">
                        <thead style="color: #a0a0b0;">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Change</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                        </tbody>
                    </table>
                </div>
                <div class="mt-5">
                    <div class="d-flex gap-3 mb-4" style="border-bottom: 1px solid #4a5568; padding-bottom: 1rem;">
                        <input type="text" id="user-search" class="form-control darkColor" placeholder="Search..." style="background-color: #3b4257; color: #a2a2bd; border: none; max-width: 300px;">
                        <button class="btn fw-semibold px-4 py-2 rounded-2" style="background-color: #3b4257; color: #a2a2bd; border: none;">Name</button>
                    </div>
                </div>
            </div>

            <div id="mail" class="tab-content d-none">
                <form id="mail-form">
                    <h3 class="display-6 fw-bold mb-4" style="color: #2e3d52;">Mail</h3>
                    <div class="mb-4">
                        <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Title</label>
                        <input type="text" id="mail-title" class="form-control darkColor" placeholder="uwaga !!!" style="background-color: #3b4257; color: #a2a2bd; border: none; font-size: 1rem;" required>
                    </div>
                    <div class="mb-4">
                        <label style="color: #2e3d52; font-weight: 600; display: block; margin-bottom: 0.5rem;">Content</label>
                        <textarea id="mail-content" class="form-control darkColor" placeholder="Wpisz treść komunikatu..." style="background-color: #3b4257; color: #a2a2bd; border: none; font-size: 1rem; min-height: 200px;" required></textarea>
                    </div>
                    <button type="submit" class="btn fw-semibold px-5 py-2 rounded-2" style="background-color: #5a8e7a; color: white; border: none;">Push</button>
                </form>
            </div>
        </div>
    </div>

    <div class="p-3 footer text-lowercase fs-5">School's website</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="manage.js"></script>
</body>
</html>