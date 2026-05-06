<?php
session_start();
header('Content-Type: application/json');

// التحقق من انتهاء الجلسة بعد 30 دقيقة
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit();
}

if (isset($_SESSION['user'])) {
    $_SESSION['LAST_ACTIVITY'] = time(); // تحديث آخر نشاط
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id_user' => $_SESSION['user']['id_user'],
            'nom' => $_SESSION['user']['nom'],
            'prenom' => $_SESSION['user']['prenom'],
            'email' => $_SESSION['user']['email'],
            'role' => $_SESSION['user']['role'],
            'statut' => $_SESSION['user']['statut'],
            'date_inscription' => $_SESSION['user']['date_inscription']
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'user' => null]);
}
?>