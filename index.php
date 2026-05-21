<?php
// Router sederhana
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

require_once __DIR__ . '/includes/header.php';

if ($page === 'home') {
    require_once __DIR__ . '/pages/home.php';
} elseif ($page === 'search') {
    require_once __DIR__ . '/pages/search.php';
} elseif ($page === 'movies') {
    require_once __DIR__ . '/pages/movies.php';
} elseif ($page === 'details') {
    require_once __DIR__ . '/pages/details.php';
} else {
    echo "<div style='padding: 150px 20px; text-align: center; height: 60vh;'><h2>404 - Halaman Tidak Ditemukan</h2></div>";
}

require_once __DIR__ . '/includes/footer.php';