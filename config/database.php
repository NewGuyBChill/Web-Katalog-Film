<?php

/**
 * Database Configuration
 */
return [
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'kinema_db'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
];
