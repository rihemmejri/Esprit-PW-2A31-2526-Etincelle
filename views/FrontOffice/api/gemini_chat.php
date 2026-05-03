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

// Get the POST data
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit;
}

// Load environment variables
$envFile = __DIR__ . '/../../../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !empty(trim($line))) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

$apiKey = getenv('GEMINI_API_KEY');
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Gemini API key not configured']);
    exit;
}
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $apiKey;

$systemInstruction = "You are the NutriLoop AI, a professional nutrition and health assistant for the NutriLoop app. " .
    "Your tone must be strictly professional, knowledgeable, and polite. " .
    "CRITICAL INSTRUCTION: If the user explicitly asks you to CREATE or GENERATE a nutrition program, " .
    "you MUST respond EXACTLY with this text: 'Voici le programme que j'ai conçu pour vous. Vous pouvez l'ajouter à votre liste en cliquant sur le bouton ci-dessous.' " .
    "AND append a strict JSON block at the VERY END of your response inside a standard markdown json block (```json ... ```). " .
    "DO NOT write a long answer. DO NOT explain the program in text. " .
    "The JSON MUST follow this structure: \n" .
    "```json\n" .
    "{\n" .
    "  \"objectif\": \"PERDRE_POIDS\", // (Choose one: PERDRE_POIDS, PRENDRE_MUSCLE, MAINTENIR, EQUILIBRE)\n" .
    "  \"duree_jours\": 3,\n" .
    "  \"repas\": [\n" .
    "    {\n" .
    "      \"nom\": \"Salade de poulet\",\n" .
    "      \"type_repas\": \"DEJEUNER\", // (Choose one: PETIT_DEJEUNER, DEJEUNER, DINER, COLLATION)\n" .
    "      \"calories\": 450,\n" .
    "      \"proteines\": 35,\n" .
    "      \"glucides\": 15,\n" .
    "      \"lipides\": 20,\n" .
    "      \"jour_semaine\": \"LUNDI\" // (LUNDI, MARDI, MERCREDI, JEUDI, VENDREDI, SAMEDI, DIMANCHE)\n" .
    "    }\n" .
    "  ]\n" .
    "}\n" .
    "```";

$data = [
    'systemInstruction' => [
        'parts' => [
            ['text' => $systemInstruction]
        ]
    ],
    'contents' => [
        [
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.2, // Lower temperature for stricter adherence
        'maxOutputTokens' => 8192
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => 'API Error', 'details' => json_decode($response, true)]);
    exit;
}

$responseData = json_decode($response, true);
$replyText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Désolé, je n\'ai pas pu générer une réponse.';

$finalReplyText = $replyText;
$jsonString = '';

// Try matching ```json ... ``` first
if (preg_match('/```(?:json)?\s*({\s*"objectif"[\s\S]*?})\s*```/i', $replyText, $matches)) {
    $jsonString = $matches[1];
    $finalReplyText = preg_replace('/```(?:json)?\s*({\s*"objectif"[\s\S]*?})\s*```/i', '', $replyText);
} 
// Fallback for custom tags
else if (preg_match('/\[PROGRAM_JSON\](.*?)\[\/PROGRAM_JSON\]/s', $replyText, $matches)) {
    $jsonString = $matches[1];
    $finalReplyText = preg_replace('/\[PROGRAM_JSON\].*?\[\/PROGRAM_JSON\]/s', '', $replyText);
}

$finalReplyText = trim($finalReplyText);
$proposedProgram = null;

if (!empty($jsonString)) {
    $proposedProgram = json_decode(trim($jsonString), true);
    if ($proposedProgram && isset($proposedProgram['repas']) && is_array($proposedProgram['repas'])) {
        $objectif = $proposedProgram['objectif'] ?? 'EQUILIBRE';
        $duree = intval($proposedProgram['duree_jours'] ?? 7);
        $dureeTxt = $duree > 1 ? "$duree jours" : "1 jour";
        $repasCount = count($proposedProgram['repas']);
        
        $mealNames = [];
        $limit = min(3, count($proposedProgram['repas']));
        for ($i = 0; $i < $limit; $i++) {
            $mealNames[] = $proposedProgram['repas'][$i]['nom'] ?? 'Repas';
        }
        $mealExamples = implode(", ", $mealNames);
        if ($repasCount > 3) {
            $mealExamples .= " et bien d'autres";
        }
        
        $finalReplyText = "Voici le programme **$objectif** sur $dureeTxt que j'ai conçu pour vous. Il contient $repasCount repas ($mealExamples). Vous pouvez l'ajouter à votre base de données en cliquant sur le bouton ci-dessous.";
    } else {
        $proposedProgram = null; // invalid format
    }
}

echo json_encode([
    'reply' => $finalReplyText,
    'proposedProgram' => $proposedProgram
]);
?>
