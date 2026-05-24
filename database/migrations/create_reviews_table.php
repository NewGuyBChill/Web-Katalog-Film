<?php

class create_reviews_table
{
    public function up(\PDO $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            media_id INT NOT NULL,
            media_type ENUM('movie', 'tv') DEFAULT 'movie',
            rating DECIMAL(3,1) NOT NULL,
            review_text TEXT,
            contains_spoilers BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );";
        $db->exec($sql);
    }

    public function down(\PDO $db)
    {
        $sql = "DROP TABLE IF EXISTS reviews;";
        $db->exec($sql);
    }
}

