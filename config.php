<?php
// config/config.php

// Charger les variables d'environnement
require_once __DIR__ . '/load_env.php';

// Vérifier si la classe n'existe pas déjà
if (!class_exists('config')) {
    class config {
        private static $conn = null;
        
        public static function getConnexion() {
            if (self::$conn === null) {
                try {
                    $host = getenv('DB_HOST') ?: 'localhost';
                    $dbname = getenv('DB_NAME') ?: 'nutriloop';
                    $user = getenv('DB_USER') ?: 'root';
                    $pass = getenv('DB_PASS') ?: '';

                    self::$conn = new PDO(
                        "mysql:host=$host;dbname=$dbname;charset=utf8",
                        $user,
                        $pass,
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