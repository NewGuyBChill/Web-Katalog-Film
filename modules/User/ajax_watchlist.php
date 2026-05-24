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
$action = $_POST['action'] ?? '';
$media_id = (int)($_POST['media_id'] ?? 0);
$media_type = $conn->real_escape_string($_POST['media_type'] ?? 'movie');
$title = $conn->real_escape_string($_POST['title'] ?? '');
$poster_path = $conn->real_escape_string($_POST['poster_path'] ?? '');

if ($media_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid media ID']);
    exit;
}

if ($action === 'add') {
    try {
        $check = $conn->query("SELECT id FROM watchlist WHERE user_id = $user_id AND media_id = $media_id");
        if ($check->num_rows == 0) {
            $sql = "INSERT INTO watchlist (user_id, media_id, media_type, title, poster_path) VALUES ($user_id, $media_id, '$media_type', '$title', '$poster_path')";
            if ($conn->query($sql)) {
                unset($_SESSION['user_watchlist']); // Hapus cache sesi agar diperbarui
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $conn->error]);
            }
        } else {
            echo json_encode(['success' => true]); // Jika sudah ada, biarkan saja
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} elseif ($action === 'remove') {
    try {
        $sql = "DELETE FROM watchlist WHERE user_id = $user_id AND media_id = $media_id";
        if ($conn->query($sql)) {
            unset($_SESSION['user_watchlist']); // Hapus cache sesi agar diperbarui
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>