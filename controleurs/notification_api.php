<?php
// controleurs/notification_api.php
require_once __DIR__ . '/NotificationController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$controller = new NotificationController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        $controller->apiGetNotifications();
        break;
    case 'mark_read':
        $controller->apiMarkAsRead();
        break;
    case 'mark_all_read':
        $controller->apiMarkAllAsRead();
        break;
    default:
        echo json_encode(['error' => 'Action non reconnue']);
}
?>