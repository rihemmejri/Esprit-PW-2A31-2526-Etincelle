<?php
// controleurs/StatsAPI.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/ConnexionStats.php';

$action = $_GET['action'] ?? '';

if ($action === 'global') {
    try {
        $statsModel = new ConnexionStats();
        $globalStats = $statsModel->getGlobalStats();
        $avgConnexions = $statsModel->getAverageConnexions();
        
        // Préparer les données pour le graphique (30 jours)
        $dailyStats = $globalStats['daily_stats'];
        $labels = [];
        $data = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($date));
            
            $found = false;
            foreach ($dailyStats as $stat) {
                if ($stat['connexion_date'] == $date) {
                    $data[] = (int)$stat['total_connexions'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $data[] = 0;
            }
        }
        
        // Top 5 utilisateurs
        $topUsers = array_slice($globalStats['top_users'], 0, 5);
        
        echo json_encode([
            'success' => true,
            'total_logins' => $globalStats['global']['total_logins'] ?? 0,
            'active_users' => $globalStats['global']['total_users_connected'] ?? 0,
            'avg_per_user' => $avgConnexions,
            'daily_labels' => $labels,
            'daily_data' => $data,
            'top_users' => $topUsers
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Action non reconnue. Utilisez action=global'
    ]);
}
?>