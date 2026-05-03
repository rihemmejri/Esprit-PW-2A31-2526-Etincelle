<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../models/objectif.php');

class ObjectifController
{
    // Ajouter un objectif
    public function addObjectif(objectif $objectif)
    {
        try {
            $sql = "INSERT INTO objectif (user_id, poids_cible, calories_objectif, eau_objectif, date_debut, date_fin) 
                    VALUES (:user_id, :poids_cible, :calories_objectif, :eau_objectif, :date_debut, :date_fin)";
            $db = Config::getConnexion();
            
            $query = $db->prepare($sql);
            $result = $query->execute([
                'user_id' => $objectif->getUserId(),
                'poids_cible' => $objectif->getPoidsCible(),
                'calories_objectif' => $objectif->getCaloriesObjectif(),
                'eau_objectif' => $objectif->getEauObjectif(),
                'date_debut' => $objectif->getDateDebut(),
                'date_fin' => $objectif->getDateFin()
            ]);
            return $result;
        } catch (Exception $e) {
            error_log('Objectif Add Error: ' . $e->getMessage());
            throw new Exception('Erreur lors de l\'ajout: ' . $e->getMessage());
        }
    }

    // Lister tous les objectifs
    public function listObjectifs()
    {
        $sql = "SELECT o.*, u.nom, u.prenom 
                FROM objectif o 
                INNER JOIN user u ON o.user_id = u.id_user 
                ORDER BY o.date_debut DESC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $objectifsData = $query->fetchAll();

            $objectifs = [];
            foreach ($objectifsData as $row) {
                $objectif = new objectif(
                    $row['user_id'], 
                    $row['poids_cible'], 
                    $row['calories_objectif'],
                    $row['eau_objectif'],
                    $row['date_debut'],
                    $row['date_fin']
                );
                $objectif->setId($row['id']);
                $objectif->setUserName($row['nom'] . ' ' . $row['prenom']);
                $objectifs[] = $objectif;
            }
            return $objectifs;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer un objectif par ID
    public function getObjectifById($id)
    {
        $sql = "SELECT * FROM objectif WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $objectif = new objectif(
                    $row['user_id'],
                    $row['poids_cible'],
                    $row['calories_objectif'],
                    $row['eau_objectif'],
                    $row['date_debut'],
                    $row['date_fin']
                );
                $objectif->setId($row['id']);
                return $objectif;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour un objectif
    public function updateObjectif(objectif $objectif)
    {
        if ($objectif->getDateDebut() > date('Y-m-d')) {
            throw new Exception("La date de début ne peut pas être dans le futur.");
        }
        $sql = "UPDATE objectif 
                SET user_id = :user_id, 
                    poids_cible = :poids_cible, 
                    calories_objectif = :calories_objectif,
                    eau_objectif = :eau_objectif,
                    date_debut = :date_debut,
                    date_fin = :date_fin
                WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'id' => $objectif->getId(),
                'user_id' => $objectif->getUserId(),
                'poids_cible' => $objectif->getPoidsCible(),
                'calories_objectif' => $objectif->getCaloriesObjectif(),
                'eau_objectif' => $objectif->getEauObjectif(),
                'date_debut' => $objectif->getDateDebut(),
                'date_fin' => $objectif->getDateFin()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer un objectif
    public function deleteObjectif($id)
    {
        $sql = "DELETE FROM objectif WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            return $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Get all users who have objectives
     */
    public function getUsers()
    {
        $db = Config::getConnexion();
        $query = $db->prepare("SELECT DISTINCT u.id_user, u.nom, u.prenom FROM user u JOIN objectif o ON u.id_user = o.user_id ORDER BY u.nom ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtenir les statistiques des objectifs (Version SMART Dashboard)
    public function getStats($objectifs = null)
    {
        if ($objectifs === null) {
            $objectifs = $this->listObjectifs();
        }
        
        $total = count($objectifs);
        $db = Config::getConnexion();
        
        // Advanced Metrics
        $totalWeightProgress = 0;
        $totalCalSuccess = 0;
        $totalWaterSuccess = 0;
        $totalDaysAnalyzed = 0;
        $sumCalDiff = 0;
        $sumWaterDiff = 0;
        $trendLabels = [];
        $trendCal = [];
        $trendWater = [];
        $insights = [];

        if ($total > 0) {
            // Get user IDs from the listed objectives
            $userIds = array_unique(array_map(function($o) { return $o->getUserId(); }, $objectifs));
            $idsString = implode(',', $userIds);

            // Fetch recent suivi data for these users.
            $sql = "SELECT s.* 
                    FROM suivi s 
                    WHERE s.user_id IN ($idsString) 
                    ORDER BY s.date ASC";
            $query = $db->prepare($sql);
            $query->execute();
            $suivis = $query->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($suivis)) {
                $totalDaysAnalyzed = count($suivis);
                $startWeight = $suivis[0]['poids'];
                $currentWeight = end($suivis)['poids'];
                
                // Use target from the latest objective
                $targetWeight = end($objectifs)->getPoidsCible();

                if ($startWeight != $targetWeight) {
                    $totalWeightProgress = round((($startWeight - $currentWeight) / ($startWeight - $targetWeight)) * 100);
                }

                $heatmap = [];
                foreach ($suivis as $s) {
                    // Try to match with the corresponding objective target for each day
                    $dayCalObj = $s['calories_objectif'];
                    $dayWaterObj = $s['eau_objectif'];
                    
                    $isCalMet = $s['calories_consommees'] <= $dayCalObj;
                    $isWaterMet = $s['eau_bue'] >= $dayWaterObj;
                    
                    if ($isCalMet) $totalCalSuccess++;
                    if ($isWaterMet) $totalWaterSuccess++;

                    $sumCalDiff += ($s['calories_consommees'] - $dayCalObj);
                    $sumWaterDiff += ($s['eau_bue'] - $dayWaterObj);

                    // Heatmap Status
                    $status = 'red';
                    if ($isCalMet && $isWaterMet) $status = 'green';
                    elseif ($isCalMet || $isWaterMet) $status = 'orange';
                    $heatmap[] = ['date' => $s['date'], 'status' => $status];
                }

                // Last 7 days for trend
                $recent = array_slice($suivis, -7);
                $trendLabels = array_column($recent, 'date');
                $trendCal = array_column($recent, 'calories_consommees');
                $trendWater = array_column($recent, 'eau_bue');
            }
        }
        
        return [
            'total' => $total,
            'weightProgress' => max(0, min(100, $totalWeightProgress)),
            'calRate' => $totalDaysAnalyzed > 0 ? round(($totalCalSuccess / $totalDaysAnalyzed) * 100) : 0,
            'waterRate' => $totalDaysAnalyzed > 0 ? round(($totalWaterSuccess / $totalDaysAnalyzed) * 100) : 0,
            'avgCalDiff' => $totalDaysAnalyzed > 0 ? round($sumCalDiff / $totalDaysAnalyzed) : 0,
            'avgWaterDiff' => $totalDaysAnalyzed > 0 ? round($sumWaterDiff / $totalDaysAnalyzed, 1) : 0,
            'trend' => ['labels' => $trendLabels, 'cal' => $trendCal, 'water' => $trendWater],
            'heatmap' => $heatmap ?? []
        ];
    }

    // Recherche avancée, Tri et Filtrage
    public function advancedSearch($searchTerm = '', $sortBy = 'date_debut', $sortOrder = 'DESC', $dateMin = '', $dateMax = '')
    {
        $sql = "SELECT o.*, u.nom, u.prenom 
                FROM objectif o 
                INNER JOIN user u ON o.user_id = u.id_user 
                WHERE 1=1";
                
        $params = [];
        
        // Filtrage par recherche textuelle
        if (!empty($searchTerm)) {
            $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR o.poids_cible LIKE :search OR o.calories_objectif LIKE :search)";
            $params['search'] = "%$searchTerm%";
        }
        
        // Filtrage par date
        if (!empty($dateMin)) {
            $sql .= " AND o.date_debut >= :dateMin";
            $params['dateMin'] = $dateMin;
        }
        
        if (!empty($dateMax)) {
            $sql .= " AND o.date_debut <= :dateMax";
            $params['dateMax'] = $dateMax;
        }
        
        // Tri sécurisé
        $allowedSortColumns = ['poids_cible', 'calories_objectif', 'eau_objectif', 'date_debut', 'date_fin', 'nom'];
        $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'date_debut';
        if ($sortBy === 'nom') $sortBy = 'u.nom';
        else $sortBy = 'o.' . $sortBy;
        
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY $sortBy $sortOrder";
        
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            $objectifsData = $query->fetchAll();

            $objectifs = [];
            foreach ($objectifsData as $row) {
                $objectif = new objectif(
                    $row['user_id'], 
                    $row['poids_cible'], 
                    $row['calories_objectif'],
                    $row['eau_objectif'],
                    $row['date_debut'],
                    $row['date_fin']
                );
                $objectif->setId($row['id']);
                $objectif->setUserName($row['nom'] . ' ' . $row['prenom']);
                $objectifs[] = $objectif;
            }
            return $objectifs;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }
}
?>
