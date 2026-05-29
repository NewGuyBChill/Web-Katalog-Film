<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$page = isset($_GET['page']) && is_string($_GET['page']) ? $_GET['page'] : 'home';

// Mapping rute ke struktur modular baru
$routes = [
    'home'           => 'modules/catalog/home.php',
    'search'         => 'modules/catalog/search.php',
    'movies'         => 'modules/catalog/movies.php',
    'tvshows'        => 'modules/catalog/tvshows.php',
    'details'        => 'modules/catalog/details.php',
    'person'         => 'modules/catalog/person.php',
    'login'          => 'modules/auth/login.php',
    'signup'         => 'modules/auth/signup.php',
    'forgot_password'=> 'modules/auth/forgot_password.php',
    'profile'        => 'modules/auth/profile.php',
    'user_profile'   => 'modules/user/user_profile.php',
    'watchlist'      => 'modules/user/watchlist.php',
    'my_reviews'     => 'modules/user/my_reviews.php',
    'notifications'  => 'modules/user/notifications.php',
    'admin'          => 'modules/admin/dashboard.php',
    'ajax_admin'     => 'modules/admin/ajax_admin.php',
    'my_lists'       => 'modules/playlists/my_lists.php',
    'view_list'      => 'modules/playlists/view_list.php',
    'ajax_playlist'  => 'modules/playlists/ajax_playlist.php',
    'ajax_watchlist' => 'modules/user/ajax_watchlist.php',
    'ajax_review'    => 'modules/user/ajax_review.php',
    'ajax_search'    => 'modules/api/ajax_search.php',
    'ajax_search_user' => 'modules/user/ajax_search_user.php',
    'ajax_notifications' => 'modules/api/ajax_notifications.php',
    'ajax_like_review' => 'modules/api/ajax_like_review.php',
    'ajax_review_reply'=> 'modules/api/ajax_review_reply.php',
    'ajax_favorite_cast' => 'modules/user/ajax_favorite_cast.php',
    'ajax_follow_user' => 'modules/user/ajax_follow_user.php',
    'ajax_activity_feed' => 'modules/user/ajax_activity_feed.php'
];

// Fungsi pencarian file (Toleransi Case-Sensitive untuk Hosting Linux)
function getModulePath($routePath) {
    $basePath = __DIR__ . '/' . $routePath;
    if (file_exists($basePath)) return $basePath;
    
    // Coba versi huruf kapital jika folder di hosting masih menggunakan huruf kapital
    $capitalized = str_replace(['modules/catalog/', 'modules/auth/', 'modules/user/', 'modules/api/'], ['modules/Catalog/', 'modules/Auth/', 'modules/User/', 'modules/Api/'], $routePath);
    $capPath = __DIR__ . '/' . $capitalized;
    if (file_exists($capPath)) return $capPath;
    
    return false;
}

if (in_array($page, ['ajax_watchlist', 'ajax_review', 'ajax_search', 'ajax_notifications', 'ajax_like_review', 'ajax_favorite_cast', 'ajax_review_reply', 'ajax_admin', 'ajax_playlist', 'ajax_follow_user', 'ajax_search_user', 'ajax_activity_feed'])) {
    if (isset($routes[$page]) && $path = getModulePath($routes[$page])) {
        require_once $path;
        exit;
    }
} elseif ($page === 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/config/data.php';
require_once __DIR__ . '/includes/header.php';

echo '<div id="topProgressBar" class="top-progress-bar"></div>';

if (isset($routes[$page]) && $path = getModulePath($routes[$page])) {
    require_once $path;
} else {
    echo "<div style='padding: 150px 20px; text-align: center; height: 60vh;'><h2>404 - Halaman Tidak Ditemukan</h2><p style='color: #888; margin-top: 10px;'>Pastikan file fisik dari halaman yang dicari tersedia (Perhatikan huruf besar/kecil folder Anda di server).</p></div>";
}

require_once __DIR__ . '/includes/footer.php';