<?php
session_start();
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Mapping rute ke struktur modular baru
$routes = [
    'home'           => 'modules/Catalog/home.php',
    'search'         => 'modules/Catalog/search.php',
    'movies'         => 'modules/Catalog/movies.php',
    'tvshows'        => 'modules/Catalog/tvshows.php',
    'details'        => 'modules/Catalog/details.php',
    'login'          => 'modules/Auth/login.php',
    'signup'         => 'modules/Auth/signup.php',
    'profile'        => 'modules/Auth/profile.php',
    'watchlist'      => 'modules/User/watchlist.php',
    'my_reviews'     => 'modules/User/my_reviews.php',
    'ajax_watchlist' => 'modules/User/ajax_watchlist.php',
    'ajax_review'    => 'modules/User/ajax_review.php',
    'ajax_search'    => 'modules/Api/ajax_search.php'
];

if (in_array($page, ['ajax_watchlist', 'ajax_review', 'ajax_search'])) {
    if (isset($routes[$page])) require_once __DIR__ . '/' . $routes[$page];
    exit;
} elseif ($page === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/data.php';
require_once __DIR__ . '/includes/header.php';

echo '<div id="topProgressBar" class="top-progress-bar"></div>';

if (isset($routes[$page])) {
    require_once __DIR__ . '/' . $routes[$page];
} else {
    echo "<div style='padding: 150px 20px; text-align: center; height: 60vh;'><h2>404 - Halaman Tidak Ditemukan</h2></div>";
}

require_once __DIR__ . '/includes/footer.php';