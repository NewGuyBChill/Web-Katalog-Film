<?php

class UserSeeder
{
    public function run(\PDO $db)
    {
        $password = password_hash('user123', PASSWORD_DEFAULT);
        
        $users = [
            ['John Doe', 'john@example.com', '#00d2ff'],
            ['Jane Smith', 'jane@example.com', '#ff00a0'],
            ['Cinephile99', 'cine@example.com', '#FCD34D']
        ];
        
        foreach ($users as $u) {
            $db->exec("INSERT IGNORE INTO users (username, email, password, role, avatar_color) VALUES ('{$u[0]}', '{$u[1]}', '$password', 'user', '{$u[2]}')");
        }
        
        echo "UserSeeder: Inserted dummy users.\n";
    }
}
