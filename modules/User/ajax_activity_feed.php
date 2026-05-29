<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$current_user_id = (int)$_SESSION['user_id'];
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 10;

$res_feed = $conn->query("
    SELECT r.*, u.name as author_name,
           (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id) as like_count,
           (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id AND user_id = $current_user_id) as is_liked_by_user
    FROM reviews r
    JOIN user_follows uf ON r.user_id = uf.following_id
    JOIN users u ON r.user_id = u.id
    WHERE uf.follower_id = $current_user_id
    ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset
");

$reviews = [];
if ($res_feed) {
    while($row = $res_feed->fetch_assoc()) {
        $row['poster'] = !empty($row['media_poster']) ? $row['media_poster'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22300%22%20viewBox%3D%220%200%20200%20300%22%3E%3Crect%20width%3D%22200%22%20height%3D%22300%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E";
        $row['starsHtml'] = '';
        for($i=0; $i<5; $i++) { $row['starsHtml'] .= $i < $row['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
        $row['initial'] = strtoupper(substr($row['author_name'], 0, 1));
        $row['activeClass'] = !empty($row['is_liked_by_user']) ? 'active' : '';
        $row['formatted_date'] = date('d M Y', strtotime($row['created_at']));
        $row['safe_text'] = nl2br(htmlspecialchars($row['review_text']));
        $row['safe_title'] = htmlspecialchars($row['media_title'] ?? 'Unknown Media');
        $row['media_type_label'] = $row['media_type'] === 'tv' ? 'TV Show' : 'Film';
        $reviews[] = $row;
    }
}

echo json_encode(['success' => true, 'reviews' => $reviews]);