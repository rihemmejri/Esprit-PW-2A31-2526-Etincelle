<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'message' => 'API fonctionne correctement',
    'time' => date('Y-m-d H:i:s')
]);
?>