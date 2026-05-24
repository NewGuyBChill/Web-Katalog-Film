<?php
require_once __DIR__ . '/../bootstrap/app.php';

use App\Core\Database;

echo "Starting Migrations...\n";

$db = Database::getInstance();
$pdo = $db->getConnection();

$migrations = [
    'create_users_table.php',
    'create_reviews_table.php',
    'create_review_comments_table.php',
    'create_review_likes_table.php',
    'create_ratings_table.php',
    'create_watchlists_table.php',
    'create_notifications_table.php',
    'create_followers_table.php',
    'create_activities_table.php',
    'create_user_preferences_table.php',
    'create_cache_logs_table.php'
];

foreach ($migrations as $file) {
    require_once __DIR__ . '/migrations/' . $file;
    $className = str_replace('.php', '', $file);
    $migration = new $className();
    
    echo "Migrating: $className\n";
    try {
        $migration->up($pdo);
        echo "Done: $className\n";
    } catch (Exception $e) {
        echo "Error in $className: " . $e->getMessage() . "\n";
    }
}

echo "All migrations completed.\n";
