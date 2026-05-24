<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header">
        <div class="header-titles">
            <h2>My Watchlist</h2>
            <p>Your curated collection of movies to watch.</p>
        </div>
    </div>
    
    <div class="movies-grid">
        <?php if(!empty($movies)): ?>
            <?php foreach($movies as $movie): ?>
            <a href="<?= url("movies/{$movie['id']}") ?>" class="grid-movie-card" style="text-decoration: none; color: inherit; position: relative;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars((string)$movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                    
                    <div class="watchlist-btn active-fav" data-id="<?= $movie['id'] ?>" onclick="toggleWatchlistAPI(event, this, <?= $movie['id'] ?>)" style="background: rgba(0,0,0,0.8); right: 10px; top: 10px;">
                        <i class="fas fa-heart" style="color: #ff3b3b;"></i>
                    </div>
                    
                    <div class="grid-movie-quick-view">
                        <div class="quick-view-title">Synopsis</div>
                        <div class="quick-view-synopsis"><?= htmlspecialchars((string)$movie['overview']) ?: 'No synopsis available' ?></div>
                    </div>
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars((string)$movie['title']) ?></div>
                    <div class="grid-movie-meta flex justify-between">
                        <span><?= htmlspecialchars((string)$movie['year']) ?> &bull; <?= htmlspecialchars((string)$movie['genre']) ?></span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Added <?= date('M d', strtotime($movie['added_at'])) ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1; background: rgba(255,255,255,0.02); border: 1px dashed #333; border-radius: 12px; padding: 4rem 2rem;">
                <i class="fas fa-heart-broken" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;">Your Watchlist is Empty</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;">You haven't added any movies to your watchlist yet.</p>
                <a href="<?= url('movies') ?>" class="btn-primary" style="text-decoration: none; display: inline-block;">Explore Movies</a>
            </div>
        <?php endif; ?>
    </div>
</main>
