<?php

namespace Modules\Recommendation\Services;

use App\Core\Database;
use App\Services\TMDB\TMDBClient;

class MovieRecommendationService
{
    private Database $db;
    private TMDBClient $tmdb;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->tmdb = new TMDBClient();
    }

    /**
     * Get personalized recommendations based on the user's highly-rated movies or watchlist.
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 10): array
    {
        // 1. Ambil 3 film terakhir yang di-review dengan nilai tinggi (>=8) ATAU ada di watchlist
        $sql = "
            SELECT media_id, media_type 
            FROM reviews 
            WHERE user_id = ? AND rating >= 8 
            ORDER BY created_at DESC 
            LIMIT 3
        ";
        $favorites = $this->db->fetchAll($sql, [$userId]);

        if (empty($favorites)) {
            // Fallback ke watchlist jika belum ada review bagus
            $sql = "
                SELECT media_id, media_type 
                FROM watchlist 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 3
            ";
            $favorites = $this->db->fetchAll($sql, [$userId]);
        }

        if (empty($favorites)) {
            // Fallback ultimate: Kasih trending movies kalau user benar-benar baru
            return $this->tmdb->formatResults($this->tmdb->trending()['results'] ?? [], 1, 'movie');
        }

        // 2. Gunakan TMDB API untuk mencari film serupa (Similar Movies) dari film favorit user
        $recommendations = [];
        $fetchedIds = []; // untuk mencegah duplikasi

        foreach ($favorites as $fav) {
            $type = $fav['media_type'];
            $id = $fav['media_id'];
            
            // Panggil API (Misal kita asumsikan tmdb punya endpoint similar)
            // Karena TMDBClient kita mungkin belum punya method similar, kita bisa buat endpoint call manual 
            // atau pakai discover berdasarkan genre. 
            // Untuk amannya, kita fetch genre dari film tersebut lalu discover.
            
            $detail = $this->tmdb->movieDetail($id);
            if ($detail && !empty($detail['genres'])) {
                $genreId = $detail['genres'][0]['id'];
                
                // Discover by genre
                $similar = $this->tmdb->fetchData("/discover/movie", [
                    'with_genres' => $genreId,
                    'sort_by' => 'popularity.desc',
                    'page' => 1
                ]);
                
                if (isset($similar['results'])) {
                    foreach ($similar['results'] as $movie) {
                        if (!in_array($movie['id'], $fetchedIds) && $movie['id'] != $id) {
                            $recommendations[] = $movie;
                            $fetchedIds[] = $movie['id'];
                        }
                    }
                }
            }
        }

        // 3. Acak dan batasi hasilnya
        shuffle($recommendations);
        $finalRaw = array_slice($recommendations, 0, $limit);
        
        return $this->tmdb->formatResults($finalRaw, 1, 'movie');
    }
}
