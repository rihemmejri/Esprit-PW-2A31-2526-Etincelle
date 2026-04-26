<?php
// views/backOffice/preparation/viewPreperation.php
include '../../controleurs/PreperationController.php';
require_once __DIR__ . '/../../models/preperation.php';

$preperationController = new PreperationController();
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: preperationList.php');
    exit;
}

$preperation = $preperationController->getPreperationById($id);
if (!$preperation) {
    header('Location: preperationList.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'étape - Préparation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        .detail-card {
            max-width: 900px;
            margin: 0 auto;
        }
        .detail-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-section h3 {
            color: #2e7d32;
            margin-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 8px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-label {
            width: 200px;
            font-weight: 600;
            color: #555;
        }
        .detail-value {
            flex: 1;
            color: #333;
        }
        .instruction-text {
            background: white;
            padding: 15px;
            border-radius: 8px;
            line-height: 1.6;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-list">
        <div class="header">
            <h1>
                <i class="fas fa-info-circle"></i>
                Détail de l'étape #<?= $id ?>
            </h1>
            <div>
                <a href="editPreperation.php?id=<?= $id ?>" class="add-btn" style="background: #ff9800;">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="preperationList.php" class="add-btn" style="background: #666;">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-section">
                <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
                <div class="detail-row">
                    <div class="detail-label">ID de l'étape :</div>
                    <div class="detail-value">#<?= $preperation->getIdEtape() ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Recette associée :</div>
                    <div class="detail-value">
                        <a href="recetteList.php?id=<?= $preperation->getIdRecette() ?>">
                            <?= htmlspecialchars($preperation->getRecetteNom()) ?>
                        </a>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Numéro d'ordre :</div>
                    <div class="detail-value">Étape <?= $preperation->getOrdre() ?></div>
                </div>
            </div>

            <div class="detail-section">
                <h3><i class="fas fa-clock"></i> Temps et température</h3>
                <div class="detail-row">
                    <div class="detail-label">Durée :</div>
                    <div class="detail-value"><?= $preperation->getDuree() ?: 0 ?> minutes</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Température :</div>
                    <div class="detail-value"><?= $preperation->getTemperature() ? $preperation->getTemperature() . '°C' : 'Non spécifiée' ?></div>
                </div>
            </div>

            <div class="detail-section">
                <h3><i class="fas fa-tools"></i> Action et matériel</h3>
                <div class="detail-row">
                    <div class="detail-label">Type d'action :</div>
                    <div class="detail-value">
                        <?php
                        $actionIcons = ['COUPER' => 'fa-cut', 'MELANGER' => 'fa-mix', 'CUISSON' => 'fa-fire'];
                        ?>
                        <i class="fas <?= $actionIcons[$preperation->getTypeAction()] ?? 'fa-question' ?>"></i>
                        <?= $preperation->getTypeAction() ?: 'Non spécifié' ?>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Outil utilisé :</div>
                    <div class="detail-value"><?= $preperation->getOutilUtilise() ?: 'Non spécifié' ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Quantité ingrédient :</div>
                    <div class="detail-value"><?= $preperation->getQuantiteIngredient() ?: 'Non spécifiée' ?></div>
                </div>
            </div>

            <div class="detail-section">
                <h3><i class="fas fa-align-left"></i> Instruction</h3>
                <div class="instruction-text">
                    <?= nl2br(htmlspecialchars($preperation->getInstruction())) ?>
                </div>
            </div>

            <?php if ($preperation->getAstuce()): ?>
            <div class="detail-section">
                <h3><i class="fas fa-lightbulb"></i> Astuce</h3>
                <div class="instruction-text" style="background: #fff8e1;">
                    <i class="fas fa-star" style="color: #ffc107;"></i>
                    <?= nl2br(htmlspecialchars($preperation->getAstuce())) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>