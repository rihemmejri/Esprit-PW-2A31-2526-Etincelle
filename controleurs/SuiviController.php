<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../models/suivi.php');
require_once(__DIR__ . '/AlertController.php');
require_once(__DIR__ . '/AIPredictionController.php');

class SuiviController
{
    // Ajouter un suivi
    public function addSuivi(suivi $suivi)
    {
        try {
            $sql = "INSERT INTO suivi (user_id, id_objectif, date, poids, calories_consommees, calories_objectif, calories_restant, eau_bue, eau_objectif) 
                    VALUES (:user_id, :id_objectif, :date, :poids, :calories_consommees, :calories_objectif, :calories_restant, :eau_bue, :eau_objectif)";
            $db = Config::getConnexion();
            
            $query = $db->prepare($sql);
            $result = $query->execute([
                'user_id' => $suivi->getUserId(),
                'id_objectif' => $suivi->getIdObjectif(),
                'date' => $suivi->getDate(),
                'poids' => $suivi->getPoids(),
                'calories_consommees' => $suivi->getCaloriesConsommees(),
                'calories_objectif' => $suivi->getCaloriesObjectif(),
                'calories_restant' => $suivi->getCaloriesRestant(),
                'eau_bue' => $suivi->getEauBue(),
                'eau_objectif' => $suivi->getEauObjectif()
            ]);

            if ($result) {
                $alertController = new AlertController();
                $alertController->processAlerts(
                    $suivi->getUserId(), 
                    $suivi->getDate(), 
                    $suivi->getCaloriesConsommees(), 
                    $suivi->getCaloriesObjectif(), 
                    $suivi->getEauBue(), 
                    $suivi->getEauObjectif()
                );

                // --- AI PREDICTION ---
                $predictionController = new AIPredictionController();
                $predictionController->generatePrediction($suivi->getUserId(), $suivi->getIdObjectif());
                
                // Trigger display
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['new_ai_action'] = true;

                // --- EMAIL NOTIFICATION ---
                require_once __DIR__ . '/EmailNotificationController.php';
                $emailController = new EmailNotificationController();
                
                $calScore = 0;
                if ($suivi->getCaloriesObjectif() > 0) {
                    if ($suivi->getCaloriesConsommees() <= $suivi->getCaloriesObjectif()) {
                        $calScore = 50;
                    } else {
                        $calScore = max(0, ($suivi->getCaloriesObjectif() / $suivi->getCaloriesConsommees()) * 50);
                    }
                }
                $waterScore = 0;
                if ($suivi->getEauObjectif() > 0) {
                    if ($suivi->getEauBue() >= $suivi->getEauObjectif()) {
                        $waterScore = 50;
                    } else {
                        $waterScore = ($suivi->getEauBue() / $suivi->getEauObjectif()) * 50;
                    }
                }
                $score = round($calScore + $waterScore);
                $emailController->processDailyScore($suivi->getUserId(), $score, $suivi->getEauBue(), $suivi->getEauObjectif());

                // Set flag to show AI/Alerts only after action
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['new_ai_action'] = true;
            }

            return $result;
        } catch (Exception $e) {
            error_log('Suivi Add Error: ' . $e->getMessage());
            throw new Exception('Erreur lors de l\'ajout: ' . $e->getMessage());
        }
    }

    // Lister tous les suivis
    public function listSuivis()
    {
        $sql = "SELECT s.*, u.nom, u.prenom, o.poids_cible 
                FROM suivi s 
                INNER JOIN user u ON s.user_id = u.id_user 
                LEFT JOIN objectif o ON s.id_objectif = o.id 
                ORDER BY s.date DESC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $suivisData = $query->fetchAll();

            $suivis = [];
            foreach ($suivisData as $row) {
                $suivi = new suivi(
                    $row['user_id'], 
                    $row['id_objectif'], 
                    $row['date'],
                    $row['poids'],
                    $row['calories_consommees'],
                    $row['calories_objectif'],
                    $row['calories_restant'],
                    $row['eau_bue'],
                    $row['eau_objectif']
                );
                $suivi->setId($row['id']);
                $suivi->setUserName($row['nom'] . ' ' . $row['prenom']);
                $suivi->setPoidsCible($row['poids_cible'] ?? 'N/A');
                $suivis[] = $suivi;
            }
            return $suivis;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer un suivi par ID
    public function getSuiviById($id)
    {
        $sql = "SELECT * FROM suivi WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $suivi = new suivi(
                    $row['user_id'],
                    $row['id_objectif'],
                    $row['date'],
                    $row['poids'],
                    $row['calories_consommees'],
                    $row['calories_objectif'],
                    $row['calories_restant'],
                    $row['eau_bue'],
                    $row['eau_objectif']
                );
                $suivi->setId($row['id']);
                return $suivi;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour un suivi
    public function updateSuivi(suivi $suivi)
    {
        $sql = "UPDATE suivi 
                SET user_id = :user_id, 
                    id_objectif = :id_objectif, 
                    date = :date,
                    poids = :poids,
                    calories_consommees = :calories_consommees,
                    calories_objectif = :calories_objectif,
                    calories_restant = :calories_restant,
                    eau_bue = :eau_bue,
                    eau_objectif = :eau_objectif
                WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'id' => $suivi->getId(),
                'user_id' => $suivi->getUserId(),
                'id_objectif' => $suivi->getIdObjectif(),
                'date' => $suivi->getDate(),
                'poids' => $suivi->getPoids(),
                'calories_consommees' => $suivi->getCaloriesConsommees(),
                'calories_objectif' => $suivi->getCaloriesObjectif(),
                'calories_restant' => $suivi->getCaloriesRestant(),
                'eau_bue' => $suivi->getEauBue(),
                'eau_objectif' => $suivi->getEauObjectif()
            ]);

            if ($result) {
                $alertController = new AlertController();
                $alertController->processAlerts(
                    $suivi->getUserId(), 
                    $suivi->getDate(), 
                    $suivi->getCaloriesConsommees(), 
                    $suivi->getCaloriesObjectif(), 
                    $suivi->getEauBue(), 
                    $suivi->getEauObjectif()
                );

                // --- AI PREDICTION ---
                $predictionController = new AIPredictionController();
                $predictionController->generatePrediction($suivi->getUserId(), $suivi->getIdObjectif());

                // --- EMAIL NOTIFICATION ---
                require_once __DIR__ . '/EmailNotificationController.php';
                $emailController = new EmailNotificationController();
                
                $calScore = 0;
                if ($suivi->getCaloriesObjectif() > 0) {
                    if ($suivi->getCaloriesConsommees() <= $suivi->getCaloriesObjectif()) {
                        $calScore = 50;
                    } else {
                        $calScore = max(0, ($suivi->getCaloriesObjectif() / $suivi->getCaloriesConsommees()) * 50);
                    }
                }
                $waterScore = 0;
                if ($suivi->getEauObjectif() > 0) {
                    if ($suivi->getEauBue() >= $suivi->getEauObjectif()) {
                        $waterScore = 50;
                    } else {
                        $waterScore = ($suivi->getEauBue() / $suivi->getEauObjectif()) * 50;
                    }
                }
                $score = round($calScore + $waterScore);
                $emailController->processDailyScore($suivi->getUserId(), $score, $suivi->getEauBue(), $suivi->getEauObjectif());

                // Set flag to show AI/Alerts only after action
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['new_ai_action'] = true;
            }

            return $result;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer un suivi
    public function deleteSuivi($id)
    {
        $sql = "DELETE FROM suivi WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            return $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Récupérer le dernier poids enregistré pour un objectif donné
    public function getLatestPoidsForObjectif($id_objectif)
    {
        $sql = "SELECT poids FROM suivi WHERE id_objectif = :id_objectif ORDER BY date DESC, id DESC LIMIT 1";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_objectif' => $id_objectif]);
            $row = $query->fetch();
            return $row ? $row['poids'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get all users for selection dropdown
     */
    public function getUsers()
    {
        $db = Config::getConnexion();
        $query = $db->prepare("SELECT DISTINCT u.id_user, u.nom, u.prenom FROM user u JOIN suivi s ON u.id_user = s.user_id ORDER BY u.nom ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les statistiques des suivis
    public function getStats($suivis = null)
    {
        if ($suivis === null) {
            $suivis = $this->listSuivis();
        }
        
        $total = count($suivis);
        $avgCaloriesConsommees = 0;
        $avgEauBue = 0;
        $maxPoids = 0;
        
        // Advanced Analytics
        $calSuccessCount = 0;
        $waterSuccessCount = 0;
        $heatmap = [];
        $patterns = ['weekend' => ['cal' => 0, 'count' => 0], 'weekday' => ['cal' => 0, 'count' => 0]];
        
        if ($total > 0) {
            $sumCalories = 0;
            $sumEau = 0;
            foreach ($suivis as $s) {
                $sumCalories += $s->getCaloriesConsommees();
                $sumEau += $s->getEauBue();
                if ($s->getPoids() > $maxPoids) {
                    $maxPoids = $s->getPoids();
                }

                // Adherence
                $isCalMet = $s->getCaloriesConsommees() <= $s->getCaloriesObjectif();
                $isWaterMet = $s->getEauBue() >= $s->getEauObjectif();
                if ($isCalMet) $calSuccessCount++;
                if ($isWaterMet) $waterSuccessCount++;

                // Heatmap Status
                $status = 'red';
                if ($isCalMet && $isWaterMet) $status = 'green';
                elseif ($isCalMet || $isWaterMet) $status = 'orange';
                $heatmap[] = ['date' => $s->getDate(), 'status' => $status];

                // Weekend vs Weekday Pattern
                $dayOfWeek = date('N', strtotime($s->getDate()));
                if ($dayOfWeek >= 6) {
                    $patterns['weekend']['cal'] += $s->getCaloriesConsommees();
                    $patterns['weekend']['count']++;
                } else {
                    $patterns['weekday']['cal'] += $s->getCaloriesConsommees();
                    $patterns['weekday']['count']++;
                }
            }
            $avgCaloriesConsommees = round($sumCalories / $total);
            $avgEauBue = round($sumEau / $total, 1);
        }

        return [
            'total' => $total,
            'avgCaloriesConsommees' => $avgCaloriesConsommees,
            'avgEauBue' => $avgEauBue,
            'maxPoids' => $maxPoids,
            'calRate' => $total > 0 ? round(($calSuccessCount / $total) * 100) : 0,
            'waterRate' => $total > 0 ? round(($waterSuccessCount / $total) * 100) : 0,
            'heatmap' => $heatmap,
            'globalScore' => $total > 0 ? round((($calSuccessCount / $total) * 50) + (($waterSuccessCount / $total) * 50)) : 0
        ];
    }

    // Recherche avancée, Tri et Filtrage pour les suivis
    public function advancedSearch($searchTerm = '', $sortBy = 'date', $sortOrder = 'DESC', $dateMin = '', $dateMax = '')
    {
        $sql = "SELECT s.*, u.nom, u.prenom, o.poids_cible 
                FROM suivi s 
                INNER JOIN user u ON s.user_id = u.id_user 
                LEFT JOIN objectif o ON s.id_objectif = o.id 
                WHERE 1=1";
                
        $params = [];
        
        // Filtrage par recherche textuelle (nom, prenom, poids cible, ou valeurs du suivi)
        if (!empty($searchTerm)) {
            $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR s.poids LIKE :search OR s.calories_consommees LIKE :search)";
            $params['search'] = "%$searchTerm%";
        }
        
        // Filtrage par date
        if (!empty($dateMin)) {
            $sql .= " AND s.date >= :dateMin";
            $params['dateMin'] = $dateMin;
        }
        
        if (!empty($dateMax)) {
            $sql .= " AND s.date <= :dateMax";
            $params['dateMax'] = $dateMax;
        }
        
        // Tri sécurisé
        $allowedSortColumns = ['poids', 'calories_consommees', 'eau_bue', 'date', 'nom'];
        $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'date';
        
        if ($sortBy === 'nom') $sortBy = 'u.nom';
        else $sortBy = 's.' . $sortBy;
        
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY $sortBy $sortOrder";
        
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            $suivisData = $query->fetchAll();

            $suivis = [];
            foreach ($suivisData as $row) {
                $suivi = new suivi(
                    $row['user_id'], 
                    $row['id_objectif'], 
                    $row['date'],
                    $row['poids'],
                    $row['calories_consommees'],
                    $row['calories_objectif'],
                    $row['calories_restant'],
                    $row['eau_bue'],
                    $row['eau_objectif']
                );
                $suivi->setId($row['id']);
                $suivi->setUserName($row['nom'] . ' ' . $row['prenom']);
                $suivi->setPoidsCible($row['poids_cible'] ?? 'N/A');
                $suivis[] = $suivi;
            }
            return $suivis;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }
}
?>
