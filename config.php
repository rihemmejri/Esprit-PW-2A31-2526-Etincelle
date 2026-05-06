<?php
require_once __DIR__ . '/load_env.php';

if (!class_exists('Config')) {
    class Config {
        private static $conn = null;
        
        public static function getConnexion() {
            if (self::$conn === null) {
                try {
                    $host = $_ENV['DB_HOST'] ?? 'localhost';
                    $dbname = $_ENV['DB_NAME'] ?? 'nutriloop';
                    $user = $_ENV['DB_USER'] ?? 'root';
                    $pass = $_ENV['DB_PASS'] ?? '';

                    self::$conn = new PDO(
                        "mysql:host=$host;dbname=$dbname",
                        $user,
                        $pass,
                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                    );
                } catch (PDOException $e) {
                    die("Erreur de connexion: " . $e->getMessage());
                }
            }
            return self::$conn;
        }
    }
}
?>