<?php
include '../../controleurs/ProduitController.php';

$produitController = new ProduitController();

if (isset($_GET['id'])) {
    $produitController->deleteProduit($_GET['id']);
    header('Location: produitList.php');
    exit;
} else {
    header('Location: produitList.php');
    exit;
}
?>
