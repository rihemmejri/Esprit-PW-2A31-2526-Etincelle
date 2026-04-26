<?php
// views/BackOffice/evenement/editEvenement.php
include '../../controleurs/EvenementController.php';
require_once __DIR__ . '/../../models/Evenement.php';

$evenementController = new EvenementController();
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: evenementList.php');
    exit;
}

$evenement = $evenementController->getEvenementById($id);
if (!$evenement) {
    header('Location: evenementList.php');
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre          = $_POST['titre']          ?? '';
    $description    = $_POST['description']    ?? '';
    $type_evenement = $_POST['type_evenement'] ?? '';
    $date_evenement = $_POST['date_evenement'] ?? '';
    $lieu           = $_POST['lieu']           ?? '';
    $nb_places_max  = $_POST['nb_places_max']  ?? 0;
    $statut         = $_POST['statut']         ?? 'ACTIF';

    if (!empty($titre) && !empty($description) && !empty($type_evenement) && !empty($lieu)) {
        $evenement->setTitre(htmlspecialchars($titre));
        $evenement->setDescription(htmlspecialchars($description));
        $evenement->setTypeEvenement($type_evenement);
        $evenement->setDateEvenement($date_evenement);
        $evenement->setLieu(htmlspecialchars($lieu));
        $evenement->setNbPlacesMax(intval($nb_places_max));
        $evenement->setStatut($statut);

        $evenementController->updateEvenement($evenement);
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
    <title>Modifier l'Événement - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/evenement.css">
</head>
<body>
    <div class="container-edit">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Modifier l'événement
                </h1>
                <p>Modifiez les informations de l'événement #<?= $id ?></p>
                <div class="event-id-badge">
                    <i class="fas fa-calendar-alt"></i>
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
                        <span class="value-tag"><strong>Titre :</strong> <?= htmlspecialchars($evenement->getTitre()) ?></span>
                        <span class="value-tag"><strong>Type :</strong> <?= $evenement->getTypeEvenement() ?></span>
                        <span class="value-tag"><strong>Date :</strong> <?= date('d/m/Y', strtotime($evenement->getDateEvenement())) ?></span>
                        <span class="value-tag"><strong>Lieu :</strong> <?= htmlspecialchars($evenement->getLieu()) ?></span>
                        <span class="value-tag"><strong>Places :</strong> <?= $evenement->getNbPlacesMax() ?></span>
                        <span class="value-tag"><strong>Statut :</strong> <?= $evenement->getStatut() ?></span>
                    </div>
                </div>

                <form action="" method="POST" id="editEvenementForm">
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
                                       value="<?= htmlspecialchars($evenement->getTitre()) ?>"
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
                                          placeholder="Décrivez l'événement... (minimum 20 caractères)" required><?= htmlspecialchars($evenement->getDescription()) ?></textarea>
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
                                       value="<?= $evenement->getDateEvenement() ?>" required>
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
                                       value="<?= htmlspecialchars($evenement->getLieu()) ?>"
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
                                       min="1" max="500" value="<?= $evenement->getNbPlacesMax() ?>" required>
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
                                    <input type="radio" name="type_evenement" id="type_sport" value="SPORT"
                                           <?= $evenement->getTypeEvenement() == 'SPORT' ? 'checked' : '' ?> required>
                                    <label for="type_sport">
                                        <i class="fas fa-running"></i>
                                        🏃 Sport
                                    </label>
                                </div>
                                <div class="type-event-option">
                                    <input type="radio" name="type_evenement" id="type_nutrition" value="NUTRITION"
                                           <?= $evenement->getTypeEvenement() == 'NUTRITION' ? 'checked' : '' ?>>
                                    <label for="type_nutrition">
                                        <i class="fas fa-apple-alt"></i>
                                        🥗 Nutrition
                                    </label>
                                </div>
                                <div class="type-event-option">
                                    <input type="radio" name="type_evenement" id="type_workshop" value="WORKSHOP"
                                           <?= $evenement->getTypeEvenement() == 'WORKSHOP' ? 'checked' : '' ?>>
                                    <label for="type_workshop">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        📚 Workshop
                                    </label>
                                </div>
                                <div class="type-event-option">
                                    <input type="radio" name="type_evenement" id="type_autre" value="AUTRE"
                                           <?= $evenement->getTypeEvenement() == 'AUTRE' ? 'checked' : '' ?>>
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
                                    <input type="radio" name="statut" id="statut_actif" value="ACTIF"
                                           <?= $evenement->getStatut() == 'ACTIF' ? 'checked' : '' ?>>
                                    <label for="statut_actif">
                                        <i class="fas fa-check-circle"></i>
                                        ✅ Actif
                                    </label>
                                </div>
                                <div class="statut-event-option">
                                    <input type="radio" name="statut" id="statut_cancelled" value="CANCELLED"
                                           <?= $evenement->getStatut() == 'CANCELLED' ? 'checked' : '' ?>>
                                    <label for="statut_cancelled">
                                        <i class="fas fa-times-circle"></i>
                                        ❌ Annulé
                                    </label>
                                </div>
                                <div class="statut-event-option">
                                    <input type="radio" name="statut" id="statut_completed" value="COMPLETED"
                                           <?= $evenement->getStatut() == 'COMPLETED' ? 'checked' : '' ?>>
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
                                Enregistrer les modifications
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
                    Les champs marqués d'un <span class="required">*</span> sont obligatoires
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/evenement.js"></script>
</body>
</html>