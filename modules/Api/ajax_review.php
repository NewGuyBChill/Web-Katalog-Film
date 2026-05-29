<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php'; // Untuk fetchTMDB dan translateText

header('Content-Type: application/json');

// 1. Validasi Input & User Login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.', 'action' => 'login']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$media_id = (int)($_POST['media_id'] ?? 0);
$media_type = in_array($_POST['media_type'], ['movie', 'tv']) ? $_POST['media_type'] : 'movie';
$media_title = $_POST['media_title'] ?? '';
$media_poster = $_POST['media_poster'] ?? '';

$rating = (int)($_POST['rating'] ?? 0);
$review_text = trim($_POST['review_text'] ?? '');
$review_id = (int)($_POST['review_id'] ?? 0); // Untuk mode edit

if ($media_id <= 0 || $rating < 1 || $rating > 5 || empty($review_text)) {
    echo json_encode(['success' => false, 'error' => translateText('alert_review')]);
    exit;
}

try {
    if ($review_id > 0) {
        // Mode Update
        $stmt = $conn->prepare("UPDATE reviews SET rating = ?, review_text = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("isii", $rating, $review_text, $review_id, $user_id);
        $stmt->execute();
    } else {
        // Mode Insert: ON DUPLICATE KEY UPDATE akan mengubah review jika user sudah pernah mereview film yang sama
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, media_id, media_type, media_title, media_poster, rating, review_text) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE rating=VALUES(rating), review_text=VALUES(review_text)");
        $stmt->bind_param("iisssis", $user_id, $media_id, $media_type, $media_title, $media_poster, $rating, $review_text);
        $stmt->execute();
    }

    // --- LOGIKA NOTIFIKASI REKOMENDASI ---
    // Jika user memberi rating tinggi (4 atau 5) dan ini adalah review baru
    if ($rating >= 4 && $review_id == 0) {
        // Cek apakah sudah ada notifikasi rekomendasi dalam 7 hari terakhir untuk user ini (mencegah spam)
        $stmt_check = $conn->prepare("SELECT id FROM notifications WHERE user_id = ? AND type = 'recommendation' AND created_at >= NOW() - INTERVAL 7 DAY");
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        
        // Jika belum ada, buat notifikasi baru
        if ($stmt_check->get_result()->num_rows === 0) {
            $notif_title = translateText('recommended_for_you');
            $notif_message = translateText('based_on_rating') . " untuk <em>" . htmlspecialchars($media_title) . "</em>.";
            
            $stmt_notif = $conn->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'recommendation', ?, ?)");
            $stmt_notif->bind_param("iss", $user_id, $notif_title, $notif_message);
            $stmt_notif->execute();
        }
    }
    // --- AKHIR LOGIKA NOTIFIKASI ---

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}