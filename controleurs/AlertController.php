<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../models/alert.php');

if (!class_exists('AlertController')) {
    class AlertController
    {
        /**
         * Add a new alert to the database
         */
        public function addAlert(Alert $alert)
        {
            // Avoid duplicates: check if an alert with same user_id, type, categorie, message and date (day) exists
            if ($this->alertExists($alert)) {
                return false;
            }

            $sql = "INSERT INTO alert (user_id, type, categorie, message, date) 
                    VALUES (:user_id, :type, :categorie, :message, :date)";
            $db = Config::getConnexion();
            try {
                $query = $db->prepare($sql);
                return $query->execute([
                    'user_id' => $alert->getUserId(),
                    'type' => $alert->getType(),
                    'categorie' => $alert->getCategorie(),
                    'message' => $alert->getMessage(),
                    'date' => $alert->getDate()
                ]);
            } catch (Exception $e) {
                error_log('Alert Add Error: ' . $e->getMessage());
                return false;
            }
        }

        /**
         * Check if alert already exists for the same day
         */
        private function alertExists(Alert $alert)
        {
            $sql = "SELECT COUNT(*) FROM alert 
                    WHERE user_id = :user_id 
                    AND type = :type 
                    AND categorie = :categorie 
                    AND message = :message 
                    AND DATE(date) = DATE(:date)";
            $db = Config::getConnexion();
            try {
                $query = $db->prepare($sql);
                $query->execute([
                    'user_id' => $alert->getUserId(),
                    'type' => $alert->getType(),
                    'categorie' => $alert->getCategorie(),
                    'message' => $alert->getMessage(),
                    'date' => $alert->getDate()
                ]);
                return $query->fetchColumn() > 0;
            } catch (Exception $e) {
                return false;
            }
        }

        /**
         * Process and generate alerts for a given set of metrics
         */
        public function processAlerts($userId, $date, $calories_consommees, $calories_objectif, $eau_bue, $eau_objectif)
        {
            // --- CALORIE ALERT ---
            if ($calories_objectif > 0) {
                $ratio = $calories_consommees / $calories_objectif;
                if ($ratio > 1.3) {
                    $this->addAlert(new Alert($userId, 'CRITICAL', 'CALORIES', "Vous avez largement dépassé votre objectif de calories ({$calories_consommees} kcal)", $date));
                } elseif ($ratio > 1) {
                    $this->addAlert(new Alert($userId, 'WARNING', 'CALORIES', "Vous avez légèrement dépassé votre objectif de calories ({$calories_consommees} kcal)", $date));
                }
            }

            // --- HYDRATION ALERT ---
            if ($eau_bue < $eau_objectif) {
                $this->addAlert(new Alert($userId, 'WARNING', 'HYDRATION', "Vous devez boire plus d'eau (Actuel: {$eau_bue}L)", $date));
            }

            // --- 3-DAY CONSECUTIVE ALERT & IMPROVEMENT ALERT ---
            $db = Config::getConnexion();
            $sqlHistory = "SELECT calories_consommees, calories_objectif, date FROM suivi 
                           WHERE user_id = :user_id 
                           ORDER BY date DESC LIMIT 3";
            $queryHistory = $db->prepare($sqlHistory);
            $queryHistory->execute(['user_id' => $userId]);
            $history = $queryHistory->fetchAll(PDO::FETCH_ASSOC);

            if (count($history) === 3) {
                // Consecutive Overconsumption
                $consecutiveOver = true;
                foreach ($history as $day) {
                    if ($day['calories_consommees'] <= $day['calories_objectif']) {
                        $consecutiveOver = false;
                        break;
                    }
                }
                if ($consecutiveOver) {
                    $this->addAlert(new Alert($userId, 'CRITICAL', 'CALORIES', "Vous avez dépassé votre objectif de calories pendant 3 jours consécutifs", $date));
                }

                // Improvement Trend
                if ($history[2]['calories_consommees'] > $history[1]['calories_consommees'] && 
                    $history[1]['calories_consommees'] > $history[0]['calories_consommees']) {
                    $this->addAlert(new Alert($userId, 'SUCCESS', 'CALORIES', "Bravo ! Votre consommation s'améliore", $date));
                }
            }
        }

        /**
         * Fetch alerts for a user
         */
        public function getAlertsByUser($userId)
        {
            $sql = "SELECT * FROM alert WHERE user_id = :user_id ORDER BY id DESC";
            $db = Config::getConnexion();
            try {
                $query = $db->prepare($sql);
                $query->execute(['user_id' => $userId]);
                return $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                return [];
            }
        }

        /**
         * Mark alert as read
         */
        public function markAsRead($alertId)
        {
            $sql = "UPDATE alert SET lu = TRUE WHERE id = :id";
            $db = Config::getConnexion();
            try {
                $query = $db->prepare($sql);
                return $query->execute(['id' => $alertId]);
            } catch (Exception $e) {
                return false;
            }
        }
    }
}
?>
