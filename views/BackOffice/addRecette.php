<?php
<<<<<<< HEAD
session_start();
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$error = "";
$success = "";
$recetteController = new RecetteController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["nom"]) && isset($_POST["description"]) && isset($_POST["type_repas"])) {
        if (!empty($_POST["nom"]) && !empty($_POST["description"]) && !empty($_POST["type_repas"])) {
            
            $recette = new recette(
                htmlspecialchars($_POST['nom']),
                htmlspecialchars($_POST['description']),
                intval($_POST['temps_preparation'] ?? 0),
                htmlspecialchars($_POST['difficulte'] ?? 'MOYEN'),
                htmlspecialchars($_POST['type_repas']),
                htmlspecialchars($_POST['origine'] ?? ''),
                intval($_POST['nb_personne'] ?? 1)
            );
            
            $recetteController->addRecette($recette);
<<<<<<< HEAD
            $_SESSION['success_message'] = 'Recette ajoutée avec succès';
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
            header('Location: recetteList.php');
            exit;
        } else {
            $error = "Tous les champs obligatoires doivent être remplis.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Recette - Nutrition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Ajouter une recette
                </h1>
                <p>Remplissez les informations ci-dessous pour ajouter une nouvelle recette</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="addRecetteForm">
                    <div class="form-grid">
                        <!-- Nom de la recette -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-utensils"></i>
                                Nom de la recette <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-book"></i>
                                <input type="text" name="nom" placeholder="Ex: Tajine Marocain" required>
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
                                <textarea name="description" placeholder="Décrivez la recette (ingrédients, étapes...) - minimum 20 caractères" required></textarea>
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
                                <input type="number" name="temps_preparation" id="temps_preparation" min="0" max="1440" value="30">
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
                                <input type="number" name="nb_personne" id="nb_personne" min="1" max="100" value="4">
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
                                    <input type="radio" name="difficulte" id="difficulte_facile" value="FACILE">
                                    <label for="difficulte_facile">
                                        <i class="fas fa-smile"></i>
                                        Facile
                                    </label>
                                </div>
                                <div class="difficulte-option">
                                    <input type="radio" name="difficulte" id="difficulte_moyen" value="MOYEN" checked>
                                    <label for="difficulte_moyen">
                                        <i class="fas fa-meh"></i>
                                        Moyen
                                    </label>
                                </div>
                                <div class="difficulte-option">
                                    <input type="radio" name="difficulte" id="difficulte_difficile" value="DIFFICILE">
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
                                    <input type="radio" name="type_repas" id="type_petitdejeuner" value="PETIT_DEJEUNER" required>
                                    <label for="type_petitdejeuner">
                                        <i class="fas fa-coffee"></i>
                                        Petit déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type_repas" id="type_dejeuner" value="DEJEUNER">
                                    <label for="type_dejeuner">
                                        <i class="fas fa-utensils"></i>
                                        Déjeuner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type_repas" id="type_diner" value="DINER">
                                    <label for="type_diner">
                                        <i class="fas fa-moon"></i>
                                        Dîner
                                    </label>
                                </div>
                                <div class="type-repas-option">
                                    <input type="radio" name="type_repas" id="type_dessert" value="DESSERT">
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
                                <input type="text" name="origine" placeholder="Ex: Marocaine, Italienne, Française...">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Ajouter la recette
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i>
                            Réinitialiser
                        </button>
                        <a href="recetteList.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                    </div>
                </form>
                
                <div class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Les champs marqués d'un <span class="required">*</span> sont obligatoires.<br>
                    <i class="fas fa-check-circle"></i> La description doit contenir au moins 20 caractères.
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/recette.js"></script>
</body>
</html>