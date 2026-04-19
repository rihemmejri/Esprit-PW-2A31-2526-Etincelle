<?php
include_once '../../controleurs/ProgrammeController.php';
include_once '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/programme.php';
require_once __DIR__ . '/../../models/repas.php';

$error = "";
$success = "";
$programmeController = new ProgrammeController();
$repasController = new RepasController();

// Récupérer tous les repas depuis la base de données
$allRepas = $repasController->listRepas();

// Vérifier si des repas existent (débogage)
// echo "Nombre de repas: " . count($allRepas); // À décommenter pour tester

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["id_user"]) && isset($_POST["objectif"]) && isset($_POST["date_debut"]) && isset($_POST["date_fin"])) {
        if (!empty($_POST["id_user"]) && !empty($_POST["objectif"]) && !empty($_POST["date_debut"]) && !empty($_POST["date_fin"])) {
            
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            
            if ($date_fin < $date_debut) {
                $error = "La date de fin doit être postérieure à la date de début.";
            } else {
                $repasList = [];
                if (isset($_POST['repas']) && is_array($_POST['repas'])) {
                    foreach ($_POST['repas'] as $index => $repasId) {
                        if (!empty($repasId)) {
                            $repasList[] = [
                                'id_repas' => $repasId,
                                'jour_semaine' => $_POST['jour_semaine'][$index],
                                'type_repas' => $_POST['type_repas'][$index]
                            ];
                        }
                    }
                }
                
                if (empty($repasList)) {
                    $error = "Veuillez ajouter au moins un repas au programme.";
                } else {
                    $programme = new programme(
                        intval($_POST['id_user']),
                        htmlspecialchars($_POST['objectif']),
                        $date_debut,
                        $date_fin
                    );
                    $programme->setRepas($repasList);
                    
                    if ($programmeController->addProgramme($programme)) {
                        $success = "Programme ajouté avec succès !";
                        header('Location: programmeList.php');
                        exit;
                    } else {
                        $error = "Erreur lors de l'ajout du programme.";
                    }
                }
            }
        } else {
            $error = "Tous les champs sont obligatoires.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Programme - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        .repas-selector {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        .repas-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 0.5fr;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .repas-header {
            font-weight: bold;
            color: #003366;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        .btn-add-repas {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        .btn-add-repas:hover {
            background: #45a049;
        }
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .btn-remove:hover {
            background: #c82333;
        }
        select, input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container-form">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Ajouter un programme repas
                </h1>
                <p>Créez un programme personnalisé avec vos repas préférés</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="form-content">
                <form action="" method="POST" id="addProgrammeForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> ID Utilisateur *</label>
                            <input type="number" name="id_user" required min="1" placeholder="Ex: 1">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-bullseye"></i> Objectif *</label>
                            <select name="objectif" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="PERDRE_POIDS">🔥 Perdre du poids</option>
                                <option value="PRENDRE_MUSCLE">💪 Prendre du muscle</option>
                                <option value="MAINTENIR">⚖️ Maintenir son poids</option>
                                <option value="EQUILIBRE">🥗 Équilibre alimentaire</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Date début *</label>
                            <input type="date" name="date_debut" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-check"></i> Date fin *</label>
                            <input type="date" name="date_fin" required>
                        </div>

                        <div class="form-group full-width">
                            <label><i class="fas fa-utensils"></i> Repas du programme *</label>
                            <div class="repas-selector">
                                <div class="repas-header repas-row">
                                    <span><i class="fas fa-utensils"></i> Repas</span>
                                    <span><i class="fas fa-calendar-day"></i> Jour</span>
                                    <span><i class="fas fa-clock"></i> Type</span>
                                    <span><i class="fas fa-trash"></i></span>
                                </div>
                                <div id="repasContainer"></div>
                                <button type="button" class="btn-add-repas" onclick="addRepasRow()">
                                    <i class="fas fa-plus"></i> Ajouter un repas
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Ajouter le programme
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Réinitialiser
                        </button>
                        <a href="programmeList.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Récupérer les repas depuis PHP (base de données)
        const allRepas = <?php 
            $repasArray = [];
            foreach ($allRepas as $r) {
                $repasArray[] = [
                    'id_repas' => $r->getIdRepas(),
                    'nom' => $r->getNom(),
                    'type' => $r->getType(),
                    'calories' => $r->getCalories()
                ];
            }
            echo json_encode($repasArray, JSON_UNESCAPED_UNICODE);
        ?>;
        
        // Vérifier dans la console du navigateur (F12)
        console.log("=== DÉBOGAGE ADD PROGRAMME ===");
        console.log("Repas chargés depuis la base:", allRepas);
        console.log("Nombre total de repas:", allRepas.length);
        
        let repasCount = 0;
        
        function addRepasRow(repasId = '', jour = '', type = '') {
            const container = document.getElementById('repasContainer');
            if (!container) return;
            
            const row = document.createElement('div');
            row.className = 'repas-row';
            row.id = `repas-row-${repasCount}`;
            
            // Sélecteur de repas
            const repasSelect = document.createElement('select');
            repasSelect.name = `repas[]`;
            repasSelect.className = 'repas-select';
            repasSelect.style.cssText = 'padding: 8px; border-radius: 8px; border: 1px solid #ddd;';
            repasSelect.innerHTML = '<option value="">-- Choisir un repas --</option>';
            
            // Ajouter les repas depuis la base de données
            if (allRepas && allRepas.length > 0) {
                for (let i = 0; i < allRepas.length; i++) {
                    const repas = allRepas[i];
                    const selected = (repas.id_repas == repasId) ? 'selected' : '';
                    repasSelect.innerHTML += `<option value="${repas.id_repas}" ${selected}>${repas.nom} (${repas.calories} kcal) - ${repas.type}</option>`;
                }
            } else {
                repasSelect.innerHTML += '<option value="">Aucun repas disponible - Ajoutez des repas dabord</option>';
            }
            
            // Sélecteur jour
            const jourSelect = document.createElement('select');
            jourSelect.name = `jour_semaine[]`;
            jourSelect.style.cssText = 'padding: 8px; border-radius: 8px; border: 1px solid #ddd;';
            jourSelect.innerHTML = `
                <option value="LUNDI" ${jour == 'LUNDI' ? 'selected' : ''}>Lundi</option>
                <option value="MARDI" ${jour == 'MARDI' ? 'selected' : ''}>Mardi</option>
                <option value="MERCREDI" ${jour == 'MERCREDI' ? 'selected' : ''}>Mercredi</option>
                <option value="JEUDI" ${jour == 'JEUDI' ? 'selected' : ''}>Jeudi</option>
                <option value="VENDREDI" ${jour == 'VENDREDI' ? 'selected' : ''}>Vendredi</option>
                <option value="SAMEDI" ${jour == 'SAMEDI' ? 'selected' : ''}>Samedi</option>
                <option value="DIMANCHE" ${jour == 'DIMANCHE' ? 'selected' : ''}>Dimanche</option>
            `;
            
            // Sélecteur type repas
            const typeSelect = document.createElement('select');
            typeSelect.name = `type_repas[]`;
            typeSelect.style.cssText = 'padding: 8px; border-radius: 8px; border: 1px solid #ddd;';
            typeSelect.innerHTML = `
                <option value="PETIT_DEJEUNER" ${type == 'PETIT_DEJEUNER' ? 'selected' : ''}>☕ Petit déjeuner</option>
                <option value="DEJEUNER" ${type == 'DEJEUNER' ? 'selected' : ''}>🍽️ Déjeuner</option>
                <option value="DINER" ${type == 'DINER' ? 'selected' : ''}>🌙 Dîner</option>
                <option value="COLLATION" ${type == 'COLLATION' ? 'selected' : ''}>🍎 Collation</option>
            `;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove';
            removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
            removeBtn.onclick = () => row.remove();
            
            row.appendChild(repasSelect);
            row.appendChild(jourSelect);
            row.appendChild(typeSelect);
            row.appendChild(removeBtn);
            
            container.appendChild(row);
            repasCount++;
        }
        
        // Ajouter des lignes par défaut
        function addDefaultRows() {
            addRepasRow('', 'LUNDI', 'PETIT_DEJEUNER');
            addRepasRow('', 'LUNDI', 'DEJEUNER');
            addRepasRow('', 'LUNDI', 'DINER');
        }
        
        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            addDefaultRows();
            
            const form = document.getElementById('addProgrammeForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const repasSelects = document.querySelectorAll('.repas-select');
                    let hasRepas = false;
                    repasSelects.forEach(select => {
                        if (select.value && select.value !== '') {
                            hasRepas = true;
                        }
                    });
                    if (!hasRepas) {
                        e.preventDefault();
                        alert('❌ Veuillez ajouter au moins un repas au programme.');
                    }
                });
            }
        });
    </script>
</body>
</html>