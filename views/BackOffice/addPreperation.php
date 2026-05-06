<?php
// views/backOffice/preparation/addPreperation.php
include '../../controleurs/PreperationController.php';
require_once __DIR__ . '/../../models/preperation.php';

$error = "";
$success = "";
$preperationController = new PreperationController();
$recettes = $preperationController->getAllRecettes();

// Récupérer l'ID de la recette depuis l'URL (si on vient de viewRecette.php)
$recette_id = isset($_GET['recette_id']) ? intval($_GET['recette_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_recette = $_POST['id_recette'] ?? null;
    $ordre = $_POST['ordre'] ?? null;
    $instruction = $_POST['instruction'] ?? '';
    $duree = $_POST['duree'] ?? 0;
    $temperature = $_POST['temperature'] ?? null;
    $type_action = $_POST['type_action'] ?? null;
    $outil_utilise = $_POST['outil_utilise'] ?? null;
    $quantite_ingredient = $_POST['quantite_ingredient'] ?? null;
    $astuce = $_POST['astuce'] ?? null;
    
    if (!empty($id_recette) && !empty($instruction)) {
        if (!$ordre || $ordre <= 0) {
            $ordre = $preperationController->getNextOrdre($id_recette);
        }
        
        $preperation = new Preperation(
            $ordre,
            htmlspecialchars($instruction),
            intval($duree),
            !empty($temperature) ? intval($temperature) : null,
            $type_action,
            $outil_utilise,
            htmlspecialchars($quantite_ingredient),
            htmlspecialchars($astuce),
            $id_recette
        );
        
        $result = $preperationController->addPreperation($preperation);
        if ($result) {
            header('Location: preperationList.php?id=' . $id_recette);
            exit;
        } else {
            $error = "Erreur lors de l'ajout de l'étape.";
        }
    } else {
        $error = "Veuillez sélectionner une recette et saisir une instruction.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une étape - Préparation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        .radio-group-modern {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 10px;
        }
        
        .radio-option-modern {
            position: relative;
            flex: 1;
            min-width: 120px;
        }
        
        .radio-option-modern input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        
        .radio-option-modern label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .radio-option-modern input[type="radio"]:checked + label {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-color: #4CAF50;
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);
        }
        
        .radio-option-modern label i {
            font-size: 2rem;
            color: #4CAF50;
        }
        
        .radio-option-modern label span {
            font-weight: 500;
            color: #333;
        }
        
        .form-card {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .form-content {
            padding: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group label i {
            color: #4CAF50;
            margin-right: 8px;
        }
        
        .required {
            color: #f44336;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
        
        .input-icon input,
        .input-icon select,
        .input-icon textarea {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 14px;
            transition: 0.3s;
        }
        
        .input-icon textarea {
            padding: 12px 15px 12px 40px;
            resize: vertical;
        }
        
        .personne-input {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .personne-input button {
            width: 40px;
            height: 40px;
            border: none;
            background: #f0f2f5;
            border-radius: 12px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .personne-input button:hover {
            background: #4CAF50;
            color: white;
        }
        
        .personne-input input {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 12px;
        }
        
        .form-actions {
            grid-column: span 2;
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #388e3c;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 12px;
            margin: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .error-field {
            color: #f44336;
            font-size: 11px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .error-summary {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }
        
        .error-summary ul {
            margin: 10px 0 0 20px;
        }
        
        small {
            font-size: 12px;
            color: #999;
            display: block;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
            .form-actions {
                flex-direction: column;
            }
            .radio-group-modern {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Ajouter une étape de préparation
                </h1>
                <p>Ajoutez une nouvelle étape à une recette existante</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="addPreperationForm">
                    <div class="form-grid">
                        <!-- Sélection de la recette - OBLIGATOIRE -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-utensils"></i>
                                Recette <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-book"></i>
                                <select name="id_recette" id="recetteSelect" required>
                                    <option value="">-- Sélectionnez une recette --</option>
                                    <?php foreach ($recettes as $recette): ?>
                                        <option value="<?= $recette['id_recette'] ?>" <?= ($recette_id == $recette['id_recette']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($recette['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Ordre de l'étape - OPTIONNEL -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-sort-numeric-down"></i>
                                Numéro d'ordre
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateOrdre(-1)">-</button>
                                <input type="number" name="ordre" id="ordre" min="1" value="" placeholder="Auto">
                                <button type="button" onclick="updateOrdre(1)">+</button>
                            </div>
                            <small>Laissez vide pour ajouter à la fin</small>
                        </div>

                        <!-- Durée - OBLIGATOIRE -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Durée (minutes) <span class="required">*</span>
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateDuree(-5)">-</button>
                                <input type="number" name="duree" id="duree" min="0" value="0" required>
                                <button type="button" onclick="updateDuree(5)">+</button>
                            </div>
                        </div>

                        <!-- Température - OBLIGATOIRE -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-thermometer-half"></i>
                                Température (°C) <span class="required">*</span>
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateTemperature(-10)">-</button>
                                <input type="number" name="temperature" id="temperature" min="0" value="0" required>
                                <button type="button" onclick="updateTemperature(10)">+</button>
                            </div>
                        </div>

                        <!-- Type d'action - OBLIGATOIRE -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-cut"></i>
                                Type d'action <span class="required">*</span>
                            </label>
                            <div class="radio-group-modern" id="actionGroup">
                                <div class="radio-option-modern">
                                    <input type="radio" name="type_action" id="action_couper" value="COUPER" required>
                                    <label for="action_couper">
                                        <i class="fas fa-cut"></i>
                                        <span>🔪 Couper</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="type_action" id="action_melanger" value="MELANGER">
                                    <label for="action_melanger">
                                        <i class="fas fa-mix"></i>
                                        <span>🥄 Mélanger</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="type_action" id="action_cuisson" value="CUISSON">
                                    <label for="action_cuisson">
                                        <i class="fas fa-fire"></i>
                                        <span>🔥 Cuisson</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Outil utilisé - OBLIGATOIRE -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-tools"></i>
                                Outil utilisé <span class="required">*</span>
                            </label>
                            <div class="radio-group-modern" id="outilGroup">
                                <div class="radio-option-modern">
                                    <input type="radio" name="outil_utilise" id="outil_four" value="FOUR" required>
                                    <label for="outil_four">
                                        <i class="fas fa-oven"></i>
                                        <span>🔥 Four</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="outil_utilise" id="outil_mixeur" value="MIXEUR">
                                    <label for="outil_mixeur">
                                        <i class="fas fa-blender"></i>
                                        <span>🥤 Mixeur</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="outil_utilise" id="outil_cuillere" value="CUILLERE">
                                    <label for="outil_cuillere">
                                        <i class="fas fa-utensil-spoon"></i>
                                        <span>🥄 Cuillère</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="outil_utilise" id="outil_rape" value="RAPE">
                                    <label for="outil_rape">
                                        <i class="fas fa-cheese"></i>
                                        <span>🫚 Râpe</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Quantité ingrédient - OPTIONNEL -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-weight-hanging"></i>
                                Quantité ingrédient
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-balance-scale"></i>
                                <input type="text" name="quantite_ingredient" id="quantite" placeholder="Ex: 200g, 1 cuillère à soupe...">
                            </div>
                        </div>

                        <!-- Instruction - OBLIGATOIRE -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-align-left"></i>
                                Instruction <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-pen"></i>
                                <textarea name="instruction" id="instruction" rows="4" placeholder="Décrivez l'étape en détail..." required></textarea>
                            </div>
                            <small>Minimum 10 caractères</small>
                        </div>

                        <!-- Astuce - OPTIONNEL -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-lightbulb"></i>
                                Astuce
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-star"></i>
                                <textarea name="astuce" id="astuce" rows="2" placeholder="Conseil ou astuce pour cette étape..."></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Ajouter l'étape
                            </button>
                            <button type="reset" class="btn btn-secondary" onclick="return confirmReset()">
                                <i class="fas fa-undo"></i>
                                Réinitialiser
                            </button>
                            <a href="preperationList.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Appel du script externe -->
    <script src="../assets/js/preperation.js"></script>
</body>
</html>