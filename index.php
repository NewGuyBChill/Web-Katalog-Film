<?php
session_start();
// Router sederhana
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

require_once __DIR__ . '/includes/header.php';

if ($page === 'home') {
    require_once __DIR__ . '/pages/home.php';
} elseif ($page === 'search') {
    require_once __DIR__ . '/pages/search.php';
} elseif ($page === 'movies') {
    require_once __DIR__ . '/pages/movies.php';
} elseif ($page === 'tvshows') {
    require_once __DIR__ . '/pages/tvshows.php';
} elseif ($page === 'details') {
    require_once __DIR__ . '/pages/details.php';
} elseif ($page === 'login') {
    require_once __DIR__ . '/pages/login.php';
} elseif ($page === 'signup') {
    require_once __DIR__ . '/pages/signup.php';
} elseif ($page === 'ajax_watchlist') {
    require_once __DIR__ . '/pages/ajax_watchlist.php';
} elseif ($page === 'watchlist') {
    require_once __DIR__ . '/pages/watchlist.php';
} elseif ($page === 'ajax_review') {
    require_once __DIR__ . '/pages/ajax_review.php';
} elseif ($page === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
} else {
    echo "<div style='padding: 150px 20px; text-align: center; height: 60vh;'><h2>404 - Halaman Tidak Ditemukan</h2></div>";
}

require_once __DIR__ . '/includes/footer.php';