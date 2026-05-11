<?php
// Database setup script for NutriLoop
echo "<h1>NutriLoop Database Setup</h1>";

try {
    // First try to connect without specifying database
    $conn = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p style='color: green;'>✓ Connected to MySQL server</p>";
    
    // Check if Nutriloop database exists
    $result = $conn->query("SHOW DATABASES LIKE 'Nutriloop'");
    $dbExists = $result->rowCount() > 0;
    
    if (!$dbExists) {
        echo "<p>Creating Nutriloop database...</p>";
        $conn->exec("CREATE DATABASE Nutriloop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✓ Database created</p>";
    } else {
        echo "<p style='color: green;'>✓ Database already exists</p>";
    }
    
    // Connect to the Nutriloop database
    $conn = new PDO(
        "mysql:host=localhost;dbname=Nutriloop",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p style='color: green;'>✓ Connected to Nutriloop database</p>";
    
    // Create user table first if it doesn't exist (needed for foreign key)
    $userTableCheck = $conn->query("SHOW TABLES LIKE 'user'");
    $userTableExists = $userTableCheck->rowCount() > 0;
    
    if (!$userTableExists) {
        echo "<p>Creating user table...</p>";
        $sql = "CREATE TABLE user (
            id_user INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100),
            prenom VARCHAR(100),
            email VARCHAR(150) UNIQUE,
            mot_de_passe VARCHAR(255),
            date_inscription DATE,
            role ENUM('ADMIN','USER'),
            statut ENUM('ACTIF','INACTIF')
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ User table created</p>";
        
        // Insert sample user
        $stmt = $conn->prepare("INSERT INTO user (nom, prenom, email, mot_de_passe, date_inscription, role, statut) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'User', 'admin@nutriloop.com', 'password123', '2024-01-01', 'ADMIN', 'ACTIF']);
    } else {
        echo "<p style='color: green;'>✓ User table already exists</p>";
    }
    
    // Create repas table if it doesn't exist (needed for foreign key)
    $repasTableCheck = $conn->query("SHOW TABLES LIKE 'repas'");
    $repasTableExists = $repasTableCheck->rowCount() > 0;
    
    if (!$repasTableExists) {
        echo "<p>Creating repas table...</p>";
        $sql = "CREATE TABLE repas (
            id_repas INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255),
            type VARCHAR(100),
            calories INT,
            proteines FLOAT,
            glucides FLOAT,
            lipides FLOAT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Repas table created</p>";
        
        // Insert sample repas data
        $sampleRepas = [
            ["Salade Quinoa", "PETIT_DEJEUNER", 350, 12.5, 45.2, 8.3],
            ["Poulet Grillé", "DEJEUNER", 450, 35.2, 25.1, 15.8],
            ["Buddha Bowl", "DINER", 380, 18.5, 42.3, 12.7],
            ["Smoothie Bowl", "PETIT_DEJEUNER", 280, 8.2, 52.1, 6.5],
            ["Saumon Teriyaki", "DEJEUNER", 520, 42.1, 35.8, 18.9]
        ];
        
        foreach ($sampleRepas as $repas) {
            $stmt = $conn->prepare("INSERT INTO repas (nom, type, calories, proteines, glucides, lipides) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute($repas);
        }
        echo "<p style='color: green;'>✓ Sample repas data inserted</p>";
    } else {
        echo "<p style='color: green;'>✓ Repas table already exists</p>";
    }
    
    // Create programme table if it doesn't exist
    $tableCheck = $conn->query("SHOW TABLES LIKE 'programme'");
    $tableExists = $tableCheck->rowCount() > 0;
    
    if (!$tableExists) {
        echo "<p>Creating programme table...</p>";
        $sql = "CREATE TABLE programme (
            id_programme INT AUTO_INCREMENT PRIMARY KEY,
            id_user INT,
            objectif VARCHAR(255),
            date_debut DATE,
            date_fin DATE,
            id_repas INT,
            FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
            FOREIGN KEY (id_repas) REFERENCES repas(id_repas) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Programme table created</p>";
        
        // Insert sample data
        echo "<p>Inserting sample programme data...</p>";
        $sampleData = [
            [1, "PERTE POIDS", "2024-01-01", "2024-01-21", 1],
            [1, "EQUILIBRE", "2024-01-05", "2024-02-05", 2],
            [1, "MAINTIEN", "2024-01-10", "2024-02-25", 3],
            [1, "PERTE POIDS", "2024-01-15", "2024-02-15", 4],
            [1, "MUSCULATION", "2024-01-20", "2024-03-20", 5],
            [1, "EQUILIBRE", "2024-02-01", "2024-02-28", 2],
            [1, "MAINTIEN", "2024-02-05", "2024-03-05", 3],
            [1, "PERTE POIDS", "2024-02-10", "2024-03-10", 1]
        ];
        
        foreach ($sampleData as $data) {
            $stmt = $conn->prepare("INSERT INTO programme (id_user, objectif, date_debut, date_fin, id_repas) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute($data);
        }
        echo "<p style='color: green;'>✓ Sample programme data inserted</p>";
    } else {
        echo "<p style='color: green;'>✓ Programme table already exists</p>";
    }
    
    // Test the connection like the API does
    $testResult = $conn->query("SELECT COUNT(*) as count FROM programme");
    $count = $testResult->fetch(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✓ Found {$count['count']} programmes in database</p>";
    
    // Show sample objectives
    $objResult = $conn->query("SELECT DISTINCT objectif, COUNT(*) as count FROM programme GROUP BY objectif ORDER BY count DESC");
    echo "<h3>Program Types in Database:</h3><ul>";
    while ($row = $objResult->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>{$row['objectif']}: {$row['count']} programmes</li>";
    }
    echo "</ul>";
    
    echo "<h2 style='color: green;'>✓ Database setup complete!</h2>";
    echo "<p>You can now use the statistics button - it should work properly.</p>";
    echo "<p><a href='views/BackOffice/programmeList.php'>Go to Programme Management</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Database Connection Error</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>Solutions to try:</h3>";
    echo "<ol>";
    echo "<li>Make sure MySQL/MariaDB server is running</li>";
    echo "<li>Check if MySQL is installed on your system</li>";
    echo "<li>Verify the database credentials in config.php</li>";
    echo "<li>Try restarting your MySQL server</li>";
    echo "</ol>";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<p style='color: orange;'>Note: This looks like a credentials issue. Make sure MySQL username 'root' with no password is correct.</p>";
    }
    
    if (strpos($e->getMessage(), 'No such file') !== false || strpos($e->getMessage(), 'connection refused') !== false) {
        echo "<p style='color: orange;'>Note: This looks like MySQL server is not running. Start MySQL service.</p>";
    }
}
?>
