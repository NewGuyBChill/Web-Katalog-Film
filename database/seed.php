<?php
require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

echo "Starting Database Seeding...\n";

$db = Database::getInstance();
$pdo = $db->getConnection();

$seeders = [
    'AdminSeeder.php',
    'UserSeeder.php',
    'DemoReviewSeeder.php'
];

foreach ($seeders as $file) {
    require_once __DIR__ . '/seeders/' . $file;
    $className = str_replace('.php', '', $file);
    $seeder = new $className();
    
    echo "Running Seeder: $className\n";
    try {
        $seeder->run($pdo);
    } catch (Exception $e) {
        echo "Error in $className: " . $e->getMessage() . "\n";
    }
}

echo "All seeders completed.\n";
