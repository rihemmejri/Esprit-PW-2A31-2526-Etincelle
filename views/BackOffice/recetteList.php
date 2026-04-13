<?php
session_start();
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$RecetteController = new RecetteController();
$recettes = $RecetteController->listRecettes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Recettes - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        /* Style supplémentaire pour garantir l'affichage complet */
        .description-full {
            white-space: normal !important;
            word-wrap: break-word !important;
            line-height: 1.5 !important;
        }
        
        /* Pour que le tableau soit large */
        .table-container {
            overflow-x: auto !important;
        }
        
        table {
            min-width: 1800px !important;
        }
        
        td:nth-child(3) {
            min-width: 450px !important;
            max-width: none !important;
        }
    </style>
</head>
<body>
    <div class="container-list">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="notification-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="header">
            <h1>
                <i class="fas fa-utensils"></i>
                Gestion des Recettes
            </h1>
            <a href="addRecette.php" class="add-btn">
                <i class="fas fa-plus-circle"></i>
                Ajouter une recette
            </a>
        </div>

        <div class="stats-bar">
            <div class="stats-info">
                <div class="stat-item">
                    <i class="fas fa-utensils"></i>
                    <span>Total: <strong><?= count($recettes) ?></strong> recettes</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-clock"></i>
                    <span>Temps moyen: <strong>
                        <?php 
                        if (count($recettes) > 0) {
                            $totalTemps = 0;
                            foreach ($recettes as $r) {
                                $totalTemps += $r->getTempsPreparation();
                            }
                            echo round($totalTemps / count($recettes)) . ' min';
                        } else {
                            echo '0 min';
                        }
                        ?>
                    </strong></span>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table id="recettesTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-utensils"></i> Nom</th>
                            <th><i class="fas fa-align-left"></i> Description</th>
                            <th><i class="fas fa-clock"></i> Temps (min)</th>
                            <th><i class="fas fa-chart-line"></i> Difficulté</th>
                            <th><i class="fas fa-mug-hot"></i> Type de repas</th>
                            <th><i class="fas fa-globe"></i> Origine</th>
                            <th><i class="fas fa-users"></i> Personnes</th>
                            <th><i class="fas fa-cog"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recettes) > 0): ?> 
                            <?php foreach ($recettes as $recette): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($recette->getIdRecette()) ?></td>
                                    <td class="recette-title"><?= htmlspecialchars($recette->getNom()) ?></td>
                                    <td class="description-full"><?= nl2br(htmlspecialchars($recette->getDescription())) ?> <!-- DESCRIPTION COMPLÈTE -->
                                    <td class="text-center"><i class="fas fa-hourglass-half"></i> <?= htmlspecialchars($recette->getTempsPreparation()) ?> min</td>
                                    <td>
                                        <?php 
                                        $difficulte = $recette->getDifficulte();
                                        $difficulteClass = '';
                                        $difficulteIcon = '';
                                        
                                        switch($difficulte) {
                                            case 'FACILE':
                                                $difficulteClass = 'difficulte-FACILE';
                                                $difficulteIcon = 'fa-smile';
                                                break;
                                            case 'MOYEN':
                                                $difficulteClass = 'difficulte-MOYEN';
                                                $difficulteIcon = 'fa-meh';
                                                break;
                                            case 'DIFFICILE':
                                                $difficulteClass = 'difficulte-DIFFICILE';
                                                $difficulteIcon = 'fa-frown';
                                                break;
                                        }
                                        ?>
                                        <span class="difficulte-badge <?= $difficulteClass ?>">
                                            <i class="fas <?= $difficulteIcon ?>"></i>
                                            <?= $difficulte ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $typeRepas = $recette->getTypeRepas();
                                        $typeIcon = '';
                                        $typeText = '';
                                        
                                        switch($typeRepas) {
                                            case 'PETIT_DEJEUNER':
                                                $typeIcon = 'fa-coffee';
                                                $typeText = 'Petit déjeuner';
                                                break;
                                            case 'DEJEUNER':
                                                $typeIcon = 'fa-utensils';
                                                $typeText = 'Déjeuner';
                                                break;
                                            case 'DINER':
                                                $typeIcon = 'fa-moon';
                                                $typeText = 'Dîner';
                                                break;
                                            case 'DESSERT':
                                                $typeIcon = 'fa-cake-candles';
                                                $typeText = 'Dessert';
                                                break;
                                        }
                                        ?>
                                        <span class="type-repas-badge">
                                            <i class="fas <?= $typeIcon ?>"></i>
                                            <?= $typeText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($recette->getOrigine()): ?>
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?= htmlspecialchars($recette->getOrigine()) ?>
                                        <?php else: ?>
                                            <span style="color: #999;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><i class="fas fa-users"></i> <?= htmlspecialchars($recette->getNbPersonne()) ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="recetteList.php?id=<?= $recette->getIdRecette() ?>" class="action-btn view">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <a href="editRecette.php?id=<?= $recette->getIdRecette() ?>" class="action-btn edit">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
<a href="#" class="action-btn delete" onclick="confirmDelete(<?= $recette->getIdRecette() ?>); return false;">
    <i class="fas fa-trash"></i> Suppr
</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?> 
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="empty-message">
                                    <i class="fas fa-empty-folder"></i>
                                    <h3>Aucune recette trouvée</h3>
                                    <p>Commencez par ajouter votre première recette !</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <div class="pagination">
                <button class="page-btn" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
                <button class="page-btn active" id="page1">1</button>
                <button class="page-btn" id="page2" style="display:none;">2</button>
                <button class="page-btn" id="page3" style="display:none;">3</button>
                <button class="page-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button class="export-btn" onclick="exportTable()">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>
    </div>

    <script src="../assets/js/recette.js"></script>
</body>
</html>