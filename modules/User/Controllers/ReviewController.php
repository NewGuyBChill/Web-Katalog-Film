<?php

namespace Modules\User\Controllers;

use App\Core\Controller;
use App\Core\Request;

class ReviewController extends Controller
{
    /**
     * Handle AJAX review submissions and deletions.
     * Maps to legacy index.php?page=ajax_review
     */
    public function ajax(Request $request): void
    {
        header('Content-Type: application/json');

        if (!auth()->check()) {
            echo json_encode(['error' => 'You must be logged in to do this.']);
            return;
        }

        $userId = auth()->id();
        $action = $request->input('action', 'submit');

        if ($action === 'delete') {
            $reviewId = (int)$request->input('review_id', 0);
            
            // Verify ownership
            $rev = $this->db->fetch("SELECT id FROM reviews WHERE id = ? AND user_id = ?", [$reviewId, $userId]);
            if (!$rev) {
                echo json_encode(['error' => 'Review not found or unauthorized.']);
                return;
            }

            $this->db->execute("DELETE FROM reviews WHERE id = ?", [$reviewId]);
            echo json_encode(['success' => true]);
            return;
        }

        // Handle submission (Insert or Update)
        $mediaId   = (int)$request->input('media_id', 0);
        $mediaType = $request->input('media_type', 'movie');
        $rating    = (int)$request->input('rating', 0);
        $text      = $request->input('review_text', '');

        if ($mediaId <= 0 || $rating < 1 || $rating > 5 || empty(trim($text))) {
            echo json_encode(['error' => 'Invalid input data.']);
            return;
        }

        // Check if user already reviewed this media
        $existing = $this->db->fetch(
            "SELECT id FROM reviews WHERE user_id = ? AND media_id = ? AND media_type = ?", 
            [$userId, $mediaId, $mediaType]
        );

        $reviewId = 0;
        if ($existing) {
            $reviewId = $existing['id'];
            $this->db->execute(
                "UPDATE reviews SET rating = ?, review_text = ?, updated_at = NOW() WHERE id = ?",
                [$rating, trim($text), $reviewId]
            );
        } else {
            $reviewId = $this->db->insert('reviews', [
                'user_id'     => $userId,
                'media_id'    => $mediaId,
                'media_type'  => $mediaType,
                'rating'      => $rating,
                'review_text' => trim($text),
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        }

        $user = auth()->user();

        echo json_encode([
            'success'      => true,
            'review_id'    => $reviewId,
            'user_name'    => $user['username'],
            'user_initial' => strtoupper(substr($user['username'], 0, 1))
        ]);
    }
}
