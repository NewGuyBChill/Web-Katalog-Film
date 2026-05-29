<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? 'add';

try {
    if ($action === 'delete') {
        $reply_id = (int)($_POST['reply_id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM review_replies WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $reply_id, $user_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } elseif ($action === 'add') {
        $review_id = (int)($_POST['review_id'] ?? 0);
        $reply_text = trim($_POST['reply_text'] ?? '');
        
        if ($review_id <= 0 || empty($reply_text)) {
            echo json_encode(['success' => false, 'error' => 'Data tidak valid']);
            exit;
        }
        
        $stmt = $conn->prepare("INSERT INTO review_replies (review_id, user_id, reply_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $review_id, $user_id, $reply_text);
        $stmt->execute();
        $new_reply_id = $conn->insert_id;
        
        // Buat Notifikasi untuk si penulis ulasan awal
        $stmt_get_rev = $conn->prepare("SELECT r.user_id, r.media_title, u.name FROM reviews r JOIN users u ON u.id = ? WHERE r.id = ?");
        $stmt_get_rev->bind_param("ii", $user_id, $review_id);
        $stmt_get_rev->execute();
        $rev_data = $stmt_get_rev->get_result()->fetch_assoc();
        
        // Jika bukan dia sendiri yang me-reply, berikan notifikasi
        if ($rev_data && $rev_data['user_id'] != $user_id) {
            $notif_title = "Balasan Baru di Ulasan Anda";
            $notif_message = "Pengguna <strong>" . htmlspecialchars($rev_data['name']) . "</strong> mengomentari ulasan Anda pada <em>" . htmlspecialchars($rev_data['media_title']) . "</em>.";
            $stmt_notif = $conn->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'comment', ?, ?)");
            $stmt_notif->bind_param("iss", $rev_data['user_id'], $notif_title, $notif_message);
            $stmt_notif->execute();
        }
        
        echo json_encode([
            'success' => true, 'reply_id' => $new_reply_id, 'user_id' => $user_id,
            'user_name' => $_SESSION['user'], 'user_initial' => strtoupper(substr($_SESSION['user'], 0, 1)),
            'reply_text' => htmlspecialchars($reply_text)
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}