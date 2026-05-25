<?php
// Include database connection
require_once 'db.php';

// Set response header to JSON
header('Content-Type: application/json');

// Get the id parameter from query string (if provided)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    if ($id !== null) {
        // Fetch single product by ID
        $sql = 'SELECT id, name, category, picture, price_cents, discount_percent, stock FROM products WHERE id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        } else {
            $product = $stmt->fetch();
            // Convert numeric strings to integers for JSON
            $product['id'] = intval($product['id']);
            $product['price_cents'] = intval($product['price_cents']);
            $product['discount_percent'] = $product['discount_percent'] !== null ? intval($product['discount_percent']) : null;
            $product['stock'] = intval($product['stock']);
            echo json_encode($product);
        }
    } else {
        // Fetch all products
        $sql = 'SELECT id, name, category, picture, price_cents, discount_percent, stock FROM products';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $products = [];
        while ($row = $stmt->fetch()) {
            // Convert numeric strings to integers for JSON
            $row['id'] = intval($row['id']);
            $row['price_cents'] = intval($row['price_cents']);
            $row['discount_percent'] = $row['discount_percent'] !== null ? intval($row['discount_percent']) : null;
            $row['stock'] = intval($row['stock']);
            $products[] = $row;
        }
        
        echo json_encode($products);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
}
?>
