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
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
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