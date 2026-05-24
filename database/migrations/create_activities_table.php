<?php

class create_activities_table
{
    public function up(\PDO $db)
    {
        $sql = "CREATE TABLE IF NOT EXISTS activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activity_type VARCHAR(50) NOT NULL,
            target_id INT,
            target_type VARCHAR(50),
            metadata JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );";
        $db->exec($sql);
    }

    public function down(\PDO $db)
    {
        $sql = "DROP TABLE IF EXISTS activities;";
        $db->exec($sql);
    }
}

