<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Validasi Otentikasi Admin Lapis Kedua
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Akses Ditolak. Anda bukan admin.']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'delete_review') {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM reviews WHERE id = $id"); // Tidak ada filter "user_id" karena ini eksekusi hak istimewa (Admin)
    echo json_encode(['success' => true]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Aksi tidak dikenali.']);