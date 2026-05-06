<?php
// views/backOffice/recette/viewRecette.php
include_once '../../controleurs/RecetteController.php';
include_once '../../controleurs/PreperationController.php';

$recetteController = new RecetteController();
$preperationController = new PreperationController();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: recetteList.php');
    exit;
}

$recette = $recetteController->getRecetteById($id);
if (!$recette) {
    header('Location: recetteList.php');
    exit;
}

// Récupérer TOUTES les étapes de cette recette
$etapes = $preperationController->getPreperationsByRecetteId($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recette->getNom()) ?> - Détail de la recette</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .detail-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        /* Bouton retour */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #6c757d;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            margin-bottom: 25px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .btn-back:hover {
            background: #5a6268;
            transform: translateX(-5px);
        }
        
        /* Header de la recette */
        .recette-header {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: white;
            padding: 35px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .recette-header h1 {
            font-size: 2.2rem;
            margin-bottom: 15px;
        }
        
        .recette-header h1 i {
            margin-right: 15px;
        }
        
        .recette-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .meta-item {
            background: rgba(255,255,255,0.2);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            backdrop-filter: blur(5px);
        }
        
        /* Sections */
        .section-title {
            font-size: 1.6rem;
            color: #2e7d32;
            margin: 35px 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 3px solid #2e7d32;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .add-etape-btn {
            background: #2e7d32;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-etape-btn:hover {
            background: #1b5e20;
            transform: scale(1.02);
        }
        
        .btn-view-all {
            background: #2196f3;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-view-all:hover {
            background: #1976d2;
            transform: scale(1.02);
        }
        
        .description-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            line-height: 1.7;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            font-size: 1rem;
        }
        
        /* Liste des étapes */
        .etapes-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .etape-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .etape-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        
        /* En-tête de l'étape */
        .etape-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 18px 25px;
            border-bottom: 2px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .etape-numero {
            background: #2e7d32;
            color: white;
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .etape-infos {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .etape-info {
            font-size: 13px;
            color: #555;
            background: white;
            padding: 5px 12px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .etape-info i {
            margin-right: 6px;
            color: #2e7d32;
        }
        
        /* Corps de l'étape */
        .etape-body {
            padding: 25px;
        }
        
        .etape-instruction {
            line-height: 1.7;
            margin-bottom: 20px;
            font-size: 1rem;
            background: #f8f9fa;
            padding: 18px;
            border-radius: 12px;
        }
        
        .etape-astuce {
            background: #fff8e1;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            color: #856404;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
        }
        
        /* Actions */
        .etape-actions {
            display: flex;
            gap: 12px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
        }
        
        .btn-edit {
            background: #ff9800;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-edit:hover {
            background: #e68900;
            transform: scale(1.02);
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-delete:hover {
            background: #d32f2f;
            transform: scale(1.02);
        }
        
        /* Message vide */
        .empty-etapes {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 16px;
            color: #666;
        }
        
        .empty-etapes i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .detail-container {
                padding: 15px;
            }
            .recette-header {
                padding: 20px;
            }
            .etape-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .section-title {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="detail-container">
        <a href="recetteList.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste des recettes
        </a>

        <!-- ========== DÉTAILS DE LA RECETTE ========== -->
        <div class="recette-header">
            <h1>
                <i class="fas fa-utensils"></i> 
                <?= htmlspecialchars($recette->getNom()) ?>
            </h1>
            <div class="recette-meta">
                <div class="meta-item">
                    <i class="fas fa-clock"></i> <?= $recette->getTempsPreparation() ?> minutes
                </div>
                <div class="meta-item">
                    <i class="fas fa-users"></i> <?= $recette->getNbPersonne() ?> personnes
                </div>
                <div class="meta-item">
                    <i class="fas fa-chart-line"></i> <?= $recette->getDifficulte() ?>
                </div>
                <div class="meta-item">
                    <i class="fas fa-mug-hot"></i> 
                    <?php
                    $types = [
                        'PETIT_DEJEUNER' => 'Petit déjeuner',
                        'DEJEUNER' => 'Déjeuner',
                        'DINER' => 'Dîner',
                        'DESSERT' => 'Dessert'
                    ];
                    echo $types[$recette->getTypeRepas()] ?? $recette->getTypeRepas();
                    ?>
                </div>
                <?php if ($recette->getOrigine()): ?>
                <div class="meta-item">
                    <i class="fas fa-globe"></i> <?= htmlspecialchars($recette->getOrigine()) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========== DESCRIPTION ========== -->
        <h2 class="section-title">
            <i class="fas fa-align-left"></i> Description
        </h2>
        <div class="description-box">
            <?= nl2br(htmlspecialchars($recette->getDescription())) ?>
        </div>

        <!-- ========== ÉTAPES DE PRÉPARATION ========== -->
        <h2 class="section-title">
            <div>
                <i class="fas fa-list-ol"></i> Étapes de préparation
                <span style="font-size: 14px; color: #666; margin-left: 10px;">
                    (<?= count($etapes) ?> étape(s))
                </span>
            </div>
            <div style="display: flex; gap: 10px;">
                <!-- NOUVEAU BOUTON : Voir toutes les étapes dans la liste -->
                <a href="preperationList.php?recette=<?= urlencode($recette->getNom()) ?>" class="btn-view-all">
                    <i class="fas fa-list-ul"></i> Voir toutes les étapes
                </a>
                <a href="addPreperation.php?recette_id=<?= $id ?>" class="add-etape-btn">
                    <i class="fas fa-plus-circle"></i> Ajouter une étape
                </a>
            </div>
        </h2>
        
        <?php if (count($etapes) > 0): ?>
            <div class="etapes-list">
                <?php foreach ($etapes as $index => $etape): ?>
                    <div class="etape-card">
                        <!-- En-tête avec numéro et informations générales -->
                        <div class="etape-header">
                            <span class="etape-numero">
                                <i class="fas fa-check-circle"></i> Étape <?= htmlspecialchars($etape->getOrdre()) ?>
                            </span>
                            <div class="etape-infos">
                                <?php if ($etape->getDuree() > 0): ?>
                                <span class="etape-info">
                                    <i class="fas fa-hourglass-half"></i> Durée : <?= $etape->getDuree() ?> min
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($etape->getTemperature()): ?>
                                <span class="etape-info">
                                    <i class="fas fa-thermometer-half"></i> Température : <?= $etape->getTemperature() ?>°C
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($etape->getTypeAction()): ?>
                                <span class="etape-info">
                                    <i class="fas <?= $etape->getTypeAction() == 'CUISSON' ? 'fa-fire' : ($etape->getTypeAction() == 'MELANGER' ? 'fa-mix' : 'fa-cut') ?>"></i>
                                    Action : <?= $etape->getTypeAction() ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($etape->getOutilUtilise()): ?>
                                <span class="etape-info">
                                    <i class="fas fa-tools"></i> Outil : <?= $etape->getOutilUtilise() ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($etape->getQuantiteIngredient()): ?>
                                <span class="etape-info">
                                    <i class="fas fa-weight-hanging"></i> Quantité : <?= htmlspecialchars($etape->getQuantiteIngredient()) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Corps avec instruction et astuce -->
                        <div class="etape-body">
                            <div class="etape-instruction">
                                <strong><i class="fas fa-align-left"></i> Instruction :</strong><br>
                                <?= nl2br(htmlspecialchars($etape->getInstruction())) ?>
                            </div>
                            
                            <?php if ($etape->getAstuce()): ?>
                            <div class="etape-astuce">
                                <i class="fas fa-lightbulb"></i> 
                                <strong>Astuce :</strong> <?= nl2br(htmlspecialchars($etape->getAstuce())) ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Boutons d'action -->
                            <div class="etape-actions">
                                <a href="editPreperation.php?id=<?= $etape->getIdEtape() ?>&recette_id=<?= $id ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Modifier cette étape
                                </a>
                                <button onclick="confirmDeleteEtape(<?= $etape->getIdEtape() ?>, <?= $id ?>)" class="btn-delete">
                                    <i class="fas fa-trash"></i> Supprimer cette étape
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-etapes">
                <i class="fas fa-info-circle"></i>
                <h3>Aucune étape de préparation</h3>
                <p>Cette recette n'a pas encore d'étapes de préparation.</p>
                <a href="addPreperation.php?recette_id=<?= $id ?>" class="add-etape-btn" style="display: inline-block; margin-top: 15px;">
                    <i class="fas fa-plus-circle"></i> Ajouter la première étape
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmDeleteEtape(etapeId, recetteId) {
            if(confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette étape ?\n\nCette action est irréversible.')) {
                window.location.href = 'deletePreperation.php?id=' + etapeId + '&recette_id=' + recetteId + '&confirm=yes';
            }
        }
    </script>
</body>
</html>