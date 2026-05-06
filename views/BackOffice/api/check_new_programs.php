<?php
require_once __DIR__ . '/../../../config.php';

header('Content-Type: application/json');

$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

try {
    $db = Config::getConnexion();
    
    // Si lastId est 0, on renvoie juste l'ID maximum actuel pour initialiser
    if ($lastId === 0) {
        $stmt = $db->query("SELECT MAX(id_programme) as max_id FROM programme");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'status' => 'success',
            'last_id' => $result['max_id'] ? (int)$result['max_id'] : 0,
            'new_programs' => []
        ]);
        exit;
    }
    
    // Sinon, on cherche les programmes ajoutés depuis lastId
    $stmt = $db->prepare("SELECT id_programme, id_user, objectif FROM programme WHERE id_programme > :last_id ORDER BY id_programme ASC");
    $stmt->execute(['last_id' => $lastId]);
    $newPrograms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $maxId = $lastId;
    if (count($newPrograms) > 0) {
        $maxId = $newPrograms[count($newPrograms) - 1]['id_programme'];
    }
    
    echo json_encode([
        'status' => 'success',
        'last_id' => (int)$maxId,
        'new_programs' => $newPrograms
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
