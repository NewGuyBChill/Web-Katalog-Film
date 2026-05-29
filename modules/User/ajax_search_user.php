<?php
error_reporting(0);
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    echo json_encode([]);
    exit;
}

$safe_search = $conn->real_escape_string($q);
$results = [];
// Ambil maksimal 5 pengguna untuk pratinjau dropdown (live search)
$res_search = $conn->query("SELECT id, name, avatar, created_at FROM users WHERE name LIKE '%$safe_search%' LIMIT 5");
if ($res_search) {
    while($row = $res_search->fetch_assoc()) {
        $results[] = [
            'id' => $row['id'],
            'name' => htmlspecialchars($row['name']),
            'avatar' => $row['avatar'] ? htmlspecialchars($row['avatar']) : null,
            'initial' => strtoupper(substr($row['name'], 0, 1)),
            'member_since' => date('M Y', strtotime($row['created_at']))
        ];
    }
}
echo json_encode($results);