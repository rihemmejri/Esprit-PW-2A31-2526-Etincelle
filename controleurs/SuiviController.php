<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../models/suivi.php');

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
            return $result;
        } catch (Exception $e) {
            error_log('Suivi Add Error: ' . $e->getMessage());
            throw new Exception('Erreur lors de l\'ajout: ' . $e->getMessage());
        }
    }

    // Lister tous les suivis
    public function listSuivis()
    {
        $sql = "SELECT * FROM suivi ORDER BY date DESC";
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
            return $query->execute([
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
}
?>
