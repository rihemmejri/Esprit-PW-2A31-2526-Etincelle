<?php

class EnvLoader
{
    /**
     * Load environment variables from a file into $_ENV and putenv().
     *
     * @param string $path Path to the .env file.
     * @return void
     * @throws Exception If the .env file is missing.
     */
    public static function load($path)
    {
        if (!file_exists($path)) {
            throw new Exception(".env file not found at: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                // Remove quotes if present
                $value = trim($value, '"\'');

                if (!empty($name)) {
                    $_ENV[$name] = $value;
                    putenv("$name=$value");
                }
            }
        }
    }

    /**
     * Check if required environment variables are set.
     *
     * @param array $required List of required keys.
     * @return void
     * @throws Exception If any key is missing.
     */
    public static function validate($required)
    {
        foreach ($required as $key) {
            if (getenv($key) === false || getenv($key) === '') {
                die("Critical Error: Missing required environment variable: $key. Please check your .env file.");
            }
        }
    }
}
