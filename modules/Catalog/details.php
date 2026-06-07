<?php
require_once __DIR__ . '/../../config/data.php';
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$type = isset($_GET['type']) && $_GET['type'] === 'tv' ? 'tv' : 'movie';
$movie = getMediaDetails($id, $type);

if (empty($movie) || (isset($movie['success']) && $movie['success'] === false)) {
    echo "<main class='container' style='padding-top: 150px; text-align: center; min-height: 70vh;'>
            <i class='fas fa-exclamation-triangle' style='font-size: 4rem; color: #ff3b3b; margin-bottom: 20px;'></i>
            <h2>Film / TV Show Tidak Ditemukan</h2>
            <p style='color: var(--text-muted); margin-top: 10px;'>Data tidak tersedia, ID salah, atau parameter tipe media tidak sesuai.</p>
            <a href='index.php?page=home' class='btn-primary' style='margin-top: 20px; display: inline-flex;'><i class='fas fa-home'></i> Kembali ke Beranda</a>
          </main>";
    return;
}

$backdrop = !empty($movie['backdrop_path']) ? "https://image.tmdb.org/t/p/original" . $movie['backdrop_path'] : "";
$poster = !empty($movie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22500%22%20height%3D%22750%22%20viewBox%3D%220%200%20500%20750%22%3E%3Crect%20width%3D%22500%22%20height%3D%22750%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E";
$title = $movie['title'] ?? $movie['original_title'] ?? $movie['name'] ?? 'Unknown';
$overview = $movie['overview'] ?? translateText('no_synopsis');
$rating = isset($movie['vote_average']) ? round($movie['vote_average'], 1) : 0;
$release_date = isset($movie['release_date']) ? $movie['release_date'] : ($movie['first_air_date'] ?? "-");
$runtime = isset($movie['runtime']) && $movie['runtime'] > 0 ? $movie['runtime'] . " min" : (isset($movie['episode_run_time'][0]) ? $movie['episode_run_time'][0] . " min/ep" : "N/A");

$extraInfo = "";
if ($type === 'tv') {
    $seasons = $movie['number_of_seasons'] ?? 0;
    $episodes = $movie['number_of_episodes'] ?? 0;
    if ($seasons > 0) $extraInfo = " &bull; $seasons " . translateText('seasons') . " ($episodes " . translateText('episodes') . ")";
}

// Format daftar Genre
$genresList = "";
if (!empty($movie['genres'])) {
    $genresList = implode(', ', array_map(function($g) { return $g['name']; }, $movie['genres']));
}

// Cari video trailer dari YouTube (jika ada)
$trailerUrl = "#";
if (!empty($movie['videos']['results'])) {
    foreach ($movie['videos']['results'] as $video) {
        if ($video['site'] === 'YouTube' && ($video['type'] === 'Trailer' || $video['type'] === 'Teaser')) {
            $trailerUrl = "https://www.youtube.com/watch?v=" . $video['key'];
            break; // Ambil trailer pertama yang ditemukan
        }
    }
}
?>

<?php
$userReview = null;
$userFavCasts = [];
$userPlaylists = [];
$dbPath = __DIR__ . '/../../config/db.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
    global $conn;
    if ($conn && isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        $checkRev = $conn->query("SELECT rating, review_text FROM reviews WHERE user_id = $uid AND media_id = " . intval($id) . " AND media_type = '" . $conn->real_escape_string($type) . "'");
        if ($checkRev && $checkRev->num_rows > 0) {
            $userReview = $checkRev->fetch_assoc();
        }
        
        // Ambil daftar ID pemeran favorit milik user ini
        $resFavCasts = $conn->query("SELECT cast_id FROM favorite_casts WHERE user_id = $uid");
        if ($resFavCasts) {
            while ($rowCast = $resFavCasts->fetch_assoc()) {
                $userFavCasts[] = $rowCast['cast_id'];
            }
        }
        
        // Ambil daftar Playlist kustom milik user ini
        $resLists = $conn->query("SELECT id, name FROM custom_playlists WHERE user_id = $uid ORDER BY name ASC");
        if ($resLists) {
            while ($rowList = $resLists->fetch_assoc()) {
                $userPlaylists[] = $rowList;
            }
        }
    }
}

// Extract metadata
$directors = [];
$writers = [];
if (!empty($movie['credits']['crew'])) {
    foreach ($movie['credits']['crew'] as $crew) {
        if ($crew['job'] === 'Director' || $crew['job'] === 'Executive Producer') {
            $directors[] = $crew['name'];
        }
        if ($crew['department'] === 'Writing') {
            $writers[] = $crew['name'];
        }
    }
}
$directorText = !empty($directors) ? implode(', ', array_unique($directors)) : '-';
$writerText = !empty($writers) ? implode(', ', array_unique($writers)) : '-';

$countries = [];
if (!empty($movie['production_countries'])) {
    foreach ($movie['production_countries'] as $c) {
        $countries[] = $c['name'];
    }
}
$countryText = !empty($countries) ? implode(', ', $countries) : '-';

$languages = [];
if (!empty($movie['spoken_languages'])) {
    foreach ($movie['spoken_languages'] as $l) {
        $languages[] = $l['english_name'];
    }
}
$languageText = !empty($languages) ? implode(', ', $languages) : '-';
$year = isset($release_date) && strlen((string)$release_date) >= 4 ? substr($release_date, 0, 4) : "-";

$popularity = isset($movie['popularity']) ? number_format($movie['popularity']) : '0';
?>

<style>
/* Reset and hide global header/footer */
/* Removed: #mainNavbar, .site-footer { display: none !important; } */

/* Custom Base Styles */
body {
    background-color: #0d0d0d;
    color: #fff;
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
}

/* Tahap 1: Navbar (Removed as we use the default navbar) */

/* Tahap 2: Hero Banner */
#playlistDropdown.show { display: block !important; }

.detail-hero-wrapper {
    position: relative;
    width: 100%;
    height: 100vh;
    min-height: 100vh;
    overflow: hidden;
}
.detail-hero-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    z-index: 1;
}
.detail-hero-video {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100vw;
    height: 56.25vw; /* 16:9 Aspect Ratio */
    min-height: 100vh;
    min-width: 177.77vh; /* 16:9 Aspect Ratio */
    transform: translate(-50%, -50%);
    pointer-events: none;
    z-index: 1;
}
.detail-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(13,13,13,0.3) 0%, rgba(13,13,13,0.6) 60%, #0d0d0d 100%);
    z-index: 2;
}
.detail-hero-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 3rem 4rem;
    z-index: 3;
}
.detail-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    transition: all 0.3s ease;
    z-index: 10;
}
.detail-play-btn:hover { transform: translate(-50%, -50%) scale(1.1); background: white; }
.detail-play-btn i { color: #ff3b3b; font-size: 2rem; margin-left: 5px; }

.hero-bottom-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    z-index: 10;
    position: relative;
    padding-bottom: 2rem;
}
.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 10px rgba(0,0,0,0.8);
}
.hero-year {
    font-size: 1.5rem;
    font-weight: 400;
    opacity: 0.8;
}
.hero-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}
.action-btn {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    backdrop-filter: blur(5px);
    transition: 0.3s;
    text-decoration: none;
}
.action-btn:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }
.imdb-badge { background: #f5c518; color: black; padding: 2px 6px; border-radius: 4px; font-weight: 900; font-size: 0.75rem; letter-spacing: 1px; }

/* Tahap 3: Pembagian Layout Utama */
.detail-grid-container {
    display: flex;
    gap: 3rem;
    padding: 0 4rem 4rem 4rem;
    max-width: 1600px;
    margin: 0 auto;
    position: relative;
    z-index: 20;
    margin-top: -3rem;
}
.detail-col-left {
    flex: 0 0 250px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.detail-poster-img {
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    margin-bottom: 1rem;
}
.vertical-btn {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
    padding: 0.8rem;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: 0.3s;
}
.vertical-btn:hover { background: rgba(255,255,255,0.15); }
.comments-btn {
    width: 100%;
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    padding: 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: 0.3s;
    margin-top: 1rem;
}
.comments-btn:hover { background: rgba(255,255,255,0.2); }

.detail-col-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding-top: 4rem;
}
.movie-title-small { font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; gap: 15px; margin-bottom: 0.5rem; }
.movie-tagline { font-size: 1.1rem; color: #ccc; margin-bottom: 1.5rem; }
.movie-metadata-row { display: flex; align-items: center; gap: 15px; font-size: 0.85rem; color: #aaa; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1.5rem; }
.movie-metadata-row i.fa-star { color: #f5c518; }
.award-badge { border: 1px solid rgba(255,255,255,0.3); padding: 3px 8px; border-radius: 4px; letter-spacing: 1px; font-size: 0.75rem; color: #fff; }

.split-content {
    display: flex;
    gap: 3rem;
}
.details-list, .cast-list { flex: 1; }
.section-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; }
.detail-item { font-size: 0.9rem; margin-bottom: 0.8rem; color: #aaa; }
.detail-item strong { color: #fff; font-weight: 500; display: inline-block; width: 110px; }
.detail-item a { color: #aaa; text-decoration: none; }
.detail-item a:hover { color: white; }

.cast-item { display: flex; align-items: center; gap: 15px; margin-bottom: 1rem; }
.cast-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.cast-info { display: flex; flex-direction: column; }
.cast-name { font-size: 0.95rem; font-weight: 500; color: #fff; }
.cast-role { font-size: 0.8rem; color: #aaa; }
.show-more-text { color: #aaa; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px; margin-top: 1rem; text-decoration: none; }

.media-column { flex: 0 0 250px; }
.gallery-main { width: 100%; height: 140px; border-radius: 8px; position: relative; margin-bottom: 10px; overflow: hidden; }
.gallery-main img { width: 100%; height: 100%; object-fit: cover; }
.gallery-play { position: absolute; inset: 0; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.4); cursor: pointer; }
.gallery-play i { font-size: 2rem; color: white; opacity: 0.8; transition: 0.3s; }
.gallery-play:hover i { opacity: 1; transform: scale(1.1); }
.gallery-thumbs { display: flex; gap: 10px; margin-bottom: 1rem; }
.gallery-thumbs img { width: calc(33.333% - 6.66px); height: 50px; border-radius: 4px; object-fit: cover; cursor: pointer; }
.soundtrack-btn { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 1rem; text-align: center; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: 0.3s; width: 100%; display: block; color: white; text-decoration: none; }
.soundtrack-btn:hover { background: rgba(255,255,255,0.1); }

.storyline-section { margin-top: 3rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; }
.storyline-text { font-size: 0.95rem; line-height: 1.8; color: #aaa; }

/* Tahap 4: Similar Movies */
.similar-section { padding: 0 4rem 4rem 4rem; max-width: 1600px; margin: 0 auto; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 2rem; padding-top: 3rem; }
.similar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.similar-title { font-size: 1.2rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
.similar-filter { font-size: 0.85rem; color: #aaa; cursor: pointer; }
.similar-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.5rem; }
.similar-card { display: flex; flex-direction: column; gap: 0.8rem; text-decoration: none; }
.similar-card img { width: 100%; aspect-ratio: 2/3; object-fit: cover; border-radius: 8px; transition: transform 0.3s; }
.similar-card:hover img { transform: scale(1.05); }
.similar-info { display: flex; flex-direction: column; gap: 4px; }
.similar-name { color: white; font-size: 0.95rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.similar-sub { color: #aaa; font-size: 0.8rem; }
.similar-rating { display: flex; align-items: center; gap: 5px; color: #aaa; font-size: 0.8rem; margin-top: 5px; }
.similar-rating i { color: #f5c518; }
.similar-rating span { font-size: 0.7rem; letter-spacing: 2px; }
.btn-load-more { width: 100%; padding: 1rem; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); color: white; font-size: 0.9rem; font-weight: 600; border-radius: 8px; margin-top: 3rem; cursor: pointer; transition: 0.3s; }
.btn-load-more:hover { background: rgba(255,255,255,0.05); }
</style>

<!-- Tahap 2: Hero Banner Sinematik -->
<div class="detail-hero-wrapper">
    <div class="detail-hero-bg" style="background-image: <?= $backdrop ? "url('$backdrop')" : "url('$poster')" ?>;"></div>
    <?php if ($trailerUrl !== "#" && strpos($trailerUrl, 'youtube.com/watch?v=') !== false): 
        $ytKey = str_replace('https://www.youtube.com/watch?v=', '', $trailerUrl);
    ?>
    <iframe class="detail-hero-video" src="https://www.youtube.com/embed/<?= $ytKey ?>?autoplay=1&mute=1&controls=0&loop=1&playlist=<?= $ytKey ?>&modestbranding=1&showinfo=0&rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    <?php endif; ?>
    <div class="detail-hero-overlay"></div>
    <div class="detail-hero-content">
        
        <div class="hero-bottom-info">
            <div>
                <h1 class="hero-title"><?= htmlspecialchars($title) ?> <span class="hero-year">(<?= $year ?>)</span></h1>
            </div>
            <div class="hero-actions">
                <div class="action-btn" style="cursor: default;"><i class="fas fa-star" style="color: #f5c518;"></i> <?= $rating ?></div>
                <button class="action-btn watchlist-btn-detail" data-id="<?= $id ?>" data-type="<?= $type ?>" onclick="toggleWatchlistDetail(event, this, '<?= $id ?>', '<?= $type ?>', '<?= addslashes(htmlspecialchars($title)) ?>', '<?= $poster ?>')">
                    <i class="fas fa-heart"></i> <span class="btn-text">ADD TO WATCHLIST</span>
                </button>
                <a href="javascript:void(0)" class="action-btn" onclick="alert('Feature coming soon!')"><i class="fas fa-user-plus"></i> Invite Friends</a>
            </div>
        </div>
    </div>
</div>

<!-- Tahap 3: Pembagian Layout Utama (Struktur Multi-Kolom Tengah) -->
<div class="detail-grid-container">
    
    <!-- Kolom Kiri -->
    <div class="detail-col-left">
        <img src="<?= $poster ?>" alt="<?= htmlspecialchars($title) ?>" class="detail-poster-img">
        
        <button class="vertical-btn watchlist-btn-detail" data-id="<?= $id ?>" data-type="<?= $type ?>" onclick="toggleWatchlistDetail(event, this, '<?= $id ?>', '<?= $type ?>', '<?= addslashes(htmlspecialchars($title)) ?>', '<?= $poster ?>')">
            <i class="fas fa-heart"></i> <span class="btn-text">ADD TO WATCHLIST</span>
        </button>
        <div style="position: relative;">
            <button class="vertical-btn playlist-btn-detail" id="mainPlaylistBtn" onclick="document.getElementById('playlistDropdown').classList.toggle('show')">
                <i class="fas fa-plus"></i> <span class="btn-text">ADD TO PLAYLIST</span>
            </button>
            <div id="playlistDropdown" class="custom-dropdown dropdown-menu-list" style="position: absolute; top: 100%; left: 0; min-width: 250px; z-index: 100; display: none; background: #111; border: 1px solid #333; border-radius: 8px; padding: 10px;">
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=login" style="color: var(--text-muted); display:block; padding:10px;">Login untuk daftar</a>
                <?php else: ?>
                    <div id="playlistContainer">
                        <?php if(empty($userPlaylists)): ?>
                            <div style="color: var(--text-muted); padding:10px; font-size:0.9rem;" id="emptyPlaylistMsg">Belum ada playlist.</div>
                        <?php else: ?>
                            <?php foreach($userPlaylists as $pl): ?>
                                <a href="javascript:void(0)" onclick="addToPlaylist(<?= $pl['id'] ?>, <?= $id ?>, '<?= $type ?>', '<?= addslashes(htmlspecialchars($title)) ?>', '<?= $poster ?>', this)" style="display:block; padding:10px; color: white; text-decoration: none; border-bottom: 1px solid #222;">
                                    <i class="fas fa-folder" style="color: #00d2ff; margin-right: 5px;"></i> <?= htmlspecialchars($pl['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #333;">
                        <input type="text" id="newPlaylistName" placeholder="Nama playlist baru..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #444; background: #222; color: white; margin-bottom: 8px; box-sizing: border-box;">
                        <button onclick="createNewPlaylist(<?= $id ?>, '<?= $type ?>', '<?= addslashes(htmlspecialchars($title)) ?>', '<?= $poster ?>')" style="width: 100%; padding: 8px; border-radius: 4px; border: none; background: #ff3b3b; color: white; font-weight: bold; cursor: pointer;">Buat & Tambahkan</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <button class="comments-btn" onclick="document.getElementById('reviewsSection').scrollIntoView({behavior: 'smooth'})">
            <i class="fas fa-comment"></i> Show Comments
        </button>
    </div>
    
    <!-- Kolom Kanan/Tengah -->
    <div class="detail-col-right">
        <!-- Baris 1: Sinopsis Singkat & Rating -->
        <h2 class="movie-title-small"><?= htmlspecialchars($title) ?> <span class="award-badge" style="font-size: 0.9rem; padding: 4px 8px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);"><i class="fas fa-star" style="color: #f5c518;"></i> <?= $rating ?></span></h2>
        <p class="movie-tagline"><?= !empty($movie['tagline']) ? htmlspecialchars($movie['tagline']) : 'An amazing ' . ($type == 'tv' ? 'TV Show' : 'Movie') . ' to watch.' ?></p>
        
        <div class="movie-metadata-row">
            <span><i class="far fa-eye"></i> <?= $popularity ?></span>
            <span>
                <?php for($i=0; $i<5; $i++): ?>
                    <?= $i < round($rating/2) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>' ?>
                <?php endfor; ?>
            </span>
            <span>PG-13</span>
            <span><?= $runtime ?></span>
            <span><?= htmlspecialchars($genresList) ?></span>
            <span><?= $year ?></span>
            <span class="award-badge">OSCARS</span>
        </div>
        
        <!-- Baris 2 & 3: Split Grid -->
        <div class="split-content">
            <!-- Kolom Details -->
            <div class="details-list">
                <h3 class="section-title">Details</h3>
                <div class="detail-item"><strong>Director:</strong> <?= htmlspecialchars($directorText) ?></div>
                <div class="detail-item"><strong>Writers:</strong> <?= htmlspecialchars($writerText) ?></div>
                <div class="detail-item"><strong>Country:</strong> <?= htmlspecialchars($countryText) ?></div>
                <div class="detail-item"><strong>Language:</strong> <?= htmlspecialchars($languageText) ?></div>
                <div class="detail-item"><strong>Release Date:</strong> <?= $release_date ?></div>
                <div class="detail-item"><strong>Also Known As:</strong> <?= htmlspecialchars($title) ?></div>
            </div>
            
            <!-- Kolom Cast -->
            <div class="cast-list">
                <h3 class="section-title">Cast</h3>
                <?php 
                if (!empty($movie['credits']['cast'])): 
                    $topCasts = array_slice($movie['credits']['cast'], 0, 4);
                    foreach($topCasts as $cast): 
                        $castImg = !empty($cast['profile_path']) ? "https://image.tmdb.org/t/p/w200" . $cast['profile_path'] : "https://via.placeholder.com/40";
                ?>
                <div class="cast-item">
                    <a href="index.php?page=person&id=<?= $cast['id'] ?>" style="text-decoration: none; display: flex; align-items: center; gap: 15px; width: 100%; padding: 5px; border-radius: 8px; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                        <img src="<?= $castImg ?>" class="cast-avatar" alt="Cast">
                        <div class="cast-info">
                            <span class="cast-name"><?= htmlspecialchars((string)$cast['name']) ?></span>
                            <span class="cast-role"><?= htmlspecialchars((string)$cast['character']) ?></span>
                        </div>
                        <i class="fas fa-info-circle" style="margin-left: auto; color: rgba(255,255,255,0.3);"></i>
                    </a>
                </div>
                <?php 
                    endforeach; 
                endif; 
                ?>
                <?php if(!empty($movie['credits']['cast']) && count($movie['credits']['cast']) > 4): ?>
                    <a href="javascript:void(0)" class="show-more-text"><i class="fas fa-users"></i> Full Cast & Crew</a>
                <?php endif; ?>
            </div>
            
            <!-- Kolom Media -->
            <div class="media-column">
                <h3 class="section-title">Gallery</h3>
                <div class="gallery-main" onclick="openTrailerModal('<?= $trailerUrl ?>')">
                    <img src="<?= !empty($movie['backdrop_path']) ? 'https://image.tmdb.org/t/p/w500'.$movie['backdrop_path'] : $poster ?>" alt="Trailer">
                    <div class="gallery-play">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
                <div class="gallery-thumbs">
                    <img src="<?= $poster ?>" alt="Thumb">
                    <img src="<?= !empty($movie['backdrop_path']) ? 'https://image.tmdb.org/t/p/w500'.$movie['backdrop_path'] : $poster ?>" alt="Thumb">
                    <img src="<?= $poster ?>" alt="Thumb">
                </div>
                <a href="javascript:void(0)" class="soundtrack-btn"><i class="fas fa-music"></i> Soundtracks</a>
            </div>
        </div>
        
        <!-- Baris 4: Storyline -->
        <div class="storyline-section">
            <h3 class="section-title">Storyline</h3>
            <p class="storyline-text">
                <?= nl2br(htmlspecialchars($overview)) ?>
            </p>
        </div>
        
        <!-- Bagian User Review (Restored) -->
        <div id="reviewsSection" style="margin-top: 4rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem;">
            <h3 class="section-title">User Reviews</h3>
            
            <div class="review-box" style="margin-bottom: 2rem;">
                <h4 id="reviewFormTitle" style="margin-bottom: 1.2rem; font-size: 1rem; font-weight: 600;"><?= $userReview ? 'Edit Review' : 'Write a Review' ?></h4>
                <form id="reviewForm" style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div id="starRating" style="color: #f5c518; font-size: 1.2rem; cursor: pointer; letter-spacing: 5px; transition: 0.2s;" data-rating="<?= $userReview ? $userReview['rating'] : 0 ?>">
                            <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                        </div>
                    </div>
                    <textarea id="reviewText" placeholder="What did you think of this movie?" rows="4" style="width: 100%; padding: 1rem; border-radius: 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; resize: vertical; font-family: inherit; font-size: 0.9rem; outline: none; box-sizing: border-box;"><?= $userReview ? htmlspecialchars($userReview['review_text']) : '' ?></textarea>
                    <button type="submit" id="submitReviewBtn" class="action-btn" style="align-self: flex-start; padding: 0.6rem 2rem; border-radius: 30px; background: white; color: black; border: none; font-weight: 700;"><?= $userReview ? 'Update Review' : 'Submit Review' ?></button>
                </form>
            </div>
            
            <div id="reviewsList" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php
                if (file_exists($dbPath)) {
                    global $conn;
                    if ($conn) {
                        try {
                            $current_user_id = $_SESSION['user_id'] ?? 0;
                            
                            $replies = [];
                            $repSql = "SELECT rr.*, u.name FROM review_replies rr JOIN users u ON rr.user_id = u.id WHERE rr.review_id IN (SELECT id FROM reviews WHERE media_id = " . intval($id) . " AND media_type = '" . $conn->real_escape_string($type) . "') ORDER BY rr.created_at ASC";
                            $repRes = $conn->query($repSql);
                            if ($repRes) {
                                while($r = $repRes->fetch_assoc()) {
                                    $replies[$r['review_id']][] = $r;
                                }
                            }

                            $revSql = "
                                SELECT r.*, u.name, 
                                       (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id) as like_count,
                                       (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id AND user_id = $current_user_id) as is_liked_by_user
                                FROM reviews r 
                                JOIN users u ON r.user_id = u.id 
                                WHERE r.media_id = " . intval($id) . " 
                                  AND r.media_type = '" . $conn->real_escape_string($type) . "' 
                                ORDER BY r.created_at DESC
                            ";
                            $revRes = $conn->query($revSql);
                            if ($revRes && $revRes->num_rows > 0) {
                                while($rev = $revRes->fetch_assoc()) {
                                    $starsHtml = '';
                                    for($i=0; $i<5; $i++) { $starsHtml .= $i < $rev['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
                                    $initial = strtoupper(substr($rev['name'], 0, 1));
                                    $date = date('d M Y', strtotime($rev['created_at']));
                                    $deleteBtn = '';
                                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rev['user_id']) {
                                        $deleteBtn = '<button onclick="deleteReview('.$rev['id'].', this)" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:0.85rem; margin-left:15px; transition: 0.3s;" title="Delete"><i class="fas fa-trash"></i></button>';
                                    }
                                    
                                    $like_count = $rev['like_count'] ?? 0;
                                    $activeClass = !empty($rev['is_liked_by_user']) ? 'style="color:#00d2ff;"' : 'style="color:#aaa;"';
                                    
                                    $revReplies = $replies[$rev['id']] ?? [];
                                    $reply_count = count($revReplies);
                                    
                                    echo "
                                    <div class='review-item' style='background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px;'>
                                        <div style='display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;'>
                                            <div style='display: flex; align-items: center; gap: 15px;'>
                                                <div style='width: 40px; height: 40px; background: rgba(255,255,255,0.1); color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-weight: 600; font-size: 1.1rem;'>{$initial}</div>
                                                <div>
                                                    <h4 style='margin: 0; font-size: 1rem; color: #fff;'>".htmlspecialchars($rev['name'])."</h4>
                                                    <span style='color: #666; font-size: 0.8rem;'>{$date}</span>
                                                </div>
                                            </div>
                                            <div style='display: flex; align-items: center;'>
                                                <div style='color: #f5c518; font-size: 0.8rem; letter-spacing: 2px;'>{$starsHtml}</div>
                                                {$deleteBtn}
                                            </div>
                                        </div>
                                        <p style='color: #aaa; line-height: 1.6; font-size: 0.9rem; margin-bottom: 1.2rem;'>" . nl2br(htmlspecialchars($rev['review_text'])) . "</p>
                                        
                                        <div style='display: flex; gap: 20px; align-items: center; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.05);'>
                                            <button class='like-btn' onclick='toggleLikeReview(event, this, {$rev['id']})' {$activeClass} style='background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; transition: 0.3s;'>
                                                <i class='fas fa-thumbs-up'></i> <span class='like-count'>{$like_count}</span>
                                            </button>
                                            <button class='reply-toggle-btn' onclick='document.getElementById(\"replyArea-{$rev['id']}\").style.display = document.getElementById(\"replyArea-{$rev['id']}\").style.display === \"none\" ? \"block\" : \"none\"' style='background: none; border: none; color: #aaa; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; transition: 0.3s;'>
                                                <i class='fas fa-comment-dots'></i> <span>{$reply_count} Replies</span>
                                            </button>
                                        </div>
                                        
                                        <div id='replyArea-{$rev['id']}' style='display: none; margin-top: 1.5rem; padding-left: 2rem; border-left: 2px solid rgba(255,255,255,0.1);'>
                                            <div style='display: flex; gap: 10px; margin-bottom: 1.5rem;'>
                                                <input type='text' id='replyInput-{$rev['id']}' placeholder='Write a reply...' style='flex: 1; padding: 0.6rem 1rem; border-radius: 20px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; outline: none; font-size: 0.85rem;'>
                                                <button onclick='submitReply({$rev['id']})' class='action-btn' style='padding: 0 1.2rem; border-radius: 20px; height: 36px; border: none; background: white; color: black;'><i class='fas fa-paper-plane'></i></button>
                                            </div>
                                            <div id='repliesList-{$rev['id']}' style='display: flex; flex-direction: column; gap: 1rem;'>";
                                            
                                            foreach($revReplies as $reply) {
                                                $rInit = strtoupper(substr($reply['name'], 0, 1));
                                                $rDate = date('d M, H:i', strtotime($reply['created_at']));
                                                echo "
                                                <div style='background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px;'>
                                                    <div style='display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem;'>
                                                        <div style='width: 25px; height: 25px; background: rgba(255,255,255,0.1); color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 0.7rem; font-weight: bold;'>{$rInit}</div>
                                                        <strong style='font-size: 0.85rem; color: #ddd;'>".htmlspecialchars($reply['name'])."</strong>
                                                        <span style='font-size: 0.7rem; color: #666;'>{$rDate}</span>
                                                    </div>
                                                    <p style='margin: 0; color: #aaa; font-size: 0.85rem; padding-left: 35px;'>".htmlspecialchars($reply['reply_text'])."</p>
                                                </div>";
                                            }
                                            
                                    echo "
                                            </div>
                                        </div>
                                    </div>";
                                }
                            } else {
                                echo "<p style='color: #666; font-size: 0.9rem;'>No reviews yet. Be the first to review!</p>";
                            }
                        } catch(Exception $e) {}
                    }
                }
                ?>
            </div>
        </div>

<script>
// Skrip terkait Review dan Rating Bintang
document.addEventListener('DOMContentLoaded', () => {
    const starRating = document.getElementById('starRating');
    const reviewForm = document.getElementById('reviewForm');
    
    if (starRating && reviewForm) {
        const stars = starRating.querySelectorAll('i');
        let currentRating = parseInt(starRating.getAttribute('data-rating')) || 0;
        
        const updateStars = (rating) => {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        };
        
        updateStars(currentRating);
        
        stars.forEach((star, index) => {
            star.addEventListener('mouseover', () => updateStars(index + 1));
            star.addEventListener('mouseout', () => updateStars(currentRating));
            star.addEventListener('click', () => {
                currentRating = index + 1;
                starRating.setAttribute('data-rating', currentRating);
                updateStars(currentRating);
            });
        });
        
        reviewForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
                alert("Silakan login terlebih dahulu untuk mengirim ulasan!");
                window.location.href = 'index.php?page=login';
                return;
            }
            
            const text = document.getElementById('reviewText').value.trim();
            if (currentRating === 0 || text === '') {
                alert("Mohon isi rating dan ulasan terlebih dahulu.");
                return;
            }
            
            const btn = document.getElementById('submitReviewBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            btn.disabled = true;
            
            fetch('index.php?page=ajax_review', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `media_id=<?= $id ?>&media_type=<?= $type ?>&rating=${currentRating}&review_text=${encodeURIComponent(text)}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Gagal mengirim ulasan: ' + (data.error || 'Unknown error'));
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
});

function deleteReview(reviewId, btn) {
    if(!confirm("Apakah Anda yakin ingin menghapus ulasan ini?")) return;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    fetch('index.php?page=ajax_review', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete&review_id=${reviewId}`
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) window.location.reload();
        else alert('Gagal: ' + data.error);
    });
}
</script>
        
    </div>
</div>

<!-- Tahap 4: Similar Movies -->
<?php
// Fetch similar movies locally within details script
$similarMovies = [];
if ($type === 'movie') {
    $simData = fetchTMDB("movie/" . intval($id) . "/similar");
    if (!empty($simData['results'])) $similarMovies = $simData['results'];
} else {
    $simData = fetchTMDB("tv/" . intval($id) . "/similar");
    if (!empty($simData['results'])) $similarMovies = $simData['results'];
}
?>
<?php if(!empty($similarMovies)): ?>
<div class="similar-section">
    <div class="similar-header">
        <div class="similar-title"><i class="fas fa-film"></i> Similar <?= $type == 'movie' ? 'Movies' : 'TV Shows' ?></div>
        <div class="similar-filter">Filter by Popularity &nbsp; <i class="fas fa-caret-down"></i></div>
    </div>
    
    <div class="similar-grid" id="similarGrid">
        <?php 
        $count = 0;
        foreach($similarMovies as $smovie): 
            $sPoster = !empty($smovie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $smovie['poster_path'] : "https://via.placeholder.com/500x750?text=No+Poster";
            $sTitle = $smovie['title'] ?? $smovie['name'] ?? 'Unknown';
            $sYear = isset($smovie['release_date']) ? substr($smovie['release_date'], 0, 4) : (isset($smovie['first_air_date']) ? substr($smovie['first_air_date'], 0, 4) : '-');
            $sRating = isset($smovie['vote_average']) ? round($smovie['vote_average'], 1) : 0;
            $count++;
            $hiddenStyle = $count > 5 ? 'style="display: none;"' : '';
            $extraClass = $count > 5 ? 'extra-similar-card' : '';
        ?>
        <a href="index.php?page=details&id=<?= $smovie['id'] ?>&type=<?= $type ?>" class="similar-card <?= $extraClass ?>" <?= $hiddenStyle ?>>
            <img src="<?= $sPoster ?>" alt="<?= htmlspecialchars($sTitle) ?>">
            <div class="similar-info">
                <div class="similar-name"><?= htmlspecialchars($sTitle) ?></div>
                <div class="similar-sub"><?= htmlspecialchars($sTitle) ?> (<?= $sYear ?>)</div>
                <div class="similar-rating">
                    <?php for($i=0; $i<5; $i++): ?>
                        <?= $i < round($sRating/2) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>' ?>
                    <?php endfor; ?>
                    <span><?= $sRating ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    
    <?php if(count($similarMovies) > 5): ?>
    <button class="btn-load-more" onclick="document.querySelectorAll('.extra-similar-card').forEach(el => el.style.display = 'flex'); this.style.display='none';"><i class="fas fa-plus-circle"></i> Show More</button>
    <?php endif; ?>
</div>
<?php endif; ?>