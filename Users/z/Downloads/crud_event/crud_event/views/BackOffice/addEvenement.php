<?php
// views/BackOffice/evenement/addEvenement.php
include '../../controleurs/EvenementController.php';
require_once __DIR__ . '/../../models/Evenement.php';

$error   = "";
$success = "";
$evenementController = new EvenementController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre          = $_POST['titre']          ?? '';
    $description    = $_POST['description']    ?? '';
    $type_evenement = $_POST['type_evenement'] ?? null;
    $date_evenement = $_POST['date_evenement'] ?? '';
    $lieu           = $_POST['lieu']           ?? '';
    $nb_places_max  = $_POST['nb_places_max']  ?? 0;
    $statut         = $_POST['statut']         ?? 'ACTIF';

    if (!empty($titre) && !empty($description) && !empty($type_evenement) && !empty($date_evenement) && !empty($lieu)) {
        $evenement = new Evenement(
            htmlspecialchars($titre),
            htmlspecialchars($description),
            $type_evenement,
            $date_evenement,
            htmlspecialchars($lieu),
            intval($nb_places_max),
            $statut
        );

        $evenementController->addEvenement($evenement);
        header('Location: evenementList.php');
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
    <title>Ajouter un Événement - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/evenement.css">
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un événement
                </h1>
                <p>Remplissez les informations ci-dessous pour créer un nouvel événement nutritionnel</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="addEvenementForm">
                    <div class="form-grid">

                        <!-- Titre -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-heading"></i>
                                Titre <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-calendar-alt"></i>
                                <input type="text" name="titre" id="titre"
                                       placeholder="Ex: Atelier Nutrition Méditerranéenne" required>
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
                                <textarea name="description" id="description" rows="4"
                                          placeholder="Décrivez l'événement en détail... (minimum 20 caractères)" required></textarea>
                            </div>
                            <small>Minimum 20 caractères</small>
                        </div>

                        <!-- Date -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-calendar-day"></i>
                                Date de l'événement <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-calendar"></i>
                                <input type="date" name="date_evenement" id="date_evenement"
                                       min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <!-- Lieu -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-map-marker-alt"></i>
                                Lieu <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fas fa-location-dot"></i>
                                <input type="text" name="lieu" id="lieu"
                                       placeholder="Ex: Centre Culturel de Tunis" required>
                            </div>
                        </div>

                        <!-- Nombre de places -->
                        <div class="form-group">
                            <label>
                                <i class="fas fa-users"></i>
                                Nombre de places max <span class="required">*</span>
                            </label>
                            <div class="places-input">
                                <button type="button" onclick="updatePlaces(-10)">−</button>
                                <input type="number" name="nb_places_max" id="nb_places_max"
                                       min="1" max="500" value="30" required>
                                <button type="button" onclick="updatePlaces(10)">+</button>
                            </div>
                        </div>

                        <!-- Type d'événement - OBLIGATOIRE -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-tag"></i>
                                Type d'événement <span class="required">*</span>
                            </label>
                            <div class="type-event-group" id="typeEvenementGroup">
                                <div class="type-event-option">
                                    <input type="radio" name="type_evenement" id="type_sport" value="SPORT" required>
                                    <label for="type_sport">
                                        <i class="fas fa-running"></i>
                                        🏃 Sport
                                    </label>
                                </div>
                                <div class="type-event-option">
                                    <input type="radio" name="type_evenement" id="type_nutrition" value="NUTRITION">
                                    <label for="type_nutrition">
                                        <i class="fas fa-apple-alt"></i>
                                        🥗 Nutrition
                                    </label>
                                </div>
                                <div class="type-event-option">
                                    <input type="radio" name="type_evenement" id="type_workshop" value="WORKSHOP">
                                    <label for="type_workshop">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        📚 Workshop
                                    </label>
                                </div>
                                <div class="type-event-option">
                                    <input type="radio" name="type_evenement" id="type_autre" value="AUTRE">
                                    <label for="type_autre">
                                        <i class="fas fa-calendar-star"></i>
                                        📅 Autre
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Statut - OBLIGATOIRE -->
                        <div class="form-group full-width">
                            <label>
                                <i class="fas fa-circle"></i>
                                Statut <span class="required">*</span>
                            </label>
                            <div class="statut-event-group" id="statutGroup">
                                <div class="statut-event-option">
                                    <input type="radio" name="statut" id="statut_actif" value="ACTIF" checked>
                                    <label for="statut_actif">
                                        <i class="fas fa-check-circle"></i>
                                        ✅ Actif
                                    </label>
                                </div>
                                <div class="statut-event-option">
                                    <input type="radio" name="statut" id="statut_cancelled" value="CANCELLED">
                                    <label for="statut_cancelled">
                                        <i class="fas fa-times-circle"></i>
                                        ❌ Annulé
                                    </label>
                                </div>
                                <div class="statut-event-option">
                                    <input type="radio" name="statut" id="statut_completed" value="COMPLETED">
                                    <label for="statut_completed">
                                        <i class="fas fa-flag-checkered"></i>
                                        🏁 Terminé
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Ajouter l'événement
                            </button>
                            <button type="reset" class="btn btn-secondary" onclick="return confirmReset()">
                                <i class="fas fa-undo"></i>
                                Réinitialiser
                            </button>
                            <a href="evenementList.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        </div>

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

    <script src="../assets/js/evenement.js"></script>
</body>
</html>