<?php
include '../../controleurs/SuiviController.php';
include '../../controleurs/ObjectifController.php';
require_once __DIR__ . '/../../models/suivi.php';

$suiviController = new SuiviController();
$objectifController = new ObjectifController();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    if ($action === 'create' || $action === 'update') {
        $user_id = $_POST['user_id'] ?? 1;
        $id_objectif = $_POST['id_objectif'] ?? null;
        $date = $_POST['date'] ?? null;
        $poids = $_POST['poids'] ?? null;
        $calories_consommees = $_POST['calories_consommees'] ?? null;
        $calories_objectif = $_POST['calories_objectif'] ?? null;
        $eau_bue = $_POST['eau_bue'] ?? null;
        $eau_objectif = $_POST['eau_objectif'] ?? null;
        
        // Calculate remaining calories
        $calories_restant = null;
        if ($calories_objectif !== null && $calories_consommees !== null) {
            $calories_restant = $calories_objectif - $calories_consommees;
        }
        
        $suivi = new suivi($user_id, $id_objectif, $date, $poids, $calories_consommees, $calories_objectif, $calories_restant, $eau_bue, $eau_objectif);
        
        if ($action === 'update') {
            $suivi->setId($_POST['id_suivi'] ?? null);
            $success = $suiviController->updateSuivi($suivi);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Suivi modifié avec succès' : 'Erreur lors de la modification'
            ]);
        } else {
            $success = $suiviController->addSuivi($suivi);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Suivi ajouté avec succès' : 'Erreur lors de l\'ajout'
            ]);
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        $success = $suiviController->deleteSuivi($id);
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Suivi supprimé avec succès' : 'Erreur lors de la suppression'
        ]);
    }
    exit;
}

// Handle AJAX GET for details
if (isset($_GET['ajax']) && $_GET['ajax'] == 'details') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $suivi = $suiviController->getSuiviById($id);
        
        if ($suivi) {
            echo json_encode([
                'success' => true,
                'id' => $suivi->getId(),
                'id_objectif' => $suivi->getIdObjectif(),
                'date' => $suivi->getDate(),
                'poids' => $suivi->getPoids(),
                'calories_consommees' => $suivi->getCaloriesConsommees(),
                'calories_objectif' => $suivi->getCaloriesObjectif(),
                'eau_bue' => $suivi->getEauBue(),
                'eau_objectif' => $suivi->getEauObjectif()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Suivi non trouvé']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Display page
$suivis = $suiviController->listSuivis();
$objectifs = $objectifController->listObjectifs();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Suivi Quotidien - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        /* Styles additionnels pour les suivis */
        .suivi-card {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-left: 5px solid var(--success-green);
            transition: all 0.3s ease;
        }

        .suivi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .suivi-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .suivi-date {
            background: linear-gradient(135deg, var(--success-green), var(--primary-light));
            color: var(--white);
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .suivi-actions {
            display: flex;
            gap: 10px;
        }

        .suivi-actions button {
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

        .edit-suivi-btn {
            background: var(--primary-blue);
            color: var(--white);
        }

        .edit-suivi-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .delete-suivi-btn {
            background: var(--danger);
            color: var(--white);
        }

        .delete-suivi-btn:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .suivi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 15px 0;
        }

        .suivi-item {
            background: var(--gray-light);
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--gray-medium);
        }

        .suivi-item-label {
            font-size: 0.85em;
            color: var(--gray-dark);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .suivi-item-value {
            font-size: 1.5em;
            color: var(--primary-blue);
            font-weight: 700;
        }

        .suivi-item-unit {
            font-size: 0.75em;
            color: var(--gray-dark);
            margin-left: 5px;
            font-weight: normal;
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
        .form-group select {
            padding: 12px 15px;
            border: 2px solid var(--gray-medium);
            border-radius: 10px;
            font-size: 1em;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }

        /* Error and Valid states */
        .form-group input.error,
        .form-group select.error {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
        }

        .form-group input.valid,
        .form-group select.valid {
            border-color: #28a745 !important;
            background-color: #f8fff8;
        }

        .field-error {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .field-error i {
            font-size: 0.9em;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-blue), var(--success-green));
            color: var(--white);
            flex: 1;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .cancel-btn {
            background: var(--gray-light);
            color: var(--gray-dark);
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: none;
        }

        .hero-section {
            background: linear-gradient(90deg, #2E7D32 0%, #003366 100%);
            color: var(--white);
            padding: 50px 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .nav-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .btn-nav {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            backdrop-filter: blur(5px);
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .btn-nav:hover {
            background: white;
            color: var(--primary-blue);
        }

        .success-message, .error-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .success-message { background: #e8f5e9; color: #2e7d32; border-left: 5px solid #4caf50; }
        .error-message { background: #ffeerb; color: #c62828; border-left: 5px solid #f44336; }

        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
        }
        
        body { background: #f0f4f8; margin: 0; padding-bottom: 60px; }
        .simple-footer { background: #2E7D32; color: white; text-align: center; padding: 15px; position: fixed; bottom: 0; width: 100%; z-index: 100; }
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
            <div class="nav-toggle">
            </div>
            <h1>
                <i class="fas fa-chart-line"></i>
                Gestion des Suivis
            </h1>
            <p>Enregistrez vos métriques quotidiennes pour atteindre vos objectifs</p>
        </div>

        <!-- Formulaire Ajouter/Modifier Suivi -->
        <div class="form-card">
            <div class="form-header" style="display: flex; justify-content: space-between; align-items: center; padding-right: 30px;">
                <h2 class="form-title">
                    <i class="fas fa-plus-circle"></i>
                    Nouvelle entrée de suivi
                </h2>
                <a href="afficherObjectif.php" style="background: var(--success-green); color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9em; font-weight: 600; transition: 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                    <i class="fas fa-bullseye"></i> Mes Objectifs
                </a>
            </div>
            <div class="form-content">
                <form id="addSuiviForm">
                    <input type="hidden" name="id_suivi" value="">
                    <input type="hidden" name="user_id" value="1">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_objectif">
                                <i class="fas fa-bullseye"></i> Objectif lié
                            </label>
                            <select id="id_objectif" name="id_objectif" required>
                                <option value="">Choisir un objectif...</option>
                                <?php foreach ($objectifs as $obj): ?>
                                    <option value="<?= $obj->getId() ?>" 
                                            data-calories="<?= $obj->getCaloriesObjectif() ?>" 
                                            data-eau="<?= $obj->getEauObjectif() ?>">
                                        Objectif <?= $obj->getId() ?> (du <?= date('d/m/Y', strtotime($obj->getDateDebut())) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date">
                                <i class="fas fa-calendar-day"></i> Date
                            </label>
                            <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" readonly required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="poids">
                                <i class="fas fa-weight"></i> Poids Actuel (kg)
                            </label>
                            <input type="number" id="poids" name="poids" step="0.1" placeholder="Ex: 78.2" required>
                        </div>
                        <div class="form-group">
                            <label for="calories_consommees">
                                <i class="fas fa-utensils"></i> Calories Consommées (kcal)
                            </label>
                            <input type="number" id="calories_consommees" name="calories_consommees" placeholder="Ex: 1850" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="calories_objectif">
                                <i class="fas fa-fire"></i> Objectif Calories (kcal)
                            </label>
                            <input type="number" id="calories_objectif" name="calories_objectif" placeholder="Rempli automatiquement" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="eau_bue">
                                <i class="fas fa-glass-water"></i> Eau Bue (litres)
                            </label>
                            <input type="number" id="eau_bue" name="eau_bue" step="0.1" placeholder="Ex: 1.5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="eau_objectif">
                                <i class="fas fa-droplet"></i> Objectif Eau (litres)
                            </label>
                            <input type="number" id="eau_objectif" name="eau_objectif" step="0.1" placeholder="Rempli automatiquement" readonly required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <button type="button" class="cancel-btn">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des Suivis -->
        <div class="suivis-list">
            <?php if (count($suivis) > 0): ?>
                <h2 style="margin-bottom: 20px; color: var(--text-dark);">
                    <i class="fas fa-history"></i> Historique de Suivi
                </h2>
                
                <?php foreach ($suivis as $s): ?>
                    <div class="suivi-card">
                        <div class="suivi-header">
                            <div class="suivi-date">
                                <i class="fas fa-calendar-check"></i>
                                <?= date('d/m/Y', strtotime($s->getDate())) ?>
                            </div>
                            <div class="suivi-actions">
                                <button class="edit-suivi-btn" data-id="<?= $s->getId() ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete-suivi-btn" data-id="<?= $s->getId() ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="suivi-grid">
                            <div class="suivi-item">
                                <div class="suivi-item-label">Poids</div>
                                <div class="suivi-item-value"><?= number_format($s->getPoids(), 1) ?> <span class="suivi-item-unit">kg</span></div>
                            </div>
                            <div class="suivi-item">
                                <div class="suivi-item-label">Calories</div>
                                <div class="suivi-item-value"><?= $s->getCaloriesConsommees() ?> / <?= $s->getCaloriesObjectif() ?></div>
                                <div style="font-size: 0.8em; color: <?= ($s->getCaloriesRestant() >= 0) ? 'green' : 'red' ?>;">
                                    Reste: <?= $s->getCaloriesRestant() ?> kcal
                                </div>
                            </div>
                            <div class="suivi-item">
                                <div class="suivi-item-label">Eau</div>
                                <div class="suivi-item-value"><?= number_format($s->getEauBue(), 1) ?> / <?= number_format($s->getEauObjectif(), 1) ?> <span class="suivi-item-unit">L</span></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-objectif" style="text-align: center; padding: 40px; background: white; border-radius: 20px;">
                    <i class="fas fa-clipboard-list" style="font-size: 3em; opacity: 0.3; margin-bottom: 20px;"></i>
                    <p>Aucun suivi enregistré pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="simple-footer">
        💚 NutriLoop AI - Votre compagnon santé
    </div>

    <script src="../assets/js/suivi.js"></script>
    <script>
        // Auto-fill objectives based on selection
        document.getElementById('id_objectif').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('calories_objectif').value = selected.dataset.calories;
                document.getElementById('eau_objectif').value = selected.dataset.eau;
            }
        });
    </script>
</body>
</html>
