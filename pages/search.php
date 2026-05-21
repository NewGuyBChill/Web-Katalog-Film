<?php 
require_once __DIR__ . '/../config/data.php'; 
$query = isset($_GET['q']) ? $_GET['q'] : '';
$results = searchMovies($query);
?>

<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="section-header">
        <h2>Search Results for "<?= htmlspecialchars($query) ?>"</h2>
        <p><?= count($results) ?> movies found</p>
    </div>
    
    <div class="movie-row" style="flex-wrap: wrap; overflow-x: visible; gap: 1.5rem;">
        <?php if(count($results) > 0): ?>
            <?php foreach($results as $movie): ?>
            <div class="movie-card" style="margin-bottom: 1.5rem;">
                <div class="movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars($movie['rating']) ?></div>
                <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                <div class="movie-title"><?= htmlspecialchars($movie['title']) ?></div>
                <div class="movie-meta"><?= htmlspecialchars($movie['year']) ?> &bull; <?= htmlspecialchars($movie['genre']) ?></div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-muted);">No movies found. Try a different keyword.</p>
        <?php endif; ?>
    </div>
</main>