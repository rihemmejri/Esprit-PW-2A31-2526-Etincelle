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
    // Dans controleurs/PreperationController.php

public function exportPreparationsToPDF($preperations, $statsOutils, $statsActions, $search = '') {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Liste des Étapes de Préparation</title>
        <style>
            @page {
                margin: 1.5cm;
                size: landscape;
            }
            body {
                font-family: DejaVu Sans, sans-serif;
                margin: 0;
                padding: 0;
                font-size: 10px;
            }
            h1 {
                color: #2196f3;
                text-align: center;
                margin-bottom: 5px;
                font-size: 22px;
            }
            .subtitle {
                text-align: center;
                color: #666;
                margin-bottom: 15px;
                font-size: 11px;
            }
            .header-pdf {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #2196f3;
                padding-bottom: 10px;
            }
            .stats-pdf {
                margin: 15px 0;
                padding: 10px;
                background: #f5f5f5;
                border-radius: 8px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
                gap: 15px;
            }
            .stat-item {
                text-align: center;
                font-size: 10px;
            }
            .stat-item strong {
                color: #2196f3;
                font-size: 13px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
                font-size: 9px;
            }
            th {
                background: #2196f3;
                color: white;
                padding: 8px 5px;
                text-align: left;
                font-size: 9px;
            }
            td {
                border: 1px solid #ddd;
                padding: 5px;
                vertical-align: top;
            }
            tr:nth-child(even) {
                background: #f9f9f9;
            }
            .footer-pdf {
                margin-top: 20px;
                text-align: center;
                font-size: 8px;
                color: #999;
                border-top: 1px solid #eee;
                padding-top: 10px;
            }
            .badge-action, .badge-outil {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 15px;
                font-size: 8px;
                font-weight: bold;
            }
            .action-COUPER { background: #e3f2fd; color: #1565c0; }
            .action-MELANGER { background: #f3e5f5; color: #7b1fa2; }
            .action-CUISSON { background: #ffebee; color: #c62828; }
            .outil-FOUR { background: #fff3e0; color: #e65100; }
            .outil-MIXEUR { background: #e8f5e9; color: #2e7d32; }
            .outil-CUILLERE { background: #e0f7fa; color: #00838f; }
        </style>
    </head>
    <body>
        <div class="header-pdf">
            <h1>🍽️ Liste des Étapes de Préparation</h1>
            <div class="subtitle">📅 Exporté le ' . date('d/m/Y à H:i:s') . '</div>
        </div>';
    
    if (!empty($search)) {
        $html .= '<div style="margin-bottom: 15px; padding: 8px; background: #e3f2fd; border-radius: 8px; text-align: center;">
            🔍 Résultats pour : <strong>' . htmlspecialchars($search) . '</strong> (' . count($preperations) . ' étape(s) trouvée(s))
        </div>';
    }
    
    $html .= '<div class="stats-pdf">
        <div class="stat-item">📊 <strong>' . count($preperations) . '</strong> étapes</div>
        <div class="stat-item">⏱️ <strong>' . array_sum(array_column($preperations, 'duree')) . '</strong> min total</div>';
    
    foreach ($statsOutils as $outil => $count) {
        $html .= '<div class="stat-item">🔧 ' . $outil . ': <strong>' . $count . '</strong></div>';
    }
    foreach ($statsActions as $action => $count) {
        $html .= '<div class="stat-item">✂️ ' . $action . ': <strong>' . $count . '</strong></div>';
    }
    
    $html .= '</div>';
    
    $html .= '<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Recette</th>
                    <th>Ordre</th>
                    <th>Instruction</th>
                    <th>Durée</th>
                    <th>Température</th>
                    <th>Action</th>
                    <th>Outil</th>
                    <th>Quantité</th>
                    <th>Astuce</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($preperations as $p) {
        $instruction = strlen($p->getInstruction()) > 100 ? substr($p->getInstruction(), 0, 100) . '...' : $p->getInstruction();
        $html .= '<tr>
                    <td>#' . $p->getIdEtape() . '</td>
                    <td>' . htmlspecialchars($p->getRecetteNom()) . '</td>
                    <td>Étape ' . $p->getOrdre() . '</td>
                    <td>' . htmlspecialchars($instruction) . '</td>
                    <td>⏱️ ' . $p->getDuree() . ' min</td>
                    <td>' . ($p->getTemperature() ? '🔥 ' . $p->getTemperature() . '°C' : '—') . '</td>
                    <td><span class="badge-action action-' . $p->getTypeAction() . '">' . ($p->getTypeAction() ?: '—') . '</span></td>
                    <td><span class="badge-outil outil-' . $p->getOutilUtilise() . '">' . ($p->getOutilUtilise() ?: '—') . '</span></td>
                    <td>' . ($p->getQuantiteIngredient() ?: '—') . '</td>
                    <td>' . ($p->getAstuce() ? '💡 ' . htmlspecialchars(substr($p->getAstuce(), 0, 60)) : '—') . '</td>
                 </tr>';
    }
    
    $html .= '</tbody>
        </table>
        <div class="footer-pdf">
            <p>🍽️ NutriLoop - Application de gestion nutritionnelle</p>
            <p>📋 Rapport généré le ' . date('d/m/Y') . '</p>
        </div>
    </body>
    </html>';
    
    return $html;
}
}
?>