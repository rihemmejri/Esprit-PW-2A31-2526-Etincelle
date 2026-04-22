<?php
// views/backOffice/preparation/deletePreperation.php
include '../../controleurs/PreperationController.php';
require_once __DIR__ . '/../../models/preperation.php';

$preperationController = new PreperationController();
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: preperationList.php');
    exit;
}

if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $preperationController->deletePreperation($id);
    header('Location: preperationList.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de suppression - Étape</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nutrition-style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container-delete {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container-delete">
        <div class="confirmation-card">
            <div class="warning-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <div class="content">
                <h2>⚠️ Confirmation</h2>
                <p>Êtes-vous sûr de vouloir supprimer cette étape ?</p>
                
                <div class="warning-text">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Cette action est irréversible. Toutes les données associées à cette étape seront définitivement supprimées.</span>
                </div>
                
                <div class="actions">
                    <a href="deletePreperation.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Oui, supprimer
                    </a>
                    <a href="preperationList.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Non, annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>