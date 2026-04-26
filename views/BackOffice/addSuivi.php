<?php
session_start();
include '../../controleurs/SuiviController.php';
include '../../controleurs/ObjectifController.php';
require_once __DIR__ . '/../../models/suivi.php';

$SuiviController = new SuiviController();
$ObjectifController = new ObjectifController();
$objectifs = $ObjectifController->listObjectifs();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'] ?? '';
    $id_objectif = $_POST['id_objectif'] ?? null;
    $date = $_POST['date'] ?? '';
    $poids = $_POST['poids'] ?? '';
    $calories_consommees = $_POST['calories_consommees'] ?? '';
    $calories_objectif = $_POST['calories_objectif'] ?? '';
    $eau_bue = $_POST['eau_bue'] ?? '';
    $eau_objectif = $_POST['eau_objectif'] ?? '';

    $calories_restant = $calories_objectif - $calories_consommees;

    $suivi = new suivi($user_id, $id_objectif, $date, $poids, $calories_consommees, $calories_objectif, $calories_restant, $eau_bue, $eau_objectif);
    $SuiviController->addSuivi($suivi);
    $_SESSION['success_message'] = 'Suivi ajouté avec succès';
    header('Location: suiviList.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Suivi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #ffffff; padding: 40px 20px; }
        .form-container { max-width: 800px; margin: 0 auto; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .form-header h1 { color: #1a1a2e; text-align: center; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 10px; transition: 0.3s; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #4CAF50; box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1); }
        .form-group input.error, .form-group select.error { border-color: #dc3545; background-color: #fff8f8; }
        .form-group input.valid, .form-group select.valid { border-color: #28a745; background-color: #f8fff8; }
        .field-error { color: #dc3545; font-size: 0.85rem; margin-top: 5px; display: flex; align-items: center; gap: 5px; font-weight: 500; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .btn-group { display: flex; gap: 15px; margin-top: 30px; }
        .btn { flex: 1; padding: 14px; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none; }
        .btn-primary { background: #4CAF50; color: white; }
        .btn-secondary { background: #f0f0f0; color: #666; }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header"><h1>Ajouter un Suivi</h1></div>
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>User ID</label>
                    <input type="number" name="user_id" required>
                </div>
                <div class="form-group">
                    <label>Objectif</label>
                    <select name="id_objectif" id="id_objectif">
                        <option value="">Néant</option>
                        <?php foreach($objectifs as $o): ?>
                            <option value="<?= $o->getId() ?>" data-cal="<?= $o->getCaloriesObjectif() ?>" data-eau="<?= $o->getEauObjectif() ?>">Objectif #<?= $o->getId() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" readonly required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Poids (kg)</label>
                    <input type="number" name="poids" step="0.1" required>
                </div>
                <div class="form-group">
                    <label>Calories Consommées</label>
                    <input type="number" name="calories_consommees" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Calories Objectif</label>
                    <input type="number" name="calories_objectif" id="calories_objectif" readonly required>
                </div>
                <div class="form-group">
                    <label>Eau Bue (L)</label>
                    <input type="number" name="eau_bue" step="0.1" required>
                </div>
            </div>
            <div class="form-group">
                <label>Eau Objectif (L)</label>
                <input type="number" name="eau_objectif" id="eau_objectif" step="0.1" readonly required>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="suiviList.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('id_objectif').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('calories_objectif').value = selected.dataset.cal;
                document.getElementById('eau_objectif').value = selected.dataset.eau;
            } else {
                document.getElementById('calories_objectif').value = '';
                document.getElementById('eau_objectif').value = '';
            }
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            const fields = [
                { name: 'user_id', label: 'ID Utilisateur' },
                { name: 'id_objectif', label: 'Objectif' },
                { name: 'date', label: 'Date' },
                { name: 'poids', label: 'Poids' },
                { name: 'calories_consommees', label: 'Calories' },
                { name: 'eau_bue', label: 'Eau' }
            ];

            fields.forEach(f => {
                const field = this.querySelector(`[name="${f.name}"]`);
                if (!validateField(field)) isValid = false;
            });

            if (!isValid) {
                e.preventDefault();
                const firstError = this.querySelector('.error');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => validateField(input));
        });

        function validateField(field) {
            const name = field.name;
            const value = field.value.trim();
            let error = '';

            if (name === 'user_id' && !value) error = "ID Utilisateur obligatoire";
            if (name === 'id_objectif' && !value) error = "Veuillez sélectionner un objectif";
            if (name === 'date') {
                const now = new Date();
                const todayStr = now.getFullYear() + '-' + 
                                 String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                                 String(now.getDate()).padStart(2, '0');
                if (!value) error = "Date obligatoire";
                else if (value > todayStr) error = "La date ne peut pas être dans le futur";
            }
            if (name === 'poids') {
                const poids = parseFloat(value);
                if (!value) error = "Poids obligatoire";
                else if (isNaN(poids) || poids < 30 || poids > 300) error = "Poids: 30-300 kg";
            }
            if (name === 'calories_consommees') {
                const cal = parseInt(value);
                if (!value) error = "Calories obligatoires";
                else if (isNaN(cal) || cal < 0) error = "Calories >= 0";
            }
            if (name === 'eau_bue') {
                const eau = parseFloat(value);
                if (!value) error = "Eau obligatoire";
                else if (isNaN(eau) || eau < 0) error = "Eau >= 0";
            }

            showError(field, error);
            return !error;
        }

        function showError(field, message) {
            const parent = field.parentElement;
            let errorDiv = parent.querySelector('.field-error');
            if (errorDiv) errorDiv.remove();

            if (message) {
                field.classList.add('error');
                field.classList.remove('valid');
                errorDiv = document.createElement('div');
                errorDiv.className = 'field-error';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
                parent.appendChild(errorDiv);
            } else {
                field.classList.remove('error');
                if (field.value) field.classList.add('valid');
            }
        }
    </script>
</body>
</html>
