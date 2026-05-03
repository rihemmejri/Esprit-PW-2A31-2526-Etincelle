<?php
if (!class_exists('Config')) {
    class Config {
        private static $conn = null;
        
        public static function getConnexion() {
            if (self::$conn === null) {
                try {
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