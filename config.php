<?php
// config/config.php

// Vérifier si la classe n'existe pas déjà
if (!class_exists('config')) {
    class config {
        private static $conn = null;
        
        public static function getConnexion() {
            if (self::$conn === null) {
                try {
                    self::$conn = new PDO(
                        "mysql:host=localhost;dbname=nutriloop;charset=utf8",
                        "root",
                        "",
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false
                        ]
                    );
                } catch (PDOException $e) {
                    die("Erreur de connexion à la base de données: " . $e->getMessage());
                }
            }
            return self::$conn;
        }
    }
}
?>