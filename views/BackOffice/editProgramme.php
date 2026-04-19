<?php
include_once '../../controleurs/ProgrammeController.php';
include_once '../../controleurs/RepasController.php';
require_once __DIR__ . '/../../models/programme.php';
require_once __DIR__ . '/../../models/repas.php';

$programmeController = new ProgrammeController();
$repasController = new RepasController();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: programmeList.php');
    exit;
}

$programme = $programmeController->getProgrammeById($id);

if (!$programme) {
    header('Location: programmeList.php');
    exit;
}

$allRepas = $repasController->listRepas();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = intval($_POST['id_user']);
    $objectif = htmlspecialchars($_POST['objectif']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    
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
    
    if (empty($id_user) || empty($objectif) || empty($date_debut) || empty($date_fin)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($date_fin < $date_debut) {
        $error = "La date de fin doit être postérieure à la date de début.";
    } elseif (empty($repasList)) {
        $error = "Veuillez ajouter au moins un repas au programme.";
    } else {
        $programme->setIdUser($id_user);
        $programme->setObjectif($objectif);
        $programme->setDateDebut($date_debut);
        $programme->setDateFin($date_fin);
        $programme->setRepas($repasList);
        
        if ($programmeController->updateProgramme($programme)) {
            header('Location: programmeList.php');
            exit;
        } else {
            $error = "Erreur lors de la modification du programme.";
        }
    }
}

$currentRepas = $programme->getRepas();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Programme - NutriLoop</title>
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
        }
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
        }
        select, input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .current-values {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .value-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .value-tag {
            background: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container-edit">
        <div class="form-card">
            <div class="header">
                <h1>
                    <i class="fas fa-edit"></i>
                    Modifier le programme #<?= $id ?>
                </h1>
                <p>Modifiez les informations du programme repas</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="form-content">
                <div class="current-values">
                    <h3><i class="fas fa-info-circle"></i> Valeurs actuelles</h3>
                    <div class="value-tags">
                        <span class="value-tag"><strong>👤 Utilisateur:</strong> #<?= $programme->getIdUser() ?></span>
                        <span class="value-tag"><strong>🎯 Objectif:</strong> <?= $programme->getObjectif() ?></span>
                        <span class="value-tag"><strong>📅 Du:</strong> <?= $programme->getDateDebut() ?></span>
                        <span class="value-tag"><strong>📅 Au:</strong> <?= $programme->getDateFin() ?></span>
                        <span class="value-tag"><strong>🍽️ Repas:</strong> <?= count($currentRepas) ?> repas</span>
                    </div>
                </div>

                <form action="" method="POST" id="editProgrammeForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> ID Utilisateur *</label>
                            <input type="number" name="id_user" value="<?= $programme->getIdUser() ?>" required min="1">
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-bullseye"></i> Objectif *</label>
                            <select name="objectif" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="PERDRE_POIDS" <?= $programme->getObjectif() == 'PERDRE_POIDS' ? 'selected' : '' ?>>🔥 Perdre du poids</option>
                                <option value="PRENDRE_MUSCLE" <?= $programme->getObjectif() == 'PRENDRE_MUSCLE' ? 'selected' : '' ?>>💪 Prendre du muscle</option>
                                <option value="MAINTENIR" <?= $programme->getObjectif() == 'MAINTENIR' ? 'selected' : '' ?>>⚖️ Maintenir son poids</option>
                                <option value="EQUILIBRE" <?= $programme->getObjectif() == 'EQUILIBRE' ? 'selected' : '' ?>>🥗 Équilibre alimentaire</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Date début *</label>
                            <input type="date" name="date_debut" value="<?= $programme->getDateDebut() ?>" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-check"></i> Date fin *</label>
                            <input type="date" name="date_fin" value="<?= $programme->getDateFin() ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label><i class="fas fa-utensils"></i> Repas du programme *</label>
                            <div class="repas-selector">
                                <div class="repas-header repas-row">
                                    <span>Repas</span>
                                    <span>Jour</span>
                                    <span>Type</span>
                                    <span></span>
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
                            <i class="fas fa-save"></i> Enregistrer
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
        // Récupérer tous les repas depuis PHP
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
        
        // Récupérer les repas actuels du programme
        const currentRepas = <?php 
            $currentArray = [];
            foreach ($currentRepas as $item) {
                $currentArray[] = [
                    'id_repas' => $item['id_repas'],
                    'jour_semaine' => $item['jour_semaine'],
                    'type_repas' => $item['type_repas']
                ];
            }
            echo json_encode($currentArray, JSON_UNESCAPED_UNICODE);
        ?>;
        
        console.log("Repas disponibles:", allRepas);
        console.log("Repas actuels:", currentRepas);
        
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
            
            if (allRepas && allRepas.length > 0) {
                for (let i = 0; i < allRepas.length; i++) {
                    const repas = allRepas[i];
                    const selected = (repas.id_repas == repasId) ? 'selected' : '';
                    repasSelect.innerHTML += `<option value="${repas.id_repas}" ${selected}>${repas.nom} (${repas.calories} kcal) - ${repas.type}</option>`;
                }
            } else {
                repasSelect.innerHTML += '<option value="">Aucun repas disponible</option>';
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
        
        // Charger les repas existants du programme
        function loadCurrentRepas() {
            if (currentRepas && currentRepas.length > 0) {
                for (let i = 0; i < currentRepas.length; i++) {
                    const item = currentRepas[i];
                    addRepasRow(item.id_repas, item.jour_semaine, item.type_repas);
                }
            } else {
                // Ajouter 3 lignes par défaut
                addRepasRow('', 'LUNDI', 'PETIT_DEJEUNER');
                addRepasRow('', 'LUNDI', 'DEJEUNER');
                addRepasRow('', 'LUNDI', 'DINER');
            }
        }
        
        // Validation du formulaire
        document.getElementById('editProgrammeForm').addEventListener('submit', function(e) {
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
        
        // Initialiser
        loadCurrentRepas();
    </script>
</body>
</html>