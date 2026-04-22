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
                header('Location: preperationList.php?id=' . $recette_id_retour);
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
    <link rel="stylesheet" href="../assets/css/preperation.css">
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

                        <!-- Instruction - OBLIGATOIRE -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-align-left"></i>
                                Instruction <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-pen"></i>
                                <textarea name="instruction" id="instruction" rows="4" placeholder="Décrivez l'étape en détail..." required><?= htmlspecialchars($preperation->getInstruction()) ?></textarea>
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
                                <textarea name="astuce" id="astuce" rows="2" placeholder="Conseil ou astuce pour cette étape..."><?= htmlspecialchars($preperation->getAstuce()) ?></textarea>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Enregistrer les modifications
                            </button>
                            <?php if ($recette_id_retour): ?>
                                <a href="viewRecette.php?id=<?= $recette_id_retour ?>" class="btn btn-secondary">
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

    <!-- Appel du script externe -->
    <script src="../assets/js/preperation.js"></script>
</body>
</html>