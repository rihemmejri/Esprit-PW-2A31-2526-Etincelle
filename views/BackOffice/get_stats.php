<?php
session_start();
require_once '../../controleurs/UserController.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

$userController = new UserController();

// Compter tous les utilisateurs
$sql = "SELECT COUNT(*) as total FROM user";
$db = config::getConnexion();
$result = $db->query($sql);
$total = $result->fetch(PDO::FETCH_ASSOC)['total'];

// Compter les utilisateurs actifs
$sql = "SELECT COUNT(*) as active FROM user WHERE statut = 'actif'";
$result = $db->query($sql);
$active = $result->fetch(PDO::FETCH_ASSOC)['active'];

// Compter les admins
$sql = "SELECT COUNT(*) as admins FROM user WHERE role = 'ADMIN'";
$result = $db->query($sql);
$admins = $result->fetch(PDO::FETCH_ASSOC)['admins'];

echo json_encode([
    'total' => $total,
    'active' => $active,
    'admins' => $admins
]);
?>