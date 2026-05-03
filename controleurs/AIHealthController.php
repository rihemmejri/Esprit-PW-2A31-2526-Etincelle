<?php
require_once __DIR__ . '/../config.php';

class AIHealthController
{
    private $apiKey;

    public function __construct() {
        $this->apiKey = getenv('GROQ_API_KEY_HEALTH');
    }
    private $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model = 'llama-3.1-8b-instant';

    public function handleRequest()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;
        $date = $input['date'] ?? date('Y-m-d');
        $calories = (float)($input['calories'] ?? 0);
        $water = (float)($input['water'] ?? 0);
        $targetCalories = (float)($input['targetCalories'] ?? 2000);
        $targetWater = (float)($input['targetWater'] ?? 2);

        // Calculate a precise score between 0 and 100
        $calScore = 0;
        if ($targetCalories > 0) {
            if ($calories <= $targetCalories) {
                $calScore = 50;
            } else {
                $calScore = max(0, ($targetCalories / $calories) * 50);
            }
        }

        $waterScore = 0;
        if ($targetWater > 0) {
            if ($water >= $targetWater) {
                $waterScore = 50;
            } else {
                $waterScore = ($water / $targetWater) * 50;
            }
        }

        $score = round($calScore + $waterScore);

        // Determine Status
        if ($score >= 80) {
            $status = 'perfect';
            $color = '#10b981'; // Green
        } elseif ($score >= 50) {
            $status = 'normal';
            $color = '#f59e0b'; // Yellow
        } else {
            $status = 'bad';
            $color = '#ef4444'; // Red
        }

        // Save to score_journalier
        if ($userId) {
            $db = Config::getConnexion();
            // Check if exists
            $stmt = $db->prepare("SELECT id FROM score_journalier WHERE user_id = :user_id AND date = :date");
            $stmt->execute(['user_id' => $userId, 'date' => $date]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $stmtUpdate = $db->prepare("UPDATE score_journalier SET calories_consommees = :cal, eau_bue = :eau, objectif_calories = :obj_cal, objectif_eau = :obj_eau, score = :score WHERE id = :id");
                $stmtUpdate->execute([
                    'cal' => $calories,
                    'eau' => $water,
                    'obj_cal' => $targetCalories,
                    'obj_eau' => $targetWater,
                    'score' => $score,
                    'id' => $exists
                ]);
            } else {
                $stmtInsert = $db->prepare("INSERT INTO score_journalier (user_id, date, calories_consommees, eau_bue, objectif_calories, objectif_eau, score) VALUES (:user_id, :date, :cal, :eau, :obj_cal, :obj_eau, :score)");
                $stmtInsert->execute([
                    'user_id' => $userId,
                    'date' => $date,
                    'cal' => $calories,
                    'eau' => $water,
                    'obj_cal' => $targetCalories,
                    'obj_eau' => $targetWater,
                    'score' => $score
                ]);
            }

            // Trigger Email Notification System
            require_once __DIR__ . '/EmailNotificationController.php';
            $emailController = new EmailNotificationController();
            $emailController->processDailyScore($userId, $score, $water, $targetWater);
        }

        $prompt = "Vous êtes une IA d'analyse de santé. L'utilisateur a une journée avec les métriques suivantes:
        - Calories: $calories / $targetCalories kcal
        - Eau: $water / $targetWater L
        - Score global: $score/100

        Générez un message de motivation personnalisé, court (maximum 1 ou 2 phrases), en français, qui donne un retour rapide sur cette performance.
        Retournez UNIQUEMENT un objet JSON valide avec cette structure exacte, sans aucun texte ou markdown autour :
        {
          \"message\": \"votre message ici\"
        }";

        $response = $this->callGroqAPI($prompt);

        if ($response === false) {
            echo json_encode([
                'score' => $score,
                'status' => $status,
                'color' => $color,
                'calories' => $calories,
                'water' => $water,
                'message' => 'Continuez vos efforts pour atteindre vos objectifs quotidiens !'
            ]);
        } else {
            $jsonStr = $this->extractJson($response);
            $aiData = json_decode($jsonStr, true);
            $message = $aiData['message'] ?? 'Continuez vos efforts pour atteindre vos objectifs quotidiens !';
            
            echo json_encode([
                'score' => $score,
                'status' => $status,
                'color' => $color,
                'calories' => $calories,
                'water' => $water,
                'message' => $message
            ]);
        }
    }

    private function extractJson($text) {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false) {
            return substr($text, $start, $end - $start + 1);
        }
        return $text;
    }

    private function callGroqAPI($prompt)
    {
        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a nutrition AI assistant. Respond ONLY with valid JSON. No explanations. No markdown.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.1
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $jsonResponse = json_decode($result, true);
            return $jsonResponse['choices'][0]['message']['content'] ?? false;
        }

        return false;
    }
}

if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === 'AIHealthController.php') {
    $controller = new AIHealthController();
    $controller->handleRequest();
}
?>
