<?php
// setup.php - Database setup script

echo "MindBridge Database Setup\n";
echo "==========================\n\n";

try {
    // Connect without database
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    echo "✓ Connected to MySQL\n";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS mindbridge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'mindbridge' created\n";

    // Select database
    $pdo->exec("USE mindbridge");

    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database/schema.sql');
    $pdo->exec($sql);
    echo "✓ Database schema imported\n";

    echo "\n🎉 Setup completed successfully!\n";
    echo "You can now access MindBridge at: http://localhost/MindBridge/\n";
    echo "Default login: admin@mindbridge.local / password123\n";

} catch (PDOException $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "\n";
    echo "Please ensure:\n";
    echo "1. XAMPP is installed and running\n";
    echo "2. Apache and MySQL are started in XAMPP Control Panel\n";
    echo "3. Run this script from the MindBridge directory\n";
}
?>