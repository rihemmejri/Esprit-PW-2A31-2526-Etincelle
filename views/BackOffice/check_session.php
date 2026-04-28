<?php
session_start();
header('Content-Type: application/json');

// التحقق من انتهاء الجلسة
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'ADMIN') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time();

echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
?>