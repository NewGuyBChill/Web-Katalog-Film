<?php
require_once __DIR__ . '/../bootstrap/app.php';

$pdo = App\Core\Database::getInstance()->getConnection();

echo "Dropping all tables...\n";
$pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
$tables = ['users', 'reviews', 'review_comments', 'review_likes', 'ratings', 'watchlist', 'notifications', 'followers', 'activities', 'user_preferences', 'cache_logs'];
foreach ($tables as $t) {
    $pdo->exec("DROP TABLE IF EXISTS `$t`");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
echo "Tables dropped.\n";
