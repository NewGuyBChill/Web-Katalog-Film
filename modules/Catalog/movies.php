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
$moviesList = discoverMovies($filters, 30, $currentPage); 

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
    <style>
        /* INLINE FILTER STYLES */
        .anime-filter-container {
            margin-bottom: 2rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 1.5rem;
        }
        .anime-btn-primary {
            background: var(--accent); color: #000; padding: 0.8rem 2rem; border-radius: 9999px; font-weight: 800; cursor: pointer; border: none; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem; transition: 0.3s;
        }
        .anime-btn-primary:hover {
            transform: scale(1.05); box-shadow: 0 0 20px var(--accent);
        }
        .inline-filter-box {
            background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 1.5rem; padding: 1.5rem; margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;
        }
        .filter-section {
            display: flex; flex-direction: column; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .filter-section:last-child { border-bottom: none; padding-bottom: 0; }
        .filter-section-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem; color: #fff; }
        .filter-pill-group { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .filter-pill input { display: none; }
        .filter-pill .pill-content {
            display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border-radius: 9999px; border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text-muted); background-color: var(--card-bg); font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease-in-out;
        }
        .filter-pill .check-icon { display: none; font-size: 0.75rem; }
        .filter-pill input:checked + .pill-content {
            border-color: var(--accent); color: var(--accent); background: rgba(0, 210, 255, 0.1); box-shadow: 0 0 12px rgba(0, 210, 255, 0.4);
        }
        .filter-pill input:checked + .pill-content .check-icon { display: inline-block; color: var(--accent); }
        .filter-pill .pill-content:hover { border-color: var(--accent-hover); color: var(--accent-hover); }
        .filter-pill .pill-content:active { transform: scale(0.95); }
        
        .dual-slider-container { padding: 0 10px; margin-bottom: 10px; }
        .slider-labels { display: flex; justify-content: space-between; margin-bottom: 1rem; font-weight: bold; font-size: 0.95rem; }
        .slider-track { position: relative; width: 100%; height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 3px; }
        .slider-fill { position: absolute; height: 100%; background: var(--accent); border-radius: 3px; left: 0%; width: 100%; box-shadow: 0 0 8px rgba(0, 210, 255, 0.6); }
        .dual-slider-container input[type="range"] { position: absolute; top: -7px; left: 0; width: 100%; appearance: none; background: none; pointer-events: none; }
        .dual-slider-container input[type="range"]::-webkit-slider-thumb {
            appearance: none; pointer-events: all; width: 20px; height: 20px; border-radius: 50%; background: var(--card-bg); border: 2px solid var(--accent); cursor: pointer; box-shadow: 0 0 12px rgba(0, 210, 255, 0.8); transition: all 0.3s ease-in-out;
        }
        .dual-slider-container input[type="range"]::-webkit-slider-thumb:hover { border-color: var(--accent-hover); box-shadow: 0 0 18px rgba(14, 165, 233, 1); }

        /* GRID & CARDS */
        .new-release-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .anime-card {
            background: #111; border-radius: 8px; overflow: hidden; text-decoration: none; color: #fff; transition: all 0.3s ease-in-out; border: 1px solid transparent;
        }
        .anime-card-img-wrap { position: relative; width: 100%; padding-top: 150%; overflow: hidden; }
        .anime-card img {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease-in-out;
        }
        .anime-card:hover { border-color: var(--accent); box-shadow: 0 0 15px rgba(0, 210, 255, 0.3); transform: translateY(-5px); }
        .anime-card:hover img { transform: scale(1.05); }
        .anime-card-info { padding: 1rem; }
        .anime-card-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 0.3rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .anime-card-meta { font-size: 0.8rem; color: var(--text-muted); }
    </style>

    <div class="anime-filter-container">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 2rem; font-weight: 900; margin-bottom: 0.5rem; color: #fff;"><?= $pageTitle ?></h2>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;"><?= $pageDesc ?></p>
            </div>
            <button type="button" onclick="toggleFilterBox()" class="anime-btn-primary">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>

        <?php $hasFilters = !empty($filters['genre']) || !empty($filters['year']) || !empty($filters['lang']) || !empty($filters['rating']) || !empty($filters['sort']); ?>
        <div class="inline-filter-box" id="inlineFilterBox" style="display: <?= $hasFilters ? 'flex' : 'none' ?>; transition: opacity 0.3s ease;">
            <form id="moviesFilterForm" action="index.php" method="GET">
                <input type="hidden" name="page" value="movies">
                <?php if(!empty($filters['category'])): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($filters['category']) ?>">
                <?php endif; ?>

                <!-- Genre -->
                <div class="filter-section">
                    <h4 class="filter-section-title">Genre</h4>
                    <div class="filter-pill-group">
                        <label class="filter-pill">
                            <input type="radio" name="genre" value="" <?= empty($filters['genre']) ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> Semua Genre</span>
                        </label>
                        <?php foreach($genreMap as $id => $name): ?>
                        <label class="filter-pill">
                            <input type="radio" name="genre" value="<?= $id ?>" <?= $filters['genre'] == $id ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> <?= htmlspecialchars($name) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Release Year -->
                <div class="filter-section">
                    <h4 class="filter-section-title">Release Year</h4>
                    <div class="filter-pill-group">
                        <label class="filter-pill">
                            <input type="radio" name="year" value="" <?= empty($filters['year']) ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> Semua Tahun</span>
                        </label>
                        <?php for($y = date('Y'); $y >= 2000; $y -= 2): ?>
                        <label class="filter-pill">
                            <input type="radio" name="year" value="<?= $y ?>" <?= $filters['year'] == $y ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> <?= $y ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Language -->
                <div class="filter-section">
                    <h4 class="filter-section-title">Language</h4>
                    <div class="filter-pill-group">
                        <label class="filter-pill">
                            <input type="radio" name="lang" value="" <?= empty($filters['lang']) ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> Semua Bahasa</span>
                        </label>
                        <?php if(!empty($langMap)) foreach($langMap as $code => $name): ?>
                        <label class="filter-pill">
                            <input type="radio" name="lang" value="<?= $code ?>" <?= ($filters['lang'] ?? '') == $code ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> <?= htmlspecialchars($name) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sort By -->
                <div class="filter-section">
                    <h4 class="filter-section-title">Sort By</h4>
                    <div class="filter-pill-group">
                        <label class="filter-pill">
                            <input type="radio" name="sort" value="" <?= empty($filters['sort']) || $filters['sort'] == 'popularity.desc' ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> Paling Populer</span>
                        </label>
                        <label class="filter-pill">
                            <input type="radio" name="sort" value="primary_release_date.desc" <?= $filters['sort'] == 'primary_release_date.desc' ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> Rilis Terbaru</span>
                        </label>
                        <label class="filter-pill">
                            <input type="radio" name="sort" value="vote_average.desc" <?= $filters['sort'] == 'vote_average.desc' ? 'checked' : '' ?> onchange="document.getElementById('moviesFilterForm').submit()">
                            <span class="pill-content"><i class="fas fa-check check-icon"></i> Rating Tertinggi</span>
                        </label>
                    </div>
                </div>

                <!-- Minimum Rating Slider -->
                <div class="filter-section">
                    <h4 class="filter-section-title">Minimum Rating</h4>
                    <div class="dual-slider-container">
                        <div class="slider-labels">
                            <span>5.0</span>
                            <span id="ratingLabelVal" style="color: #fff;"><?= !empty($filters['rating']) ? number_format((float)$filters['rating'], 1) : '5.0' ?></span>
                            <span>9.0</span>
                        </div>
                        <div class="slider-track">
                            <div class="slider-fill" id="sliderFill" style="width: <?= !empty($filters['rating']) ? (($filters['rating'] - 5) / 4) * 100 : 0 ?>%;"></div>
                        </div>
                        <input type="range" name="rating" min="5" max="9" step="0.5" value="<?= !empty($filters['rating']) ? $filters['rating'] : 5 ?>" 
                               onchange="document.getElementById('moviesFilterForm').submit()"
                               oninput="document.getElementById('ratingLabelVal').innerText = parseFloat(this.value).toFixed(1); document.getElementById('sliderFill').style.width = ((this.value - 5) / 4) * 100 + '%';">
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="new-release-grid">
        <?php if(!empty($moviesList) && is_array($moviesList)): ?>
            <?php foreach($moviesList as $movie): ?>
            <a href="index.php?page=details&type=movie&id=<?= $movie['id'] ?>" class="anime-card">
                <div class="anime-card-img-wrap">
                    <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                </div>
                <div class="anime-card-info">
                    <div class="anime-card-title"><?= htmlspecialchars((string)$movie['title']) ?></div>
                    <div class="anime-card-meta"><i class="fas fa-star" style="color: #FCD34D;"></i> <?= htmlspecialchars((string)$movie['rating']) ?> &bull; <?= htmlspecialchars((string)$movie['year']) ?></div>
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
            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                <i class="fas fa-ghost" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <p>Tidak ada film yang ditemukan berdasarkan filter tersebut.</p>
                <a href="index.php?page=movies" class="anime-btn-primary" style="margin-top: 1rem; display: inline-block;">
                    <i class="fas fa-sync-alt"></i> Reset Filter
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
    function toggleFilterBox() {
        const box = document.getElementById('inlineFilterBox');
        if (box.style.display === 'none' || box.style.display === '') {
            box.style.display = 'flex';
            setTimeout(() => box.style.opacity = '1', 10);
        } else {
            box.style.opacity = '0';
            setTimeout(() => box.style.display = 'none', 300);
        }
    }
    </script>
</main>