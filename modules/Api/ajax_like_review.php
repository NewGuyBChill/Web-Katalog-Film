<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// 1. Validasi Input & User Login
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['review_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.', 'action' => 'login']);
    exit;
}

$liker_id = (int)$_SESSION['user_id'];
$review_id = (int)$_POST['review_id'];
$action = $_POST['action'] ?? 'like'; // Aksi: 'like' atau 'unlike'

try {
    if ($action === 'like') {
        // 2. Masukkan data 'like' ke tabel review_likes, abaikan jika duplikat (lebih efisien)
        $stmt = $conn->prepare("INSERT IGNORE INTO review_likes (user_id, review_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $liker_id, $review_id);
        $stmt->execute();
        
        // Cek apakah ada baris baru yang ditambahkan (artinya ini adalah 'like' pertama kali)
        if ($stmt->affected_rows > 0) {
            // 3. Buat Notifikasi untuk si penulis review
            // Ambil ID penulis review, judul film, dan nama si Liker
            $stmt_get_data = $conn->prepare(
                "SELECT r.user_id AS author_id, u_liker.name AS liker_name, r.media_title 
                 FROM reviews r
                 JOIN users u_liker ON u_liker.id = ?
                 WHERE r.id = ?"
            );
            $stmt_get_data->bind_param("ii", $liker_id, $review_id);
            $stmt_get_data->execute();
            $review_data = $stmt_get_data->get_result()->fetch_assoc();

            if ($review_data) {
                $author_id = $review_data['author_id'];
                $liker_name = $review_data['liker_name'];
                $media_title = $review_data['media_title'];

                // Jangan kirim notifikasi jika user me-like review-nya sendiri
                if ($author_id != $liker_id) {
                    $notif_title = "Ulasan Anda disukai";
                    $notif_message = "Pengguna <strong>" . htmlspecialchars($liker_name) . "</strong> menyukai ulasan Anda";
                    if (!empty($media_title)) {
                        $notif_message .= " pada <em>" . htmlspecialchars($media_title) . "</em>.";
                    } else {
                        $notif_message .= ".";
                    }
                    
                    $stmt_notif = $conn->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'like', ?, ?)");
                    $stmt_notif->bind_param("iss", $author_id, $notif_title, $notif_message);
                    $stmt_notif->execute();
                }
            }
        }
        echo json_encode(['success' => true, 'action' => 'liked']);

    } elseif ($action === 'unlike') {
        // Hapus data 'like' dari tabel
        $stmt = $conn->prepare("DELETE FROM review_likes WHERE user_id = ? AND review_id = ?");
        $stmt->bind_param("ii", $liker_id, $review_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'action' => 'unliked']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}