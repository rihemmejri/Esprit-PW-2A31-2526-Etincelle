<?php
session_start();
include '../../controleurs/SuiviController.php';

$SuiviController = new SuiviController();

$id = $_GET['id'] ?? null;

if ($id) {
    if ($SuiviController->deleteSuivi($id)) {
        $_SESSION['success_message'] = 'Suivi supprimé avec succès';
    } else {
        $_SESSION['error_message'] = 'Erreur lors de la suppression';
    }
}

header('Location: suiviList.php');
exit();
?>
