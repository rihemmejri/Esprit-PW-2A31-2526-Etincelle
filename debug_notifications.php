<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $db = Config::getConnexion();
    
    // Check if programmes table exists and has data
    $stmt = $db->query("SELECT COUNT(*) as count FROM programme");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalPrograms = $result['count'];
    
    // Get latest program
    $stmt = $db->query("SELECT id_programme, id_user, objectif FROM programme ORDER BY id_programme DESC LIMIT 5");
    $recentPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get max ID
    $stmt = $db->query("SELECT MAX(id_programme) as max_id FROM programme");
    $maxResult = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxId = $maxResult['max_id'] ? (int)$maxResult['max_id'] : 0;
    
    echo json_encode([
        'status' => 'success',
        'total_programs' => $totalPrograms,
        'max_id' => $maxId,
        'recent_programs' => $recentPrograms,
        'debug_info' => 'Database connection successful'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'debug_info' => 'Database connection failed'
    ]);
}
?>
