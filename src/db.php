<?php
// Database connection configuration
$host = 'sql313.infinityfree.com';
$database = 'if0_42050554_szama';
$user = 'if0_42050554';
$password = 'nU2ceqtTyfwBl';

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
