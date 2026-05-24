<?php

namespace Modules\Reviews\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;

class ReviewController extends Controller
{
    public function store(Request $request): void
    {
        $mediaId = (int) $request->input('media_id');
        $mediaType = $request->input('media_type', 'movie');
        $rating = (int) $request->input('rating');
        $reviewText = $request->input('review_text', '');
        
        if (!$mediaId || !$rating || !$reviewText) {
            Response::json(['error' => 'Incomplete data'], 400);
            return;
        }

        $userId = auth()->id();
        
        // Check existing
        $existing = $this->db->fetch("SELECT id FROM reviews WHERE user_id = ? AND media_id = ?", [$userId, $mediaId]);

        if ($existing) {
            $this->db->update('reviews', [
                'rating' => $rating,
                'review_text' => $reviewText,
                'updated_at' => date('Y-m-d H:i:s')
            ], ['id' => $existing['id']]);
            
            Response::json(['success' => true, 'action' => 'updated']);
        } else {
            $this->db->insert('reviews', [
                'user_id' => $userId,
                'media_id' => $mediaId,
                'media_type' => $mediaType,
                'rating' => $rating,
                'review_text' => $reviewText,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            Response::json(['success' => true, 'action' => 'created']);
        }
    }
}
