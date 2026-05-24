<?php

class create_review_likes_table
{
    public function up(\PDO $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS review_likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            review_id INT NOT NULL,
            user_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY (review_id, user_id),
            FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );";
        $db->exec($sql);
    }

    public function down(\PDO $db)
    {
        $sql = "DROP TABLE IF EXISTS review_likes;";
        $db->exec($sql);
    }
}

