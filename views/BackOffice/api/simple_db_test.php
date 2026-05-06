<?php
// Prevent any HTML output
ob_start();
header('Content-Type: application/json');

// Turn off error display
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Test database connection directly with correct name
    $conn = new PDO(
        "mysql:host=localhost;dbname=Nutriloop",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Test basic query
    $result = $conn->query("SELECT 1 as test");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
    // Check if programme table exists
    $tables = $conn->query("SHOW TABLES LIKE 'programme'");
    $tableExists = $tables->rowCount() > 0;
    
    $programmeCount = 0;
    if ($tableExists) {
        $countResult = $conn->query("SELECT COUNT(*) as count FROM programme");
        $countRow = $countResult->fetch(PDO::FETCH_ASSOC);
        $programmeCount = $countRow['count'];
    }
    
    $response = [
        'status' => 'success',
        'message' => 'Database connection successful',
        'test_result' => $row['test'],
        'table_exists' => $tableExists,
        'programme_count' => $programmeCount
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Database connection failed',
        'error' => $e->getMessage(),
        'solutions' => [
            'Start MySQL/MariaDB server',
            'Check database name "nutriloop" exists',
            'Verify username "root" and password are correct',
            'Make sure MySQL is running on localhost:3306',
            'Run setup_database.php to create the database'
        ]
    ];
}

// Clear any output and send JSON
ob_clean();
echo json_encode($response);
exit;
?>
