<?php
require_once '../../controleurs/EvenementController.php';
require_once '../../controleurs/ParticipationController.php';

$error                   = "";
$success                 = "";
$participationController = new ParticipationController();
$evenementController     = new EvenementController();
$evenements              = $participationController->getAllEvenements();

$evenement_id = isset($_GET['evenement_id']) ? intval($_GET['evenement_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_evenement        = $_POST['id_evenement']        ?? null;
    $id_user             = $_POST['id_user']             ?? null;
    $nom                 = $_POST['nom']                 ?? '';
    $email               = $_POST['email']               ?? '';
    $telephone           = $_POST['telephone']           ?? null;
    $statut              = $_POST['statut']              ?? 'EN_ATTENTE';
    $nb_places_reservees = !empty($_POST['nb_places_reservees']) ? intval($_POST['nb_places_reservees']) : 1;

    if (!empty($id_evenement) && !empty($id_user) && !empty($nom) && !empty($email)) {
        $participation = new Participation(
            intval($id_evenement),
            intval($id_user),
            htmlspecialchars($nom),
            htmlspecialchars($email),
            !empty($telephone) ? htmlspecialchars($telephone) : null,
            $statut,
            null, null, null,
            $nb_places_reservees
        );
        $participationController->addParticipation($participation);
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
    <title>Ajouter une Participation - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/participation.css">
    <style>
        .input-error { border-color: #f44336 !important; background: #fff5f5 !important; }
        .input-ok    { border-color: #4CAF50 !important; background: #f5fff5 !important; }
        .field-error { color: #f44336; font-size: 0.78rem; margin-top: 5px; display: none; }
        .field-error.show { display: block; }
    </style>
</head>
<body>
<div class="container-list">
    <div class="form-card">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Ajouter une participation</h1>
            <p>Inscrivez un utilisateur à un événement NutriLoop</p>
        </div>

        <?php if ($error): ?>
        <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-content">
            <form action="" method="POST" id="addParticipationForm" novalidate>
                <div class="form-grid">

                    <!-- Événement -->
                    <div class="form-group full-width">
                        <label><i class="fas fa-calendar-alt"></i> Événement <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-calendar-check"></i>
                            <select name="id_evenement" id="evenementSelect">
                                <option value="">-- Sélectionnez un événement --</option>
                                <?php foreach ($evenements as $ev): ?>
                                    <option value="<?= $ev['id_evenement'] ?>"
                                        <?= ($evenement_id == $ev['id_evenement']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ev['titre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <span class="field-error" id="err-evenement">Veuillez sélectionner un événement.</span>
                    </div>

                    <!-- ID User -->
                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> ID Utilisateur <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="number" name="id_user" id="id_user" min="1" placeholder="Ex: 12">
                        </div>
                        <span class="field-error" id="err-iduser">L'ID utilisateur est obligatoire (min 1).</span>
                    </div>

                    <!-- Nom -->
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nom complet <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-signature"></i>
                            <input type="text" name="nom" id="nom" placeholder="Nom du participant">
                        </div>
                        <span class="field-error" id="err-nom">Le nom est obligatoire (minimum 3 caractères).</span>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email <span class="required">*</span></label>
                        <div class="input-icon">
                            <i class="fas fa-at"></i>
                            <input type="email" name="email" id="email" placeholder="email@exemple.com">
                        </div>
                        <span class="field-error" id="err-email">Veuillez entrer un email valide.</span>
                    </div>

                    <!-- Téléphone -->
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Téléphone</label>
                        <div class="input-icon">
                            <i class="fas fa-mobile-alt"></i>
                            <input type="text" name="telephone" id="telephone" placeholder="+216 XX XXX XXX">
                        </div>
                        <span class="field-error" id="err-telephone">Numéro invalide (chiffres, +, espaces uniquement).</span>
                    </div>

                    <!-- Places réservées -->
                    <div class="form-group">
                        <label><i class="fas fa-ticket-alt"></i> Places réservées <span class="required">*</span></label>
                        <div class="note-input">
                            <button type="button" onclick="updatePlaces(-1)">−</button>
                            <input type="number" name="nb_places_reservees" id="placesField" min="1" max="10" value="1">
                            <button type="button" onclick="updatePlaces(1)">+</button>
                        </div>
                        <span class="field-error" id="err-places">Le nombre de places doit être entre 1 et 10.</span>
                    </div>

                    <!-- Statut -->
                    <div class="form-group full-width">
                        <label><i class="fas fa-circle"></i> Statut <span class="required">*</span></label>
                        <div class="radio-group-modern" id="statutGroup">
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_attente" value="EN_ATTENTE" checked>
                                <label for="statut_attente"><i class="fas fa-hourglass-half"></i><span>⏳ En attente</span></label>
                            </div>
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_confirmee" value="CONFIRMEE">
                                <label for="statut_confirmee"><i class="fas fa-check-circle"></i><span>✅ Confirmée</span></label>
                            </div>
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_annulee" value="ANNULEE">
                                <label for="statut_annulee"><i class="fas fa-times-circle"></i><span>❌ Annulée</span></label>
                            </div>
                            <div class="radio-option-modern">
                                <input type="radio" name="statut" id="statut_presente" value="PRESENTE">
                                <label for="statut_presente"><i class="fas fa-user-check"></i><span>🎯 Présente</span></label>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Ajouter la participation
                        </button>
                        <button type="reset" class="btn btn-secondary" onclick="resetErrors()">
                            <i class="fas fa-undo"></i> Réinitialiser
                        </button>
                        <a href="<?= $evenement_id ? 'participationList.php' : 'evenementList.php' ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>

                </div>
            </form>
            <div class="help-text">
                <i class="fas fa-info-circle"></i> Les champs marqués d'un <span class="required">*</span> sont obligatoires.
            </div>
        </div>
    </div>
</div>

<script>
// ===== COUNTER PLACES =====
function updatePlaces(delta) {
    const input = document.getElementById('placesField');
    if (!input) return;
    const newValue = (parseInt(input.value) || 1) + delta;
    if (newValue >= 1 && newValue <= 10) input.value = newValue;
    validatePlaces();
}

// ===== HELPERS =====
function showError(inputEl, errId, show) {
    const err = document.getElementById(errId);
    if (!inputEl || !err) return;
    if (show) {
        inputEl.classList.add('input-error');
        inputEl.classList.remove('input-ok');
        err.classList.add('show');
    } else {
        inputEl.classList.remove('input-error');
        inputEl.classList.add('input-ok');
        err.classList.remove('show');
    }
}

// ===== VALIDATIONS =====
function validateEvenement() {
    const sel = document.getElementById('evenementSelect');
    const ok  = sel && sel.value !== '';
    showError(sel, 'err-evenement', !ok);
    return ok;
}

function validateIdUser() {
    const input = document.getElementById('id_user');
    const val   = parseInt(input?.value);
    const ok    = !isNaN(val) && val >= 1;
    showError(input, 'err-iduser', !ok);
    return ok;
}

function validateNom() {
    const input = document.getElementById('nom');
    const ok    = input && input.value.trim().length >= 3;
    showError(input, 'err-nom', !ok);
    return ok;
}

function validateEmail() {
    const input = document.getElementById('email');
    const ok    = input && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim());
    showError(input, 'err-email', !ok);
    return ok;
}

function validateTelephone() {
    const input = document.getElementById('telephone');
    if (!input || input.value.trim() === '') {
        input?.classList.remove('input-error', 'input-ok');
        document.getElementById('err-telephone')?.classList.remove('show');
        return true;
    }
    const ok = /^[0-9+\s\-()]{6,20}$/.test(input.value.trim());
    showError(input, 'err-telephone', !ok);
    return ok;
}

function validatePlaces() {
    const input = document.getElementById('placesField');
    const val   = parseInt(input?.value);
    const ok    = !isNaN(val) && val >= 1 && val <= 10;
    showError(input, 'err-places', !ok);
    return ok;
}

function resetErrors() {
    document.querySelectorAll('.input-error, .input-ok').forEach(el => {
        el.classList.remove('input-error', 'input-ok');
    });
    document.querySelectorAll('.field-error.show').forEach(el => {
        el.classList.remove('show');
    });
}

// ===== ÉVÉNEMENTS TEMPS RÉEL =====
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('evenementSelect')?.addEventListener('change', validateEvenement);
    document.getElementById('id_user')?.addEventListener('input', validateIdUser);
    document.getElementById('nom')?.addEventListener('input', validateNom);
    document.getElementById('email')?.addEventListener('input', validateEmail);
    document.getElementById('telephone')?.addEventListener('input', validateTelephone);
    document.getElementById('placesField')?.addEventListener('input', validatePlaces);

    // ===== SOUMISSION =====
    document.getElementById('addParticipationForm')?.addEventListener('submit', function(e) {
        const ok = validateEvenement() & validateIdUser() & validateNom()
                 & validateEmail() & validateTelephone() & validatePlaces();
        if (!ok) {
            e.preventDefault();
            const firstError = document.querySelector('.input-error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
</body>
</html>