<?php
require_once __DIR__ . '/../../config/data.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    echo json_encode([]);
    exit;
}

// Ambil max 5 hasil pencarian saja untuk dropdown (agar tidak terlalu panjang)
$results = array_slice(searchMovies($q), 0, 5);
echo json_encode($results);