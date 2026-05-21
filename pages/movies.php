<?php 
require_once __DIR__ . '/../config/data.php'; 
$popularMovies = getPopularMovies(20); // Ambil 20 film paling populer
?>

<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header">
        <h2>Movies</h2>
        <div class="filters-section">
            <div class="filter-dropdown">
                <button class="filter-btn">Genre <i class="fas fa-chevron-down"></i></button>
            </div>
            <div class="filter-dropdown">
                <button class="filter-btn">Tahun <i class="fas fa-chevron-down"></i></button>
            </div>
            <div class="filter-dropdown">
                <button class="filter-btn">Rating <i class="fas fa-chevron-down"></i></button>
            </div>
        </div>
    </div>
    
    <div class="movies-grid">
        <?php if(!empty($popularMovies) && is_array($popularMovies)): ?>
            <?php foreach($popularMovies as $movie): ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars((string)$movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                    <div class="play-overlay"><i class="fas fa-play"></i></div>
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars((string)$movie['title']) ?></div>
                    <div class="grid-movie-meta"><?= htmlspecialchars((string)$movie['year']) ?> &bull; <?= htmlspecialchars((string)$movie['genre']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-muted);">No popular movies found.</p>
        <?php endif; ?>
    </div>
</main>