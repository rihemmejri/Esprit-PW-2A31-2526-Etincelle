<?php
if (!class_exists('Config')) {
    class Config {
        private static $conn = null;
        
        public static function loadEnv() {
            $path = __DIR__ . '/.env';
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos(trim($line), '#') === 0) continue;
                    list($name, $value) = explode('=', $line, 2);
                    $_ENV[trim($name)] = trim($value);
                }
            }
        }

        public static function getConnexion() {
            if (self::$conn === null) {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $dbname = $_ENV['DB_NAME'] ?? 'nutriloop';
                $user = $_ENV['DB_USER'] ?? 'root';
                $pass = $_ENV['DB_PASS'] ?? '';

                try {
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
    Config::loadEnv();
}
?>
