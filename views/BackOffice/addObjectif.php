<?php
session_start();
include '../../controleurs/ObjectifController.php';
require_once __DIR__ . '/../../models/objectif.php';

$ObjectifController = new ObjectifController();

$error = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'] ?? '';
    $poids_cible = $_POST['poids_cible'] ?? '';
    $calories_objectif = $_POST['calories_objectif'] ?? '';
    $eau_objectif = $_POST['eau_objectif'] ?? '';
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';

    if ($user_id && $poids_cible && $calories_objectif && $eau_objectif && $date_debut && $date_fin) {
        try {
            $objectif = new objectif($user_id, $poids_cible, $calories_objectif, $eau_objectif, $date_debut, $date_fin);
            $ObjectifController->addObjectif($objectif);
            $_SESSION['success_message'] = 'Objectif ajouté avec succès';
            header('Location: objectifList.php');
            exit();
        } catch (Exception $e) {
            $error = "L'ID utilisateur " . htmlspecialchars($user_id) . " n'existe pas.";
        }
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            padding: 40px 20px;
            color: #2c3e50;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: #ffffff !important;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .form-header {
            margin-bottom: 35px;
            text-align: center;
        }

        .form-header h1 {
            color: #1a1a2e;
            font-size: 2rem;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .form-header h1 i {
            color: #4CAF50;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-weight: 600;
            color: #555;
            font-size: 0.95rem;
        }

        .form-group label i {
            color: #4CAF50;
            width: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: 0.3s;
            font-family: inherit;
            background: #fdfdfd;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
            background: white;
        }

        .form-group input.error {
            border-color: #dc3545;
            background-color: #fff8f8;
        }

        .form-group input.valid {
            border-color: #4CAF50;
        }

        .field-error {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 40px;
        }

        .btn {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            border: none;
            font-family: inherit;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #666;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
            color: #333;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .form-container {
                padding: 25px;
            }
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
                <input type="number" id="user_id" name="user_id" required min="1" value="<?= htmlspecialchars($user_id ?? '') ?>" class="<?= $error ? 'error' : '' ?>">
                <?php if ($error): ?>
                    <span class="field-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="poids_cible"><i class="fas fa-weight"></i> Poids Cible (kg)</label>
                    <input type="number" id="poids_cible" name="poids_cible" required step="0.1" min="0" value="<?= htmlspecialchars($poids_cible ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="calories_objectif"><i class="fas fa-fire"></i> Calories Objectif</label>
                    <input type="number" id="calories_objectif" name="calories_objectif" required min="0" value="<?= htmlspecialchars($calories_objectif ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="eau_objectif"><i class="fas fa-water"></i> Eau Objectif (L)</label>
                    <input type="number" id="eau_objectif" name="eau_objectif" required step="0.1" min="0" value="<?= htmlspecialchars($eau_objectif ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_debut"><i class="fas fa-calendar"></i> Date Début</label>
                    <input type="date" id="date_debut" name="date_debut" required value="<?= htmlspecialchars($date_debut ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="date_fin"><i class="fas fa-calendar"></i> Date Fin</label>
                    <input type="date" id="date_fin" name="date_fin" required value="<?= htmlspecialchars($date_fin ?? '') ?>">
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ajouter</button>
                <a href="objectifList.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
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
