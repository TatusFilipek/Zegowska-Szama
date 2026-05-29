<?php
// Database connection configuration
$host = 'localhost';
$database = 'szama';
$user = 'root';
$password = 's';

// DSN (Data Source Name) for MySQL
$dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";

try {
    // Create PDO connection
    $conn = new PDO($dsn, $user, $password);
    
    // Set error mode to exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
?>
