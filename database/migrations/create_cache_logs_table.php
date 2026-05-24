<?php

class create_cache_logs_table
{
    public function up(\PDO $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS cache_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cache_key VARCHAR(255) NOT NULL UNIQUE,
            hits INT DEFAULT 0,
            last_hit TIMESTAMP NULL,
            expires_at TIMESTAMP NULL
        );";
        $db->exec($sql);
    }

    public function down(\PDO $db)
    {
        $sql = "DROP TABLE IF EXISTS cache_logs;";
        $db->exec($sql);
    }
}

