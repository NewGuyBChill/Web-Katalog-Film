<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Silakan login terlebih dahulu.']);
    exit;
}

$uid = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'quick_toggle') {
    $media_id = (int)($_POST['media_id'] ?? 0);
    $media_type = $conn->real_escape_string($_POST['media_type'] ?? 'movie');
    $media_title = $conn->real_escape_string($_POST['media_title'] ?? '');
    $media_poster = $conn->real_escape_string($_POST['media_poster'] ?? '');

    // Get or create default playlist
    $plRes = $conn->query("SELECT id FROM custom_playlists WHERE user_id = $uid ORDER BY id ASC LIMIT 1");
    if ($plRes && $plRes->num_rows > 0) {
        $playlist_id = $plRes->fetch_assoc()['id'];
    } else {
        $conn->query("INSERT INTO custom_playlists (user_id, name) VALUES ($uid, 'My Playlist')");
        $playlist_id = $conn->insert_id;
    }

    $checkItem = $conn->query("SELECT id FROM playlist_items WHERE playlist_id = $playlist_id AND media_id = $media_id AND media_type = '$media_type'");
    if ($checkItem && $checkItem->num_rows > 0) {
        $item_id = $checkItem->fetch_assoc()['id'];
        $conn->query("DELETE FROM playlist_items WHERE id = $item_id");
        echo json_encode(['success' => true, 'status' => 'removed']);
    } else {
        $conn->query("INSERT INTO playlist_items (playlist_id, media_id, media_type, media_title, media_poster) VALUES ($playlist_id, $media_id, '$media_type', '$media_title', '$media_poster')");
        echo json_encode(['success' => true, 'status' => 'added']);
    }
    exit;
}

if ($action === 'create_playlist') {
    $name = $conn->real_escape_string(trim($_POST['name'] ?? 'New Playlist'));
    if (empty($name)) $name = 'New Playlist';
    $conn->query("INSERT INTO custom_playlists (user_id, name) VALUES ($uid, '$name')");
    echo json_encode(['success' => true, 'id' => $conn->insert_id, 'name' => $name]);
    exit;
}

if ($action === 'add') {
    $playlist_id = (int)($_POST['playlist_id'] ?? 0);
    $media_id = (int)($_POST['media_id'] ?? 0);
    $media_type = $conn->real_escape_string($_POST['media_type'] ?? 'movie');
    $media_title = $conn->real_escape_string($_POST['media_title'] ?? '');
    $media_poster = $conn->real_escape_string($_POST['media_poster'] ?? '');

    // Pastikan user ini adalah pemilik playlist
    $checkOwner = $conn->query("SELECT id FROM custom_playlists WHERE id = $playlist_id AND user_id = $uid");
    if ($checkOwner && $checkOwner->num_rows > 0) {
        $checkItem = $conn->query("SELECT id FROM playlist_items WHERE playlist_id = $playlist_id AND media_id = $media_id AND media_type = '$media_type'");
        if ($checkItem && $checkItem->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Media ini sudah ada di dalam daftar Anda.']);
        } else {
            $conn->query("INSERT INTO playlist_items (playlist_id, media_id, media_type, media_title, media_poster) VALUES ($playlist_id, $media_id, '$media_type', '$media_title', '$media_poster')");
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Daftar (Playlist) tidak ditemukan atau Anda tidak memiliki akses.']);
    }
    exit;
} elseif ($action === 'remove') {
    $item_id = (int)($_POST['item_id'] ?? 0);
    $checkOwner = $conn->query("SELECT pi.id FROM playlist_items pi JOIN custom_playlists cp ON pi.playlist_id = cp.id WHERE pi.id = $item_id AND cp.user_id = $uid");
    if ($checkOwner && $checkOwner->num_rows > 0) {
        $conn->query("DELETE FROM playlist_items WHERE id = $item_id");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Akses ditolak.']);
    }
    exit;
} elseif ($action === 'delete_playlist') {
    $playlist_id = (int)($_POST['playlist_id'] ?? 0);
    $checkOwner = $conn->query("SELECT id FROM custom_playlists WHERE id = $playlist_id AND user_id = $uid");
    if ($checkOwner && $checkOwner->num_rows > 0) {
        $conn->query("DELETE FROM custom_playlists WHERE id = $playlist_id");
        echo json_encode(['success' => true]);
    } else echo json_encode(['success' => false, 'error' => 'Akses ditolak.']);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Aksi tidak valid.']);