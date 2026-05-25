<?php 
require_once __DIR__ . '/../../config/data.php'; 
$query = isset($_GET['q']) ? $_GET['q'] : '';
$currentPage = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$currentSort = isset($_GET['sort']) ? $_GET['sort'] : 'popularity.desc';
$results = searchMovies($query, $currentPage);

// Fitur pengurutan (Sorting) khusus untuk halaman hasil pencarian saat ini
if ($currentSort === 'vote_average.desc') {
    usort($results, function($a, $b) { return $b['rating'] <=> $a['rating']; });
} elseif ($currentSort === 'primary_release_date.desc') {
    usort($results, function($a, $b) { return strcmp($b['year'], $a['year']); });
} elseif ($currentSort === 'primary_release_date.asc') {
    usort($results, function($a, $b) { 
        $yearA = $a['year'] === '-' ? '9999' : $a['year'];
        $yearB = $b['year'] === '-' ? '9999' : $b['year'];
        return strcmp($yearA, $yearB); 
    });
}

$sortMap = [
    'popularity.desc' => translateText('sort_pop_desc'),
    'vote_average.desc' => translateText('sort_rating_desc'),
    'primary_release_date.desc' => translateText('sort_date_desc'),
    'primary_release_date.asc' => translateText('sort_date_asc')
];
$currentSortLabel = isset($sortMap[$currentSort]) ? $sortMap[$currentSort] : translateText('sort_pop_desc');
$sortQuery = $currentSort !== 'popularity.desc' ? '&sort='.$currentSort : '';
?>

<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header">
        <div class="header-titles">
            <h2><?= translateText('search_results_for') ?> "<?= htmlspecialchars($query) ?>"</h2>
            <p><?= translateText('page') ?> <?= $currentPage ?> &bull; <?= count($results) ?> <?= translateText('movies_on_page') ?></p>
        </div>
        
        <?php if(count($results) > 0 || $currentSort !== 'popularity.desc'): ?>
        <div class="filters-section">
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= $currentSort !== 'popularity.desc' ? 'active-filter' : '' ?>">
                    <span><?= translateText('sort_by') ?>: <?= $currentSortLabel ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <?php foreach($sortMap as $val => $label): ?>
                        <a href="index.php?page=search&q=<?= urlencode($query) ?>&sort=<?= $val === 'popularity.desc' ? '' : $val ?>" class="<?= ($currentSort == $val || (empty($currentSort) && $val == 'popularity.desc')) ? 'active' : '' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if($currentSort !== 'popularity.desc'): ?>
                <a href="index.php?page=search&q=<?= urlencode($query) ?>" class="reset-btn">
                    <i class="fas fa-times"></i> <?= translateText('reset_filter') ?>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="movies-grid">
        <?php if(count($results) > 0): ?>
            <?php foreach($results as $movie): ?>
            <a href="index.php?page=details&type=<?= $movie['type'] ?? 'movie' ?>&id=<?= $movie['id'] ?>" class="grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars((string)$movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                    <div class="watchlist-btn" data-id="<?= $movie['id'] ?>" data-type="<?= $movie['type'] ?? 'movie' ?>" data-title="<?= htmlspecialchars((string)$movie['title']) ?>" onclick="toggleWatchlist(event, this)">
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
                        <a href="index.php?page=search&q=<?= urlencode($query) ?><?= $sortQuery ?>&p=<?= $currentPage - 1 ?>" class="page-btn" title="Previous"><i class="fas fa-chevron-left"></i></a>
                    <?php else: ?>
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <?php endif; ?>

                    <!-- Page 1 & Dots -->
                    <?php if($currentPage > 2): ?>
                        <a href="index.php?page=search&q=<?= urlencode($query) ?><?= $sortQuery ?>&p=1" class="page-btn">1</a>
                        <?php if($currentPage > 3): ?><span class="page-dots">...</span><?php endif; ?>
                    <?php endif; ?>

                    <!-- Prev Page -->
                    <?php if($currentPage > 1): ?>
                        <a href="index.php?page=search&q=<?= urlencode($query) ?><?= $sortQuery ?>&p=<?= $currentPage - 1 ?>" class="page-btn"><?= $currentPage - 1 ?></a>
                    <?php endif; ?>

                    <!-- Current Page -->
                    <span class="page-btn active"><?= $currentPage ?></span>

                    <!-- Next Pages (Hanya muncul jika hasil ada 20) -->
                    <?php if(count($results) === 20): ?>
                        <a href="index.php?page=search&q=<?= urlencode($query) ?><?= $sortQuery ?>&p=<?= $currentPage + 1 ?>" class="page-btn"><?= $currentPage + 1 ?></a>
                        <?php if($currentPage == 1): ?><a href="index.php?page=search&q=<?= urlencode($query) ?><?= $sortQuery ?>&p=3" class="page-btn">3</a><?php endif; ?>
                        <a href="index.php?page=search&q=<?= urlencode($query) ?><?= $sortQuery ?>&p=<?= $currentPage + 1 ?>" class="page-btn" title="Next"><i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                        <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;"><?= translateText('no_movies') ?></h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;"><?= translateText('no_movies_desc') ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>