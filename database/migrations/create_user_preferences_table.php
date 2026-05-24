<?php

class create_user_preferences_table
{
    public function up(\PDO $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS user_preferences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            favorite_genres JSON NULL,
            email_notifications BOOLEAN DEFAULT TRUE,
            theme ENUM('dark', 'light', 'system') DEFAULT 'dark',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );";
        $db->exec($sql);
    }

    public function down(\PDO $db)
    {
        $sql = "DROP TABLE IF EXISTS user_preferences;";
        $db->exec($sql);
    }
}

