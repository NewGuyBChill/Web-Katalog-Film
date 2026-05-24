<?php 
function buildSearchUrl($query, $page) {
    return url('search', ['q' => $query, 'page' => $page]);
}
?>

<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header">
        <div class="header-titles">
            <h2>Search Results for "<?= htmlspecialchars($query) ?>"</h2>
            <?php if($query): ?>
                <p>Page <?= $currentPage ?> of <?= $totalPages ?> &bull; <?= count($results) ?> results on this page</p>
            <?php else: ?>
                <p>Enter a query to search for movies or TV shows.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="movies-grid">
        <?php if(count($results) > 0): ?>
            <?php foreach($results as $movie): ?>
            <a href="<?= url("movies/{$movie['id']}") ?>" class="grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars((string)$movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                    
                    <?php if (auth()->check()): ?>
                    <div class="watchlist-btn" data-id="<?= $movie['id'] ?>" onclick="toggleWatchlistAPI(event, this, <?= $movie['id'] ?>)">
                        <i class="fas fa-heart"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="grid-movie-quick-view">
                        <div class="quick-view-title">Synopsis</div>
                        <div class="quick-view-synopsis"><?= htmlspecialchars((string)$movie['overview']) ?: 'No synopsis available' ?></div>
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
                        <a href="<?= buildSearchUrl($query, $currentPage - 1) ?>" class="page-btn" title="Previous"><i class="fas fa-chevron-left"></i></a>
                    <?php else: ?>
                        <button class="page-btn" disabled><i class="fas fa-chevron-left"></i></button>
                    <?php endif; ?>

                    <!-- Page 1 & Dots -->
                    <?php if($currentPage > 2): ?>
                        <a href="<?= buildSearchUrl($query, 1) ?>" class="page-btn">1</a>
                        <?php if($currentPage > 3): ?><span class="page-dots">...</span><?php endif; ?>
                    <?php endif; ?>

                    <!-- Prev Page -->
                    <?php if($currentPage > 1): ?>
                        <a href="<?= buildSearchUrl($query, $currentPage - 1) ?>" class="page-btn"><?= $currentPage - 1 ?></a>
                    <?php endif; ?>

                    <!-- Current Page -->
                    <span class="page-btn active"><?= $currentPage ?></span>

                    <!-- Next Pages -->
                    <?php if($currentPage < $totalPages): ?>
                        <a href="<?= buildSearchUrl($query, $currentPage + 1) ?>" class="page-btn"><?= $currentPage + 1 ?></a>
                    <?php endif; ?>
                    
                    <?php if($currentPage == 1 && $totalPages >= 3): ?>
                        <a href="<?= buildSearchUrl($query, 3) ?>" class="page-btn">3</a>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if($currentPage < $totalPages): ?>
                        <a href="<?= buildSearchUrl($query, $currentPage + 1) ?>" class="page-btn" title="Next"><i class="fas fa-chevron-right"></i></a>
                    <?php else: ?>
                        <button class="page-btn" disabled><i class="fas fa-chevron-right"></i></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <?php if($query): ?>
                    <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;">Oops, No Movies Found!</h3>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;">Try adjusting your search query to see other results.</p>
                <?php else: ?>
                    <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;">Search Movies & TV Shows</h3>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;">Use the search bar above to find your favorite media.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
