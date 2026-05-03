<?php
include '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/repas.php';

$error = "";
$success = "";
$repasController = new RepasController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["nom"]) && isset($_POST["type"])) {
        if (!empty($_POST["nom"]) && !empty($_POST["type"])) {
            
            // Validation des valeurs nutritionnelles
            $calories = intval($_POST['calories'] ?? 0);
            $proteines = floatval($_POST['proteines'] ?? 0);
            $glucides = floatval($_POST['glucides'] ?? 0);
            $lipides = floatval($_POST['lipides'] ?? 0);
            
            // Validation supplémentaire
            if ($calories < 0 || $calories > 2000) {
                $error = "Les calories doivent être comprises entre 0 et 2000 kcal.";
            } elseif ($proteines < 0 || $proteines > 200) {
                $error = "Les protéines doivent être comprises entre 0 et 200 g.";
            } elseif ($glucides < 0 || $glucides > 300) {
                $error = "Les glucides doivent être compris entre 0 et 300 g.";
            } elseif ($lipides < 0 || $lipides > 150) {
                $error = "Les lipides doivent être compris entre 0 et 150 g.";
            } else {
                $repas = new repas(
                    htmlspecialchars($_POST['nom']),
                    htmlspecialchars($_POST['type']),
                    $calories,
                    $proteines,
                    $glucides,
                    $lipides
                );
                
                if ($repasController->addRepas($repas)) {
                    $success = "Repas ajouté avec succès !";
                    header('Location: repasList.php');
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout du repas.";
                }
            }
        } else {
            $error = "Le nom et le type de repas sont obligatoires.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Repas - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            padding: 40px 20px;
        }
        .container-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 1.8rem;
            color: #1a1a2e;
            display: flex;
            align-items: center;
        }
        .header h1 i {
            color: #4CAF50;
            margin-right: 10px;
        }
        .header p {
            color: #666;
            margin-top: 5px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
        }
        .btn-primary, .btn-secondary {
            padding: 12px 25px;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: white;
        }
        .btn-primary { background: #4CAF50; }
        .btn-primary:hover { background: #45a049; transform: translateY(-2px); }
        .btn-secondary { background: #003366; }
        .btn-secondary:hover { background: #002244; transform: translateY(-2px); }
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f2f5;
        }
        .error-message, .success-message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .error-message { background: #ffebee; color: #c62828; }
        .success-message { background: #e8f5e9; color: #2e7d32; }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .full-width { grid-column: 1 / -1; }
        
        .type-repas-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .type-repas-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .type-repas-option label {
            margin-bottom: 0;
            font-weight: normal;
        }
        .personne-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .personne-input button {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .personne-input input {
            width: 100px;
            text-align: center;
        }
        .nutrition-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 10px;
        }
        .summary-stats {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .stat {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .help-text {
            margin-top: 30px;
            padding: 15px;
            background: #e3f2fd;
            color: #1565c0;
            border-radius: 8px;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un repas
                </h1>
                <p>Remplissez les informations ci-dessous pour ajouter un nouveau repas</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="addRepasForm">
                    <div class="form-grid">
                        <!-- Nom du repas -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-utensils"></i>
                                Nom du repas <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-book"></i>
                                <input type="text" name="nom" placeholder="Ex: Salade César, Poulet rôti, Porridge..." required>
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
                                    <input type="radio" name="type" id="type_petitdejeuner" value="PETIT_DEJEUNER" required>
                                    <label for="type_petitdejeuner">
                                        <i class="fas fa-coffee"></i>
                                        Petit déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type" id="type_dejeuner" value="DEJEUNER">
                                    <label for="type_dejeuner">
                                        <i class="fas fa-utensils"></i>
                                        Déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type" id="type_diner" value="DINER">
                                    <label for="type_diner">
                                        <i class="fas fa-moon"></i>
                                        Dîner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type" id="type_collation" value="COLLATION">
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
                                <input type="number" name="calories" id="calories" min="0" max="2000" value="400" step="10">
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
                                <input type="number" name="proteines" id="proteines" min="0" max="200" value="20" step="1">
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
                                <input type="number" name="glucides" id="glucides" min="0" max="300" value="45" step="1">
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
                                <input type="number" name="lipides" id="lipides" min="0" max="150" value="15" step="1">
                                <button type="button" onclick="updateLipides(5)">+</button>
                            </div>
                        </div>

                        <!-- Résumé nutritionnel -->
                        <div class="form-group full-width">
                            <div class="nutrition-summary">
                                <h4><i class="fas fa-chart-pie"></i> Résumé nutritionnel</h4>
                                <div class="summary-stats">
                                    <div class="stat">
                                        <span class="stat-label">🔥 Calories:</span>
                                        <span class="stat-value" id="totalCalories">400</span>
                                        <span class="stat-unit">kcal</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">💪 Protéines:</span>
                                        <span class="stat-value" id="totalProteines">20</span>
                                        <span class="stat-unit">g</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">🍞 Glucides:</span>
                                        <span class="stat-value" id="totalGlucides">45</span>
                                        <span class="stat-unit">g</span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">🧈 Lipides:</span>
                                        <span class="stat-value" id="totalLipides">15</span>
                                        <span class="stat-unit">g</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Ajouter le repas
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                            Réinitialiser
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
                    <i class="fas fa-chart-line"></i> Les valeurs nutritionnelles sont approximatives et peuvent varier selon les ingrédients.<br>
                    <i class="fas fa-calculator"></i> Total calories ≈ (Protéines × 4) + (Glucides × 4) + (Lipides × 9)
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/repas.js"></script>
</body>
</html>