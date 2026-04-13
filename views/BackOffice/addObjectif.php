<?php
session_start();
include '../../controleurs/ObjectifController.php';
require_once __DIR__ . '/../../models/objectif.php';

$ObjectifController = new ObjectifController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'] ?? '';
    $poids_cible = $_POST['poids_cible'] ?? '';
    $calories_objectif = $_POST['calories_objectif'] ?? '';
    $eau_objectif = $_POST['eau_objectif'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';

    if ($user_id && $poids_cible && $calories_objectif && $eau_objectif && $date_debut && $date_fin) {
        $objectif = new objectif($user_id, $poids_cible, $calories_objectif, $eau_objectif, $date_debut, $date_fin);
        $ObjectifController->addObjectif($objectif);
        $_SESSION['success_message'] = 'Objectif ajouté avec succès';
        header('Location: objectifList.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Objectif</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        .form-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-header h1 {
            color: #2c5f8d;
            margin: 0 0 30px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #2c5f8d;
            box-shadow: 0 0 5px rgba(44, 95, 141, 0.3);
        }
        .form-group input.error {
            border-color: #dc3545;
            background-color: #fff5f5;
        }
        .form-group input.valid {
            border-color: #27ab5f;
        }
        .field-error {
            display: block;
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        .btn-submit {
            flex: 1;
            padding: 12px;
            background-color: #27ab5f;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #1f8849;
        }
        .btn-cancel {
            flex: 1;
            padding: 12px;
            background-color: #ccc;
            color: #333;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-cancel:hover {
            background-color: #aaa;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h1><i class="fas fa-plus-circle"></i> Ajouter un Objectif</h1>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="user_id"><i class="fas fa-user"></i> User ID</label>
                <input type="number" id="user_id" name="user_id" required min="1">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="poids_cible"><i class="fas fa-weight"></i> Poids Cible (kg)</label>
                    <input type="number" id="poids_cible" name="poids_cible" required step="0.1" min="0">
                </div>
                <div class="form-group">
                    <label for="calories_objectif"><i class="fas fa-fire"></i> Calories Objectif</label>
                    <input type="number" id="calories_objectif" name="calories_objectif" required min="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="eau_objectif"><i class="fas fa-water"></i> Eau Objectif (L)</label>
                    <input type="number" id="eau_objectif" name="eau_objectif" required step="0.1" min="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_debut"><i class="fas fa-calendar"></i> Date Début</label>
                    <input type="date" id="date_debut" name="date_debut" required>
                </div>
                <div class="form-group">
                    <label for="date_fin"><i class="fas fa-calendar"></i> Date Fin</label>
                    <input type="date" id="date_fin" name="date_fin" required>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Ajouter</button>
                <a href="objectifList.php" class="btn-cancel"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </form>
    </div>
    <script>
        // Simple validation without preventing form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input');
            const dateDebut = form.querySelector('[name="date_debut"]');
            const dateFin = form.querySelector('[name="date_fin"]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', validateField);
                input.addEventListener('input', validateField);
            });
            
            function validateField(e) {
                const field = e.target;
                const name = field.name;
                const value = field.value.trim();
                let error = '';
                
                if (name === 'poids_cible') {
                    const poids = parseFloat(value);
                    if (value && (isNaN(poids) || poids <= 0 || poids > 300 || poids < 30)) {
                        error = 'Poids: 30-300 kg';
                    }
                } else if (name === 'calories_objectif') {
                    const cal = parseInt(value);
                    if (value && (isNaN(cal) || cal < 500 || cal > 10000)) {
                        error = 'Calories: 500-10000 kcal';
                    }
                } else if (name === 'eau_objectif') {
                    const eau = parseFloat(value);
                    if (value && (isNaN(eau) || eau < 0.5 || eau > 20)) {
                        error = 'Eau: 0.5-20 litres';
                    }
                } else if (name === 'date_debut' || name === 'date_fin') {
                    if (dateDebut.value && dateFin.value) {
                        const debut = new Date(dateDebut.value);
                        const fin = new Date(dateFin.value);
                        if (debut > fin) {
                            error = 'Date début doit être avant date fin';
                        }
                    }
                }
                
                showFieldError(field, error);
            }
            
            function showFieldError(field, message) {
                const parent = field.parentElement;
                let errorDiv = parent.querySelector('.field-error');
                
                if (errorDiv) errorDiv.remove();
                
                if (message) {
                    field.classList.add('error');
                    field.classList.remove('valid');
                    errorDiv = document.createElement('span');
                    errorDiv.className = 'field-error';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
                    parent.appendChild(errorDiv);
                } else {
                    field.classList.remove('error');
                    if (field.value.trim()) field.classList.add('valid');
                }
            }
        });
    </script>
</body>
</html>
