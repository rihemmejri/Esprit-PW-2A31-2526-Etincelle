<?php
require_once __DIR__ . '/../config.php';

class ChatbotController
{
    private $apiKey;
    private $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model = 'llama-3.1-8b-instant';

    public function __construct() {
        $this->apiKey = $_ENV['GROQ_API_KEY'] ?? '';
    }

    public function handleRequest()
    {
        // Set header to return JSON
        header('Content-Type: application/json');

        // Check if it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Get the input (handle both JSON and form data)
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        $message = $jsonInput['message'] ?? $_POST['message'] ?? '';

        if (empty(trim($message))) {
            echo json_encode(['response' => 'Please enter a food description.', 'reply' => 'Please enter a food description.']);
            return;
        }

        // Call Groq API
        $response = $this->callGroqAPI($message);

        if ($response === false) {
            echo json_encode(['response' => 'Sorry, AI service is unavailable right now.', 'reply' => 'Sorry, AI service is unavailable right now.']);
        } else {
            echo json_encode(['response' => $response, 'reply' => $response]);
        }
    }

    private function callGroqAPI($userMessage)
    {
        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Tu es un assistant nutritionnel. Estime les calories à partir des descriptions d\'aliments. Sois précis mais concis. Réponds obligatoirement en français. Format de réponse : total des calories et une courte explication (optionnel).'
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024
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

// Instantiate and handle the request if this file is called directly
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === 'ChatbotController.php') {
    $controller = new ChatbotController();
    $controller->handleRequest();
}
?>
