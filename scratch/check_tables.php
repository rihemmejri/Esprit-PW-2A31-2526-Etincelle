<?php
require_once __DIR__ . '/../config.php';
try {
    $db = Config::getConnexion();
    $stmt = $db->query("SHOW TABLES LIKE 'etape'");
    if ($stmt->fetch()) {
        echo "TABLE_ETAPE_EXISTS";
    } else {
        $stmt = $db->query("SHOW TABLES LIKE 'preperation'");
        if ($stmt->fetch()) {
            echo "TABLE_PREPERATION_EXISTS";
        } else {
            echo "NONE_FOUND";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
