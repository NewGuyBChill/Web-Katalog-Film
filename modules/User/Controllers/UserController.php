<?php

namespace Modules\User\Controllers;

use App\Core\Controller;
use App\Core\Request;

class UserController extends Controller
{
    public function profile(Request $request): void
    {
        $userId = auth()->id();
        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        
        $stats = $this->db->fetch("
            SELECT 
                (SELECT COUNT(*) FROM reviews WHERE user_id = ?) as total_reviews,
                (SELECT COUNT(*) FROM watchlist WHERE user_id = ?) as total_watchlist,
                (SELECT ROUND(AVG(rating), 1) FROM reviews WHERE user_id = ?) as avg_rating
        ", [$userId, $userId, $userId]);

        $this->view('layouts.app', [
            'content' => $this->capture('user.profile', ['user' => $user, 'stats' => $stats]),
            'title' => 'My Profile — CelesView'
        ]);
    }

    public function reviews(Request $request): void
    {
        $userId = auth()->id();
        $reviews = $this->db->fetchAll("SELECT * FROM reviews WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
        
        // Fetch movie details for each review
        $tmdb = new \App\Services\TMDB\TMDBClient();
        foreach ($reviews as &$rev) {
            $detail = $tmdb->movieDetail($rev['media_id']);
            if ($detail) {
                $formatted = $tmdb->formatResults([$detail], 1, 'movie')[0] ?? null;
                $rev['movie'] = $formatted;
            }
        }

        $this->view('layouts.app', [
            'content' => $this->capture('user.reviews', ['reviews' => $reviews]),
            'title' => 'My Reviews — CelesView'
        ]);
    }
    
    /**
     * Capture a view to string.
     */
    private function capture(string $view, array $data = []): string
    {
        extract($data);
        $parts = explode('.', $view);
        $module = ucfirst($parts[0]);
        $file = $parts[1] ?? 'index';
        $path = base_path("modules/{$module}/Views/{$file}.php");

        if (!file_exists($path)) {
            throw new \RuntimeException("View [{$view}] not found.");
        }

        ob_start();
        require $path;
        return ob_get_clean();
    }
}
