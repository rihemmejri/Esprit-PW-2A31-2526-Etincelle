<?php
// verify_setup.php
require_once 'config.php';

echo "<h1>NutriLoop Verification</h1>";

// 1. Check DB Connection
try {
    $db = config::getConnexion();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Check if new tables exist
    $tables = ['user', 'score_journalier', 'alert', 'ai_prediction', 'notifications'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Table '$table' exists.</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Table '$table' does not exist in the current database.</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// 2. Check Environment Variables
$groq_key = getenv('GROQ_API_KEY_HEALTH');
if ($groq_key) {
    echo "<p style='color: green;'>✅ Environment variables loaded (GROQ_API_KEY_HEALTH is set).</p>";
} else {
    echo "<p style='color: orange;'>⚠️ GROQ_API_KEY_HEALTH is not set. Check your .env file.</p>";
}

echo "<p><a href='views/FrontOffice/home.php'>Go to Home Page</a></p>";
?>
