<?php

class AdminSeeder
{
    public function run(\PDO $db)
    {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $db->exec("INSERT IGNORE INTO users (username, email, password, role, avatar_color) VALUES ('SuperAdmin', 'admin@celesview.com', '$password', 'admin', '#FF0055')");
        echo "AdminSeeder: Inserted default admin.\n";
    }
}
