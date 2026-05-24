<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$userWatchlist = [];
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['user_watchlist'])) {
        require_once __DIR__ . '/../config/db.php';
        $uid = (int)$_SESSION['user_id'];
        $tempWatchlist = [];
        try {
            $res = $conn->query("SELECT media_id FROM watchlist WHERE user_id = $uid");
            if ($res) {
                while($row = $res->fetch_assoc()) {
                    $tempWatchlist[] = $row['media_id'];
                }
            }
            $_SESSION['user_watchlist'] = $tempWatchlist;
        } catch (mysqli_sql_exception $e) {}
    }
    $userWatchlist = $_SESSION['user_watchlist'] ?? [];
}

// Genre map for navbar dropdown
$navGenres = [
    28 => "Action", 878 => "Sci-Fi", 18 => "Drama",
    27 => "Horror", 10749 => "Romance", 35 => "Comedy",
    53 => "Thriller", 14 => "Fantasy", 16 => "Animation",
    80 => "Crime", 99 => "Documentary", 9648 => "Mystery"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CelesView - Katalog Film</title>
    <meta name="description" content="Kinema — Discover, rate, and discuss movies and TV shows. Your personal film catalog.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@200;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Modular CSS (Dimuat secara paralel) -->
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/components/navbar.css">
    <link rel="stylesheet" href="assets/css/components/hero.css">
    <link rel="stylesheet" href="assets/css/components/cards.css">
    <link rel="stylesheet" href="assets/css/components/auth.css">
    <link rel="stylesheet" href="assets/css/themes/light-mode.css">
    
    <!-- CSS Utama diletakkan di paling bawah -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">

    <!-- Font Awesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        const userWatchlist = <?= json_encode($userWatchlist) ?>;
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;

        // Terapkan tema sebelum rendering body untuk mencegah efek FOUC (Berkedip)
        if (localStorage.getItem('kinema_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
</head>
<body>
    <!-- ========== NAVBAR ========== -->
    <nav class="navbar" id="mainNavbar">
        <div class="nav-container">
            <!-- Left: Logo + Main Links -->
            <div class="nav-left">
                <a href="index.php?page=home" class="nav-logo" id="navLogo">
                    <span class="logo-text"><span class="logo-bold">Celes</span><span class="logo-thin">View</span></span>
                </a>

                <ul class="nav-menu" id="navMenu">
                    <!-- Movies Dropdown -->
                    <li class="nav-item has-dropdown" id="navMovies">
                        <a href="index.php?page=movies" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'movies') ? 'active' : ''; ?>">
                            <i class="fas fa-clapperboard nav-link-icon"></i>
                            Movies
                            <i class="fas fa-chevron-down nav-caret"></i>
                        </a>
                        <div class="nav-dropdown" id="dropdownMovies">
                            <div class="nav-dropdown-inner">
                                <a href="index.php?page=movies&sort=popularity.desc" class="nav-dropdown-item">
                                    <i class="fas fa-fire"></i>
                                    <div>
                                        <span class="dropdown-item-title">Popular</span>
                                        <span class="dropdown-item-desc">Most watched by everyone</span>
                                    </div>
                                </a>
                                <a href="index.php?page=movies&sort=release_date.desc" class="nav-dropdown-item">
                                    <i class="fas fa-sparkles"></i>
                                    <div>
                                        <span class="dropdown-item-title">Latest Releases</span>
                                        <span class="dropdown-item-desc">Freshly out of theaters</span>
                                    </div>
                                </a>
                                <a href="index.php?page=movies&category=upcoming" class="nav-dropdown-item">
                                    <i class="fas fa-calendar-star"></i>
                                    <div>
                                        <span class="dropdown-item-title">Upcoming</span>
                                        <span class="dropdown-item-desc">Coming soon to cinemas</span>
                                    </div>
                                </a>
                                <a href="index.php?page=movies&category=now_playing" class="nav-dropdown-item">
                                    <i class="fas fa-ticket"></i>
                                    <div>
                                        <span class="dropdown-item-title">Now Playing</span>
                                        <span class="dropdown-item-desc">Currently in theaters</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- Genres Dropdown -->
                    <li class="nav-item has-dropdown" id="navGenres">
                        <a href="#" class="nav-link" onclick="event.preventDefault();">
                            <i class="fas fa-masks-theater nav-link-icon"></i>
                            Genres
                            <i class="fas fa-chevron-down nav-caret"></i>
                        </a>
                        <div class="nav-dropdown nav-dropdown-genres" id="dropdownGenres">
                            <div class="nav-dropdown-inner nav-dropdown-grid">
                                <?php foreach ($navGenres as $gid => $gname): ?>
                                <a href="index.php?page=movies&genre=<?= $gid ?>" class="nav-dropdown-genre-item">
                                    <?= htmlspecialchars($gname) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </li>

                    <!-- Top Rated -->
                    <li class="nav-item">
                        <a href="index.php?page=movies&sort=vote_average.desc&rating=7" class="nav-link <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'vote_average.desc') ? 'active' : ''; ?>">
                            <i class="fas fa-trophy nav-link-icon"></i>
                            Top Rated
                        </a>
                    </li>

                    <!-- Trending -->
                    <li class="nav-item">
                        <a href="index.php?page=home#trending" class="nav-link">
                            <i class="fas fa-arrow-trend-up nav-link-icon"></i>
                            Trending
                        </a>
                    </li>



                    <!-- Community -->
                    <li class="nav-item">
                        <a href="index.php?page=my_reviews" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'my_reviews') ? 'active' : ''; ?>">
                            <i class="fas fa-users nav-link-icon"></i>
                            Community
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Right: Search + Actions -->
            <div class="nav-right">
                <!-- Search Bar -->
                <form action="index.php" method="GET" class="nav-search" id="navSearch">
                    <input type="hidden" name="page" value="search">
                    <button type="submit" class="nav-search-btn" id="searchTrigger">
                        <i class="fas fa-search"></i>
                    </button>
                    <input type="text" name="q" class="nav-search-input" id="searchInput" placeholder="Search movies, actors, genres..." autocomplete="off">
                    <i class="fas fa-times nav-search-clear" id="clearSearch" title="Clear search"></i>

                    <div class="live-search-results" id="liveSearchResults"></div>
                </form>

                <!-- Language Selector -->
                <div class="nav-lang" id="langContainer">
                    <div class="nav-lang-trigger" id="langTrigger">
                        <i class="fas fa-globe"></i>
                        <span id="currentLang">EN</span>
                        <i class="fas fa-chevron-down nav-caret-sm"></i>
                    </div>
                    <div class="nav-lang-dropdown" id="langDropdown">
                        <div class="lang-option active" data-lang="EN" data-value="en-US">English</div>
                        <div class="lang-option" data-lang="ID" data-value="id-ID">Indonesia</div>
                    </div>
                </div>

                <!-- Theme Switch -->
                <div class="nav-action-btn" id="themeSwitch" title="Toggle Light/Dark Mode">
                    <i class="fas fa-sun" id="themeIcon"></i>
                </div>

                <!-- Notification Bell -->
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="nav-action-btn nav-notification" id="notifBell" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge" id="notifBadge">3</span>
                </div>
                <div class="nav-notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <span>Notifications</span>
                        <a href="#" class="notif-mark-read">Mark all read</a>
                    </div>
                    <div class="notif-list">
                        <div class="notif-item unread">
                            <div class="notif-icon"><i class="fas fa-heart"></i></div>
                            <div class="notif-content">
                                <p><strong>Someone</strong> liked your review on <em>Inception</em></p>
                                <span class="notif-time">2 hours ago</span>
                            </div>
                        </div>
                        <div class="notif-item unread">
                            <div class="notif-icon"><i class="fas fa-user-plus"></i></div>
                            <div class="notif-content">
                                <p><strong>CineFan99</strong> started following you</p>
                                <span class="notif-time">5 hours ago</span>
                            </div>
                        </div>
                        <div class="notif-item">
                            <div class="notif-icon"><i class="fas fa-star"></i></div>
                            <div class="notif-content">
                                <p>New recommendation: <em>Dune Part Three</em></p>
                                <span class="notif-time">1 day ago</span>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="notif-see-all">See all notifications</a>
                </div>
                <?php endif; ?>

                <!-- Divider -->
                <div class="nav-divider"></div>

                <!-- Profile / Auth -->
                <?php if(isset($_SESSION['user'])):
                    $uname = $_SESSION['user'];
                    $initial = strtoupper(substr($uname, 0, 1));
                    $avatarColors = [
                        'linear-gradient(135deg, #f5576c 0%, #f093fb 100%)',
                        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                        'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                        'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'
                    ];
                    $colorIndex = ord($initial) % count($avatarColors);
                    $activeAvatarBg = $avatarColors[$colorIndex];
                ?>
                <div class="nav-profile" id="navProfile">
                    <div class="nav-profile-trigger" title="<?= htmlspecialchars($uname) ?>">
                        <div class="profile-avatar" style="background: <?= $activeAvatarBg ?>;"><?= htmlspecialchars($initial) ?></div>
                        <span class="profile-name-text"><?= htmlspecialchars($uname) ?></span>
                        <i class="fas fa-chevron-down nav-caret-sm profile-caret"></i>
                    </div>
                    <div class="nav-profile-dropdown" id="profileDropdown">
                        <div class="profile-dropdown-header">
                            <div class="profile-avatar-lg" style="background: <?= $activeAvatarBg ?>;"><?= htmlspecialchars($initial) ?></div>
                            <div>
                                <div class="profile-dropdown-name"><?= htmlspecialchars($uname) ?></div>
                                <div class="profile-dropdown-email">Member</div>
                            </div>
                        </div>
                        <div class="profile-dropdown-divider"></div>
                        <a href="index.php?page=profile" class="profile-dropdown-item">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="index.php?page=my_reviews" class="profile-dropdown-item">
                            <i class="fas fa-star"></i> My Reviews
                        </a>

                        <a href="index.php?page=profile" class="profile-dropdown-item">
                            <i class="fas fa-gear"></i> Settings
                        </a>
                        <div class="profile-dropdown-divider"></div>
                        <a href="index.php?page=logout" class="profile-dropdown-item profile-dropdown-logout">
                            <i class="fas fa-right-from-bracket"></i> Logout
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <a href="index.php?page=login" class="btn-login"><i class="fas fa-user"></i> Login</a>
                <a href="index.php?page=signup" class="btn-signup">Sign Up <i class="fas fa-arrow-right"></i></a>
                <?php endif; ?>

                <!-- Mobile Hamburger -->
                <button class="nav-hamburger" id="navHamburger" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Overlay -->
    <div class="nav-mobile-overlay" id="navMobileOverlay"></div>