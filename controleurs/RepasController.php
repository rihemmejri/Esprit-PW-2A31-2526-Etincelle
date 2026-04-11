<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/repas.php');

class RepasController
{
    // Ajouter un repas
    public function addRepas(repas $repas)
    {
        $sql = "INSERT INTO repas (nom, type, calories, proteines, glucides, lipides) 
                VALUES (:nom, :type, :calories, :proteines, :glucides, :lipides)";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $repas->getNom(),
                'type' => $repas->getType(),
                'calories' => $repas->getCalories(),
                'proteines' => $repas->getProteines(),
                'glucides' => $repas->getGlucides(),
                'lipides' => $repas->getLipides()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Lister tous les repas
    public function listRepas()
    {
        $sql = "SELECT * FROM repas ORDER BY nom ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $repasData = $query->fetchAll();

            $repas = [];
            foreach ($repasData as $row) {
                $repasObj = new repas(
                    $row['nom'], 
                    $row['type'], 
                    $row['calories'],
                    $row['proteines'],
                    $row['glucides'],
                    $row['lipides']
                );
                $repasObj->setIdRepas($row['id_repas']);
                $repas[] = $repasObj;
            }
            return $repas;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer un repas par ID (retourne un objet repas)
    public function getRepasById($id)
    {
        $sql = "SELECT * FROM repas WHERE id_repas = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $repas = new repas(
                    $row['nom'],
                    $row['type'],
                    $row['calories'],
                    $row['proteines'],
                    $row['glucides'],
                    $row['lipides']
                );
                $repas->setIdRepas($row['id_repas']);
                return $repas;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour un repas
    public function updateRepas(repas $repas)
    {
        $sql = "UPDATE repas 
                SET nom = :nom, 
                    type = :type, 
                    calories = :calories,
                    proteines = :proteines,
                    glucides = :glucides,
                    lipides = :lipides
                WHERE id_repas = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $repas->getIdRepas(),
                'nom' => $repas->getNom(),
                'type' => $repas->getType(),
                'calories' => $repas->getCalories(),
                'proteines' => $repas->getProteines(),
                'glucides' => $repas->getGlucides(),
                'lipides' => $repas->getLipides()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer un repas
    public function deleteRepas($id)
    {
        $sql = "DELETE FROM repas WHERE id_repas = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
}
?>