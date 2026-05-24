<?php
error_reporting(0); // Mencegah PHP Warning merusak format JSON
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? 'add';

if ($action === 'delete') {
    $review_id = (int)($_POST['review_id'] ?? 0);
    try {
        if ($conn->query("DELETE FROM reviews WHERE id = $review_id AND user_id = $user_id")) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

$media_id = (int)($_POST['media_id'] ?? 0);
$media_type = $conn->real_escape_string($_POST['media_type'] ?? 'movie');
$rating = (int)($_POST['rating'] ?? 0);
$review_text = $conn->real_escape_string($_POST['review_text'] ?? '');
$title = $conn->real_escape_string(urldecode($_POST['title'] ?? ''));
$poster = $conn->real_escape_string(urldecode($_POST['poster'] ?? ''));

if ($media_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Data tidak valid']);
    exit;
}

try {
    $checkSql = "SELECT id FROM reviews WHERE user_id = $user_id AND media_id = $media_id AND media_type = '$media_type'";
    $checkRes = $conn->query($checkSql);
    
    if ($checkRes && $checkRes->num_rows > 0) {
        $row = $checkRes->fetch_assoc();
        $review_id = $row['id'];
        $updateSql = "UPDATE reviews SET rating = $rating, review_text = '$review_text', media_title = '$title', media_poster = '$poster', created_at = CURRENT_TIMESTAMP WHERE id = $review_id";
        if ($conn->query($updateSql)) {
            echo json_encode(['success' => true, 'action' => 'updated', 'review_id' => $review_id, 'user_name' => $_SESSION['user'], 'user_initial' => strtoupper(substr($_SESSION['user'], 0, 1))]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } else {
        $insertSql = "INSERT INTO reviews (user_id, media_id, media_type, media_title, media_poster, rating, review_text) VALUES ($user_id, $media_id, '$media_type', '$title', '$poster', $rating, '$review_text')";
        if ($conn->query($insertSql)) {
            echo json_encode(['success' => true, 'action' => 'inserted', 'review_id' => $conn->insert_id, 'user_name' => $_SESSION['user'], 'user_initial' => strtoupper(substr($_SESSION['user'], 0, 1))]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>