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
    <link rel="stylesheet" href="../assets/css/preperation.css">
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