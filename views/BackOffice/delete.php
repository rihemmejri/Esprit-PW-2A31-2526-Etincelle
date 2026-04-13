<?php
session_start();
require_once '../../controleurs/UserController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $userController = new UserController();
    $userController->deleteUser($_GET['id']);
    header('Location: list.php?success=4');
    exit();
} else {
    header('Location: list.php?error=1');
    exit();
}
?>