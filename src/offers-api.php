<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query('SELECT id, name, price FROM offers');
    $offers = $stmt->fetchAll();
    echo json_encode($offers);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Nie udało się pobrać ofert']);
}
?>