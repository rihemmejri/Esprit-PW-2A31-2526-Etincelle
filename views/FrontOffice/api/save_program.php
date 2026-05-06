<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../controleurs/ProgrammeController.php';
require_once __DIR__ . '/../../../controleurs/RepasController.php';
require_once __DIR__ . '/../../../models/programme.php';
require_once __DIR__ . '/../../../models/repas.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input) || !isset($input['repas']) || !is_array($input['repas'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid program data']);
    exit;
}

$programData = $input;

try {
    $progController = new ProgrammeController();
    $repasController = new RepasController();
    
    $dateDebut = date('Y-m-d');
    $duree = intval($programData['duree_jours'] ?? 7);
    $dateFin = date('Y-m-d', strtotime("+" . ($duree - 1) . " days"));
    $objectif = $programData['objectif'] ?? 'EQUILIBRE';
    
    $nouveauProgramme = new programme(1, $objectif, $dateDebut, $dateFin);
    
    if ($progController->addProgramme($nouveauProgramme)) {
        $id_programme = $nouveauProgramme->getIdProgramme();
        $repasToLink = [];
        
        foreach ($programData['repas'] as $r) {
            $nouveauRepas = new repas(
                $r['nom'] ?? 'Repas généré',
                $r['type_repas'] ?? 'DEJEUNER',
                $r['calories'] ?? 0,
                $r['proteines'] ?? 0,
                $r['glucides'] ?? 0,
                $r['lipides'] ?? 0
            );
            
            if ($repasController->addRepas($nouveauRepas)) {
                $id_repas = $nouveauRepas->getIdRepas();
                if ($id_repas) {
                    $repasToLink[] = [
                        'id_repas' => $id_repas,
                        'jour_semaine' => $r['jour_semaine'] ?? 'LUNDI',
                        'type_repas' => $r['type_repas'] ?? 'DEJEUNER'
                    ];
                }
            }
        }
        
        if (!empty($repasToLink)) {
            $progController->addRepasToProgramme($id_programme, $repasToLink);
            echo json_encode(['success' => true, 'message' => 'Program saved successfully']);
            exit;
        }
    }
    
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save program']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server Error', 'details' => $e->getMessage()]);
}
?>
