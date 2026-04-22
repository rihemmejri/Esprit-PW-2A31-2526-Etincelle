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
    // Dans controleurs/RecetteController.php

public function exportRecettesToPDF($recettes, $search = '') {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des Recettes</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { color: #4CAF50; text-align: center; }
            .date { text-align: center; color: #666; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background: #4CAF50; color: white; padding: 10px; text-align: left; }
            td { border: 1px solid #ddd; padding: 8px; }
            tr:nth-child(even) { background: #f9f9f9; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <h1>📋 Liste des Recettes</h1>
        <div class="date">Date d\'export : ' . date('d/m/Y H:i:s') . '</div>';
    
    if (!empty($search)) {
        $html .= '<div style="margin-bottom: 20px; color: #4CAF50;">Recherche : <strong>' . htmlspecialchars($search) . '</strong> (' . count($recettes) . ' résultat(s))</div>';
    }
    
    $html .= '<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Temps (min)</th>
                    <th>Difficulté</th>
                    <th>Type</th>
                    <th>Origine</th>
                    <th>Personnes</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($recettes as $r) {
        $description = strlen($r->getDescription()) > 100 ? substr($r->getDescription(), 0, 100) . '...' : $r->getDescription();
        $html .= '<tr>
                    <td>#' . $r->getIdRecette() . '</td>
                    <td>' . htmlspecialchars($r->getNom()) . '</td>
                    <td>' . htmlspecialchars($description) . '</td>
                    <td>' . $r->getTempsPreparation() . ' min</td>
                    <td>' . $r->getDifficulte() . '</td>
                    <td>' . $r->getTypeRepas() . '</td>
                    <td>' . htmlspecialchars($r->getOrigine() ?: 'Non spécifiée') . '</td>
                    <td>' . $r->getNbPersonne() . '</td>
                </tr>';
    }
    
    $html .= '</tbody>
        </table>
        <div class="footer">
            <p>Généré par NutriLoop - ' . date('Y') . '</p>
        </div>
    </body>
    </html>';
    
    return $html;
}
}
?>