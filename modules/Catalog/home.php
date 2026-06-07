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
                        <button class="hero-btn-primary watchlist-btn-detail" style="background: white; color: black; padding: 1rem 2.5rem; border-radius: 50px; font-weight: 800; font-size: 1rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.5);" data-id="<?= $hero['id'] ?? 0 ?>" data-type="<?= $hero['type'] ?? 'movie' ?>" onclick="toggleWatchlistDetail(event, this, '<?= $hero['id'] ?? 0 ?>', '<?= $hero['type'] ?? 'movie' ?>', '<?= addslashes(htmlspecialchars((string)($hero['title'] ?? ''))) ?>', '<?= $hero['poster'] ?? '' ?>')">
                            <i class="fas fa-heart" style="transition: color 0.3s ease;"></i> <span class="btn-text">ADD TO WATCHLIST</span>
                        </button>
                        
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

    <!-- NEW ANIME LAYOUT START -->
    <?php 
    global $genreMap, $tvGenreMap, $langMap;
    $filters = [
        'genre' => $_GET['genre'] ?? '',
        'year' => $_GET['year'] ?? '',
        'rating' => $_GET['rating'] ?? '',
        'sort' => $_GET['sort'] ?? 'popularity.desc',
        'type' => $_GET['type'] ?? 'movie',
        'lang' => $_GET['lang'] ?? ''
    ];
    
    if (($filters['type'] ?? 'movie') === 'tv') {
        $animeMoviesList = discoverTVShows($filters, 24, 1);
        $activeGenreMap = $tvGenreMap;
    } else {
        $animeMoviesList = discoverMovies($filters, 24, 1);
        $activeGenreMap = $genreMap;
    }
    
    $animeTrending = getTrendingMovies(); 
    $animeFeatured = !empty($animeMoviesList) ? $animeMoviesList[0] : (!empty($animeTrending) ? $animeTrending[0] : null);
    ?>

    <section class="anime-main-layout">
        <!-- CSS Internal untuk Layout Baru -->
        <style>
            .anime-main-layout {
                padding-top: 20px;
                background-color: var(--bg-color);
                color: var(--text-main);
                font-family: 'Inter', sans-serif;
                overflow-x: hidden;
            }

            /* Tahap 1: Hero Banner Dinamis */
            .anime-hero {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: linear-gradient(135deg, #111, #1a1a1a);
                border-radius: 20px;
                margin: 0 4rem 2rem 4rem;
                padding: 3rem 4rem;
                position: relative;
                overflow: visible; /* Untuk efek pop-out */
                box-shadow: 0 20px 40px rgba(0,0,0,0.5);
                border: 1px solid rgba(255,255,255,0.05);
            }
            .anime-hero-left {
                flex: 1;
                z-index: 2;
                max-width: 60%;
            }
            .anime-hero-left h1 {
                font-size: 3.5rem;
                font-weight: 900;
                text-transform: uppercase;
                margin-bottom: 1rem;
                text-shadow: 2px 2px 10px rgba(0,0,0,0.8);
                line-height: 1.1;
            }
            .anime-hero-left p {
                font-size: 1.1rem;
                color: var(--text-muted);
                margin-bottom: 2rem;
                line-height: 1.6;
            }
            .anime-hero-actions {
                display: flex;
                gap: 1rem;
            }
            .anime-btn-primary {
                background: var(--accent);
                color: #000;
                padding: 1rem 2rem;
                border-radius: 8px;
                font-weight: 800;
                text-decoration: none;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .anime-btn-primary:hover {
                transform: scale(1.05);
                box-shadow: 0 0 20px var(--accent);
            }
            .anime-btn-secondary {
                background: rgba(255,255,255,0.1);
                color: #fff;
                padding: 1rem 2rem;
                border-radius: 8px;
                font-weight: 800;
                text-decoration: none;
                border: 1px solid rgba(255,255,255,0.2);
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .anime-btn-secondary:hover {
                background: rgba(255,255,255,0.2);
                transform: scale(1.05);
            }
            .anime-hero-right {
                flex: 1;
                position: relative;
                height: 400px;
                z-index: 1;
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }
            .anime-hero-img {
                height: 120%; /* Pop-out effect */
                position: absolute;
                bottom: -10%;
                right: 0;
                object-fit: contain;
                filter: drop-shadow(0 20px 30px rgba(0,0,0,0.8));
                animation: floating 4s ease-in-out infinite;
            }

            @keyframes floating {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
                100% { transform: translateY(0px); }
            }

            .fade-in-up-1 { animation: fadeUp 0.8s ease-out forwards; animation-delay: 0.1s; opacity: 0; }
            .fade-in-up-2 { animation: fadeUp 0.8s ease-out forwards; animation-delay: 0.3s; opacity: 0; }
            .fade-in-up-3 { animation: fadeUp 0.8s ease-out forwards; animation-delay: 0.5s; opacity: 0; }

            @keyframes fadeUp {
                0% { opacity: 0; transform: translateY(30px); }
                100% { opacity: 1; transform: translateY(0); }
            }

            /* Tahap 2: Bar Pencarian & Filter Multi-Dropdown */
            .anime-filter-container {
                margin: 0 4rem 2rem 4rem;
                background: rgba(255,255,255,0.03);
                border: 1px solid rgba(255,255,255,0.05);
                border-radius: 12px;
                padding: 1.5rem;
            }
            .anime-search-row {
                display: flex;
                gap: 1rem;
                margin-bottom: 1rem;
            }
            .anime-search-input {
                flex: 1;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                background: #000;
                border: 1px solid #333;
                color: #fff;
                font-size: 1rem;
                transition: all 0.3s;
            }
            .anime-search-input:focus {
                outline: none;
                border-color: var(--accent);
                box-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
            }
            .anime-search-btn {
                background: var(--accent);
                color: #000;
                padding: 0 2.5rem;
                border-radius: 8px;
                font-weight: 800;
                border: none;
                cursor: pointer;
                transition: 0.3s;
                font-size: 1rem;
            }
            .anime-search-btn:hover { background: #fff; transform: scale(1.02); }

            .anime-dropdown-row {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
            }
            .anime-select {
                background: #000;
                border: 1px solid #333;
                color: #fff;
                padding: 0.8rem 1.2rem;
                border-radius: 6px;
                cursor: pointer;
                font-size: 0.9rem;
                min-width: 150px;
                flex: 1;
            }
            .anime-select:focus { outline: none; border-color: var(--accent); }

            /* Tahap 3: Struktur Konten Utama (Split Grid) */
            .anime-split-grid {
                display: grid;
                grid-template-columns: 70% 28%;
                gap: 2%;
                margin: 0 4rem 4rem 4rem;
                position: relative;
            }

            .anime-section-title {
                font-size: 1.5rem;
                font-weight: 800;
                border-bottom: 2px solid rgba(255,255,255,0.1);
                padding-bottom: 0.8rem;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
            }
            .anime-section-title span {
                border-bottom: 3px solid var(--accent);
                padding-bottom: 0.8rem;
                margin-bottom: -1rem; /* Align with bottom border */
            }

            /* Tahap 4: Desain Card & Sidebar List */
            .new-release-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 1.5rem;
                transition: opacity 0.3s ease;
            }
            .anime-card {
                background: #111;
                border-radius: 8px;
                overflow: hidden;
                text-decoration: none;
                color: #fff;
                transition: all 0.3s ease-in-out;
                position: relative;
                border: 1px solid transparent;
            }
            .anime-card-img-wrap {
                position: relative;
                width: 100%;
                padding-top: 150%; /* 2:3 aspect ratio */
                overflow: hidden;
            }
            .anime-card img {
                position: absolute;
                top: 0; left: 0; width: 100%; height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease-in-out;
            }
            .anime-card:hover {
                border-color: var(--accent);
                box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
                transform: translateY(-5px); /* Setara scale-105 untuk kontainer */
            }
            .anime-card:hover img {
                transform: scale(1.05); /* Membesar halus */
            }
            .anime-card-info {
                padding: 1rem;
            }
            .anime-card-title {
                font-size: 0.95rem;
                font-weight: 600;
                margin-bottom: 0.3rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .anime-card-meta {
                font-size: 0.8rem;
                color: var(--text-muted);
            }

            .trending-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            .trending-item {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 0.5rem;
                border-radius: 8px;
                text-decoration: none;
                color: #fff;
                transition: all 0.3s ease-in-out;
                background: rgba(255,255,255,0.02);
                border: 1px solid rgba(255,255,255,0.05);
            }
            .trending-item:hover {
                transform: translateX(10px); /* Bergeser mulus ke kanan (translate-x-1) */
                background: rgba(255,255,255,0.05);
                border-color: var(--accent);
            }
            .trending-num {
                font-size: 2.5rem;
                font-weight: 900;
                color: transparent;
                -webkit-text-stroke: 1px #555;
                min-width: 40px;
                text-align: center;
                transition: all 0.3s;
            }
            .trending-item:hover .trending-num {
                -webkit-text-stroke: 1px var(--accent);
                color: rgba(0, 210, 255, 0.1);
            }
            .trending-item img {
                width: 60px;
                height: 80px;
                object-fit: cover;
                border-radius: 4px;
            }
            .trending-info {
                flex: 1;
                overflow: hidden;
            }
            .trending-title {
                font-size: 0.95rem;
                font-weight: 600;
                margin-bottom: 0.2rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .trending-meta {
                font-size: 0.8rem;
                color: var(--text-muted);
            }

            .ajax-loading {
                opacity: 0.5;
                pointer-events: none;
            }

            @media (max-width: 1024px) {
                .anime-split-grid { grid-template-columns: 1fr; }
                .anime-hero { flex-direction: column; padding: 2rem; margin: 1rem; }
                .anime-hero-right { display: none; }
                .anime-hero-left { max-width: 100%; }
            }

            /* --- INLINE FILTER STYLES --- */
            .inline-filter-box {
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 1.5rem;
                padding: 1.5rem;
                margin-bottom: 2rem;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            .filter-section {
                display: flex;
                flex-direction: column;
                padding-bottom: 1.5rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
            .filter-section:last-child { border-bottom: none; padding-bottom: 0; }
            .filter-section-title {
                font-size: 1.1rem;
                font-weight: 700;
                margin-bottom: 1rem;
            }
            .filter-pill-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }
            .filter-pill input { display: none; }
            .filter-pill .pill-content {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.6rem 1.2rem;
                border-radius: 9999px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: var(--text-muted);
                background-color: var(--card-bg);
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease-in-out;
            }
            .filter-pill .check-icon { display: none; font-size: 0.75rem; }
            .filter-pill input:checked + .pill-content {
                border-color: var(--accent);
                color: var(--accent);
                background: rgba(0, 210, 255, 0.1);
                box-shadow: 0 0 12px rgba(0, 210, 255, 0.4);
            }
            .filter-pill input:checked + .pill-content .check-icon {
                display: inline-block;
                color: var(--accent);
            }
            .filter-pill .pill-content:hover {
                border-color: var(--accent-hover);
                color: var(--accent-hover);
            }
            .filter-pill .pill-content:active { transform: scale(0.95); }
            
            .dual-slider-container { padding: 0 10px; margin-bottom: 10px; }
            .slider-labels {
                display: flex; justify-content: space-between;
                margin-bottom: 1rem; font-weight: bold; font-size: 0.95rem;
            }
            .slider-track {
                position: relative; width: 100%; height: 6px;
                background: rgba(255, 255, 255, 0.1); border-radius: 3px;
            }
            .slider-fill {
                position: absolute; height: 100%; background: var(--accent); border-radius: 3px;
                left: 0%; width: 100%;
                box-shadow: 0 0 8px rgba(0, 210, 255, 0.6);
            }
            .dual-slider-container input[type="range"] {
                position: absolute; top: -7px; left: 0; width: 100%;
                appearance: none; background: none; pointer-events: none;
            }
            .dual-slider-container input[type="range"]::-webkit-slider-thumb {
                appearance: none; pointer-events: all; width: 20px; height: 20px;
                border-radius: 50%; background: var(--card-bg); border: 2px solid var(--accent);
                cursor: pointer;
                box-shadow: 0 0 12px rgba(0, 210, 255, 0.8);
                transition: all 0.3s ease-in-out;
            }
            .dual-slider-container input[type="range"]::-webkit-slider-thumb:hover {
                border-color: var(--accent-hover);
                box-shadow: 0 0 18px rgba(14, 165, 233, 1);
            }
            
            /* Custom Type Dropdown */
            .custom-type-dropdown-container {
                transition: all 0.3s ease-in-out;
            }
            .custom-type-selected {
                transition: color 0.3s ease-in-out;
            }
            .custom-type-selected:hover {
                color: var(--accent-hover);
            }
            .custom-type-options {
                background: var(--card-bg) !important;
            }
            .custom-type-options.show {
                opacity: 1 !important;
                visibility: visible !important;
                transform: translateY(10px) !important;
                border-color: var(--accent) !important;
                box-shadow: 0 10px 30px rgba(0, 210, 255, 0.15), 0 0 15px rgba(0, 210, 255, 0.3) !important;
            }
            .custom-type-selected i.rotate {
                transform: rotate(180deg);
                color: var(--accent);
                text-shadow: 0 0 8px rgba(0, 210, 255, 0.8);
            }
            .custom-type-option {
                transition: all 0.3s ease-in-out;
            }
            .custom-type-option:hover {
                background: rgba(0, 210, 255, 0.1) !important;
                color: var(--accent-hover) !important;
            }
            .custom-type-option:hover i {
                color: var(--accent-hover) !important;
            }
        </style>



        <!-- TAHAP 2: Header & Inline Filter Box -->
        <div class="anime-filter-container" style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem;">Explore Movies & TV Shows</h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">Find your next favorite watch by filtering through genres, release years, and the latest trends.</p>
                </div>
                <button onclick="toggleFilterBox()" class="anime-btn-primary" style="padding: 0.8rem 2rem; border-radius: 9999px; cursor: pointer; border: none; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; background: var(--accent); color: #000; font-weight: bold; transition: 0.3s;">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </div>

            <!-- TAHAP 1: Inline Filter Box -->
            <div class="inline-filter-box" id="inlineFilterBox" style="display: none; transition: opacity 0.3s ease;">
                <form id="ajaxFilterForm" onsubmit="event.preventDefault();">
                    <input type="hidden" name="type" id="hiddenTypeFilter" value="<?= htmlspecialchars($filters['type'] ?? 'movie') ?>">
                    
                    <!-- TAHAP 3 & 4: Genre (Pills) -->
                    <div class="filter-section">
                        <h4 class="filter-section-title">Genre</h4>
                        <div class="filter-pill-group">
                            <label class="filter-pill">
                                <input type="radio" name="genre" value="" <?= empty($filters['genre']) ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> Semua Genre</span>
                            </label>
                            <?php foreach($activeGenreMap as $id => $name): ?>
                            <label class="filter-pill">
                                <input type="radio" name="genre" value="<?= $id ?>" <?= $filters['genre'] == $id ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> <?= htmlspecialchars($name) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- TAHAP 3 & 4: Release Year (Pills) -->
                    <div class="filter-section">
                        <h4 class="filter-section-title">Release Year</h4>
                        <div class="filter-pill-group">
                            <label class="filter-pill">
                                <input type="radio" name="year" value="" <?= empty($filters['year']) ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> Semua Tahun</span>
                            </label>
                            <?php for($y = date('Y'); $y >= 2000; $y -= 2): ?>
                            <label class="filter-pill">
                                <input type="radio" name="year" value="<?= $y ?>" <?= $filters['year'] == $y ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> <?= $y ?></span>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- TAHAP 3 & 4: Language (Pills) -->
                    <div class="filter-section">
                        <h4 class="filter-section-title">Language</h4>
                        <div class="filter-pill-group">
                            <label class="filter-pill">
                                <input type="radio" name="lang" value="" <?= empty($filters['lang']) ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> Semua Bahasa</span>
                            </label>
                            <?php global $langMap; if(!empty($langMap)) foreach($langMap as $code => $name): ?>
                            <label class="filter-pill">
                                <input type="radio" name="lang" value="<?= $code ?>" <?= ($filters['lang'] ?? '') == $code ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> <?= htmlspecialchars($name) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- TAHAP 3 & 4: Sort By (Pills) -->
                    <div class="filter-section">
                        <h4 class="filter-section-title">Sort By</h4>
                        <div class="filter-pill-group">
                            <label class="filter-pill">
                                <input type="radio" name="sort" value="popularity.desc" <?= (empty($filters['sort']) || $filters['sort'] == 'popularity.desc') ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> Paling Populer</span>
                            </label>
                            <label class="filter-pill">
                                <?php $dateSortValue = ($filters['type'] ?? 'movie') === 'tv' ? 'first_air_date.desc' : 'primary_release_date.desc'; ?>
                                <input type="radio" name="sort" value="<?= $dateSortValue ?>" <?= $filters['sort'] == $dateSortValue ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> Rilis Terbaru</span>
                            </label>
                            <label class="filter-pill">
                                <input type="radio" name="sort" value="vote_average.desc" <?= $filters['sort'] == 'vote_average.desc' ? 'checked' : '' ?> onchange="triggerDebouncedAjax()">
                                <span class="pill-content"><i class="fas fa-check check-icon"></i> Rating Tertinggi</span>
                            </label>
                        </div>
                    </div>

                    <!-- TAHAP 5: Dual Thumb Slider (Rating Range) -->
                    <div class="filter-section">
                        <h4 class="filter-section-title">Minimum Rating</h4>
                        <div class="dual-slider-container">
                            <div class="slider-labels">
                                <span>5.0</span>
                                <span id="ratingLabelVal" style="color: #fff;"><?= !empty($filters['rating']) ? number_format((float)$filters['rating'], 1) : '5.0' ?></span>
                                <span>9.0</span>
                            </div>
                            <div class="slider-track">
                                <div class="slider-fill" id="sliderFill" style="width: 0%;"></div>
                                <input type="range" name="rating" min="5" max="9" step="0.5" value="<?= !empty($filters['rating']) ? $filters['rating'] : 5 ?>" oninput="updateSliderUI(this.value)" onchange="triggerDebouncedAjax()">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- TAHAP 3: Struktur Konten Utama (Split Grid) -->
        <div class="anime-split-grid">
            
            <!-- Kolom Kiri: New Release -->
            <div class="left-col">
                <div class="anime-section-title" style="overflow: visible; z-index: 50;">
                    <span class="custom-type-dropdown-container" style="position: relative; display: inline-block; cursor: pointer; user-select: none;" onclick="toggleTypeDropdown(event)">
                        <div class="custom-type-selected" style="display: flex; align-items: center; gap: 8px;">
                            <?= ($filters['type'] ?? 'movie') == 'tv' ? 'TV SHOWS' : 'MOVIES' ?>
                            <i class="fas fa-chevron-down" style="font-size: 0.8rem; color: var(--accent); transition: transform 0.3s ease;"></i>
                        </div>
                        <div class="custom-type-options" style="position: absolute; top: 100%; left: 0; background: #111; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 0.5rem 0; min-width: 150px; z-index: 999; display: flex; flex-direction: column; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                            <div class="custom-type-option <?= ($filters['type'] ?? 'movie') == 'movie' ? 'active' : '' ?>" onclick="selectTypeFilter('movie')" style="padding: 0.8rem 1.5rem; font-size: 1.1rem; color: #fff; transition: all 0.2s; display: flex; align-items: center; gap: 8px; white-space: nowrap;">
                                <i class="fas fa-film" style="color: <?= ($filters['type'] ?? 'movie') == 'movie' ? 'var(--accent)' : '#666' ?>; width: 20px; text-align: center;"></i> MOVIES
                            </div>
                            <div class="custom-type-option <?= ($filters['type'] ?? 'movie') == 'tv' ? 'active' : '' ?>" onclick="selectTypeFilter('tv')" style="padding: 0.8rem 1.5rem; font-size: 1.1rem; color: #fff; transition: all 0.2s; display: flex; align-items: center; gap: 8px; white-space: nowrap;">
                                <i class="fas fa-tv" style="color: <?= ($filters['type'] ?? 'movie') == 'tv' ? 'var(--accent)' : '#666' ?>; width: 20px; text-align: center;"></i> TV SHOWS
                            </div>
                        </div>
                    </span>
                </div>
                
                <div class="new-release-grid" id="ajaxContentArea">
                    <!-- TAHAP 4: Desain Card dengan Hover Transisi -->
                    <?php if(!empty($animeMoviesList) && is_array($animeMoviesList)): ?>
                        <?php foreach($animeMoviesList as $movie): ?>
                        <?php $itemType = ($filters['type'] ?? 'movie') === 'tv' ? 'tv' : 'movie'; ?>
                        <a href="index.php?page=details&type=<?= $itemType ?>&id=<?= $movie['id'] ?>" class="anime-card">
                            <div class="anime-card-img-wrap">
                                <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                            </div>
                            <div class="anime-card-info">
                                <div class="anime-card-title"><?= htmlspecialchars((string)$movie['title']) ?></div>
                                <div class="anime-card-meta"><i class="fas fa-star" style="color: #FCD34D;"></i> <?= htmlspecialchars((string)$movie['rating']) ?> &bull; <?= htmlspecialchars((string)$movie['year']) ?></div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                        
                        <!-- Tombol See More dinamis -->
                        <div style="grid-column: 1/-1; text-align: center; margin-top: 2.5rem; margin-bottom: 1rem;">
                            <a href="index.php?page=<?= ($filters['type'] ?? 'movie') === 'tv' ? 'tvshows' : 'movies' ?>" class="hero-btn-primary" style="display: inline-block; padding: 0.8rem 2.5rem; border-radius: 30px; font-weight: 700; text-decoration: none; font-size: 1rem; color: #000; background: var(--accent); border: 2px solid var(--accent); transition: all 0.3s ease;">
                                See More <?= ($filters['type'] ?? 'movie') === 'tv' ? 'TV Shows' : 'Movies' ?> <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="fas fa-ghost" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p>Tidak ada konten yang ditemukan berdasarkan filter tersebut.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kolom Kanan: Top Trending Sidebar -->
            <div class="right-col">
                <div class="anime-section-title">
                    <span>TOP TRENDING</span>
                </div>
                <div class="trending-list">
                    <?php 
                    $trendingForSidebar = array_slice($animeTrending ?? [], 0, 10);
                    $rank = 1;
                    foreach($trendingForSidebar as $trend): 
                    ?>
                    <a href="index.php?page=details&id=<?= $trend['id'] ?>" class="trending-item">
                        <div class="trending-num"><?= str_pad($rank++, 2, '0', STR_PAD_LEFT) ?></div>
                        <img src="<?= htmlspecialchars((string)($trend['poster_path'] ?? $trend['image'])) ?>" alt="Poster">
                        <div class="trending-info">
                            <div class="trending-title"><?= htmlspecialchars((string)$trend['title']) ?></div>
                            <div class="trending-meta"><i class="fas fa-fire" style="color: #ff3b3b;"></i> Sedang Panas</div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Integrasi Skrip AJAX Bawaan (Memperbarui Konten Tanpa Reload) -->
        <script>
        let debounceTimer;

        // Custom Type Dropdown Logic
        function toggleTypeDropdown(e) {
            e.stopPropagation();
            const options = document.querySelector('.custom-type-options');
            const icon = document.querySelector('.custom-type-selected i');
            if (options) {
                options.classList.toggle('show');
                icon.classList.toggle('rotate');
            }
        }

        function selectTypeFilter(type) {
            document.getElementById('hiddenTypeFilter').value = type;
            const selectedText = document.querySelector('.custom-type-selected');
            selectedText.innerHTML = (type === 'tv' ? 'TV SHOWS' : 'MOVIES') + ' <i class="fas fa-chevron-down" style="font-size: 0.8rem; color: var(--accent); transition: transform 0.3s ease;"></i>';
            
            // Re-bind click event to text
            triggerDebouncedAjax();
            
            // Highlight selected in dropdown
            const opts = document.querySelectorAll('.custom-type-option');
            opts.forEach(opt => {
                opt.querySelector('i').style.color = '#666';
            });
            event.currentTarget.querySelector('i').style.color = 'var(--accent)';
        }

        document.addEventListener('click', function(e) {
            const options = document.querySelector('.custom-type-options');
            const icon = document.querySelector('.custom-type-selected i');
            if(options && options.classList.contains('show')) {
                options.classList.remove('show');
                icon.classList.remove('rotate');
            }
        });

        // Toggle Filter Box Visibility
        function toggleFilterBox() {
            const box = document.getElementById('inlineFilterBox');
            if (box.style.display === 'none') {
                box.style.display = 'flex';
                // Trigger smooth fade if needed
                setTimeout(() => box.style.opacity = '1', 10);
            } else {
                box.style.display = 'none';
                box.style.opacity = '0';
            }
        }

        // Inisialisasi UI Slider saat dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const ratingInput = document.querySelector('input[name="rating"]');
            if(ratingInput) updateSliderUI(ratingInput.value);
        });

        // Update Slider UI Visual
        function updateSliderUI(val) {
            document.getElementById('ratingLabelVal').innerText = parseFloat(val).toFixed(1);
            const min = 5;
            const max = 9;
            const percent = ((val - min) / (max - min)) * 100;
            document.getElementById('sliderFill').style.width = percent + '%';
        }

        // Debounced AJAX Trigger untuk Pill/Slider
        function triggerDebouncedAjax() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                doAjaxFilter();
            }, 300); // Debounce 300ms
        }

        // Fungsi AJAX Utama
        function doAjaxFilter() {
            const form = document.getElementById('ajaxFilterForm');
            if(!form) return;
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Tambahkan transisi visual loading skeleton
            const contentArea = document.getElementById('ajaxContentArea');
            if(contentArea) contentArea.classList.add('ajax-loading');
            
            // Memanfaatkan routing bawaan dan mengambil parsial HTML, kita ambil dari page=home
            fetch('index.php?page=home&' + params.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('ajaxContentArea');
                
                if(newContent && contentArea) {
                    contentArea.innerHTML = newContent.innerHTML;
                }
                if(contentArea) contentArea.classList.remove('ajax-loading');
                
                // Perbarui URL bar di browser secara otomatis
                window.history.pushState({}, '', 'index.php?page=home&' + params.toString());
            })
            .catch(err => {
                console.error('AJAX Fetch Error:', err);
                if(contentArea) contentArea.classList.remove('ajax-loading');
            });
        }
        </script>
    </section>
    <!-- NEW ANIME LAYOUT END -->

    <!-- Recommended For You (Fitur Personal) -->
    <?php 
    $personalized = getPersonalizedRecommendations();
    if (!empty($personalized) && isset($_SESSION['user'])): 
    ?>
    <section class="container">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: none;">
            <div>
                <h2 style="color: #fff; margin-bottom: 0;"><?= translateText('recommended_for_you') ?></h2>
                <p style="margin-top: 5px; color: var(--text-muted); font-size: 0.9rem;"><?= translateText('based_on_rating') ?></p>
            </div>
            <div class="ur-nav" style="margin-bottom: 10px;">
                <button class="ur-nav-btn" onclick="document.getElementById('personalized-row').scrollBy({ left: -300, behavior: 'smooth' })"><i class="fas fa-chevron-left"></i></button>
                <button class="ur-nav-btn" onclick="document.getElementById('personalized-row').scrollBy({ left: 300, behavior: 'smooth' })"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        <div class="movie-row" id="personalized-row" style="scrollbar-width: none; -ms-overflow-style: none;">
            <style>#personalized-row::-webkit-scrollbar { display: none; }</style>
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

<!-- User Rating Section -->
<style>
.ur-section {
    margin: 3rem auto;
    color: var(--text-main);
}
.ur-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}
.ur-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0;
}
.ur-nav {
    display: flex;
    gap: 0.8rem;
}
.ur-nav-btn {
    width: 2.8rem;
    height: 2.8rem;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.2);
    background: transparent;
    color: var(--text-main);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}
.ur-nav-btn:hover {
    background: var(--accent);
    color: #000;
    border-color: var(--accent);
}
.ur-carousel {
    display: flex;
    overflow-x: auto;
    gap: 1.5rem;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 2rem;
    padding-top: 1rem;
    scroll-behavior: smooth;
}
.ur-carousel::-webkit-scrollbar {
    display: none;
}
.ur-card {
    scroll-snap-align: center;
    flex: 0 0 16rem;
    background-color: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255,255,255,0.05);
    display: flex;
    flex-direction: column;
    position: relative;
    text-decoration: none;
    color: var(--text-main);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    opacity: 0;
    transform: translateY(1.5rem);
}
.ur-card.show {
    opacity: 1;
    transform: translateY(0);
}
.ur-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 20px 25px rgba(0, 0, 0, 0.5);
    border-color: rgba(255,255,255,0.2);
}
.ur-top-bleed {
    height: 60px;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 0;
}
.ur-content {
    position: relative;
    z-index: 1;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.ur-poster {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
    border-radius: 8px;
    margin-top: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.5);
}
.ur-movie-info {
    margin-top: 1rem;
    display: flex;
    flex-direction: column;
}
.ur-movie-title {
    font-size: 1.1rem;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.4rem;
}
.ur-badge {
    display: inline-block;
    padding: 2px 8px;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 4px;
    font-size: 0.7rem;
    color: var(--text-muted);
    align-self: flex-start;
    text-transform: uppercase;
    font-weight: 600;
}
.ur-divider {
    height: 1px;
    background-color: rgba(255,255,255,0.1);
    margin: 1rem 0;
}
.ur-score-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: auto;
}
.ur-score-text-container {
    display: flex;
    flex-direction: column;
    max-width: 65%;
}
.ur-score-label {
    font-size: 0.65rem;
    color: var(--text-muted);
    letter-spacing: 0.05em;
    margin-bottom: 2px;
}
.ur-score-word {
    font-size: 0.8rem;
    font-weight: 800;
    line-height: 1.2;
}
.ur-score-sub {
    font-size: 0.6rem;
    color: var(--text-muted);
    margin-top: 2px;
}
.ur-score-box {
    width: 3.2rem;
    height: 3.2rem;
    border-radius: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.5rem;
    font-weight: 900;
    color: #000;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}
.ur-skeleton {
    animation: urPulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes urPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.ur-skel-block {
    background-color: rgba(255,255,255,0.05);
    border-radius: 4px;
}
</style>

<section class="container ur-section">
    <div class="ur-header" style="align-items: flex-end;">
        <div>
            <h2 class="ur-title" style="margin-bottom: 5px;">User Rating</h2>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Discover what audiences are saying. Explore titles based on real community scores.</p>
        </div>
        <div class="ur-nav" style="margin-bottom: 5px;">
            <button class="ur-nav-btn" onclick="scrollUserRating(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="ur-nav-btn" onclick="scrollUserRating(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
    
    <div class="ur-carousel" id="userRatingCarousel">
        <!-- Skeletons rendered by JS -->
    </div>
</section>

<script>
function scrollUserRating(direction) {
    const carousel = document.getElementById('userRatingCarousel');
    carousel.scrollBy({ left: direction * 280, behavior: 'smooth' });
}

function getURColorClass(score) {
    if (score >= 80) return '#22c55e'; // Green
    if (score >= 50) return '#eab308'; // Yellow
    return '#ef4444'; // Red
}

function getURScoreText(score) {
    if (score >= 80) return 'Universal Acclaim';
    if (score >= 50) return 'Mixed or Average';
    return 'Generally Unfavorable';
}

function renderURSkeletons() {
    const carousel = document.getElementById('userRatingCarousel');
    let html = '';
    for(let i=0; i<6; i++) {
        html += `
        <div class="ur-card ur-skeleton" style="opacity: 1; transform: translateY(0);">
            <div class="ur-content">
                <div class="ur-skel-block" style="width: 100%; aspect-ratio: 2/3; border-radius: 8px; margin-top: 10px;"></div>
                <div class="ur-movie-info">
                    <div class="ur-skel-block" style="height: 1.2rem; width: 80%; margin-bottom: 0.5rem;"></div>
                    <div class="ur-skel-block" style="height: 1rem; width: 30%;"></div>
                </div>
                <div class="ur-divider"></div>
                <div class="ur-score-section">
                    <div class="ur-score-text-container" style="flex: 1;">
                        <div class="ur-skel-block" style="height: 0.6rem; width: 60%; margin-bottom: 4px;"></div>
                        <div class="ur-skel-block" style="height: 0.8rem; width: 80%;"></div>
                    </div>
                    <div class="ur-skel-block" style="width: 3.2rem; height: 3.2rem; border-radius: 8px;"></div>
                </div>
            </div>
        </div>`;
    }
    carousel.innerHTML = html;
}

function fetchUserRatings() {
    renderURSkeletons();
    const apiKey = 'ac2e690e071692fe9f8e181d6370f6c7';
    
    // Fetch 3 kategori rating untuk menjamin keberagaman warna (Merah, Kuning, Hijau)
    const reqLow = fetch(`https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=en-US&sort_by=popularity.desc&vote_average.gte=1.0&vote_average.lte=4.9&vote_count.gte=150&page=1`).then(r => r.json());
    const reqMed = fetch(`https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=en-US&sort_by=popularity.desc&vote_average.gte=5.0&vote_average.lte=7.5&vote_count.gte=300&page=1`).then(r => r.json());
    const reqHigh = fetch(`https://api.themoviedb.org/3/discover/movie?api_key=${apiKey}&language=en-US&sort_by=popularity.desc&vote_average.gte=8.0&vote_count.gte=500&page=1`).then(r => r.json());
    
    Promise.all([reqLow, reqMed, reqHigh])
        .then(([lowData, medData, highData]) => {
            let combined = [];
            // Ambil 5 film paling populer dari masing-masing kategori
            if(lowData.results) combined.push(...lowData.results.slice(0, 5));
            if(medData.results) combined.push(...medData.results.slice(0, 5));
            if(highData.results) combined.push(...highData.results.slice(0, 5));
            
            // Urutkan dari rating terendah ke tertinggi sesuai permintaan
            combined.sort((a, b) => a.vote_average - b.vote_average);
            
            const carousel = document.getElementById('userRatingCarousel');
            carousel.innerHTML = '';
            
            if (combined.length > 0) {
                combined.forEach((movie, index) => {
                    const score = Math.round(movie.vote_average * 10);
                    const colorHex = getURColorClass(score);
                    const scoreText = getURScoreText(score);
                    const year = movie.release_date ? movie.release_date.split('-')[0] : '';
                    const posterUrl = movie.poster_path ? `https://image.tmdb.org/t/p/w500${movie.poster_path}` : 'https://via.placeholder.com/500x750?text=No+Poster';
                    
                    const card = document.createElement('a');
                    card.href = `index.php?page=details&type=movie&id=${movie.id}`;
                    card.className = 'ur-card';
                    card.style.transitionDelay = `${index * 80}ms`;
                    
                    card.innerHTML = `
                        <div class="ur-top-bleed" style="background-color: ${colorHex};"></div>
                        <div class="ur-content">
                            <img src="${posterUrl}" alt="${movie.title}" class="ur-poster" loading="lazy">
                            <div class="ur-movie-info">
                                <div class="ur-movie-title" title="${movie.title}">${movie.title}</div>
                                <div class="ur-badge">Movie${year ? ' • '+year : ''}</div>
                            </div>
                            <div class="ur-divider"></div>
                            <div class="ur-score-section">
                                <div class="ur-score-text-container">
                                    <span class="ur-score-label">TMDB SCORE</span>
                                    <span class="ur-score-word">${scoreText}</span>
                                    <span class="ur-score-sub">Based on user ratings</span>
                                </div>
                                <div class="ur-score-box" style="background-color: ${colorHex};">
                                    ${score}
                                </div>
                            </div>
                        </div>
                    `;
                    carousel.appendChild(card);
                    
                    setTimeout(() => {
                        card.classList.add('show');
                    }, 50);
                });
            } else {
                carousel.innerHTML = '<p style="text-align:center; width:100%; color:var(--text-muted);">Gagal memuat data rating.</p>';
            }
        })
        .catch(err => {
            console.error("Failed to fetch User Ratings:", err);
            document.getElementById('userRatingCarousel').innerHTML = '<p style="text-align:center; width:100%; color:var(--text-muted);">Gagal memuat data rating.</p>';
        });
}

document.addEventListener('DOMContentLoaded', fetchUserRatings);
</script>


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