<?php

class create_ratings_table
{
    public function up(\PDO $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS ratings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            media_id INT NOT NULL,
            media_type ENUM('movie', 'tv') DEFAULT 'movie',
            average_rating DECIMAL(3,1) DEFAULT 0,
            total_votes INT DEFAULT 0,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY (media_id, media_type)
        );";
        $db->exec($sql);
    }

    public function down(\PDO $db)
    {
        $sql = "DROP TABLE IF EXISTS ratings;";
        $db->exec($sql);
    }
}

