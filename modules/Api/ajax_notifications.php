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

// Menangani aksi POST untuk menandai semua notifikasi telah dibaca
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'mark_all_read') {
    try {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Menangani aksi POST untuk menghapus semua notifikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_all') {
    try {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Menangani aksi POST untuk menghapus satu notifikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_single' && isset($_POST['nid'])) {
    $nid = (int)$_POST['nid'];
    try {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $nid, $user_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Ambil data notifikasi dan jumlah unread
try {
    // Hitung notifikasi yang belum dibaca
    $stmtCount = $conn->prepare("SELECT COUNT(id) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmtCount->bind_param("i", $user_id);
    $stmtCount->execute();
    $resCount = $stmtCount->get_result();
    $unread_count = $resCount->fetch_assoc()['unread_count'] ?? 0;

    // Ambil maksimal 5 notifikasi terbaru beserta jenis (type) notifikasinya
    $stmtList = $conn->prepare("SELECT id, type, title, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmtList->bind_param("i", $user_id);
    $stmtList->execute();
    $resList = $stmtList->get_result();
    $notifications = [];
    while ($row = $resList->fetch_assoc()) {
        $notifications[] = $row;
    }

    echo json_encode(['success' => true, 'unread_count' => $unread_count, 'notifications' => $notifications]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}