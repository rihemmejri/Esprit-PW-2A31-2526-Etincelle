<?php
session_start();
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$recetteController = new RecetteController();
$id = $_GET['id'] ?? null;

if ($id) {
    // Vérifier si la confirmation est donnée
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        $recetteController->deleteRecette($id);
        $_SESSION['success_message'] = 'Recette supprimée avec succès';
        header('Location: recetteList.php');
        exit;
    }
} else {
    header('Location: recetteList.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de suppression - Recette</title>
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
                <p>Êtes-vous sûr de vouloir supprimer cette recette ?</p>
                
                <div class="warning-text">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Cette action est irréversible. Toutes les données associées à cette recette seront définitivement supprimées.</span>
                </div>
                
                <div class="actions">
                    <a href="deleteRecette.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>
                        Oui, supprimer
                    </a>
                    <a href="recetteList.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Non, annuler
                    </a>
                </div>
                
                <div class="footer-note">
                    <i class="fas fa-info-circle"></i>
                    Vous serez redirigé vers la liste des recettes après l'action
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/recette.js"></script>
</body>
</html>