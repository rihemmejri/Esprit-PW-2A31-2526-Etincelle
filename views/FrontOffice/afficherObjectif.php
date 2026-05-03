<?php
include '../../controleurs/ObjectifController.php';
include '../../controleurs/SuiviController.php';
<<<<<<< HEAD
include '../../controleurs/AIPredictionController.php';
=======
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
require_once __DIR__ . '/../../models/objectif.php';

$objectifController = new ObjectifController();
$suiviController = new SuiviController();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    if ($action === 'create' || $action === 'update') {
        $user_id = $_POST['user_id'] ?? 1;
        $poids_cible = $_POST['poids_cible'] ?? null;
        $calories_objectif = $_POST['calories_objectif'] ?? null;
        $eau_objectif = $_POST['eau_objectif'] ?? null;
        $date_debut = $_POST['date_debut'] ?? null;
        $date_fin = $_POST['date_fin'] ?? null;
        
        $objectif = new objectif($user_id, $poids_cible, $calories_objectif, $eau_objectif, $date_debut, $date_fin);
        
        if ($action === 'update') {
            $objectif->setId($_POST['id_objectif'] ?? null);
            $success = $objectifController->updateObjectif($objectif);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Objectif modifié avec succès' : 'Erreur lors de la modification'
            ]);
        } else {
            $success = $objectifController->addObjectif($objectif);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Objectif ajouté avec succès' : 'Erreur lors de l\'ajout'
            ]);
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        $success = $objectifController->deleteObjectif($id);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Objectif supprimé avec succès' : 'Erreur lors de la suppression'
        ]);
    } elseif ($action === 'ai_analyze') {
        $id = $_POST['id'] ?? null;
        $predictionController = new AIPredictionController();
        $result = $predictionController->generatePredictionByObjectif(1, $id);
        echo json_encode($result);
    }
    exit;
}

// Handle AJAX GET for details
if (isset($_GET['ajax']) && $_GET['ajax'] == 'details') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $objectif = $objectifController->getObjectifById($id);
        
        if ($objectif) {
            echo json_encode([
                'success' => true,
                'id' => $objectif->getId(),
                'poids_cible' => $objectif->getPoidsCible(),
                'calories_objectif' => $objectif->getCaloriesObjectif(),
                'eau_objectif' => $objectif->getEauObjectif(),
                'date_debut' => $objectif->getDateDebut(),
                'date_fin' => $objectif->getDateFin()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Objectif non trouvé']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Display page
$objectifs = $objectifController->listObjectifs();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Objectifs Nutritionnels - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        /* Styles additionnels pour les objectifs */
        .objectif-card {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 5px solid var(--primary-blue);
            transition: all 0.3s ease;
        }

        .objectif-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .objectif-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .objectif-period {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-light));
            color: var(--white);
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .objectif-actions {
            display: flex;
            gap: 10px;
        }

        .objectif-actions button {
            padding: 8px 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .edit-objectif-btn {
            background: var(--primary-blue);
            color: var(--white);
        }

        .edit-objectif-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .delete-objectif-btn {
            background: var(--danger);
            color: var(--white);
        }

        .delete-objectif-btn:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .objectif-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 15px 0;
        }

        .objectif-item {
            background: var(--gray-light);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--gray-medium);
        }

        .objectif-item-label {
            font-size: 0.85em;
            color: var(--gray-dark);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .objectif-item-value {
            font-size: 1.8em;
            color: var(--primary-blue);
            font-weight: 700;
        }

        .objectif-item-unit {
            font-size: 0.75em;
            color: var(--gray-dark);
            margin-left: 5px;
            font-weight: normal;
        }

        .progress-bar {
            background: var(--gray-medium);
            height: 10px;
            border-radius: 10px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            background: linear-gradient(90deg, var(--success-green), var(--primary-blue));
            height: 100%;
            border-radius: 10px;
            width: 65%;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.75em;
            color: var(--gray-dark);
            margin-top: 5px;
        }

        /* Form styles */
        .form-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .form-header {
            background: linear-gradient(90deg, #2E7D32 0%, #003366 100%);
            color: var(--white);
            padding: 30px;
        }

        .form-title {
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 0;
        }

        .form-content {
            padding: 40px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 15px;
            border: 2px solid var(--gray-medium);
            border-radius: 10px;
            font-size: 1em;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }

        .form-group input.error,
        .form-group textarea.error {
            border-color: var(--danger);
            background: rgba(244, 67, 54, 0.05);
        }

        .form-group input.valid,
        .form-group textarea.valid {
            border-color: var(--success-green);
            background: rgba(76, 175, 80, 0.05);
        }

        .field-error {
            color: var(--danger);
            font-size: 0.85em;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .form-actions button {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-blue), var(--success-green));
            color: var(--white);
            flex: 1;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(33, 150, 243, 0.3);
        }

        .cancel-btn {
            background: var(--gray-light);
            color: var(--gray-dark);
            display: none;
        }

        .cancel-btn:hover {
            background: var(--gray-medium);
        }

        .success-message,
        .error-message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            animation: slideDown 0.3s ease;
        }

        .success-message {
            background: rgba(76, 175, 80, 0.1);
            border-left: 4px solid var(--success-green);
            color: var(--success-dark);
        }

        .error-message {
            background: rgba(244, 67, 54, 0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-section {
            background: linear-gradient(90deg, #2E7D32 0%, #003366 100%);
            color: var(--white);
            padding: 50px 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .hero-section h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .hero-section p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .objectifs-list {
            margin: 30px 0;
        }

        .no-objectif {
            text-align: center;
            padding: 60px 30px;
            background: var(--gray-light);
            border-radius: 20px;
            color: var(--gray-dark);
        }

        .no-objectif i {
            font-size: 3em;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-objectif p {
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .objectif-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions button {
                width: 100%;
            }

            .hero-section h1 {
                font-size: 1.8em;
            }

            .objectif-header {
                flex-direction: column;
            }
        }
        
        body {
            background: #f0f4f8;
            margin: 0;
            padding-bottom: 60px; /* Space for footer */
        }
        
        .simple-footer {
            background: #2E7D32;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            font-size: 0.9em;
            font-weight: 500;
            z-index: 100;
        }
    </style>
</head>
<body>
    <div class="container-form" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <div id="messageContainer" style="display: none; margin-bottom: 20px; padding: 15px 20px; border-radius: 12px; border-left: 5px solid #4CAF50; background: #e8f5e9; color: #2e7d32; font-weight: 500; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); animation: slideDown 0.4s ease-out;">
            <i class="fas fa-check-circle"></i>
            <span id="messageText"></span>
        </div>

        <style>
            @keyframes slideDown {
                from { transform: translateY(-20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        </style>

        <!-- Hero Section -->
        <div class="hero-section">
            <div style="position: absolute; top: 20px; right: 20px;">
                <a href="afficherSuivi.php" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none; font-weight: 600; backdrop-filter: blur(5px); transition: 0.3s; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-chart-line"></i> Gestion des Suivi
                </a>
            </div>
            <h1>
                <i class="fas fa-bullseye"></i>
                Mes Objectifs Nutritionnels
            </h1>
            <p>Suivez et gérez vos objectifs de santé personnalisés</p>
            <div style="margin-top: 20px;">
                <button id="openAiModal" style="background: #6366f1; color: white; border: none; padding: 12px 25px; border-radius: 30px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); transition: 0.3s;">
                    <i class="fas fa-robot"></i> Demander Conseil à l'IA
                </button>
            </div>
        </div>

        <!-- AI Selection Modal -->
        <div id="aiModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
            <div style="background: white; width: 90%; max-width: 500px; border-radius: 20px; overflow: hidden; animation: zoomIn 0.3s ease-out;">
                <div style="background: #6366f1; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0;"><i class="fas fa-robot"></i> Assistant NutriLoop AI</h3>
                    <button id="closeAiModal" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
                </div>
                <div style="padding: 30px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #374151;">Choisissez l'objectif à analyser :</label>
                    <select id="aiObjectifSelect" style="width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e5e7eb; font-size: 1rem; margin-bottom: 20px;">
                        <option value="">-- Sélectionner un objectif --</option>
                        <?php foreach ($objectifs as $obj): ?>
                            <option value="<?= $obj->getId() ?>">Objectif Poids: <?= $obj->getPoidsCible() ?>kg (Du <?= date('d/m', strtotime($obj->getDateDebut())) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button id="runAiAnalysis" style="width: 100%; background: #6366f1; color: white; border: none; padding: 15px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s;">
                        Lancer l'analyse intelligente
                    </button>

                    <div id="aiResult" style="display: none; margin-top: 25px; padding: 20px; border-radius: 12px; background: #f9fafb; border-left: 5px solid #6366f1;">
                        <div id="aiRiskBadge" style="display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; margin-bottom: 10px;"></div>
                        <p id="aiText" style="margin: 0; color: #4b5563; line-height: 1.5; font-size: 0.95rem;"></p>
                    </div>
                    
                    <div id="aiLoading" style="display: none; text-align: center; margin-top: 20px;">
                        <i class="fas fa-circle-notch fa-spin" style="font-size: 2rem; color: #6366f1;"></i>
                        <p style="margin-top: 10px; color: #6366f1; font-weight: 600;">L'IA analyse vos données...</p>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes zoomIn {
                from { transform: scale(0.9); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
        </style>

        <!-- Formulaire Ajouter/Modifier Objectif -->
        <div class="form-card">
            <div class="form-header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 30px;">
                <h2 class="form-title">
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un nouvel objectif
                </h2>
                <a href="afficherSuivi.php" style="background: var(--primary-blue); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9em; font-weight: 600; transition: 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                    <i class="fas fa-chart-line"></i> Gestion des Suivis
                </a>
            </div>
            <div class="form-content">
                <form id="addObjectifForm">
                    <input type="hidden" name="id_objectif" value="">
                    <input type="hidden" name="user_id" value="1">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="poids_cible">
                                <i class="fas fa-weight"></i>
                                Poids Cible (kg)
                            </label>
                            <input type="number" id="poids_cible" name="poids_cible" step="0.1" placeholder="Ex: 75.5" required>
                        </div>
                        <div class="form-group">
                            <label for="calories_objectif">
                                <i class="fas fa-fire"></i>
                                Calories Diurnes (kcal)
                            </label>
                            <input type="number" id="calories_objectif" name="calories_objectif" placeholder="Ex: 2000" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="eau_objectif">
                                <i class="fas fa-droplet"></i>
                                Eau Quotidienne (litres)
                            </label>
                            <input type="number" id="eau_objectif" name="eau_objectif" step="0.1" placeholder="Ex: 2.5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_debut">
                                <i class="fas fa-calendar-alt"></i>
                                Date de Début
                            </label>
                            <input type="date" id="date_debut" name="date_debut" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin">
                                <i class="fas fa-calendar-check"></i>
                                Date de Fin
                            </label>
                            <input type="date" id="date_fin" name="date_fin" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            Enregistrer
                        </button>
                        <button type="button" class="cancel-btn">
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des Objectifs -->
        <div class="objectifs-list">
            <?php if (count($objectifs) > 0): ?>
                <h2 style="margin-bottom: 20px; color: var(--text-dark);">
                    <i class="fas fa-list"></i>
                    Vos Objectifs (<?= count($objectifs) ?>)
                </h2>
                
                <?php foreach ($objectifs as $objectif): ?>
                    <div class="objectif-card">
                        <div class="objectif-header">
                            <div class="objectif-period">
                                <i class="fas fa-calendar"></i>
                                Du <?= date('d/m/Y', strtotime($objectif->getDateDebut())) ?> 
                                au <?= date('d/m/Y', strtotime($objectif->getDateFin())) ?>
                            </div>
                            <div class="objectif-actions">
                                <button class="edit-objectif-btn" data-id="<?= $objectif->getId() ?>">
                                    <i class="fas fa-edit"></i>
                                    Modifier
                                </button>
                                <button class="delete-objectif-btn" data-id="<?= $objectif->getId() ?>">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            </div>
                        </div>

                        <div class="objectif-grid">
                            <div class="objectif-item">
                                <div class="objectif-item-label">
                                    <i class="fas fa-weight"></i> Poids Cible
                                </div>
                                <div class="objectif-item-value">
                                    <?= number_format($objectif->getPoidsCible(), 1, ',', ' ') ?>
                                    <span class="objectif-item-unit">kg</span>
                                </div>
                            </div>

                            <div class="objectif-item">
                                <div class="objectif-item-label">
                                    <i class="fas fa-fire"></i> Calories
                                </div>
                                <div class="objectif-item-value">
                                    <?= number_format($objectif->getCaloriesObjectif(), 0, ',', ' ') ?>
                                    <span class="objectif-item-unit">kcal</span>
                                </div>
                            </div>

                            <div class="objectif-item">
                                <div class="objectif-item-label">
                                    <i class="fas fa-droplet"></i> Eau
                                </div>
                                <div class="objectif-item-value">
                                    <?= number_format($objectif->getEauObjectif(), 1, ',', ' ') ?>
                                    <span class="objectif-item-unit">L</span>
                                </div>
                            </div>
                        </div>

                        <?php 
                            $latestPoids = $suiviController->getLatestPoidsForObjectif($objectif->getId());
                            $progress = 0;
                            $statusText = "Aucun suivi enregistré";
                            if ($latestPoids) {
                                $target = $objectif->getPoidsCible();
                                if ($latestPoids == $target) {
                                    $progress = 100;
                                } else if ($latestPoids < $target) {
                                    $progress = round(($latestPoids / $target) * 100);
                                } else {
                                    $diff = $latestPoids - $target;
                                    $progress = max(0, 100 - round(($diff / $target) * 100));
                                }
                                $statusText = "Dernier poids: " . $latestPoids . " kg (" . $progress . "%)";
                            }
                        ?>
                        
                        <div class="progress-section" style="margin-top: 15px; padding: 0 15px 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.85em; color: #666;">
                                <span>Progression vers l'objectif de poids</span>
                                <strong><?= $progress ?>%</strong>
                            </div>
                            <div style="width: 100%; height: 10px; background: #eee; border-radius: 5px; overflow: hidden; position: relative; border: 1px solid #ddd;">
                                <div style="width: <?= $progress ?>%; height: 100%; background: linear-gradient(90deg, #4CAF50, #81C784); border-radius: 5px; transition: width 1s ease-in-out;"></div>
                            </div>
                            <p style="font-size: 0.75em; color: #888; margin-top: 6px; font-style: italic; text-align: right;"><?= $statusText ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-objectif">
                    <i class="fas fa-inbox"></i>
                    <p>Aucun objectif défini</p>
                    <p style="font-size: 0.9em;">Créez votre premier objectif en utilisant le formulaire ci-dessus</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer Simple Match Image -->
    <div class="simple-footer">
        💚 NutriLoop AI - Manger sainement pour une vie meilleure
    </div>

    <script src="../assets/js/objectif.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('aiModal');
            const openBtn = document.getElementById('openAiModal');
            const closeBtn = document.getElementById('closeAiModal');
            const runBtn = document.getElementById('runAiAnalysis');
            const select = document.getElementById('aiObjectifSelect');
            const resultDiv = document.getElementById('aiResult');
            const loading = document.getElementById('aiLoading');
            const aiText = document.getElementById('aiText');
            const aiRisk = document.getElementById('aiRiskBadge');

            openBtn.onclick = () => modal.style.display = 'flex';
            closeBtn.onclick = () => {
                modal.style.display = 'none';
                resultDiv.style.display = 'none';
            };

            window.onclick = (event) => {
                if (event.target == modal) {
                    modal.style.display = 'none';
                    resultDiv.style.display = 'none';
                }
            };

            runBtn.onclick = function() {
                const id = select.value;
                if (!id) {
                    alert('Veuillez sélectionner un objectif');
                    return;
                }

                loading.style.display = 'block';
                resultDiv.style.display = 'none';
                runBtn.disabled = true;

                const formData = new FormData();
                formData.append('action', 'ai_analyze');
                formData.append('id', id);

                fetch('afficherObjectif.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    runBtn.disabled = false;
                    
                    if (data.error) {
                        aiText.textContent = data.error;
                        aiRisk.style.display = 'none';
                    } else {
                        aiText.textContent = data.prediction;
                        aiRisk.textContent = 'Risque: ' + data.risk_level;
                        aiRisk.style.display = 'inline-block';
                        
                        // Style based on risk
                        if (data.risk_level === 'ÉLEVÉ') {
                            aiRisk.style.background = '#fee2e2';
                            aiRisk.style.color = '#ef4444';
                            resultDiv.style.borderColor = '#ef4444';
                        } else if (data.risk_level === 'MOYEN') {
                            aiRisk.style.background = '#fef3c7';
                            aiRisk.style.color = '#f59e0b';
                            resultDiv.style.borderColor = '#f59e0b';
                        } else {
                            aiRisk.style.background = '#dcfce7';
                            aiRisk.style.color = '#10b981';
                            resultDiv.style.borderColor = '#10b981';
                        }
                    }
                    resultDiv.style.display = 'block';
                })
                .catch(error => {
                    loading.style.display = 'none';
                    runBtn.disabled = false;
                    alert('Erreur lors de l\'analyse IA');
                });
            };
        });
    </script>
</body>
</html>
