<?php
// views/BackOffice/unban_user.php
session_start();
require_once '../../controleurs/UserController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$userController = new UserController();
$adminName = $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'];

$email = $_GET['email'] ?? '';

if ($email) {
    $result = $userController->unbanUserByEmail($email, $adminName);
    if ($result) {
        header('Location: list.php?success=6');
    } else {
        header('Location: list.php?error=3');
    }
} else {
    header('Location: list.php');
}
?>