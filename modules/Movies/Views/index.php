<?php 
require_once base_path('config/data.php'); 

// Ambil data query dari URL bar (bila ada)
$filters = [
    'genre' => $_GET['genre'] ?? '',
    'year' => $_GET['year'] ?? '',
    'rating' => $_GET['rating'] ?? '',
    'sort' => $_GET['sort'] ?? '',
    'lang' => $_GET['lang'] ?? ''
];
$currentPage = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

// Panggil fungsi discover dengan meneruskan array filters
if (($filters['sort'] ?? '') === 'upcoming') {
    // Fetch upcoming
    $res = fetchTMDB("movie/upcoming?page=$currentPage");
    $moviesList = formatMovies($res['results'] ?? [], 20);
} elseif (($filters['sort'] ?? '') === 'now_playing') {
    // Fetch now playing
    $res = fetchTMDB("movie/now_playing?page=$currentPage");
    $moviesList = formatMovies($res['results'] ?? [], 20);
} elseif (($filters['sort'] ?? '') === 'trending') {
    // Fetch trending
    $res = fetchTMDB("trending/movie/week?page=$currentPage");
    $moviesList = formatMovies($res['results'] ?? [], 20);
} else {
    $moviesList = discoverMovies($filters, 18, $currentPage); 
}

// Ambil list variabel global genre untuk ditampilkan di dropdown
global $genreMap;
global $langMap;

$sortMap = [
    'popularity.desc' => translateText('sort_pop_desc'),
    'vote_average.desc' => translateText('sort_rating_desc'),
    'primary_release_date.desc' => translateText('sort_date_desc'),
    'primary_release_date.asc' => translateText('sort_date_asc'),
    'upcoming' => translateText('upcoming_movies'),
    'now_playing' => translateText('now_playing'),
    'trending' => translateText('trending_1')
];
$currentSortLabel = isset($sortMap[$filters['sort']]) ? $sortMap[$filters['sort']] : translateText('sort_pop_desc');
?>

<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header" id="explore">
        <div class="header-titles">
            <h2><?= translateText('explore_movies') ?></h2>
            <p><?= translateText('explore_desc') ?></p>
        </div>
        <div class="filters-section">
            
            <!-- Custom Dropdown untuk Genre -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['genre']) ? 'active-filter' : '' ?>">
                    <span><?= translateText('genre') ?>: <?= isset($genreMap[$filters['genre']]) ? $genreMap[$filters['genre']] : translateText('all') ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= buildFilterUrl($filters, 'genre', '') ?>" class="<?= empty($filters['genre']) ? 'active' : '' ?>"><?= translateText('all_genre') ?></a>
                    <?php foreach($genreMap as $id => $name): ?>
                        <a href="<?= buildFilterUrl($filters, 'genre', $id) ?>" class="<?= $filters['genre'] == $id ? 'active' : '' ?>"><?= $name ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Custom Dropdown untuk Tahun -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['year']) ? 'active-filter' : '' ?>">
                    <span><?= translateText('year') ?>: <?= !empty($filters['year']) ? $filters['year'] : translateText('all') ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= buildFilterUrl($filters, 'year', '') ?>" class="<?= empty($filters['year']) ? 'active' : '' ?>"><?= translateText('all_year') ?></a>
                    <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                        <a href="<?= buildFilterUrl($filters, 'year', $y) ?>" class="<?= $filters['year'] == $y ? 'active' : '' ?>"><?= $y ?></a>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Custom Dropdown untuk Rating -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['rating']) ? 'active-filter' : '' ?>">
                    <span><?= translateText('rating') ?>: <?= !empty($filters['rating']) ? '⭐ '.$filters['rating'].'.0+' : translateText('all') ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= buildFilterUrl($filters, 'rating', '') ?>" class="<?= empty($filters['rating']) ? 'active' : '' ?>"><?= translateText('all_rating') ?></a>
                    <?php for($r = 8; $r >= 5; $r--): ?>
                        <a href="<?= buildFilterUrl($filters, 'rating', $r) ?>" class="<?= $filters['rating'] == $r ? 'active' : '' ?>">⭐ <?= $r ?>.0+</a>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Custom Dropdown untuk Bahasa -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['lang']) ? 'active-filter' : '' ?>">
                    <span><?= translateText('language') ?>: <?= isset($langMap[$filters['lang']]) ? $langMap[$filters['lang']] : translateText('all') ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= buildFilterUrl($filters, 'lang', '') ?>#explore" class="<?= empty($filters['lang']) ? 'active' : '' ?>"><?= translateText('all_language') ?></a>
                    <?php foreach($langMap as $code => $name): ?>
                        <a href="<?= buildFilterUrl($filters, 'lang', $code) ?>#explore" class="<?= $filters['lang'] == $code ? 'active' : '' ?>"><?= $name ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Custom Dropdown untuk Urutkan (Sorting) -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['sort']) && $filters['sort'] !== 'popularity.desc' ? 'active-filter' : '' ?>">
                    <span><?= translateText('sort_by') ?>: <?= $currentSortLabel ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <?php foreach($sortMap as $val => $label): ?>
                        <a href="<?= buildFilterUrl($filters, 'sort', $val === 'popularity.desc' ? '' : $val) ?>#explore" class="<?= ($filters['sort'] == $val || (empty($filters['sort']) && $val == 'popularity.desc')) ? 'active' : '' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tombol reset filter muncul jika user sedang mem-filter sesuatu -->
            <?php if(!empty($filters['genre']) || !empty($filters['year']) || !empty($filters['rating']) || !empty($filters['sort']) || !empty($filters['lang'])): ?>
                <a href="index.php?page=movies" class="reset-btn">
                    <i class="fas fa-times"></i> <?= translateText('reset_filter') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="movies-grid">
        <?php if(!empty($moviesList) && is_array($moviesList)): ?>
            <?php foreach($moviesList as $movie): ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars((string)$movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                    <div class="watchlist-btn" data-id="<?= $movie['id'] ?>" data-title="<?= htmlspecialchars((string)$movie['title']) ?>" onclick="toggleWatchlist(event, this)">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="grid-movie-quick-view">
                        <div class="quick-view-title"><?= translateText('synopsis') ?></div>
                        <div class="quick-view-synopsis"><?= htmlspecialchars((string)$movie['overview']) ?: translateText('no_synopsis') ?></div>
                    </div>
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars((string)$movie['title']) ?></div>
                    <div class="grid-movie-meta"><?= htmlspecialchars((string)$movie['year']) ?> &bull; <?= htmlspecialchars((string)$movie['genre']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination">
                    <!-- Prev Button -->
                    <?php if($currentPage > 1): ?>
                        <a href="<?= buildFilterUrl($filters, 'p', $currentPage - 1) ?>#explore" class="page-btn" title="<?= translateText('prev') ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php else: ?>
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <?php endif; ?>

                    <!-- Page 1 & Dots -->
                    <?php if($currentPage > 2): ?>
                        <a href="<?= buildFilterUrl($filters, 'p', 1) ?>#explore" class="page-btn">1</a>
                        <?php if($currentPage > 3): ?><span class="page-dots">...</span><?php endif; ?>
                    <?php endif; ?>

                    <!-- Prev Page -->
                    <?php if($currentPage > 1): ?>
                        <a href="<?= buildFilterUrl($filters, 'p', $currentPage - 1) ?>#explore" class="page-btn"><?= $currentPage - 1 ?></a>
                    <?php endif; ?>

                    <!-- Current Page -->
                    <span class="page-btn active"><?= $currentPage ?></span>

                    <!-- Next Pages -->
                    <a href="<?= buildFilterUrl($filters, 'p', $currentPage + 1) ?>#explore" class="page-btn"><?= $currentPage + 1 ?></a>
                    <?php if($currentPage == 1): ?><a href="<?= buildFilterUrl($filters, 'p', 3) ?>#explore" class="page-btn">3</a><?php endif; ?>

                    <!-- Next Button -->
                    <a href="<?= buildFilterUrl($filters, 'p', $currentPage + 1) ?>#explore" class="page-btn" title="<?= translateText('next') ?>"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;"><?= translateText('no_movies') ?></h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;"><?= translateText('no_movies_desc') ?></p>
                <a href="index.php?page=movies" class="reset-btn" style="margin: 0;">
                    <i class="fas fa-sync-alt"></i> <?= translateText('clear_filter') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>