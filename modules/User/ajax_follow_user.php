<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$follower_id = (int)$_SESSION['user_id'];
$following_id = (int)($_POST['following_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($following_id <= 0 || $follower_id === $following_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid target user']);
    exit;
}

try {
    if ($action === 'follow') {
        $stmt = $conn->prepare("INSERT IGNORE INTO user_follows (follower_id, following_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $follower_id, $following_id);
        $stmt->execute();

        // Jika ada baris baru yang ditambahkan (artinya ini 'follow' pertama kali)
        if ($stmt->affected_rows > 0) {
            // Buat Notifikasi untuk si pengguna yang di-follow
            $stmt_get_name = $conn->prepare("SELECT name FROM users WHERE id = ?");
            $stmt_get_name->bind_param("i", $follower_id);
            $stmt_get_name->execute();
            $follower_name = $stmt_get_name->get_result()->fetch_assoc()['name'];

            if ($follower_name) {
                $notif_title = "Anda mendapat pengikut baru";
                $notif_message = "Pengguna <strong>" . htmlspecialchars($follower_name) . "</strong> mulai mengikuti Anda.";
                
                $stmt_notif = $conn->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'follow', ?, ?)");
                $stmt_notif->bind_param("iss", $following_id, $notif_title, $notif_message);
                $stmt_notif->execute();
            }
        }
        echo json_encode(['success' => true]);

    } elseif ($action === 'unfollow') {
        $stmt = $conn->prepare("DELETE FROM user_follows WHERE follower_id = ? AND following_id = ?");
        $stmt->bind_param("ii", $follower_id, $following_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>