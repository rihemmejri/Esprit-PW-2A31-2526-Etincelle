<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../models/AIPrediction.php');

if (!class_exists('AIPredictionController')) {
    class AIPredictionController
    {
        private $apiKey;

        public function __construct() {
            $this->apiKey = getenv('GROQ_API_KEY_PREDICTION');
        }
        private $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
        private $model = 'llama-3.1-8b-instant';

        /**
         * Generate a new AI prediction for the user based on last 5 days of data for a specific objective
         */
        public function generatePrediction($userId, $objectifId = null)
        {
            $db = Config::getConnexion();
            $today = date('Y-m-d');

            // 1. Check if prediction already exists for today
            $sqlCheck = "SELECT COUNT(*) FROM ai_prediction WHERE user_id = :user_id AND date = :date";
            $queryCheck = $db->prepare($sqlCheck);
            $queryCheck->execute(['user_id' => $userId, 'date' => $today]);
            if ($queryCheck->fetchColumn() > 0) {
                $this->deletePrediction($userId, $today);
            }

            // 2. Fetch last 5 days of tracking data FILTERED BY OBJECTIVE if provided
            $sqlData = "SELECT calories_consommees, calories_objectif, eau_bue 
                        FROM suivi 
                        WHERE user_id = :user_id ";
            
            $params = ['user_id' => $userId];
            if ($objectifId) {
                $sqlData .= " AND id_objectif = :objectif_id ";
                $params['objectif_id'] = $objectifId;
            }
            
            $sqlData .= " ORDER BY date DESC LIMIT 5";
            
            $queryData = $db->prepare($sqlData);
            $queryData->execute($params);
            $history = $queryData->fetchAll(PDO::FETCH_ASSOC);

            if (count($history) < 3) {
                return "Not enough data to generate prediction.";
            }

            // Reverse to get chronological order [oldest -> newest]
            $history = array_reverse($history);

            $calories = array_column($history, 'calories_consommees');
            $objectifs = array_column($history, 'calories_objectif');
            $eau = array_column($history, 'eau_bue');
            $avgGoal = array_sum($objectifs) / count($objectifs);

            $inputData = [
                'latest_calories' => end($calories),
                'history_calories' => $calories,
                'objectif' => $avgGoal,
                'eau' => $eau
            ];

            // 3. Prepare AI Prompt - Refined to focus on the LATEST action
            $prompt = "Analyse les données de santé de l'utilisateur. 
            DERNIER ENREGISTREMENT (Aujourd'hui): " . end($calories) . " kcal (Objectif: " . $avgGoal . ").
            Historique des 5 derniers jours: [" . implode(', ', $calories) . "].
            Eau consommée: [" . implode(', ', $eau) . "].

            Tâches:
            1. Félicite ou conseille l'utilisateur spécifiquement sur son DERNIER enregistrement.
            2. Détecte si cet enregistrement améliore ou aggrave la tendance.
            3. Prédit si l'utilisateur tiendra ses objectifs.
            4. Donne un conseil court en Français.
            5. Risque: BAS, MOYEN, ou ÉLEVÉ.

            Réponds UNIQUEMENT en JSON:
            {
                \"prediction\": \"Ton analyse ici en Français, en commençant par commenter la saisie d'aujourd'hui\",
                \"risk_level\": \"BAS | MOYEN | ÉLEVÉ\"
            }";

            // 4. Call Groq API
            $aiResponse = $this->callGroqAPI($prompt);
            
            if (!$aiResponse) {
                return "AI prediction is currently unavailable.";
            }

            $result = json_decode($aiResponse, true);
            
            // Extract prediction text
            $predictionText = $result['prediction'] ?? (is_array($result) ? ($result['message'] ?? $aiResponse) : $aiResponse);
            
            // Extract and normalize risk level
            $riskLevel = 'MOYEN'; // Default
            if (isset($result['risk_level'])) {
                $riskLevel = strtoupper(trim($result['risk_level']));
            } elseif (isset($result['risk'])) {
                $riskLevel = strtoupper(trim($result['risk']));
            }
            
            // Ensure it matches our expected values
            if (!in_array($riskLevel, ['BAS', 'MOYEN', 'ÉLEVÉ', 'ELEVE'])) {
                if (strpos($riskLevel, 'BA') !== false) $riskLevel = 'BAS';
                elseif (strpos($riskLevel, 'ELE') !== false || strpos($riskLevel, 'HIGH') !== false) $riskLevel = 'ÉLEVÉ';
                else $riskLevel = 'MOYEN';
            }
            
            // Uniformize "ELEVE" to "ÉLEVÉ"
            if ($riskLevel === 'ELEVE') $riskLevel = 'ÉLEVÉ';

            // 5. Store in Database
            $sqlInsert = "INSERT INTO ai_prediction (user_id, date, input_data, prediction, risk_level) 
                          VALUES (:user_id, :date, :input_data, :prediction, :risk_level)";
            try {
                $queryInsert = $db->prepare($sqlInsert);
                $queryInsert->execute([
                    'user_id' => $userId,
                    'date' => $today,
                    'input_data' => json_encode($inputData),
                    'prediction' => $predictionText,
                    'risk_level' => $riskLevel
                ]);
                return true;
            } catch (Exception $e) {
                return "Error storing prediction: " . $e->getMessage();
            }
        }

        /**
         * Fetch the latest prediction for a user
         */
        public function getLatestPrediction($userId)
        {
            $db = Config::getConnexion();
            $sql = "SELECT * FROM ai_prediction WHERE user_id = :user_id ORDER BY date DESC, id DESC LIMIT 1";
            try {
                $query = $db->prepare($sql);
                $query->execute(['user_id' => $userId]);
                return $query->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                return null;
            }
        }

        /**
         * Generate a prediction filtered by a specific objective
         */
        public function generatePredictionByObjectif($userId, $objectifId)
        {
            $db = Config::getConnexion();
            
            // Fetch data associated with this specific objective
            $sqlData = "SELECT calories_consommees, calories_objectif, eau_bue 
                        FROM suivi 
                        WHERE user_id = :user_id AND id_objectif = :objectif_id
                        ORDER BY date DESC 
                        LIMIT 7";
            $queryData = $db->prepare($sqlData);
            $queryData->execute(['user_id' => $userId, 'objectif_id' => $objectifId]);
            $history = $queryData->fetchAll(PDO::FETCH_ASSOC);

            if (count($history) < 2) {
                return ["error" => "Pas assez de suivis pour cet objectif (minimum 2 requis)."];
            }

            // Prepare prompt and call API (similar to general prediction)
            $calories = array_reverse(array_column($history, 'calories_consommees'));
            $avgGoal = $history[0]['calories_objectif'];

            $prompt = "Analyse cet objectif spécifique:
            Calories consommées: [" . implode(', ', $calories) . "]
            Objectif: " . $avgGoal . "
            
            Donne un conseil court et motivant en Français pour cet objectif précis. 
            Inclus aussi un niveau de risque: BAS, MOYEN, ou ÉLEVÉ.
            Réponds uniquement en JSON: {\"prediction\": \"...\", \"risk_level\": \"...\"}";

            $aiResponse = $this->callGroqAPI($prompt);
            return json_decode($aiResponse, true) ?: ["prediction" => $aiResponse, "risk_level" => "MOYEN"];
        }

        /**
         * AI Product Recommendations based on Eco-Score
         */
        public function getProductRecommendations($products) {
            if (empty($products)) return [];

            // Prepare product list for AI
            $productList = [];
            foreach ($products as $p) {
                $productList[] = [
                    "id" => $p->getIdProduit(),
                    "nom" => $p->getNom(),
                    "eco_score" => $p->getEcoScore(),
                    "origine" => $p->getOrigine()
                ];
            }

            // Sort by eco_score descending and take top 10 to limit token usage
            usort($productList, fn($a, $b) => $b['eco_score'] <=> $a['eco_score']);
            $topProducts = array_slice($productList, 0, 10);

            $prompt = "Analyse cette liste de produits alimentaires et sélectionne les 3 MEILLEURS produits en te basant UNIQUEMENT sur leur Eco-Score (plus haut est mieux).
            Produits: " . json_encode($topProducts) . "

            Réponds UNIQUEMENT en JSON sous ce format:
            {
                \"recommendations\": [
                    {\"id\": 1, \"nom\": \"Nom du produit\", \"reason\": \"Explication courte en Français pourquoi ce produit est top\"},
                    ...
                ],
                \"global_advice\": \"Un conseil général court sur la consommation durable\"
            }";

            $aiResponse = $this->callGroqAPI($prompt);
            return json_decode($aiResponse, true);
        }

        /**
         * NutriBot - Shopping Assistant
         * Parses user input to perform marketplace actions
         */
        public function processShoppingCommand($userInput, $products) {
            $productList = [];
            foreach ($products as $p) {
                $productList[] = [
                    "id" => $p->getIdProduit(), 
                    "nom" => $p->getNom(), 
                    "prix" => $p->getPrix(), 
                    "eco_score" => $p->getEcoScore()
                ];
            }

            $prompt = "Tu es NutriBot, l'assistant expert de NutriLoop.
            L'utilisateur dit: \"$userInput\"
            Produits: " . json_encode($productList) . "

            RÈGLES CRITIQUES:
            1. Si l'user mentionne un PRIX ou 'pas cher' ou 'moins de X DT' -> Action: 'filter_price', max_price: X.
            2. Si l'user mentionne 'sain', 'eco', 'durable' or 'bio' -> Action: 'filter_eco', min_score: 80.
            3. Si l'user veut ACHETER ou AJOUTER -> Action: 'add', product_id: ID.
            4. Si l'user CHERCHE un nom précis -> Action: 'search', query: 'nom'.
            5. Ne mets JAMAIS une phrase entière dans 'query'.

            EXEMPLES:
            - 'moins de 5 DT' -> {\"action\": \"filter_price\", \"max_price\": 5}
            - 'ajoute tomate' -> {\"action\": \"add\", \"product_id\": 12}
            - 'produits bio' -> {\"action\": \"filter_eco\", \"min_score\": 80}

            RÉPONDS UNIQUEMENT EN JSON:
            {
                \"action\": \"search\" | \"filter_eco\" | \"filter_price\" | \"add\" | \"cart_status\" | \"chat\",
                \"query\": \"...\",
                \"min_score\": 0,
                \"max_price\": 0,
                \"product_id\": 0,
                \"message\": \"Réponse courte en Français\"
            }";

            $aiResponse = $this->callGroqAPI($prompt);
            $cleanJson = preg_replace('/```json\n?|\n?```/', '', $aiResponse);
            return json_decode(trim($cleanJson), true);
        }


        private function deletePrediction($userId, $date)
        {
            $db = Config::getConnexion();
            $sql = "DELETE FROM ai_prediction WHERE user_id = :user_id AND date = :date";
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $userId, 'date' => $date]);
        }

        private function callGroqAPI($prompt)
        {
            $data = [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a nutrition AI assistant. Analyze eating behavior and predict risks. Respond ONLY with valid JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.2
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
}
