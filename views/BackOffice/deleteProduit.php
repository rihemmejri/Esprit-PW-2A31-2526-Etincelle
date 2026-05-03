<?php
include '../../controleurs/ProduitController.php';

$produitController = new ProduitController();

if (isset($_GET['id'])) {
    $produitController->deleteProduit($_GET['id']);
<<<<<<< HEAD
    session_start();
    $_SESSION['success_message'] = 'Produit supprimé avec succès';
=======
<<<<<<< HEAD
    session_start();
    $_SESSION['success_message'] = 'Produit supprimé avec succès';
=======
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
    header('Location: produitList.php');
    exit;
} else {
    header('Location: produitList.php');
    exit;
}
?>
