<?php

class DemoReviewSeeder
{
    public function run(\PDO $db)
    {
        $userIdStmt = $db->query("SELECT id FROM users LIMIT 3");
        $users = $userIdStmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($users)) {
            echo "DemoReviewSeeder: No users found. Run UserSeeder first.\n";
            return;
        }

        // Dummy TMDB Movie IDs (Inception, Interstellar, The Dark Knight)
        $movies = [27205, 157336, 155];
        
        $reviews = [
            "Absolutely mind-blowing masterpiece. The visuals are stunning.",
            "Great concept, but felt a bit slow in the middle.",
            "One of the best movies I have ever seen. Highly recommended."
        ];

        foreach ($users as $userId) {
            foreach ($movies as $movieId) {
                $rating = rand(7, 10); // Generates 7, 8, 9, 10
                $text = $reviews[array_rand($reviews)];
                
                // Use INSERT IGNORE to prevent duplicates if run multiple times
                $db->exec("INSERT IGNORE INTO reviews (user_id, media_id, media_type, rating, review_text) VALUES ($userId, $movieId, 'movie', $rating, '$text')");
                
                // Add to watchlist for some users
                if (rand(0, 1)) {
                    $db->exec("INSERT IGNORE INTO watchlist (user_id, media_id, media_type, status) VALUES ($userId, $movieId, 'movie', 'completed')");
                }
            }
        }
        
        echo "DemoReviewSeeder: Inserted demo reviews and watchlists.\n";
    }
}
