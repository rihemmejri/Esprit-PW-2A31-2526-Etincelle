<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../../config.php');

try {
    // Test database connection
    $db = Config::getConnexion();
    
    // Test basic query
    $result = $db->query("SELECT 1 as test");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    
    // Check if programme table exists
    $tables = $db->query("SHOW TABLES");
    $tableList = [];
    while ($table = $tables->fetch(PDO::FETCH_NUM)) {
        $tableList[] = $table[0];
    }
    
    // Check programme table if it exists
    $programmeExists = in_array('programme', $tableList);
    $programmeCount = 0;
    
    if ($programmeExists) {
        $countResult = $db->query("SELECT COUNT(*) as count FROM programme");
        $countRow = $countResult->fetch(PDO::FETCH_ASSOC);
        $programmeCount = $countRow['count'];
        
        // Get sample objectives
        $objectifResult = $db->query("SELECT DISTINCT objectif FROM programme WHERE objectif IS NOT NULL AND objectif != '' LIMIT 5");
        $objectifs = [];
        while ($obj = $objectifResult->fetch(PDO::FETCH_ASSOC)) {
            $objectifs[] = $obj['objectif'];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connection successful',
        'data' => [
            'connection_test' => $row['test'],
            'tables' => $tableList,
            'programme_table_exists' => $programmeExists,
            'programme_count' => $programmeCount,
            'sample_objectifs' => $objectifs ?? []
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage(),
        'debug_info' => [
            'host' => 'localhost',
            'database' => 'nutriloop',
            'error' => $e->getMessage()
        ]
    ]);
}
?>
