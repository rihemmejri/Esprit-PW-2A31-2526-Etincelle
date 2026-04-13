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
        $sql = "SELECT * FROM objectif ORDER BY date_debut DESC";
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
}
?>
