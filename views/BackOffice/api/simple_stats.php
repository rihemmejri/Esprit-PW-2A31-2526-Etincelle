<?php
// Minimal statistics API for debugging
header('Content-Type: application/json');

try {
    // Simple database connection
    $conn = new PDO(
        "mysql:host=localhost;dbname=Nutriloop",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Get programme statistics
    $sql = "SELECT objectif, COUNT(*) as count, 
            AVG(DATEDIFF(date_fin, date_debut) + 1) as avg_duration,
            COUNT(DISTINCT id_user) as unique_users
            FROM programme 
            WHERE objectif IS NOT NULL AND objectif != ''
            GROUP BY objectif 
            ORDER BY count DESC";
    
    $query = $conn->prepare($sql);
    $query->execute();
    $programStats = $query->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total
    $totalSql = "SELECT COUNT(*) as total FROM programme";
    $totalQuery = $conn->prepare($totalSql);
    $totalQuery->execute();
    $totalResult = $totalQuery->fetch(PDO::FETCH_ASSOC);
    $totalPrograms = $totalResult['total'];
    
    // Format response
    $response = [
        'status' => 'success',
        'data' => [
            'total_programs' => $totalPrograms,
            'by_objectif' => []
        ]
    ];
    
    // Type mappings
    $typeMappings = [
        'PERTE POIDS' => ['icon' => '📉', 'color' => '#e74c3c', 'label' => 'Perte de poids'],
        'MAINTIEN' => ['icon' => '⚖️', 'color' => '#3498db', 'label' => 'Maintien'],
        'EQUILIBRE' => ['icon' => '🥗', 'color' => '#27ae60', 'label' => 'Équilibre'],
        'MUSCULATION' => ['icon' => '💪', 'color' => '#9b59b6', 'label' => 'Musculation']
    ];
    
    foreach ($programStats as $stat) {
        $objectif = strtoupper(trim($stat['objectif']));
        $mapping = $typeMappings[$objectif] ?? ['icon' => '📋', 'color' => '#95a5a6', 'label' => $stat['objectif']];
        
        $response['data']['by_objectif'][] = [
            'type' => $stat['objectif'],
            'count' => (int)$stat['count'],
            'percentage' => $totalPrograms > 0 ? round(($stat['count'] / $totalPrograms) * 100, 1) : 0,
            'avg_duration' => round($stat['avg_duration'], 1),
            'unique_users' => (int)$stat['unique_users'],
            'icon' => $mapping['icon'],
            'color' => $mapping['color'],
            'label' => $mapping['label']
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
}
?>
