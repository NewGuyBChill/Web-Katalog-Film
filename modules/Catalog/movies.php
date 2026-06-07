<?php 
require_once __DIR__ . '/../../config/data.php'; 

// Ambil data query dari URL bar (bila ada)
$filters = [
    'genre' => $_GET['genre'] ?? '',
    'year' => $_GET['year'] ?? '',
    'rating' => $_GET['rating'] ?? '',
    'sort' => $_GET['sort'] ?? '',
    'lang' => $_GET['lang'] ?? '',
    'category' => $_GET['category'] ?? ''
];
$currentPage = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

// Panggil fungsi discover dengan meneruskan array filters
$moviesList = discoverMovies($filters, 18, $currentPage); 

// Ambil list variabel global genre untuk ditampilkan di dropdown
global $genreMap;
global $langMap;

$sortMap = [
    'popularity.desc' => '<i class="fas fa-fire" style="color: #ff3b3b; margin-right: 6px;"></i> ' . translateText('sort_pop_desc'),
    'vote_average.desc' => '<i class="fas fa-star" style="color: #FCD34D; margin-right: 6px;"></i> ' . translateText('sort_rating_desc'),
    'primary_release_date.desc' => '<i class="fas fa-bolt" style="color: #00d2ff; margin-right: 6px;"></i> ' . translateText('sort_date_desc'),
    'primary_release_date.asc' => '<i class="fas fa-history" style="margin-right: 6px;"></i> ' . translateText('sort_date_asc')
];
$currentSortLabel = isset($sortMap[$filters['sort']]) ? $sortMap[$filters['sort']] : translateText('sort_pop_desc');

global $siteLang;
$pageTitle = translateText('explore_movies');
$pageDesc = translateText('explore_desc');

if (!empty($filters['category'])) {
    if ($filters['category'] === 'upcoming') {
        $pageTitle = translateText('upcoming_movies');
        $pageDesc = $siteLang === 'id-ID' ? 'Daftar film yang akan segera tayang di bioskop.' : 'List of movies coming soon to theaters.';
    } elseif ($filters['category'] === 'now_playing') {
        $pageTitle = $siteLang === 'id-ID' ? 'Sedang Tayang' : 'Now Playing';
        $pageDesc = $siteLang === 'id-ID' ? 'Film-film yang sedang tayang di bioskop saat ini.' : 'Movies currently playing in theaters.';
    }
} elseif ($filters['sort'] === 'primary_release_date.desc') {
    $pageTitle = $siteLang === 'id-ID' ? 'Rilis Terbaru' : 'Latest Releases';
    $pageDesc = $siteLang === 'id-ID' ? 'Kumpulan film rilisan paling baru yang fresh.' : 'Freshly released movies.';
}
?>

<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header" id="explore">
        <div class="header-titles">
            <h2><?= $pageTitle ?></h2>
            <p><?= $pageDesc ?></p>
        </div>
        <div class="filters-section" style="flex-direction: column; align-items: flex-start; width: 100%;">
            <!-- Genre Pills -->
            <div class="category-pills" style="display: flex; gap: 10px; overflow-x: auto; width: 100%; scrollbar-width: none; padding-bottom: 10px;">
                <a href="<?= buildFilterUrl($filters, 'genre', '') ?>" class="filter-btn <?= empty($filters['genre']) ? 'active' : '' ?>" style="padding: 0.4rem 1.2rem; white-space: nowrap;"><?= translateText('all_genre') ?></a>
                <?php foreach($genreMap as $id => $name): ?>
                    <a href="<?= buildFilterUrl($filters, 'genre', $id) ?>" class="filter-btn <?= $filters['genre'] == $id ? 'active' : '' ?>" style="padding: 0.4rem 1.2rem; white-space: nowrap;"><?= $name ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Sort By and Rating Slider -->
            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="display: flex; gap: 10px; align-items: center;">
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
                </div>

                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">Min Rating: <span id="ratingVal" style="font-weight: bold; color: var(--accent);"><?= !empty($filters['rating']) ? $filters['rating'] : 5 ?></span>+</span>
                    <input type="range" min="5" max="9" value="<?= !empty($filters['rating']) ? $filters['rating'] : 5 ?>" 
                           onchange="window.location.href='<?= buildFilterUrl($filters, 'rating', '') ?>' + (this.value > 5 ? this.value : '')"
                           oninput="document.getElementById('ratingVal').innerText = this.value"
                           style="accent-color: var(--accent); width: 120px; cursor: pointer;">
                    
                    <?php if(!empty($filters['genre']) || !empty($filters['year']) || !empty($filters['rating']) || !empty($filters['sort']) || !empty($filters['lang']) || !empty($filters['category'])): ?>
                        <a href="index.php?page=movies" class="reset-btn" style="margin-left: 10px;">
                            <i class="fas fa-times"></i> <?= translateText('reset_filter') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
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