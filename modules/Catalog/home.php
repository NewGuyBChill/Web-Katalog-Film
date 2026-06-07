<?php require_once __DIR__ . '/../../config/data.php'; ?>
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

<style>
@keyframes kenBurns {
    0% { transform: scale(1); }
    100% { transform: scale(1.05); }
}
@keyframes fadeUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}

.animate-ken-burns {
    animation: kenBurns 20s ease-in-out infinite alternate;
}

/* Staggered animations */
.hero-slide.active .fade-up-1 { animation: fadeUp 0.8s ease-out forwards; animation-delay: 0.2s; opacity: 0; }
.hero-slide.active .fade-up-2 { animation: fadeUp 0.8s ease-out forwards; animation-delay: 0.4s; opacity: 0; }
.hero-slide.active .fade-up-3 { animation: fadeUp 0.8s ease-out forwards; animation-delay: 0.6s; opacity: 0; }
.hero-slide.active .fade-up-4 { animation: fadeUp 0.8s ease-out forwards; animation-delay: 0.8s; opacity: 0; }

.hero-slide:not(.active) .fade-up-1,
.hero-slide:not(.active) .fade-up-2,
.hero-slide:not(.active) .fade-up-3,
.hero-slide:not(.active) .fade-up-4 {
    opacity: 0;
}

.hero-btn-primary {
    transition: all 0.3s ease;
}
.hero-btn-primary:hover {
    transform: scale(1.05);
    filter: brightness(1.1);
}

.hero-btn-secondary {
    transition: all 0.3s ease;
}
.hero-btn-secondary:hover {
    background: rgba(255,255,255,0.3) !important;
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(255,255,255,0.2);
}

.review-card {
    transition: all 0.3s ease;
}
.review-card:hover {
    text-shadow: 0 0 10px rgba(255,255,255,0.8);
}
.review-card:hover .score-circle {
    box-shadow: 0 0 15px rgba(255,255,255,0.5);
    background: rgba(255,255,255,0.1);
}

.trailer-card {
    transition: all 0.3s ease;
}
.trailer-card:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 30px rgba(0,0,0,0.9);
}
.trailer-card:hover .play-icon-container {
    box-shadow: 0 0 15px rgba(255,255,255,0.8);
}
.trailer-card .play-icon-container {
    transition: all 0.3s ease;
}
</style>

<header class="hero-redesign" style="position: relative; width: 100%; height: 100vh; overflow: hidden; background-color: var(--bg-color);">
    
    <!-- Tahap 2: Pembagian Ruang Konten (3 Kolom) SLIDER (FULL SCREEN) -->
    <div class="hero-slider-track" id="customHeroSlider" style="position: absolute; inset: 0; display: flex; width: 500%; height: 100%; transition: transform 0.5s ease-in-out; z-index: 0;">
        <?php foreach($heroBanners as $index => $hero): ?>
        <div class="hero-slide <?= $index === 0 ? 'active' : '' ?>" style="width: 20%; height: 100%; position: relative;">
            
            <!-- Tahap 1: Background Sinematik Full-Screen & Animasi (Ken Burns Effect) -->
            <div class="animate-ken-burns" style="position: absolute; inset: 0; background-image: <?= $hero['bg'] ?>; background-size: cover; background-position: center; filter: brightness(0.8); z-index: 1; transform-origin: center;"></div>
            
            <!-- Tahap 2: Overlay Gradien Dinamis (Kiri, Kanan, & Bawah) -->
            <!-- Kiri -->
            <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 30%, transparent 100%); z-index: 2;"></div>
            <!-- Kanan -->
            <div style="position: absolute; inset: 0; background: linear-gradient(to left, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.6) 30%, transparent 100%); z-index: 2;"></div>
            <!-- Bawah -->
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(15,15,15,1) 0%, rgba(15,15,15,0.4) 20%, transparent 100%); z-index: 2;"></div>

            <!-- Tahap 3: Animasi Entrance & Layout Konten -->
            <div style="position: relative; z-index: 3; width: 100%; height: 100%; display: flex; justify-content: space-between; align-items: center; padding: 120px 4rem 4rem 4rem; box-sizing: border-box;">
                
                <!-- Tahap 4: Penyempurnaan Kolom Kiri -->
                <div class="hero-col-left" style="display: flex; flex-direction: column; justify-content: center; position: relative; max-width: 500px; text-align: left; padding-left: 2rem;">
                    <h1 class="fade-up-1" style="font-size: 4rem; font-weight: 900; line-height: 1.1; margin-bottom: 1.5rem; color: white; text-transform: uppercase; text-shadow: 2px 2px 10px rgba(0,0,0,0.8);">
                        <?= htmlspecialchars((string)($hero['title'] ?? '')) ?>
                    </h1>
                    <p class="fade-up-2" style="font-size: 1.1rem; color: rgba(255,255,255,0.85); line-height: 1.6; margin-bottom: 2.5rem; text-shadow: 0 2px 5px rgba(0,0,0,0.9);">
                        <?= htmlspecialchars((string)substr($hero['synopsis'] ?? '', 0, 200)) ?>...
                    </p>
                    <div class="fade-up-3" style="display: flex; gap: 1rem;">
                        <?php if (isset($hero['trailer']) && $hero['trailer'] !== "#"): ?>
                        <button class="hero-btn-primary" style="background: #00d2ff; color: black; padding: 1rem 2.5rem; border-radius: 50px; font-weight: 800; font-size: 1rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.5);" onclick="openTrailerModal('<?= $hero['trailer'] ?>')">
                            <i class="fas fa-play"></i> PLAY TRAILER
                        </button>
                        <?php else: ?>
                        <button class="hero-btn-primary" style="background: rgba(255,255,255,0.5); color: black; padding: 1rem 2.5rem; border-radius: 50px; font-weight: 800; font-size: 1rem; border: none; cursor: not-allowed; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.5);">
                            <i class="fas fa-play"></i> NO TRAILER
                        </button>
                        <?php endif; ?>
                        
                        <a href="index.php?page=details&id=<?= $hero['id'] ?? 0 ?>&type=<?= $hero['type'] ?? 'movie' ?>" class="hero-btn-secondary" style="background: rgba(255,255,255,0.2); color: white; padding: 1rem 2.5rem; border-radius: 50px; font-weight: 800; font-size: 1rem; border: 1px solid rgba(255,255,255,0.5); cursor: pointer; display: inline-flex; align-items: center; gap: 10px; text-decoration: none; backdrop-filter: blur(5px); transition: all 0.3s ease;">
                            <i class="fas fa-info-circle"></i> INFO
                        </a>
                    </div>
                </div>

                <!-- Tahap 5: Penyempurnaan Kolom Kanan -->
                <div class="hero-col-right" style="display: flex; flex-direction: column; justify-content: center; padding-right: 2rem; max-width: 400px; text-align: right; align-items: flex-end;">
                    <!-- Game/Film Reviews -->
                    <div class="fade-up-3" style="margin-bottom: 3rem; text-align: right;">
                        <h3 style="font-size: 0.85rem; font-weight: 700; letter-spacing: 2px; color: white; margin-bottom: 1.5rem; text-transform: uppercase; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 0.5rem; display: inline-block;">FILM REVIEWS</h3>
                        
                        <div class="review-card" style="display: flex; align-items: center; gap: 1.2rem; margin-bottom: 1.5rem; justify-content: flex-end; cursor: default;">
                            <div style="color: white; font-size: 0.9rem; text-align: right;">
                                <strong style="display: block; font-size: 1rem;">TMDB Rating</strong>
                                <span style="opacity: 0.8;">"Global Audience Score"</span>
                            </div>
                            <div class="score-circle" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white; display: flex; justify-content: center; align-items: center; color: white; font-weight: 700; font-size: 1.1rem; flex-shrink: 0; transition: all 0.3s ease;"><?= number_format(($hero['rating'] ?? 0), 1) ?></div>
                        </div>
                        
                        <div class="review-card" style="display: flex; align-items: center; gap: 1.2rem; justify-content: flex-end; cursor: default;">
                            <div style="color: white; font-size: 0.9rem; text-align: right;">
                                <strong style="display: block; font-size: 1rem;">Critics Choice</strong>
                                <span style="opacity: 0.8;">"Highly Recommended"</span>
                            </div>
                            <div class="score-circle" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white; display: flex; justify-content: center; align-items: center; color: white; font-weight: 700; font-size: 1.1rem; flex-shrink: 0; transition: all 0.3s ease;">9.0</div>
                        </div>
                    </div>
                    
                    <!-- Watch Trailer -->
                    <div class="fade-up-4" style="text-align: right;">
                        <h3 style="font-size: 0.85rem; font-weight: 700; letter-spacing: 2px; color: white; margin-bottom: 1rem; text-transform: uppercase;">WATCH TRAILER</h3>
                        <?php if (isset($hero['trailer']) && $hero['trailer'] !== "#"): ?>
                        <div class="trailer-card" id="trailer-card-<?= $index ?>" style="position: relative; width: 260px; height: 150px; border-radius: 12px; overflow: hidden; cursor: pointer; box-shadow: 0 10px 20px rgba(0,0,0,0.5); backdrop-filter: blur(5px);" onclick="playInlineTrailer(event, '<?= $index ?>', '<?= $hero['trailer'] ?>')">
                        <?php else: ?>
                        <div class="trailer-card" style="position: relative; width: 260px; height: 150px; border-radius: 12px; overflow: hidden; opacity: 0.5; cursor: not-allowed; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                        <?php endif; ?>
                            <?php $trailerImg = !empty($hero['yt_thumbnail']) ? $hero['yt_thumbnail'] : str_replace("url('", "", str_replace("')", "", $hero['bg'] ?? '')); ?>
                            <img src="<?= htmlspecialchars((string)$trailerImg) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Trailer">
                            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; justify-content: center; align-items: center;">
                                <div class="play-icon-container" style="width: 45px; height: 45px; border-radius: 50%; background: white; display: flex; justify-content: center; align-items: center; padding-left: 3px;">
                                    <i class="fas fa-play" style="color: var(--bg-color); font-size: 1.2rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tahap 1: Navbar Atas -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; z-index: 10;">
        <nav class="hero-redesign-nav" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 4rem;">
            <!-- Kiri: Logo -->
            <div class="hero-logo" style="font-family: 'Barlow Condensed', sans-serif; font-size: 1.8rem; color: white; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-play" style="font-size: 1.2rem;"></i>
                <span><span style="font-weight: 800;">Celes</span><span style="font-weight: 300;">View</span></span>
            </div>
            
            <!-- Tengah: Menu -->
            <div class="hero-nav-links" style="display: flex; gap: 2.5rem; font-size: 0.9rem; font-weight: 500;">
                <a href="index.php?page=movies" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 5px;">Movies <i class="fas fa-chevron-down" style="font-size: 0.6rem; opacity: 0.7;"></i></a>
                <a href="index.php?page=tv" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 5px;">TV Shows <i class="fas fa-chevron-down" style="font-size: 0.6rem; opacity: 0.7;"></i></a>
                <a href="index.php?page=home#explore" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 5px;">Explore <i class="fas fa-chevron-down" style="font-size: 0.6rem; opacity: 0.7;"></i></a>
                <a href="#" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 5px;">Services <i class="fas fa-chevron-down" style="font-size: 0.6rem; opacity: 0.7;"></i></a>
            </div>
            
            <!-- Kanan: CTA & Search -->
            <div class="hero-nav-actions" style="display: flex; align-items: center; gap: 1.5rem;">
                <a href="index.php?page=login" style="background: white; color: black; padding: 0.4rem 1.2rem; border-radius: 50px; font-weight: 700; font-size: 0.85rem; text-decoration: none;">Login</a>
                <i class="fas fa-search" style="color: white; font-size: 1.2rem; cursor: pointer;"></i>
            </div>
        </nav>
        <div style="width: 100%; height: 1px; background: rgba(255,255,255,0.1);"></div>
    </div>

    <!-- Slider Pagination Dots -->
    <div class="hero-slider-dots" style="position: absolute; bottom: 2rem; right: 4rem; display: flex; gap: 10px; z-index: 20;">
        <?php foreach($heroBanners as $index => $hero): ?>
        <div class="slider-dot" onclick="goToHeroSlide(<?= $index ?>)" id="heroDot<?= $index ?>" style="width: 30px; height: 4px; background: <?= $index === 0 ? 'white' : 'rgba(255,255,255,0.3)' ?>; cursor: pointer; transition: background 0.3s;"></div>
        <?php endforeach; ?>
    </div>
</header>

<script>
    let currentHeroSlide = 0;
    const heroSliderTrack = document.getElementById('customHeroSlider');
    const totalHeroSlides = <?= count($heroBanners) ?>;

    function goToHeroSlide(index) {
        currentHeroSlide = index;
        heroSliderTrack.style.transform = `translateX(-${index * 20}%)`;
        
        // Update dots
        for (let i = 0; i < totalHeroSlides; i++) {
            const dot = document.getElementById(`heroDot${i}`);
            if (dot) {
                dot.style.background = i === currentHeroSlide ? 'white' : 'rgba(255,255,255,0.3)';
            }
        }

        // Update active class for animations
        const slides = document.querySelectorAll('.hero-slide');
        slides.forEach((slide, i) => {
            if (i === currentHeroSlide) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
    }

    // Auto-play
    let autoPlayInterval = setInterval(() => {
        let nextSlide = (currentHeroSlide + 1) % totalHeroSlides;
        goToHeroSlide(nextSlide);
    }, 6000);

    function playInlineTrailer(event, index, url) {
        event.stopPropagation();
        clearInterval(autoPlayInterval); // Hentikan autoplay slider saat menonton video
        
        let embedUrl = url;
        if(url.includes('watch?v=')) {
            embedUrl = url.replace('watch?v=', 'embed/') + '?autoplay=1';
        }
        
        const card = document.getElementById('trailer-card-' + index);
        card.innerHTML = `<iframe width="100%" height="100%" src="${embedUrl}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen style="position: absolute; inset: 0;"></iframe>`;
        card.onclick = null;
        card.style.cursor = 'default';
        card.style.backdropFilter = 'none';
    }
</script>

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
            
            <div class="featured-today-card" style="--featured-bg: url('<?= htmlspecialchars((string)($featured['backdrop'] ?: $featured['image'])) ?>'); border-radius: 16px !important; overflow: hidden !important; background-clip: padding-box !important; -webkit-background-clip: padding-box !important;">
                <div class="featured-today-content">
                    <span class="badge-trending"><i class="fas fa-fire"></i> <?= translateText('trending_1') ?></span>
                    <h1 class="featured-today-title"><?= htmlspecialchars((string)$featured['title']) ?></h1>
                    
                    <div class="featured-today-meta">
                        <span class="rating"><i class="fas fa-star" style="color: #FCD34D;"></i> <?= htmlspecialchars((string)($featured['rating'] ?? 0)) ?>/10</span>
                        <span class="year"><?= htmlspecialchars((string)$featured['year']) ?></span>
                        <span class="genre"><?= htmlspecialchars((string)$featured['genre']) ?></span>
                    </div>
                    
                    <p class="featured-today-synopsis">
                        <?= htmlspecialchars((string)($featured['overview'] ?: translateText('no_synopsis'))) ?>
                    </p>
                    
                    <div class="featured-today-actions">
                        <a href="index.php?page=details&type=<?= $featured['type'] ?? 'movie' ?>&id=<?= $featured['id'] ?>" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-info-circle"></i> <?= translateText('read_more') ?>
                        </a>
                        <button class="btn-secondary watchlist-btn-detail" data-id="<?= $featured['id'] ?>" data-type="<?= $featured['type'] ?? 'movie' ?>" onclick="toggleWatchlistDetail(event, this, '<?= $featured['id'] ?>', '<?= $featured['type'] ?? 'movie' ?>', '<?= addslashes(htmlspecialchars($featured['title'])) ?>', '<?= $featured['poster_path'] ?? $featured['image'] ?>')" style="display: inline-flex; align-items: center; gap: 8px; transition: 0.3s;">
                            <i class="fas fa-plus"></i> <?= translateText('watchlist') ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <style>
                .featured-today-card {
                    position: relative;
                    width: 100%;
                    box-sizing: border-box;
                    min-height: 450px;
                    background-color: #121212;
                    background-image: linear-gradient(to right, #121212 0%, #121212 5%, rgba(18,18,18,0.7) 65%, transparent 100%), linear-gradient(to bottom, #121212 0%, transparent 3%, transparent 97%, #121212 100%), var(--featured-bg);
                    background-size: cover;
                    background-position: right center;
                    background-repeat: no-repeat;
                    border-radius: 16px !important; /* Diperkuat !important */
                    display: flex;
                    align-items: center;
                    padding: 3rem;
                    color: white;
                    overflow: hidden !important;    /* Diperkuat !important */
                    background-clip: padding-box !important; /* Mencegah gambar bocor keluar border */
                    -webkit-background-clip: padding-box !important;
                    transform: translateZ(0); 
                    margin-bottom: 2rem;
                    border: 1px solid rgba(255,255,255,0.05);
                }
                .featured-today-content {
                    max-width: 550px;
                    z-index: 2;
                }
                .badge-trending {
                    background-color: #e50914;
                    padding: 6px 16px;
                    border-radius: 6px;
                    font-size: 0.85rem;
                    font-weight: bold;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    margin-left: 2px;
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
                    line-clamp: 4;
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
                        background-image: linear-gradient(to top, #121212 0%, #121212 5%, rgba(18,18,18,0.7) 30%, transparent 100%), linear-gradient(to bottom, #121212 0%, transparent 3%, transparent 97%, #121212 100%), var(--featured-bg) !important;
                        background-position: center top !important;
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
            <a href="index.php?page=details&type=<?= $movie['type'] ?? 'movie' ?>&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
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
        </div>
    </section>
    <?php endif; ?>

    <!-- Trends Now -->
    <section class="container">
        <div class="section-header" style="display: flex; flex-direction: column; gap: 15px;">
            <h2><i class="fas fa-fire" style="color: var(--accent); margin-right: 10px;"></i> Trends Now</h2>
            <div class="category-pills" style="display: flex; gap: 10px; overflow-x: auto; scrollbar-width: none;">
                <a href="index.php?page=movies&genre=28" class="filter-btn active" style="padding: 0.4rem 1.2rem; white-space: nowrap;">Action</a>
                <a href="index.php?page=movies&genre=12" class="filter-btn" style="padding: 0.4rem 1.2rem; white-space: nowrap;">Adventure</a>
                <a href="index.php?page=movies&genre=35" class="filter-btn" style="padding: 0.4rem 1.2rem; white-space: nowrap;">Comedy</a>
                <a href="index.php?page=movies&genre=18" class="filter-btn" style="padding: 0.4rem 1.2rem; white-space: nowrap;">Drama</a>
                <a href="index.php?page=movies&genre=10749" class="filter-btn" style="padding: 0.4rem 1.2rem; white-space: nowrap;">Romance</a>
                <a href="index.php?page=movies&genre=878" class="filter-btn" style="padding: 0.4rem 1.2rem; white-space: nowrap;">Sci-Fi</a>
            </div>
        </div>
        <div class="movie-row" id="trending-row">
            <?php 
            foreach($trendingMovies as $movie): 
            ?>
            <a href="index.php?page=details&type=<?= $movie['type'] ?? 'movie' ?>&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
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
        <a href="index.php?page=details&type=<?= $movie['type'] ?? 'movie' ?>&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
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
            <a href="index.php?page=details&type=<?= $movie['type'] ?? 'movie' ?>&id=<?= $movie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
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
        </div>
    </section>

    <!-- Top Actors -->
    <section class="container">
        <div class="section-header">
            <h2><i class="fas fa-star" style="color: var(--accent); margin-right: 10px;"></i> Best Actors of the Week</h2>
        </div>
        <div class="movie-row" id="top-actors-row">
            <?php 
            $topActors = getTrendingPersons(12);
            foreach($topActors as $actor): 
            ?>
            <a href="index.php?page=person&id=<?= $actor['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit; align-items: center; text-align: center;">
                <div style="width: 150px; height: 150px; border-radius: 50%; overflow: hidden; margin: 0 auto 15px auto; border: 3px solid rgba(255,255,255,0.1); box-shadow: 0 10px 20px rgba(0,0,0,0.5); transition: transform 0.3s ease;">
                    <img src="<?= htmlspecialchars((string)$actor['image']) ?>" alt="<?= htmlspecialchars((string)$actor['name']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                </div>
                <div class="grid-movie-title" style="font-size: 1.1rem; margin-bottom: 5px;"><?= htmlspecialchars((string)$actor['name']) ?></div>
                <div class="grid-movie-meta" style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars((string)$actor['role']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>