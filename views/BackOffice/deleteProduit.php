<?php
include '../../controleurs/ProduitController.php';

$produitController = new ProduitController();

if (isset($_GET['id'])) {
    $produitController->deleteProduit($_GET['id']);
    session_start();
    $_SESSION['success_message'] = 'Produit supprimé avec succès';
    header('Location: produitList.php');
    exit;
} else {
    header('Location: produitList.php');
    exit;
}
?>
