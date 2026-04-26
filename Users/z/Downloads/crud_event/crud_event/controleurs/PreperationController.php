<?php
// controleurs/PreperationController.php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../models/preperation.php');
class PreperationController
{
    // Ajouter une étape de préparation
    public function addPreperation(Preperation $preperation)
    {
        $sql = "INSERT INTO preperation (ordre, instruction, duree, temperature, type_action, outil_utilise, quantite_ingredient, astuce, id_recette) 
                VALUES (:ordre, :instruction, :duree, :temperature, :type_action, :outil_utilise, :quantite_ingredient, :astuce, :id_recette)";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'ordre' => $preperation->getOrdre(),
                'instruction' => $preperation->getInstruction(),
                'duree' => $preperation->getDuree(),
                'temperature' => $preperation->getTemperature(),
                'type_action' => $preperation->getTypeAction(),
                'outil_utilise' => $preperation->getOutilUtilise(),
                'quantite_ingredient' => $preperation->getQuantiteIngredient(),
                'astuce' => $preperation->getAstuce(),
                'id_recette' => $preperation->getIdRecette()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Lister toutes les étapes avec jointure sur recette
    public function listPreperations()
    {
        $sql = "SELECT p.*, r.nom as recette_nom 
                FROM preperation p 
                LEFT JOIN recette r ON p.id_recette = r.id_recette 
                ORDER BY r.nom ASC, p.ordre ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $preperationsData = $query->fetchAll();

            $preperations = [];
            foreach ($preperationsData as $row) {
                $preperation = new Preperation(
                    $row['ordre'],
                    $row['instruction'],
                    $row['duree'],
                    $row['temperature'],
                    $row['type_action'],
                    $row['outil_utilise'],
                    $row['quantite_ingredient'],
                    $row['astuce'],
                    $row['id_recette']
                );
                $preperation->setIdEtape($row['id_etape']);
                $preperation->setRecetteNom($row['recette_nom']);
                $preperations[] = $preperation;
            }
            return $preperations;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer les étapes par recette
    public function getPreperationsByRecette($id_recette)
    {
        $sql = "SELECT p.*, r.nom as recette_nom 
                FROM preperation p 
                LEFT JOIN recette r ON p.id_recette = r.id_recette 
                WHERE p.id_recette = :id_recette 
                ORDER BY p.ordre ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_recette' => $id_recette]);
            $preperationsData = $query->fetchAll();

            $preperations = [];
            foreach ($preperationsData as $row) {
                $preperation = new Preperation(
                    $row['ordre'],
                    $row['instruction'],
                    $row['duree'],
                    $row['temperature'],
                    $row['type_action'],
                    $row['outil_utilise'],
                    $row['quantite_ingredient'],
                    $row['astuce'],
                    $row['id_recette']
                );
                $preperation->setIdEtape($row['id_etape']);
                $preperation->setRecetteNom($row['recette_nom']);
                $preperations[] = $preperation;
            }
            return $preperations;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer une étape par ID (avec jointure)
    public function getPreperationById($id)
    {
        $sql = "SELECT p.*, r.nom as recette_nom 
                FROM preperation p 
                LEFT JOIN recette r ON p.id_recette = r.id_recette 
                WHERE p.id_etape = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $preperation = new Preperation(
                    $row['ordre'],
                    $row['instruction'],
                    $row['duree'],
                    $row['temperature'],
                    $row['type_action'],
                    $row['outil_utilise'],
                    $row['quantite_ingredient'],
                    $row['astuce'],
                    $row['id_recette']
                );
                $preperation->setIdEtape($row['id_etape']);
                $preperation->setRecetteNom($row['recette_nom']);
                return $preperation;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour une étape
    public function updatePreperation(Preperation $preperation)
    {
        $sql = "UPDATE preperation 
                SET ordre = :ordre, 
                    instruction = :instruction, 
                    duree = :duree,
                    temperature = :temperature,
                    type_action = :type_action,
                    outil_utilise = :outil_utilise,
                    quantite_ingredient = :quantite_ingredient,
                    astuce = :astuce,
                    id_recette = :id_recette
                WHERE id_etape = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $preperation->getIdEtape(),
                'ordre' => $preperation->getOrdre(),
                'instruction' => $preperation->getInstruction(),
                'duree' => $preperation->getDuree(),
                'temperature' => $preperation->getTemperature(),
                'type_action' => $preperation->getTypeAction(),
                'outil_utilise' => $preperation->getOutilUtilise(),
                'quantite_ingredient' => $preperation->getQuantiteIngredient(),
                'astuce' => $preperation->getAstuce(),
                'id_recette' => $preperation->getIdRecette()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer une étape
    public function deletePreperation($id)
    {
        $sql = "DELETE FROM preperation WHERE id_etape = :id";
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

    // Récupérer toutes les recettes pour la liste déroulante
    public function getAllRecettes()
    {
        $sql = "SELECT id_recette, nom FROM recette ORDER BY nom ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Obtenir le prochain ordre pour une recette
    public function getNextOrdre($id_recette)
    {
        $sql = "SELECT MAX(ordre) as max_ordre FROM preperation WHERE id_recette = :id_recette";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_recette' => $id_recette]);
            $result = $query->fetch();
            return ($result['max_ordre'] ?? 0) + 1;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return 1;
        }
    }
    // Dans controleurs/PreperationController.php, ajoutez cette méthode :

 // Ajoutez cette méthode si elle n'existe pas :
    public function getPreperationsByRecetteId($id_recette)
    {
        $sql = "SELECT p.*, r.nom as recette_nom 
                FROM preperation p 
                LEFT JOIN recette r ON p.id_recette = r.id_recette 
                WHERE p.id_recette = :id_recette 
                ORDER BY p.ordre ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_recette' => $id_recette]);
            $preperationsData = $query->fetchAll();

            $preperations = [];
            foreach ($preperationsData as $row) {
                $preperation = new Preperation(
                    $row['ordre'],
                    $row['instruction'],
                    $row['duree'],
                    $row['temperature'],
                    $row['type_action'],
                    $row['outil_utilise'],
                    $row['quantite_ingredient'],
                    $row['astuce'],
                    $row['id_recette']
                );
                $preperation->setIdEtape($row['id_etape']);
                $preperation->setRecetteNom($row['recette_nom']);
                $preperations[] = $preperation;
            }
            return $preperations;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }
}
?>