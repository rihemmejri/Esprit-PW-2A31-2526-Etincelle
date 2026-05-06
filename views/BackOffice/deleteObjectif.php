<?php
session_start();
include '../../controleurs/ObjectifController.php';

$ObjectifController = new ObjectifController();
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: objectifList.php');
    exit();
}

$ObjectifController->deleteObjectif($id);
$_SESSION['success_message'] = 'Objectif supprimé avec succès';
header('Location: objectifList.php');
exit();
?>
