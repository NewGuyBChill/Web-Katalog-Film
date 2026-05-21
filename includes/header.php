<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$userWatchlist = [];
if(isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../config/db.php';
    $uid = (int)$_SESSION['user_id'];
    try {
        $res = $conn->query("SELECT media_id FROM watchlist WHERE user_id = $uid");
        if ($res) {
            while($row = $res->fetch_assoc()) {
                $userWatchlist[] = $row['media_id'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        // Abaikan error (tabel belum ada), biarkan $userWatchlist kosong
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinema - Katalog Film</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
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
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-left">
            <div class="logo">KINEMA</div>
            <ul class="nav-links">
                <li><a href="index.php?page=home" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="index.php?page=movies" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'movies') ? 'active' : ''; ?>">Movie</a></li>
                <li><a href="index.php?page=tvshows" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'tvshows') ? 'active' : ''; ?>">TV Show</a></li>
            </ul>
        </div>
        <div class="nav-right" style="gap: 1.5rem;">
            <form action="index.php" method="GET" class="search-container" id="searchContainer">
                <input type="hidden" name="page" value="search">
                <button type="submit" class="search-trigger" id="searchTrigger">
                    <i class="fas fa-search"></i>
                </button>
                <input type="text" name="q" class="search-input" id="searchInput" placeholder="Search movies..." autocomplete="off">
                <i class="fas fa-times clear-search" id="clearSearch" title="Clear search"></i>
                
                <div class="live-search-results" id="liveSearchResults"></div>
            </form>

            <div class="lang-container" id="langContainer">
                <div class="lang-trigger" id="langTrigger">
                    <i class="fas fa-globe"></i>
                    <span class="lang-text" id="currentLang">ID</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.6rem; margin-left: 3px;"></i>
                </div>
                
                <div class="lang-dropdown" id="langDropdown">
                    <div class="lang-option active" data-lang="ID">Indonesia</div>
                    <div class="lang-option" data-lang="EN">English</div>
                    <div class="lang-option" data-lang="KR">한국어</div>
                    <div class="lang-option" data-lang="JP">日本語</div>
                </div>
            </div>
            
            <div class="theme-switch" id="themeSwitch" title="Toggle Light/Dark Mode">
                <i class="fas fa-sun" id="themeIcon"></i>
            </div>
            
            <div style="display: flex; gap: 1rem; align-items: center; margin-left: 1rem;">
                <?php if(isset($_SESSION['user'])): 
                    $uname = $_SESSION['user'];
                    $initial = strtoupper(substr($uname, 0, 1));
                    $avatarColors = [
                        'linear-gradient(135deg, #f5576c 0%, #f093fb 100%)', // Pink-Purple
                        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', // Blue-Cyan
                        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', // Green-Mint
                        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)', // Orange-Yellow
                        'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)', // Soft Purple
                        'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'  // Red-Orange
                    ];
                    $colorIndex = ord($initial) % count($avatarColors);
                    $activeAvatarBg = $avatarColors[$colorIndex];
                ?>
                    <div class="user-menu-container">
                        <div class="user-profile-btn" title="<?= htmlspecialchars($uname) ?>">
                            <div class="profile-avatar" style="background: <?= $activeAvatarBg ?>;"><?= htmlspecialchars($initial) ?></div>
                            <span class="profile-name"><?= htmlspecialchars($uname) ?> <i class="fas fa-chevron-down caret-icon"></i></span>
                        </div>
                        <div class="user-dropdown">
                            <a href="index.php?page=watchlist"><i class="fas fa-bookmark" style="width: 20px;"></i> <?= translateText('watchlist') ?></a>
                            <a href="index.php?page=my_reviews"><i class="fas fa-star" style="width: 20px;"></i> <?= translateText('my_reviews') ?></a>
                            <a href="index.php?page=profile"><i class="fas fa-cog" style="width: 20px;"></i> <?= translateText('profile_settings') ?></a>
                            <div style="height: 1px; background: rgba(255,255,255,0.1); margin: 5px 0;"></div>
                            <a href="index.php?page=logout" class="logout-btn"><i class="fas fa-sign-out-alt" style="width: 20px;"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                <a href="index.php?page=login" class="btn-login"><i class="fas fa-user"></i> Login</a>
                    <div class="nav-divider"></div>
                <a href="index.php?page=signup" class="btn-signup">Sign Up <i class="fas fa-arrow-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>