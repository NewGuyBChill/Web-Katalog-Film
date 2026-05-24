<?php

namespace Modules\Watchlist\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

/**
 * WatchlistController — Manages user's watchlist.
 */
class WatchlistController extends Controller
{
    /**
     * GET /watchlist — Show user's watchlist page.
     */
    public function index(Request $request): void
    {
        $userId = Session::userId();

        $watchlistItems = $this->db->fetchAll(
            "SELECT * FROM watchlist WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );

        $tmdb = new \App\Services\TMDB\TMDBClient();
        $movies = [];
        foreach ($watchlistItems as $item) {
            if ($item['media_type'] === 'movie') {
                $detail = $tmdb->movieDetail($item['media_id']);
                if ($detail) {
                    $formatted = $tmdb->formatResults([$detail], 1, 'movie')[0] ?? null;
                    if ($formatted) {
                        $formatted['added_at'] = $item['created_at'];
                        $movies[] = $formatted;
                    }
                }
            }
        }

        $this->view('layouts.app', [
            'content' => $this->capture('watchlist.index', ['movies' => $movies]),
            'title' => 'Watchlist — CelesView',
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

    /**
     * POST /api/watchlist — Toggle add/remove from watchlist (AJAX).
     */
    public function toggle(Request $request): void
    {
        $userId  = Session::userId();
        $mediaId = $request->input('media_id');
        $type    = $request->input('type', 'movie');

        if (!$mediaId) {
            Response::json(['error' => 'media_id is required'], 400);
            return;
        }

        // Check if already in watchlist
        $existing = $this->db->fetch(
            "SELECT id FROM watchlist WHERE user_id = ? AND media_id = ?",
            [$userId, $mediaId]
        );

        if ($existing) {
            $this->db->delete('watchlist', ['user_id' => $userId, 'media_id' => $mediaId]);
            // Update session cache
            $wl = Session::get('user_watchlist', []);
            Session::set('user_watchlist', array_values(array_diff($wl, [$mediaId])));
            Response::json(['success' => true, 'status' => 'removed']);
        } else {
            $this->db->insert('watchlist', [
                'user_id'    => $userId,
                'media_id'   => $mediaId,
                'media_type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            // Update session cache
            $wl = Session::get('user_watchlist', []);
            $wl[] = $mediaId;
            Session::set('user_watchlist', $wl);
            Response::json(['success' => true, 'status' => 'added']);
        }
    }
}
