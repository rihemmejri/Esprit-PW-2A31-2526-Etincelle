<?php
require_once __DIR__ . '/EnvLoader.php';

try {
    EnvLoader::load(__DIR__ . '/.env');
    EnvLoader::validate(['DB_HOST', 'DB_NAME', 'DB_USER']);
} catch (Exception $e) {
    die("Configuration Error: " . $e->getMessage());
}

if (!class_exists('Config')) {
    class Config {
        private static $conn = null;
        
        public static function getConnexion() {
            if (self::$conn === null) {
                try {
                    $host = getenv('DB_HOST');
                    $dbname = getenv('DB_NAME');
                    $user = getenv('DB_USER');
                    $pass = getenv('DB_PASS');

                    self::$conn = new PDO(
                        "mysql:host=localhost;dbname=Nutriloop",
                        "root",
                        "",
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