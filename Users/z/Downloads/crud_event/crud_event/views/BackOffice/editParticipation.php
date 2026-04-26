<?php
require_once '../../controleurs/ParticipationController.php';

$participationController = new ParticipationController();
$id = $_GET['id'] ?? null;

if (!$id) { header('Location: participationList.php'); exit; }

$participation = $participationController->getParticipationById($id);
if (!$participation) { header('Location: participationList.php'); exit; }

$evenements = $participationController->getAllEvenements();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_evenement        = $_POST['id_evenement']        ?? '';
    $id_user             = $_POST['id_user']             ?? '';
    $nom                 = $_POST['nom']                 ?? '';
    $email               = $_POST['email']               ?? '';
    $telephone           = $_POST['telephone']           ?? null;
    $statut              = $_POST['statut']              ?? 'EN_ATTENTE';
    $feedback            = $_POST['feedback']            ?? null;
    $note                = !empty($_POST['note']) ? intval($_POST['note']) : null;
    $nb_places_reservees = !empty($_POST['nb_places_reservees']) ? intval($_POST['nb_places_reservees']) : 1;

    if (!empty($id_evenement) && !empty($id_user) && !empty($nom) && !empty($email)) {
        $participation->setIdEvenement(intval($id_evenement));
        $participation->setIdUser(intval($id_user));
        $participation->setNom(htmlspecialchars($nom));
        $participation->setEmail(htmlspecialchars($email));
        $participation->setTelephone(!empty($telephone) ? htmlspecialchars($telephone) : null);
        $participation->setStatut($statut);
        $participation->setFeedback(!empty($feedback) ? htmlspecialchars($feedback) : null);
        $participation->setNote($note);
        $participation->setNbPlacesReservees($nb_places_reservees);

        $participationController->updateParticipation($participation);
        header('Location: participationList.php');
        exit;
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Participation - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/participation.css">
</head>
<body>
<div class="container-list">
    <div class="form-card">
        <div class="header">
            <h1><i class="fas fa-user-edit"></i> Modifier la participation #<?= $id ?></h1>
            <p>Modifiez les informations de la participation</p>
        </div>

        <?php if ($error): ?>
        <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-content">
            <div class="event-info-form">
                <i class="fas fa-calendar-check"></i>
                Événement : <strong><?= htmlspecialchars($participation->getEvenementTitre()) ?></strong>
                &nbsp;|&nbsp;
                <i class="fas fa-user"></i>
                Participant : <strong><?= htmlspecialchars($participation->getNom()) ?></strong>
            </div>

            <form action="" method="POST" id="editParticipationForm">
                <div class="form-grid">

                    <!-- Événement -->
                    <div class="form-group full-width">
                        <label><i class="fas fa-calendar-alt"></i> Événement <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-calendar-check"></i>
                            <select name="id_evenement" id="evenementSelect" required>
                                <option value="">-- Sélectionnez un événement --</option>
                                <?php foreach ($evenements as $ev): ?>
                                    <option value="<?= $ev['id_evenement'] ?>"
                                        <?= $ev['id_evenement'] == $participation->getIdEvenement() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ev['titre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- ID User -->
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> ID Utilisateur <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="number" name="id_user" min="1" value="<?= $participation->getIdUser() ?>" required>
                        </div>
                    </div>

                    <!-- Nom -->
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom complet <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-signature"></i>
                            <input type="text" name="nom" value="<?= htmlspecialchars($participation->getNom()) ?>" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-at"></i>
                            <input type="email" name="email" value="<?= htmlspecialchars($participation->getEmail()) ?>" required>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone</label>
                        <div class="input-icon">
                            <i class="fas fa-mobile-alt"></i>
                            <input type="text" name="telephone" value="<?= htmlspecialchars($participation->getTelephone() ?? '') ?>">
                        </div>
                    </div>

                    <!-- Places réservées -->
                    <div class="form-group">
                        <label><i class="fas fa-ticket-alt"></i> Places réservées</label>
                        <div class="note-input">
                            <button type="button" onclick="updatePlaces(-1)">−</button>
                            <input type="number" name="nb_places_reservees" id="placesField"
                                   min="1" max="10" value="<?= $participation->getNbPlacesReservees() ?? 1 ?>">
                            <button type="button" onclick="updatePlaces(1)">+</button>
                        </div>
                    </div>

                    <!-- Note -->
                    <div class="form-group">
                        <label><i class="fas fa-star"></i> Note (1 à 5)</label>
                        <div class="note-input">
                            <button type="button" onclick="updateNote(-1)">−</button>
                            <input type="number" name="note" id="note" min="1" max="5"
                                   value="<?= $participation->getNote() ?>" placeholder="Optionnel">
                            <button type="button" onclick="updateNote(1)">+</button>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="form-group full-width">
                        <label><i class="fas fa-circle"></i> Statut <span class="required">*</span></label>
                        <div class="radio-group-modern" id="statutGroup">
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_attente" value="EN_ATTENTE"
                                    <?= $participation->getStatut() == 'EN_ATTENTE' ? 'checked' : '' ?>>
                                <label for="statut_attente"><i class="fas fa-hourglass-half"></i><span>⏳ En attente</span></label>
                            </div>
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_confirmee" value="CONFIRMEE"
                                    <?= $participation->getStatut() == 'CONFIRMEE' ? 'checked' : '' ?>>
                                <label for="statut_confirmee"><i class="fas fa-check-circle"></i><span>✅ Confirmée</span></label>
                            </div>
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_annulee" value="ANNULEE"
                                    <?= $participation->getStatut() == 'ANNULEE' ? 'checked' : '' ?>>
                                <label for="statut_annulee"><i class="fas fa-times-circle"></i><span>❌ Annulée</span></label>
                            </div>
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_presente" value="PRESENTE"
                                    <?= $participation->getStatut() == 'PRESENTE' ? 'checked' : '' ?>>
                                <label for="statut_presente"><i class="fas fa-user-check"></i><span>🎯 Présente</span></label>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback -->
                    <div class="form-group full-width">
                        <label><i class="fas fa-comment-alt"></i> Feedback</label>
                        <div class="input-icon">
                            <i class="fas fa-pen"></i>
                            <textarea name="feedback" id="feedback" rows="3"><?= htmlspecialchars($participation->getFeedback() ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                        <a href="participationList.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>

                </div>
            </form>
            <div class="help-text">
                <i class="fas fa-info-circle"></i> Les champs marqués d'un <span class="required">*</span> sont obligatoires
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/participation.js"></script>
<script>
function updatePlaces(delta) {
    let input = document.getElementById('placesField');
    if (!input) return;
    let value = parseInt(input.value) || 1;
    let newValue = value + delta;
    if (newValue >= 1 && newValue <= 10) input.value = newValue;
}
</script>
</body>
</html>