<?php

class create_watchlists_table
{
    public function up(\PDO $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS watchlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            media_id INT NOT NULL,
            media_type ENUM('movie', 'tv') DEFAULT 'movie',
            status ENUM('plan_to_watch', 'watching', 'completed', 'dropped') DEFAULT 'plan_to_watch',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY (user_id, media_id, media_type),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );";
        $db->exec($sql);
    }

    public function down(\PDO $db)
    {
        $sql = "DROP TABLE IF EXISTS watchlist;";
        $db->exec($sql);
    }
}

