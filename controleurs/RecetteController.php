<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/recette.php');

class RecetteController
{
    // Ajouter une recette
    public function addRecette(recette $recette)
    {
        $sql = "INSERT INTO recette (nom, description, temps_preparation, difficulte, type_repas, origine, nb_personne) 
                VALUES (:nom, :description, :temps_preparation, :difficulte, :type_repas, :origine, :nb_personne)";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $recette->getNom(),
                'description' => $recette->getDescription(),
                'temps_preparation' => $recette->getTempsPreparation(),
                'difficulte' => $recette->getDifficulte(),
                'type_repas' => $recette->getTypeRepas(),
                'origine' => $recette->getOrigine(),
                'nb_personne' => $recette->getNbPersonne()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Lister toutes les recettes
    public function listRecettes()
    {
        $sql = "SELECT * FROM recette ORDER BY nom ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $recettesData = $query->fetchAll();

            $recettes = [];
            foreach ($recettesData as $row) {
                $recette = new recette(
                    $row['nom'], 
                    $row['description'], 
                    $row['temps_preparation'],
                    $row['difficulte'],
                    $row['type_repas'],
                    $row['origine'],
                    $row['nb_personne']
                );
                $recette->setIdRecette($row['id_recette']);
                $recettes[] = $recette;
            }
            return $recettes;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer une recette par ID (retourne un objet recette)
    public function getRecetteById($id)
    {
        $sql = "SELECT * FROM recette WHERE id_recette = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $recette = new recette(
                    $row['nom'],
                    $row['description'],
                    $row['temps_preparation'],
                    $row['difficulte'],
                    $row['type_repas'],
                    $row['origine'],
                    $row['nb_personne']
                );
                $recette->setIdRecette($row['id_recette']);
                return $recette;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour une recette
    public function updateRecette(recette $recette)
    {
        $sql = "UPDATE recette 
                SET nom = :nom, 
                    description = :description, 
                    temps_preparation = :temps_preparation,
                    difficulte = :difficulte,
                    type_repas = :type_repas,
                    origine = :origine,
                    nb_personne = :nb_personne
                WHERE id_recette = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $recette->getIdRecette(),
                'nom' => $recette->getNom(),
                'description' => $recette->getDescription(),
                'temps_preparation' => $recette->getTempsPreparation(),
                'difficulte' => $recette->getDifficulte(),
                'type_repas' => $recette->getTypeRepas(),
                'origine' => $recette->getOrigine(),
                'nb_personne' => $recette->getNbPersonne()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Supprimer une recette
    public function deleteRecette($id)
    {
        $sql = "DELETE FROM recette WHERE id_recette = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
?>