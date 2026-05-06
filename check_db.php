<?php
require_once __DIR__ . '/config.php';
try {
    $db = Config::getConnexion();
    $stmt = $db->query("SELECT * FROM alert WHERE categorie = 'EMAIL_SENT' ORDER BY date DESC LIMIT 5");
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "EMAIL ALERTS IN DB:\n";
    print_r($alerts);
} catch (Exception $e) {}
?>
