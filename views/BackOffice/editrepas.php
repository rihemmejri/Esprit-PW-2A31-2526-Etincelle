<?php
include '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/repas.php';

$repasController = new RepasController();

// Récupérer l'ID du repas à modifier
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: repasList.php');
    exit;
}

// Récupérer les infos du repas
$repas = $repasController->getRepasById($id);

if (!$repas) {
    header('Location: repasList.php');
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $type = $_POST['type'];
    $calories = intval($_POST['calories'] ?? 0);
    $proteines = floatval($_POST['proteines'] ?? 0);
    $glucides = floatval($_POST['glucides'] ?? 0);
    $lipides = floatval($_POST['lipides'] ?? 0);

    // Validation des champs obligatoires
    if (empty($nom) || empty($type)) {
        $error = "Le nom et le type de repas sont obligatoires.";
    } 
    // Validation des valeurs nutritionnelles
    elseif ($calories < 0 || $calories > 2000) {
        $error = "Les calories doivent être comprises entre 0 et 2000 kcal.";
    } 
    elseif ($proteines < 0 || $proteines > 200) {
        $error = "Les protéines doivent être comprises entre 0 et 200 g.";
    } 
    elseif ($glucides < 0 || $glucides > 300) {
        $error = "Les glucides doivent être compris entre 0 et 300 g.";
    } 
    elseif ($lipides < 0 || $lipides > 150) {
        $error = "Les lipides doivent être compris entre 0 et 150 g.";
    } 
    else {
        // Mettre à jour l'objet Repas
        $repas->setNom(htmlspecialchars($nom));
        $repas->setType(htmlspecialchars($type));
        $repas->setCalories($calories);
        $repas->setProteines($proteines);
        $repas->setGlucides($glucides);
        $repas->setLipides($lipides);
        
        // Mettre à jour dans la BD
        $repasController->updateRepas($repas);
        
        // Redirection
        header('Location: repasList.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Repas - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        .nutrition-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        .stat {
            background: white;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-label {
            font-size: 0.85rem;
            color: #666;
            display: block;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        .stat-unit {
            font-size: 0.8rem;
            color: #888;
        }
        .current-values {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .current-values h3 {
            margin: 0 0 10px 0;
            color: #2e7d32;
            font-size: 1rem;
        }
        .value-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .value-tag {
            background: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #333;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .value-tag strong {
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container-edit">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Modifier le repas
                </h1>
                <p>Modifiez les informations nutritionnelles du repas #<?= $id ?></p>
                <div class="recette-id-badge">
                    <i class="fas fa-utensils"></i>
                    ID: <?= $id ?>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <!-- Résumé des valeurs actuelles -->
                <div class="current-values">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        Valeurs actuelles
                    </h3>
                    <div class="value-tags">
                        <span class="value-tag"><strong>Nom:</strong> <?= htmlspecialchars($repas->getNom()) ?></span>
                        <span class="value-tag"><strong>Type:</strong> <?= $repas->getType() ?></span>
                        <span class="value-tag"><strong>🔥 Calories:</strong> <?= $repas->getCalories() ?> kcal</span>
                        <span class="value-tag"><strong>💪 Protéines:</strong> <?= $repas->getProteines() ?> g</span>
                        <span class="value-tag"><strong>🍞 Glucides:</strong> <?= $repas->getGlucides() ?> g</span>
                        <span class="value-tag"><strong>🧈 Lipides:</strong> <?= $repas->getLipides() ?> g</span>
                    </div>
                </div>

                <form action="" method="POST" id="editRepasForm">
                    <div class="form-grid">
                        <!-- Nom du repas -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-utensils"></i>
                                Nom du repas <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-book"></i>
                                <input type="text" name="nom" value="<?= htmlspecialchars($repas->getNom()) ?>" placeholder="Ex: Salade César, Poulet rôti..." required>
                            </div>
                        </div>

                        <!-- Type de repas -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-mug-hot"></i>
                                Type de repas <span class="required">*</span>
                            </label>
                            <div class="type-repas-group">
                                <div class="type-repas-option">
                                    <input type="radio" name="type" id="type_petitdejeuner" value="PETIT_DEJEUNER" <?= $repas->getType() == 'PETIT_DEJEUNER' ? 'checked' : '' ?> required>
                                    <label for="type_petitdejeuner">
                                        <i class="fas fa-coffee"></i>
                                        Petit déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type" id="type_dejeuner" value="DEJEUNER" <?= $repas->getType() == 'DEJEUNER' ? 'checked' : '' ?>>
                                    <label for="type_dejeuner">
                                        <i class="fas fa-utensils"></i>
                                        Déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type" id="type_diner" value="DINER" <?= $repas->getType() == 'DINER' ? 'checked' : '' ?>>
                                    <label for="type_diner">
                                        <i class="fas fa-moon"></i>
                                        Dîner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type" id="type_collation" value="COLLATION" <?= $repas->getType() == 'COLLATION' ? 'checked' : '' ?>>
                                    <label for="type_collation">
                                        <i class="fas fa-apple-alt"></i>
                                        Collation
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Calories -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-fire"></i>
                                Calories (kcal)
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateCalories(-50)">-</button>
                                <input type="number" name="calories" id="calories" min="0" max="2000" value="<?= $repas->getCalories() ?>" step="10">
                                <button type="button" onclick="updateCalories(50)">+</button>
                            </div>
                        </div>

                        <!-- Protéines -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-dumbbell"></i>
                                Protéines (g)
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateProteines(-5)">-</button>
                                <input type="number" name="proteines" id="proteines" min="0" max="200" value="<?= $repas->getProteines() ?>" step="1">
                                <button type="button" onclick="updateProteines(5)">+</button>
                            </div>
                        </div>

                        <!-- Glucides -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-bread-slice"></i>
                                Glucides (g)
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateGlucides(-5)">-</button>
                                <input type="number" name="glucides" id="glucides" min="0" max="300" value="<?= $repas->getGlucides() ?>" step="1">
                                <button type="button" onclick="updateGlucides(5)">+</button>
                            </div>
                        </div>

                        <!-- Lipides -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-oil-can"></i>
                                Lipides (g)
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateLipides(-5)">-</button>
                                <input type="number" name="lipides" id="lipides" min="0" max="150" value="<?= $repas->getLipides() ?>" step="1">
                                <button type="button" onclick="updateLipides(5)">+</button>
                            </div>
                        </div>

                        <!-- Résumé nutritionnel -->
                        <div class="form-group full-width">
                            <div class="nutrition-summary">
                                <h4><i class="fas fa-chart-pie"></i> Résumé nutritionnel (après modification)</h4>
                                <div class="summary-stats">
                                    <div class="stat">
                                        <span class="stat-label">🔥 Calories:</span>
                                        <span class="stat-value" id="totalCalories"><?= $repas->getCalories() ?></span>
                                        <span class="stat-unit">kcal</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">💪 Protéines:</span>
                                        <span class="stat-value" id="totalProteines"><?= $repas->getProteines() ?></span>
                                        <span class="stat-unit">g</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">🍞 Glucides:</span>
                                        <span class="stat-value" id="totalGlucides"><?= $repas->getGlucides() ?></span>
                                        <span class="stat-unit">g</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">🧈 Lipides:</span>
                                        <span class="stat-value" id="totalLipides"><?= $repas->getLipides() ?></span>
                                        <span class="stat-unit">g</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="repasList.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    </div>
                </form>
                
                <div class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Les champs marqués d'un <span class="required">*</span> sont obligatoires.<br>
                    <i class="fas fa-calculator"></i> Total calories ≈ (Protéines × 4) + (Glucides × 4) + (Lipides × 9)
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/repas.js"></script>
</body>
</html>