<?php
// setup.php - Database setup script

echo "MindBridge Database Setup\n";
echo "==========================\n\n";

try {
    // Connect without database
    $pdo = new PDO("mysql:host=localhost;charset=utf8", "root", "");
    echo "✓ Connected to MySQL\n";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS mindbridge");
    echo "✓ Database 'mindbridge' created\n";

    // Select database
    $pdo->exec("USE mindbridge");

    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database/mindbridge.sql');
    $pdo->exec($sql);
    echo "✓ Database schema imported\n";

    echo "\n🎉 Setup completed successfully!\n";
    echo "You can now access MindBridge at: http://localhost/MindBridge/\n";
    echo "Default login: admin@mindbridge.com / password123\n";

} catch (PDOException $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "\n";
    echo "Please ensure:\n";
    echo "1. XAMPP is installed\n";
    echo "2. Apache and MySQL are running\n";
    echo "3. Run this script from the MindBridge directory\n";
}
?>