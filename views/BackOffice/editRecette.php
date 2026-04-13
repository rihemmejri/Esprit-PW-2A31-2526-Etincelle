<?php
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$recetteController = new RecetteController();

// Récupérer l'ID de la recette à modifier
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: recetteList.php');
    exit;
}

// Récupérer les infos de la recette
$recette = $recetteController->getRecetteById($id);

if (!$recette) {
    header('Location: recetteList.php');
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $temps_preparation = $_POST['temps_preparation'];
    $difficulte = $_POST['difficulte'];
    $type_repas = $_POST['type_repas'];
    $origine = $_POST['origine'];
    $nb_personne = $_POST['nb_personne'];

    if (!empty($nom) && !empty($description) && !empty($type_repas)) {
        // Mettre à jour l'objet Recette
        $recette->setNom($nom);
        $recette->setDescription($description);
        $recette->setTempsPreparation($temps_preparation);
        $recette->setDifficulte($difficulte);
        $recette->setTypeRepas($type_repas);
        $recette->setOrigine($origine);
        $recette->setNbPersonne($nb_personne);
        
        // Mettre à jour dans la BD
        $recetteController->updateRecette($recette);
        
        // Redirection
        header('Location: recetteList.php');
        exit;
    } else {
        $error = "Tous les champs obligatoires doivent être remplis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Recette - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-edit">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Modifier la recette
                </h1>
                <p>Modifiez les informations de la recette #<?= $id ?></p>
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
                        <span class="value-tag"><strong>Nom:</strong> <?= htmlspecialchars($recette->getNom()) ?></span>
                        <span class="value-tag"><strong>Difficulté:</strong> <?= $recette->getDifficulte() ?></span>
                        <span class="value-tag"><strong>Type:</strong> <?= $recette->getTypeRepas() ?></span>
                        <span class="value-tag"><strong>Temps:</strong> <?= $recette->getTempsPreparation() ?> min</span>
                        <span class="value-tag"><strong>Personnes:</strong> <?= $recette->getNbPersonne() ?></span>
                    </div>
                </div>

                <form action="" method="POST" id="editRecetteForm">
                    <div class="form-grid">
                        <!-- Nom de la recette -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-utensils"></i>
                                Nom de la recette <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-book"></i>
                                <input type="text" name="nom" value="<?= htmlspecialchars($recette->getNom()) ?>" placeholder="Ex: Tajine Marocain" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-align-left"></i>
                                Description <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-pen"></i>
                                <textarea name="description" placeholder="Décrivez la recette..." required><?= htmlspecialchars($recette->getDescription()) ?></textarea>
                            </div>
                        </div>

                        <!-- Temps de préparation -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Temps de préparation (minutes)
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updateTemps(-5)">-</button>
                                <input type="number" name="temps_preparation" id="temps_preparation" min="0" value="<?= $recette->getTempsPreparation() ?>">
                                <button type="button" onclick="updateTemps(5)">+</button>
                            </div>
                        </div>

                        <!-- Nombre de personnes -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-users"></i>
                                Nombre de personnes
                            </label>
                            <div class="personne-input">
                                <button type="button" onclick="updatePersonnes(-1)">-</button>
                                <input type="number" name="nb_personne" id="nb_personne" min="1" value="<?= $recette->getNbPersonne() ?>">
                                <button type="button" onclick="updatePersonnes(1)">+</button>
                            </div>
                        </div>

                        <!-- Difficulté -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-chart-line"></i>
                                Difficulté
                            </label>
                            <div class="difficulte-group">
                                <div class="difficulte-option">
                                    <input type="radio" name="difficulte" id="difficulte_facile" value="FACILE" <?= $recette->getDifficulte() == 'FACILE' ? 'checked' : '' ?>>
                                    <label for="difficulte_facile">
                                        <i class="fas fa-smile"></i>
                                        Facile
                                    </label>
                                </div>
                                <div class="difficulte-option">
                                    <input type="radio" name="difficulte" id="difficulte_moyen" value="MOYEN" <?= $recette->getDifficulte() == 'MOYEN' ? 'checked' : '' ?>>
                                    <label for="difficulte_moyen">
                                        <i class="fas fa-meh"></i>
                                        Moyen
                                    </label>
                                </div>
                                <div class="difficulte-option">
                                    <input type="radio" name="difficulte" id="difficulte_difficile" value="DIFFICILE" <?= $recette->getDifficulte() == 'DIFFICILE' ? 'checked' : '' ?>>
                                    <label for="difficulte_difficile">
                                        <i class="fas fa-frown"></i>
                                        Difficile
                                    </label>
                                </div>
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
                                    <input type="radio" name="type_repas" id="type_petitdejeuner" value="PETIT_DEJEUNER" <?= $recette->getTypeRepas() == 'PETIT_DEJEUNER' ? 'checked' : '' ?> required>
                                    <label for="type_petitdejeuner">
                                        <i class="fas fa-coffee"></i>
                                        Petit déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type_repas" id="type_dejeuner" value="DEJEUNER" <?= $recette->getTypeRepas() == 'DEJEUNER' ? 'checked' : '' ?>>
                                    <label for="type_dejeuner">
                                        <i class="fas fa-utensils"></i>
                                        Déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type_repas" id="type_diner" value="DINER" <?= $recette->getTypeRepas() == 'DINER' ? 'checked' : '' ?>>
                                    <label for="type_diner">
                                        <i class="fas fa-moon"></i>
                                        Dîner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type_repas" id="type_dessert" value="DESSERT" <?= $recette->getTypeRepas() == 'DESSERT' ? 'checked' : '' ?>>
                                    <label for="type_dessert">
                                        <i class="fas fa-cake-candles"></i>
                                        Dessert
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Origine -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-globe"></i>
                                Origine
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" name="origine" value="<?= htmlspecialchars($recette->getOrigine()) ?>" placeholder="Ex: Marocaine, Italienne, Française...">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="recetteList.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    </div>
                </form>
                
                <div class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Les champs marqués d'un <span class="required">*</span> sont obligatoires
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/recette.js"></script>
</body>
</html>