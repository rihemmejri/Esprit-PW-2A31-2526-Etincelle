<?php

// Simple .env loader
$envFilePath = __DIR__ . '/.env';
if (file_exists($envFilePath)) {
    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

if (!class_exists('Config')) {
    class Config {
        private static $conn = null;
        
        public static function getConnexion() {
            if (self::$conn === null) {
                try {
                    self::$conn = new PDO(
                        "mysql:host=localhost;dbname=nutriloop",
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