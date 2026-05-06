<?php
require_once __DIR__ . '/../config.php';
try {
    $db = Config::getConnexion();
    $stmt = $db->query("SHOW TABLES");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
