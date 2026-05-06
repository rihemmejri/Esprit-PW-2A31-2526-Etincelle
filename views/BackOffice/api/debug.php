<?php
// Debug endpoint to see what's happening
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Content-Type: text/plain\n\n";
echo "=== DEBUG INFO ===\n";

echo "1. Testing basic PHP...\n";
echo "PHP is working\n\n";

echo "2. Testing database connection...\n";
try {
    $conn = new PDO(
        "mysql:host=localhost;dbname=Nutriloop",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Database connection: SUCCESS\n";
    
    // Test query
    $result = $conn->query("SELECT COUNT(*) as count FROM programme");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "Programme count: " . $row['count'] . "\n";
    
} catch (Exception $e) {
    echo "Database connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing JSON response...\n";
header('Content-Type: application/json');
$data = ['status' => 'success', 'message' => 'Debug endpoint working'];
echo json_encode($data);
?>
