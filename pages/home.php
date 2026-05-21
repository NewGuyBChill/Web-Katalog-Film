<?php require_once __DIR__ . '/../config/data.php'; ?>
<?php 
  $heroBanners = getHeroBanners(); 
  $firstHero = $heroBanners[0] ?? ['bg' => '', 'title' => 'NO DATA', 'meta' => '', 'synopsis' => ''];
?>
<!-- Pass data dari PHP ke Javascript -->
<script>const dynamicBanners = <?= json_encode($heroBanners) ?>;</script>

<!-- Hero Section -->
<header class="hero" style="background-image: <?= $firstHero['bg'] ?>;">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="badges">
            <span class="badge smart-tv">SMART TV</span>
            <span class="badge resolution">4K</span>
            <span class="badge rating">16+</span>
        </div>
        <h1><?= htmlspecialchars($firstHero['title']) ?></h1>
        <p class="meta"><?= htmlspecialchars($firstHero['meta']) ?></p>
        <p class="cast">Park Shin-hye &bull; Kim Jae-young &bull; Kim In-kwon</p>
        <p class="synopsis"><?= htmlspecialchars(substr($firstHero['synopsis'] ?? '', 0, 150)) ?>...</p>
        <div class="hero-buttons">
            <button class="btn-primary"><i class="fas fa-play"></i> Watch Trailer</button>
            <button class="btn-secondary" onclick="window.location.href='index.php?page=details&id=<?= $firstHero['id'] ?? 0 ?>'">Details</button>
        </div>
    </div>
    <div class="hero-arrows">
        <button class="arrow-btn" id="prevSlide">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="arrow-btn" id="nextSlide">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <div class="hero-dots" id="heroDots"></div>
</header>

<main>
    <!-- Featured Today -->
    <section class="container">
        <div class="section-header">
            <h2>Featured Today</h2>
            <p>What to watch based on your likes</p>
        </div>
        <div class="featured-grid">
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=600&q=80" alt="Cinematography">
                <h3>Visual Splendor & Masterful Cinematography</h3>
                <p>Explore films with striking art direction and composition...</p>
                <a href="#" class="read-more">View movies ></a>
            </div>
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=600&q=80" alt="Hidden Gems">
                <h3>Hidden Gems & Underrated Masterpieces</h3>
                <p>Brilliant films that might have slipped under the radar...</p>
                <a href="#" class="read-more">View movies ></a>
            </div>
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?auto=format&fit=crop&w=600&q=80" alt="Lore">
                <h3>Immersive Worlds & Complex Lore</h3>
                <p>A selection of films featuring strong world-building...</p>
                <a href="#" class="read-more">View movies ></a>
            </div>
        </div>
    </section>

    <!-- Trending Now -->
    <section class="container">
        <div class="section-header">
            <h2>Trending Now</h2>
        </div>
        <div class="movie-row" id="trending-row">
            <?php 
            $trendingMovies = getTrendingMovies();
            foreach($trendingMovies as $movie): 
            ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="movie-card" style="text-decoration: none; color: inherit; display: block;">
                <div class="movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars($movie['rating']) ?></div>
                <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                <div class="movie-title"><?= htmlspecialchars($movie['title']) ?></div>
                <div class="movie-meta"><?= htmlspecialchars($movie['year']) ?> &bull; <?= htmlspecialchars($movie['genre']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Top Picks -->
    <section class="container">
        <div class="section-header">
            <h2>Top Picks</h2>
        </div>
        <div class="movie-row" id="top-picks-row">
            <?php 
            $topPicks = getTopPicks();
            foreach($topPicks as $movie): 
            ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="movie-card" style="text-decoration: none; color: inherit; display: block;">
                <div class="movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars($movie['rating']) ?></div>
                <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                <div class="movie-title"><?= htmlspecialchars($movie['title']) ?></div>
                <div class="movie-meta"><?= htmlspecialchars($movie['year']) ?> &bull; <?= htmlspecialchars($movie['genre']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>