<?php
// views/backOffice/preparation/editPreperation.php
include '../../controleurs/PreperationController.php';
require_once __DIR__ . '/../../models/preperation.php';

$preperationController = new PreperationController();
$id = $_GET['id'] ?? null;
$recette_id_retour = $_GET['recette_id'] ?? null;

if (!$id) {
    header('Location: preperationList.php');
    exit;
}

$preperation = $preperationController->getPreperationById($id);
if (!$preperation) {
    header('Location: preperationList.php');
    exit;
}

$recettes = $preperationController->getAllRecettes();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_recette = $_POST['id_recette'];
    $ordre = $_POST['ordre'];
    $instruction = $_POST['instruction'];
    $duree = $_POST['duree'];
    $temperature = $_POST['temperature'];
    $type_action = $_POST['type_action'];
    $outil_utilise = $_POST['outil_utilise'];
    $quantite_ingredient = $_POST['quantite_ingredient'];
    $astuce = $_POST['astuce'];

    if (!empty($id_recette) && !empty($instruction)) {
        $preperation->setIdRecette($id_recette);
        $preperation->setOrdre($ordre);
        $preperation->setInstruction(htmlspecialchars($instruction));
        $preperation->setDuree(intval($duree));
        $preperation->setTemperature(!empty($temperature) ? intval($temperature) : null);
        $preperation->setTypeAction($type_action);
        $preperation->setOutilUtilise($outil_utilise);
        $preperation->setQuantiteIngredient(htmlspecialchars($quantite_ingredient));
        $preperation->setAstuce(htmlspecialchars($astuce));
        
        if ($preperationController->updatePreperation($preperation)) {
            if ($recette_id_retour) {
                header('Location: ../recette/viewRecette.php?id=' . $recette_id_retour);
            } else {
                header('Location: preperationList.php');
            }
            exit;
        } else {
            $error = "Erreur lors de la mise à jour.";
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
    <title>Modifier une étape - Préparation</title>
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
        
        .recette-info {
            background: #e8f5e9;
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2e7d32;
            font-weight: 500;
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
    <div class="container-edit">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Modifier l'étape #<?= $id ?>
                </h1>
                <p>Modifiez les informations de l'étape de préparation</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <div class="recette-info">
                    <i class="fas fa-utensils"></i>
                    Recette: <strong><?= htmlspecialchars($preperation->getRecetteNom()) ?></strong>
                </div>

                <form action="" method="POST" id="editPreperationForm">
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
                                        <option value="<?= $recette['id_recette'] ?>" 
                                            <?= $recette['id_recette'] == $preperation->getIdRecette() ? 'selected' : '' ?>>
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
                                <input type="number" name="ordre" id="ordre" min="1" value="<?= $preperation->getOrdre() ?>" placeholder="Auto">
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
                                <input type="number" name="duree" id="duree" min="0" value="<?= $preperation->getDuree() ?>" required>
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
                                <input type="number" name="temperature" id="temperature" min="0" value="<?= $preperation->getTemperature() ?>" required>
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
                                    <input type="radio" name="type_action" id="action_couper" value="COUPER" 
                                        <?= $preperation->getTypeAction() == 'COUPER' ? 'checked' : '' ?> required>
                                    <label for="action_couper">
                                        <i class="fas fa-cut"></i>
                                        <span>🔪 Couper</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="type_action" id="action_melanger" value="MELANGER"
                                        <?= $preperation->getTypeAction() == 'MELANGER' ? 'checked' : '' ?>>
                                    <label for="action_melanger">
                                        <i class="fas fa-mix"></i>
                                        <span>🥄 Mélanger</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="type_action" id="action_cuisson" value="CUISSON"
                                        <?= $preperation->getTypeAction() == 'CUISSON' ? 'checked' : '' ?>>
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
                                    <input type="radio" name="outil_utilise" id="outil_four" value="FOUR"
                                        <?= $preperation->getOutilUtilise() == 'FOUR' ? 'checked' : '' ?> required>
                                    <label for="outil_four">
                                        <i class="fas fa-oven"></i>
                                        <span>🔥 Four</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="outil_utilise" id="outil_mixeur" value="MIXEUR"
                                        <?= $preperation->getOutilUtilise() == 'MIXEUR' ? 'checked' : '' ?>>
                                    <label for="outil_mixeur">
                                        <i class="fas fa-blender"></i>
                                        <span>🥤 Mixeur</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="outil_utilise" id="outil_cuillere" value="CUILLERE"
                                        <?= $preperation->getOutilUtilise() == 'CUILLERE' ? 'checked' : '' ?>>
                                    <label for="outil_cuillere">
                                        <i class="fas fa-utensil-spoon"></i>
                                        <span>🥄 Cuillère</span>
                                    </label>
                                </div>
                                <div class="radio-option-modern">
                                    <input type="radio" name="outil_utilise" id="outil_rape" value="RAPE"
                                        <?= $preperation->getOutilUtilise() == 'RAPE' ? 'checked' : '' ?>>
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
                                <input type="text" name="quantite_ingredient" id="quantite" value="<?= htmlspecialchars($preperation->getQuantiteIngredient()) ?>" placeholder="Ex: 200g, 1 cuillère à soupe...">
                            </div>
                        </div>

                        <!-- Instruction - OBLIGATOIRE (uniquement lettres, espaces, ponctuation) -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-align-left"></i>
                                Instruction <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-pen"></i>
                                <textarea name="instruction" id="instruction" rows="4" placeholder="Décrivez l'étape en détail..." required><?= htmlspecialchars($preperation->getInstruction()) ?></textarea>
                            </div>
                            <small>Uniquement lettres, espaces, points, virgules, ! et ? - Pas de chiffres ni symboles</small>
                        </div>

                        <!-- Astuce - OPTIONNEL (uniquement lettres, espaces, ponctuation) -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-lightbulb"></i>
                                Astuce
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-star"></i>
                                <textarea name="astuce" id="astuce" rows="2" placeholder="Conseil ou astuce pour cette étape..."><?= htmlspecialchars($preperation->getAstuce()) ?></textarea>
                            </div>
                            <small>Uniquement lettres, espaces, points, virgules, ! et ? - Pas de chiffres ni symboles</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Enregistrer les modifications
                            </button>
                            <?php if ($recette_id_retour): ?>
                                <a href="../recette/viewRecette.php?id=<?= $recette_id_retour ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            <?php else: ?>
                                <a href="preperationList.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fonctions pour les boutons + et -
        function updateOrdre(delta) {
            let input = document.getElementById('ordre');
            let value = parseInt(input.value) || 0;
            let newValue = value + delta;
            if (newValue >= 1) input.value = newValue;
            validateOrdre();
        }
        
        function updateDuree(delta) {
            let input = document.getElementById('duree');
            let value = parseInt(input.value) || 0;
            let newValue = value + delta;
            if (newValue >= 0) input.value = newValue;
            validateDuree();
        }
        
        function updateTemperature(delta) {
            let input = document.getElementById('temperature');
            let value = parseInt(input.value) || 0;
            let newValue = value + delta;
            if (newValue >= 0) input.value = newValue;
            validateTemperature();
        }
        
        // ========== FONCTIONS DE VALIDATION ==========
        
        function showError(input, message) {
            if (!input) return;
            clearError(input);
            input.style.borderColor = '#f44336';
            input.style.borderWidth = '2px';
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-field';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
        
        function clearError(input) {
            if (!input) return;
            input.style.borderColor = '#ddd';
            input.style.borderWidth = '1px';
            
            const nextSibling = input.nextSibling;
            if (nextSibling && nextSibling.className === 'error-field') {
                nextSibling.remove();
            }
        }
        
        // Validation Recette (obligatoire)
        function validateRecette() {
            const recette = document.getElementById('recetteSelect');
            if (!recette.value) {
                showError(recette, 'Veuillez sélectionner une recette');
                return false;
            } else {
                clearError(recette);
                return true;
            }
        }
        
        // Validation Instruction (obligatoire + seulement lettres, espaces, ponctuation)
        function validateInstruction() {
            const instruction = document.getElementById('instruction');
            const value = instruction.value.trim();
            const regex = /^[a-zA-ZÀ-ÿ\s\.,!?'\-]+$/;
            
            if (!value) {
                showError(instruction, 'L\'instruction est obligatoire');
                return false;
            } else if (value.length < 10) {
                showError(instruction, 'L\'instruction doit contenir au moins 10 caractères');
                return false;
            } else if (!regex.test(value)) {
                showError(instruction, 'L\'instruction ne doit contenir que des lettres, espaces, points, virgules, ! et ? (pas de chiffres ni symboles)');
                return false;
            } else {
                clearError(instruction);
                return true;
            }
        }
        
        // Validation Astuce (optionnelle mais si renseignée: seulement lettres, espaces, ponctuation)
        function validateAstuce() {
            const astuce = document.getElementById('astuce');
            const value = astuce.value.trim();
            const regex = /^[a-zA-ZÀ-ÿ\s\.,!?'\-]*$/;
            
            if (value !== '' && !regex.test(value)) {
                showError(astuce, 'L\'astuce ne doit contenir que des lettres, espaces, points, virgules, ! et ? (pas de chiffres ni symboles)');
                return false;
            } else if (value !== '' && value.length < 5) {
                showError(astuce, 'L\'astuce doit contenir au moins 5 caractères');
                return false;
            } else {
                clearError(astuce);
                return true;
            }
        }
        
        // Validation Ordre (optionnel)
        function validateOrdre() {
            const ordre = document.getElementById('ordre');
            if (ordre.value !== '') {
                const value = parseInt(ordre.value);
                if (isNaN(value) || value < 1) {
                    showError(ordre, 'Le numéro d\'ordre doit être supérieur à 0');
                    return false;
                } else {
                    clearError(ordre);
                    return true;
                }
            } else {
                clearError(ordre);
                return true;
            }
        }
        
        // Validation Durée (obligatoire)
        function validateDuree() {
            const duree = document.getElementById('duree');
            const value = parseInt(duree.value);
            if (isNaN(value) || value < 0) {
                showError(duree, 'La durée doit être un nombre positif');
                return false;
            } else {
                clearError(duree);
                return true;
            }
        }
        
        // Validation Température (obligatoire)
        function validateTemperature() {
            const temp = document.getElementById('temperature');
            const value = parseInt(temp.value);
            if (isNaN(value) || value < 0) {
                showError(temp, 'La température doit être un nombre positif');
                return false;
            } else if (value > 300) {
                showError(temp, 'La température ne peut pas dépasser 300°C');
                return false;
            } else {
                clearError(temp);
                return true;
            }
        }
        
        // Validation Quantité (optionnelle)
        function validateQuantite() {
            const quantite = document.getElementById('quantite');
            if (quantite.value !== '' && quantite.value.length < 2) {
                showError(quantite, 'La quantité est trop courte');
                return false;
            } else {
                clearError(quantite);
                return true;
            }
        }
        
        // Validation Type d'action (obligatoire - radio)
        function validateTypeAction() {
            const selected = document.querySelector('input[name="type_action"]:checked');
            if (!selected) {
                const group = document.getElementById('actionGroup');
                const errorDiv = document.getElementById('actionError');
                if (!errorDiv) {
                    const error = document.createElement('div');
                    error.id = 'actionError';
                    error.className = 'error-field';
                    error.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez sélectionner un type d\'action';
                    group.parentNode.insertBefore(error, group.nextSibling);
                }
                return false;
            } else {
                const errorDiv = document.getElementById('actionError');
                if (errorDiv) errorDiv.remove();
                return true;
            }
        }
        
        // Validation Outil utilisé (obligatoire - radio)
        function validateOutil() {
            const selected = document.querySelector('input[name="outil_utilise"]:checked');
            if (!selected) {
                const group = document.getElementById('outilGroup');
                const errorDiv = document.getElementById('outilError');
                if (!errorDiv) {
                    const error = document.createElement('div');
                    error.id = 'outilError';
                    error.className = 'error-field';
                    error.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez sélectionner un outil';
                    group.parentNode.insertBefore(error, group.nextSibling);
                }
                return false;
            } else {
                const errorDiv = document.getElementById('outilError');
                if (errorDiv) errorDiv.remove();
                return true;
            }
        }
        
        // ========== ÉVÉNEMENTS EN TEMPS RÉEL ==========
        document.getElementById('recetteSelect').addEventListener('change', validateRecette);
        document.getElementById('instruction').addEventListener('input', validateInstruction);
        document.getElementById('astuce').addEventListener('input', validateAstuce);
        document.getElementById('ordre').addEventListener('input', validateOrdre);
        document.getElementById('duree').addEventListener('input', validateDuree);
        document.getElementById('temperature').addEventListener('input', validateTemperature);
        document.getElementById('quantite').addEventListener('input', validateQuantite);
        
        // Validation des radios au clic
        document.querySelectorAll('input[name="type_action"]').forEach(radio => {
            radio.addEventListener('click', validateTypeAction);
        });
        document.querySelectorAll('input[name="outil_utilise"]').forEach(radio => {
            radio.addEventListener('click', validateOutil);
        });
        
        // ========== VALIDATION AU SUBMIT ==========
        document.getElementById('editPreperationForm').addEventListener('submit', function(e) {
            let isValid = true;
            let errors = [];
            
            if (!validateRecette()) {
                isValid = false;
                errors.push('❌ Veuillez sélectionner une recette');
            }
            if (!validateInstruction()) {
                isValid = false;
                errors.push('❌ L\'instruction est obligatoire (minimum 10 caractères, uniquement lettres et espaces)');
            }
            if (!validateAstuce()) {
                isValid = false;
                errors.push('❌ L\'astuce ne doit contenir que des lettres et espaces');
            }
            if (!validateOrdre()) {
                isValid = false;
                errors.push('❌ Le numéro d\'ordre doit être supérieur à 0');
            }
            if (!validateDuree()) {
                isValid = false;
                errors.push('❌ La durée est obligatoire');
            }
            if (!validateTemperature()) {
                isValid = false;
                errors.push('❌ La température est obligatoire (0-300°C)');
            }
            if (!validateQuantite()) {
                isValid = false;
                errors.push('❌ La quantité ingrédient est trop courte');
            }
            if (!validateTypeAction()) {
                isValid = false;
                errors.push('❌ Veuillez sélectionner un type d\'action');
            }
            if (!validateOutil()) {
                isValid = false;
                errors.push('❌ Veuillez sélectionner un outil');
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Supprimer l'ancien résumé
                const oldSummary = document.querySelector('.error-summary');
                if (oldSummary) oldSummary.remove();
                
                // Créer le résumé des erreurs
                const form = document.querySelector('form');
                const summaryDiv = document.createElement('div');
                summaryDiv.className = 'error-summary';
                let errorHtml = '<strong><i class="fas fa-times-circle"></i> Veuillez corriger les erreurs suivantes :</strong><ul>';
                errors.forEach(error => {
                    errorHtml += `<li>${error}</li>`;
                });
                errorHtml += '</ul>';
                summaryDiv.innerHTML = errorHtml;
                form.insertBefore(summaryDiv, form.firstChild);
                summaryDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        // Initialiser les validations au chargement
        document.addEventListener('DOMContentLoaded', function() {
            validateRecette();
            validateInstruction();
            validateDuree();
            validateTemperature();
            validateTypeAction();
            validateOutil();
        });
    </script>
</body>
</html>