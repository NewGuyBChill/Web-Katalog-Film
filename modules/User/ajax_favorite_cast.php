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
$cast_id = (int)($_POST['cast_id'] ?? 0);
$cast_name = $conn->real_escape_string($_POST['cast_name'] ?? '');
$cast_image = $conn->real_escape_string($_POST['cast_image'] ?? '');

if ($cast_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid cast ID']);
    exit;
}

try {
    if ($action === 'add') {
        // Gunakan ON DUPLICATE KEY untuk mencegah pesan error bila tak sengaja data ganda dikirim
        $stmt = $conn->prepare("INSERT INTO favorite_casts (user_id, cast_id, cast_name, cast_image) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE cast_name=VALUES(cast_name), cast_image=VALUES(cast_image)");
        $stmt->bind_param("iiss", $user_id, $cast_id, $cast_name, $cast_image);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } elseif ($action === 'remove') {
        $stmt = $conn->prepare("DELETE FROM favorite_casts WHERE user_id = ? AND cast_id = ?");
        $stmt->bind_param("ii", $user_id, $cast_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>