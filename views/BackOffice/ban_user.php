<?php
// views/BackOffice/ban_user.php
session_start();
require_once '../../controleurs/UserController.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    header('Location: ../FrontOffice/login.php');
    exit();
}

$userController = new UserController();
$adminName = $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'];

$email = $_GET['email'] ?? '';
$reason = $_GET['reason'] ?? 'Violation des conditions d\'utilisation';

if ($email) {
    $result = $userController->banUserByEmail($email, $adminName, $reason);
    if ($result) {
        header('Location: list.php?success=5');
    } else {
        header('Location: list.php?error=2');
    }
} else {
    header('Location: list.php');
}
?>