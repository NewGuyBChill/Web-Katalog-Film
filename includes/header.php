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
                <input type="text" name="q" class="search-input" id="searchInput" placeholder="Search movies...">
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
            
            <div style="display: flex; gap: 1rem; align-items: center; margin-left: 1rem;">
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="index.php?page=watchlist" style="color: var(--text-main); text-decoration: none; font-size: 0.9rem; margin-right: 10px; display: flex; align-items: center; gap: 5px;"><i class="fas fa-bookmark" style="color: var(--accent);"></i> My Watchlist</a>
                    <div class="profile-icon" title="<?= htmlspecialchars($_SESSION['user']) ?>"><i class="fas fa-user-circle" style="color: var(--accent); font-size: 1.5rem;"></i></div>
                    <a href="index.php?page=logout" class="btn-login" style="color: #ff3b3b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn-login">Login</a>
                    <a href="index.php?page=signup" class="btn-signup">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>