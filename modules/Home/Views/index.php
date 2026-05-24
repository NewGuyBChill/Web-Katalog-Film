<?php require_once base_path('config/data.php'); ?>
<?php 
  $heroBanners = getHeroBanners(); 
  $firstHero = $heroBanners[0] ?? ['bg' => '', 'title' => 'NO DATA', 'meta' => '', 'synopsis' => '', 'trailer' => '#'];
?>
<!-- Pass data dari PHP ke Javascript -->
<script>
    const dynamicBanners = <?= json_encode($heroBanners) ?>;
    const langStrings = {
        watchTrailer: "<?= translateText('watch_trailer') ?>",
        noTrailer: "<?= translateText('no_trailer') ?>"
    };
</script>

<!-- Hero Section -->
<header class="hero" style="background-image: <?= $firstHero['bg'] ?>;">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="badges">
            <span class="badge smart-tv"><?= translateText('now_playing') ?></span>
            <span class="badge resolution">HD</span>
            <span class="badge rating" id="heroRating"><i class="fas fa-star"></i> <?= htmlspecialchars($firstHero['rating'] ?? 0) ?></span>
        </div>
        <h1><?= htmlspecialchars($firstHero['title']) ?></h1>
        <p class="meta"><?= htmlspecialchars($firstHero['meta']) ?></p>
        <p class="synopsis"><?= htmlspecialchars(substr($firstHero['synopsis'] ?? '', 0, 150)) ?>...</p>
        <div class="hero-buttons">
            <?php if (isset($firstHero['trailer']) && $firstHero['trailer'] !== "#"): ?>
                <button class="btn-primary" onclick="openTrailerModal('<?= $firstHero['trailer'] ?>')"><i class="fas fa-play"></i> <?= translateText('watch_trailer') ?></button>
            <?php else: ?>
                <button class="btn-primary" style="opacity: 0.5; cursor: not-allowed;" disabled><i class="fas fa-play"></i> <?= translateText('no_trailer') ?></button>
            <?php endif; ?>
            <button class="btn-secondary" onclick="window.location.href='index.php?page=details&id=<?= $firstHero['id'] ?? 0 ?>'"><?= translateText('details') ?></button>
        </div>
    </div>
    <div class="hero-dots" id="heroDots"></div>
</header>

<main>
    <?php 
    // Mengambil data trending, lalu menggunakan film pertama (Trending #1) sebagai Featured Today
    $trendingMovies = getTrendingMovies();
    $featured = !empty($trendingMovies) ? $trendingMovies[0] : null; 
    ?>

    <!-- Featured Today -->
    <?php if ($featured): ?>
    <section class="container">
        <div class="section-header">
            <h2><?= translateText('featured_today') ?></h2>
            <p><?= translateText('highlight_day') ?></p>
        </div>
        <div class="featured-today-card" style="background-image: linear-gradient(to right, rgba(18,18,18,1) 15%, rgba(18,18,18,0.7) 50%, rgba(18,18,18,0.1)), url('<?= htmlspecialchars($featured['backdrop'] ?: $featured['image']) ?>');">
            <div class="featured-today-content">
                <span class="badge-trending"><i class="fas fa-fire"></i> <?= translateText('trending_1') ?></span>
                <h1 class="featured-today-title"><?= htmlspecialchars($featured['title']) ?></h1>
                
                <div class="featured-today-meta">
                    <span class="rating"><i class="fas fa-star" style="color: #FCD34D;"></i> <?= htmlspecialchars($featured['rating']) ?>/10</span>
                    <span class="year"><?= htmlspecialchars($featured['year']) ?></span>
                    <span class="genre"><?= htmlspecialchars($featured['genre']) ?></span>
                </div>
                
                <p class="featured-today-synopsis">
                    <?= htmlspecialchars($featured['overview'] ?: translateText('no_synopsis')) ?>
                </p>
                
                <div class="featured-today-actions">
                    <a href="index.php?page=details&id=<?= $featured['id'] ?>" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle"></i> <?= translateText('read_more') ?>
                    </a>
                    <button class="btn-secondary watchlist-btn-detail" data-id="<?= $featured['id'] ?>" onclick="toggleWatchlistDetail(event, this, '<?= $featured['id'] ?>', 'movie', '<?= addslashes(htmlspecialchars($featured['title'])) ?>', '<?= $featured['poster_path'] ?? $featured['image'] ?>')" style="display: inline-flex; align-items: center; gap: 8px; transition: 0.3s;">
                        <i class="fas fa-plus"></i> <?= translateText('watchlist') ?>
                    </button>
                </div>
            </div>
        </div>
        
        <style>
            .featured-today-card {
                position: relative;
                width: 100%;
                min-height: 450px;
                background-size: cover;
                background-position: center top;
                border-radius: 16px;
                display: flex;
                align-items: center;
                padding: 3rem;
                color: white;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5);
                overflow: hidden;
                margin-bottom: 2rem;
                border: 1px solid rgba(255,255,255,0.05);
            }
            .featured-today-content {
                max-width: 550px;
                z-index: 2;
            }
            .badge-trending {
                background-color: #e50914;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 0.85rem;
                font-weight: bold;
                display: inline-block;
                margin-bottom: 1rem;
            }
            .featured-today-title {
                font-size: 2.8rem;
                margin: 0 0 10px 0;
                line-height: 1.1;
                font-weight: 800;
            }
            .featured-today-meta {
                display: flex;
                gap: 15px;
                margin-bottom: 15px;
                font-size: 0.95rem;
                color: #ccc;
                font-weight: 600;
            }
            .featured-today-synopsis {
                line-height: 1.6;
                margin-bottom: 25px;
                display: -webkit-box;
                -webkit-line-clamp: 4;
                -webkit-box-orient: vertical;
                overflow: hidden;
                color: #ddd;
                font-size: 1.05rem;
            }
            .featured-today-actions {
                display: flex;
                gap: 15px;
                align-items: center;
            }
            @media (max-width: 768px) {
                .featured-today-card {
                    padding: 2rem;
                    background-image: linear-gradient(to top, rgba(18,18,18,1) 20%, rgba(18,18,18,0.6)), url('<?= htmlspecialchars($featured['backdrop'] ?: $featured['image']) ?>') !important;
                    align-items: flex-end;
                }
                .featured-today-title { font-size: 2rem; }
            }
        </style>
    </section>
    <?php endif; ?>

    <!-- Recommended For You (Fitur Personal) -->
    <?php 
    $personalized = getPersonalizedRecommendations();
    if (!empty($personalized) && isset($_SESSION['user'])): 
    ?>
    <section class="container">
        <div class="section-header">
            <h2 style="color: var(--accent);"><i class="fas fa-magic"></i> <?= translateText('recommended_for_you') ?></h2>
            <p><?= translateText('based_on_rating') ?></p>
        </div>
        <div class="movie-row" id="personalized-row">
            <?php foreach($personalized as $movie): ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
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
        </div>
    </section>
    <?php endif; ?>

    <!-- Trending Now -->
    <section class="container">
        <div class="section-header">
            <h2><?= translateText('trending_now') ?></h2>
        </div>
        <div class="movie-row" id="trending-row">
            <?php 
            foreach($trendingMovies as $movie): 
            ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars($movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    <div class="watchlist-btn" data-id="<?= $movie['id'] ?>" data-title="<?= htmlspecialchars((string)$movie['title']) ?>" onclick="toggleWatchlist(event, this)">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="grid-movie-quick-view">
                        <div class="quick-view-title"><?= translateText('synopsis') ?></div>
                        <div class="quick-view-synopsis"><?= htmlspecialchars((string)$movie['overview']) ?: translateText('no_synopsis') ?></div>
                    </div>
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars($movie['title']) ?></div>
                    <div class="grid-movie-meta"><?= htmlspecialchars($movie['year']) ?> &bull; <?= htmlspecialchars($movie['genre']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

<!-- Upcoming Movies -->
<section class="container">
    <div class="section-header">
        <h2><?= translateText('upcoming_movies') ?></h2>
    </div>
    <div class="movie-row" id="upcoming-row">
        <?php 
        $upcomingMovies = getUpcomingMovies();
        foreach($upcomingMovies as $movie): 
        ?>
        <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
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
    </div>
</section>
    
    <!-- Top Picks -->
    <section class="container">
        <div class="section-header">
            <h2><?= translateText('top_picks') ?></h2>
        </div>
        <div class="movie-row" id="top-picks-row">
            <?php 
            $topPicks = getTopPicks();
            foreach($topPicks as $movie): 
            ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars($movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                    <div class="watchlist-btn" data-id="<?= $movie['id'] ?>" data-title="<?= htmlspecialchars((string)$movie['title']) ?>" onclick="toggleWatchlist(event, this)">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="grid-movie-quick-view">
                        <div class="quick-view-title"><?= translateText('synopsis') ?></div>
                        <div class="quick-view-synopsis"><?= htmlspecialchars((string)$movie['overview']) ?: translateText('no_synopsis') ?></div>
                    </div>
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars($movie['title']) ?></div>
                    <div class="grid-movie-meta"><?= htmlspecialchars($movie['year']) ?> &bull; <?= htmlspecialchars($movie['genre']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>