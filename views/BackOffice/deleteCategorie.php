<?php
include '../../controleurs/CategorieController.php';
session_start();

$categorieController = new CategorieController();

if (isset($_GET['id'])) {
    $categorieController->deleteCategorie($_GET['id']);
    $_SESSION['success_message'] = 'Catégorie supprimée avec succès';
}

header('Location: categorieList.php');
exit;
?>
