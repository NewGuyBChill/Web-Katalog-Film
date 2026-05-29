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
$genresHtml = "";
if (!empty($movie['genres'])) {
    $genresHtml = " &bull; " . implode(', ', array_map(function($g) { return $g['name']; }, $movie['genres']));
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
?>

<main class="detail-main">
    <!-- Bagian Header (Poster, Judul, Info) -->
    <div class="detail-hero" style="background: <?= $backdrop ? "url('$backdrop')" : '#1a1a1a' ?> center top / cover no-repeat;">
        <div class="detail-hero-overlay"></div>
        
        <div class="detail-poster">
            <img src="<?= $poster ?>" alt="<?= htmlspecialchars($title) ?>" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.8);">
        </div>
        
        <div class="detail-info">
            <h1 class="detail-title"><?= htmlspecialchars($title) ?></h1>
            <p class="detail-meta"><?= $release_date ?> &bull; <?= $runtime ?><?= $extraInfo ?><?= $genresHtml ?></p>
            
            <div class="detail-actions">
                <div style="background: #FCD34D; color: black; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 800; font-size: 1.2rem;">
                    <i class="fas fa-star"></i> <?= $rating ?> / 10
                </div>
                <?php if ($trailerUrl !== "#"): ?>
                    <a href="javascript:void(0)" onclick="openTrailerModal('<?= $trailerUrl ?>')" class="btn-primary" style="padding: 0.7rem 1.5rem; text-decoration: none;"><i class="fas fa-play"></i> <?= translateText('watch_trailer') ?></a>
                <?php else: ?>
                    <button class="btn-primary" style="padding: 0.7rem 1.5rem; opacity: 0.5; cursor: not-allowed;" disabled><i class="fas fa-play"></i> <?= translateText('no_trailer') ?></button>
                <?php endif; ?>
                <button class="btn-secondary watchlist-btn-detail" data-id="<?= $id ?>" onclick="toggleWatchlistDetail(event, this, '<?= $id ?>', '<?= $type ?>', '<?= addslashes(htmlspecialchars($title)) ?>', '<?= $poster ?>')" style="padding: 0.7rem 1.5rem; transition: 0.3s;"><i class="fas fa-heart"></i> <?= translateText('add_favorite') ?></button>
                
                <!-- Dropdown Tambah ke Playlist -->
                <div class="custom-dropdown" style="display: inline-block;">
                    <button class="btn-secondary dropdown-toggle" style="padding: 0.7rem 1.5rem; transition: 0.3s; min-width: auto; border-radius: 30px; font-size: 0.95rem;">
                        <i class="fas fa-list"></i> Tambah ke Daftar <i class="fas fa-chevron-down" style="font-size: 0.7rem; margin-left: 5px;"></i>
                    </button>
                    <div class="dropdown-menu" style="min-width: 230px;">
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <a href="index.php?page=login" style="color: var(--text-muted);">Login untuk menggunakan daftar</a>
                        <?php elseif(empty($userPlaylists)): ?>
                            <a href="index.php?page=my_lists" style="color: var(--text-muted);"><i class="fas fa-plus"></i> Buat Daftar Baru</a>
                        <?php else: ?>
                            <?php foreach($userPlaylists as $pl): ?>
                                <a href="javascript:void(0)" onclick="addToPlaylist(<?= $pl['id'] ?>, <?= $id ?>, '<?= $type ?>', '<?= addslashes(htmlspecialchars($title)) ?>', '<?= $poster ?>')"><i class="fas fa-folder"></i> <?= htmlspecialchars($pl['name']) ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <h3 class="detail-synopsis-title"><?= translateText('synopsis') ?></h3>
            <p class="detail-synopsis-text"><?= htmlspecialchars($overview) ?></p>
        </div>
    </div>

    <!-- Bagian Cast / Pemeran Utama -->
    <?php if (!empty($movie['credits']['cast'])): ?>
    <div class="container detail-review-container" style="padding-bottom: 0;">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <h2><?= translateText('top_cast') ?></h2>
        </div>
        <div class="movie-row" style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 15px; scrollbar-width: thin;">
            <?php 
            $casts = array_slice($movie['credits']['cast'], 0, 10); // Ambil maksimal 10 pemeran
            foreach($casts as $cast): 
                $castImg = !empty($cast['profile_path']) ? "https://image.tmdb.org/t/p/w200" . $cast['profile_path'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22300%22%20fill%3D%22%23222%22%3E%3Crect%20width%3D%22200%22%20height%3D%22300%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23666%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20text-anchor%3D%22middle%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fsvg%3E";
                $isFavCast = in_array($cast['id'], $userFavCasts);
            ?>
            <div style="flex: 0 0 140px; text-align: center; position: relative;">
                <a href="index.php?page=person&id=<?= $cast['id'] ?>"><img src="<?= $castImg ?>" alt="<?= htmlspecialchars((string)$cast['name']) ?>" style="width: 140px; height: 210px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); margin-bottom: 10px; background: var(--card-bg); transition: 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'"></a>
                
                <!-- Tombol Bintang (Favorite Cast) -->
                <button class="cast-fav-btn <?= $isFavCast ? 'active-cast-fav' : '' ?>" onclick="toggleFavoriteCast(event, this, <?= $cast['id'] ?>, '<?= htmlspecialchars(addslashes((string)$cast['name'])) ?>', '<?= htmlspecialchars((string)$castImg) ?>')" style="position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.6); border: none; color: white; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
                    <i class="fas fa-star" <?= $isFavCast ? 'style="color: #FCD34D;"' : '' ?>></i>
                </button>
                <a href="index.php?page=person&id=<?= $cast['id'] ?>" style="text-decoration: none;"><div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars((string)$cast['name']) ?></div></a>
                <div style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars((string)$cast['character']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bagian User Review -->
    <div class="container detail-review-container">
        <div class="section-header" style="margin-bottom: 2rem;">
            <h2><?= translateText('user_reviews') ?></h2>
            <p><?= translateText('share_opinion') ?></p>
        </div>
        
        <div class="review-box">
            <h3 id="reviewFormTitle" style="margin-bottom: 1.2rem; font-size: 1.1rem; font-weight: 600;"><?= $userReview ? translateText('edit_review') : translateText('write_review') ?></h3>
            <form id="reviewForm" style="display: flex; flex-direction: column; gap: 1.2rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;"><?= translateText('rating') ?>:</label>
                    <div id="starRating" style="color: #FCD34D; font-size: 1.5rem; cursor: pointer; letter-spacing: 5px; transition: 0.2s;" data-rating="<?= $userReview ? $userReview['rating'] : 0 ?>">
                        <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                    </div>
                </div>
                <textarea id="reviewText" placeholder="<?= translateText('review_placeholder') ?>" rows="4" style="width: 100%; padding: 1.2rem; border-radius: 8px; background: #080808; border: 1px solid #333; color: white; resize: vertical; font-family: inherit; font-size: 1rem; outline: none;"><?= $userReview ? htmlspecialchars($userReview['review_text']) : '' ?></textarea>
                <button type="submit" id="submitReviewBtn" class="btn-primary" style="align-self: flex-start; padding: 0.8rem 2rem; border-radius: 30px;"><?= $userReview ? translateText('update_review') : translateText('submit_review') ?></button>
            </form>
        </div>
        
        <div id="reviewsList" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php
            $dbPath = __DIR__ . '/../../config/db.php';
            if (file_exists($dbPath)) {
                require_once $dbPath;
                global $conn;
                if ($conn) {
                    try {
                        $current_user_id = $_SESSION['user_id'] ?? 0;
                        
                        // AUTO-CREATE TABEL BALASAN JIKA BELUM ADA
                        $conn->query("CREATE TABLE IF NOT EXISTS review_replies (
                            id INT AUTO_INCREMENT PRIMARY KEY, review_id INT NOT NULL, user_id INT NOT NULL,
                            reply_text TEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
                            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                        )");
                        
                        // AMBIL SEMUA BALASAN TERKAIT FILM INI
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
                                    $deleteBtn = '<button onclick="deleteReview('.$rev['id'].', this)" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:0.85rem; margin-left:15px; transition: 0.3s;" title="' . translateText('delete_review') . '"><i class="fas fa-trash"></i></button>';
                                }
                                
                                $like_count = $rev['like_count'] ?? 0;
                                $activeClass = !empty($rev['is_liked_by_user']) ? 'active' : '';
                                
                                $revReplies = $replies[$rev['id']] ?? [];
                                $reply_count = count($revReplies);
                                
                                $repliesHtml = '';
                                foreach ($revReplies as $rep) {
                                    $repInit = strtoupper(substr($rep['name'], 0, 1));
                                    $repDate = date('d M Y, H:i', strtotime($rep['created_at']));
                                    $repDel = '';
                                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rep['user_id']) {
                                        $repDel = '<button onclick="deleteReply('.$rep['id'].', '.$rev['id'].')" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:0.8rem; padding:0;" title="Hapus"><i class="fas fa-times"></i></button>';
                                    }
                                    $repliesHtml .= '
                                    <div id="reply-'.$rep['id'].'" style="display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start;">
                                        <a href="index.php?page=user_profile&id='.$rep['user_id'].'" style="text-decoration: none; flex-shrink: 0;"><div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold; font-size: 0.8rem;">'.$repInit.'</div></a>
                                        <div style="background: rgba(255,255,255,0.03); padding: 10px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); flex-grow: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                                <div><a href="index.php?page=user_profile&id='.$rep['user_id'].'" style="text-decoration: none; color: inherit;"><span style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">'.htmlspecialchars($rep['name']).'</span></a><span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 5px;">'.$repDate.'</span></div>
                                                '.$repDel.'
                                            </div>
                                            <div style="color: #ddd; font-size: 0.9rem; margin-top: 5px; line-height: 1.5;">'.nl2br(htmlspecialchars($rep['reply_text'])).'</div>
                                        </div>
                                    </div>';
                                }

                                echo '
                                <div id="review-'.$rev['id'].'" class="review-item">
                                    <button class="review-like-btn '.$activeClass.'" onclick="likeReview(this, '.$rev['id'].')">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count">'.$like_count.'</span>
                                    </button>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                        <div style="display: flex; gap: 1rem; align-items: center;">
                                            <a href="index.php?page=user_profile&id='.$rev['user_id'].'" style="text-decoration: none; display: contents;">
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold; cursor: pointer;">'.$initial.'</div>
                                            </a>
                                            <div>
                                                <a href="index.php?page=user_profile&id='.$rev['user_id'].'" style="text-decoration: none; color: inherit; transition: color 0.2s;" onmouseover="this.style.color=\'var(--accent)\'" onmouseout="this.style.color=\'\'">
                                                    <div style="font-weight: 600;">'.htmlspecialchars($rev['name']).'</div>
                                                </a>
                                                <div style="font-size: 0.8rem; color: var(--text-muted);">'.$date.'</div>
                                            </div>
                                        </div>
                                        <div style="color: #FCD34D; display: flex; align-items: center; padding-right: 4rem;">'.$starsHtml.$deleteBtn.'</div>
                                    </div>
                                    <p style="color: #ddd; line-height: 1.6;">'.nl2br(htmlspecialchars($rev['review_text'])).'</p>
                                    
                                    <!-- TOMBOL DAN AREA BALASAN -->
                                    <div style="margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                                        <button onclick="toggleReplies('.$rev['id'].')" style="background:none; border:none; color:var(--accent); cursor:pointer; font-size:0.9rem; padding:0; display:flex; align-items:center; gap:6px; transition: 0.2s;" onmouseover="this.style.opacity=\'0.8\'" onmouseout="this.style.opacity=\'1\'"><i class="fas fa-comment-alt"></i> <span id="reply-count-'.$rev['id'].'">'.$reply_count.'</span> Balasan</button>
                                        <div id="replies-section-'.$rev['id'].'" style="display:none; margin-top: 1.5rem; padding-left: 1.5rem; border-left: 2px solid rgba(255,255,255,0.05);">
                                            <div id="replies-list-'.$rev['id'].'">
                                                '.$repliesHtml.'
                                            </div>
                                            <div style="display: flex; gap: 10px; margin-top: 10px; align-items: flex-start;">
                                                <input type="text" id="reply-input-'.$rev['id'].'" placeholder="Tulis balasan untuk '.htmlspecialchars($rev['name']).'..." style="flex-grow: 1; padding: 10px 15px; border-radius: 20px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; outline: none; font-size: 0.9rem;">
                                                <button onclick="submitReply('.$rev['id'].')" class="btn-primary" style="padding: 10px 18px; border-radius: 20px; font-size: 0.9rem;"><i class="fas fa-paper-plane"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo '<p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">' . translateText('no_reviews_yet') . '</p>';
                        }
                    } catch (Exception $e) {}
                }
            }
            ?>
        </div>
    </div>

    <!-- Bagian Film Serupa (Similar Movies) -->
    <?php 
    $similarMovies = getSimilarMedia($id, $type);
    if (!empty($similarMovies)): 
    ?>
    <div class="container detail-review-container" style="padding-top: 0;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <h2><?= translateText('similar_movies') ?></h2>
        </div>
        <div class="movie-row" id="similar-row" style="padding-left: 5px; padding-right: 5px;">
            <?php foreach($similarMovies as $smovie): ?>
            <a href="index.php?page=details&type=<?= $type ?>&id=<?= $smovie['id'] ?>" class="movie-card grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars((string)$smovie['rating']) ?></div>
                    <img src="<?= htmlspecialchars((string)$smovie['image']) ?>" alt="<?= htmlspecialchars((string)$smovie['title']) ?>">
                    <div class="watchlist-btn" data-id="<?= $smovie['id'] ?>" data-title="<?= htmlspecialchars((string)$smovie['title']) ?>" onclick="toggleWatchlist(event, this)">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="grid-movie-quick-view">
                        <div class="quick-view-title"><?= translateText('synopsis') ?></div>
                        <div class="quick-view-synopsis"><?= htmlspecialchars((string)$smovie['overview']) ?: translateText('no_synopsis') ?></div>
                    </div>
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars((string)$smovie['title']) ?></div>
                    <div class="grid-movie-meta"><?= htmlspecialchars((string)$smovie['year']) ?> &bull; <?= htmlspecialchars((string)$smovie['genre']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
// Interaksi sederhana untuk klik rating bintang
let currentRating = parseInt(document.getElementById('starRating').getAttribute('data-rating')) || 0;
if (currentRating > 0) {
    document.querySelectorAll('#starRating i').forEach((s, i) => {
        s.className = i < currentRating ? 'fas fa-star' : 'far fa-star';
    });
}

document.querySelectorAll('#starRating i').forEach((star, index) => {
    star.addEventListener('click', () => {
        currentRating = index + 1;
        document.querySelectorAll('#starRating i').forEach((s, i) => {
            s.className = i <= index ? 'fas fa-star' : 'far fa-star';
        });
    });
});

document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
        alert('<?= translateText('login_to_review') ?>');
        window.location.href = 'index.php?page=login';
        return;
    }

    const text = document.getElementById('reviewText').value;
    if (!text || currentRating === 0) {
        alert('<?= translateText('alert_review') ?>');
        return;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<?= translateText('sending') ?>';
    submitBtn.disabled = true;

    fetch('index.php?page=ajax_review', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `media_id=<?= $id ?>&media_type=<?= $type ?>&title=<?= urlencode($title) ?>&poster=<?= urlencode($poster) ?>&rating=${currentRating}&review_text=${encodeURIComponent(text)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const reviewList = document.getElementById('reviewsList');
            
            // Hapus teks "Belum ada ulasan" jika ada
            if (reviewList.innerHTML.includes('Belum ada ulasan')) reviewList.innerHTML = '';
            if (reviewList.innerHTML.includes('No reviews yet')) reviewList.innerHTML = '';

            // Hapus ulasan lama dari list jika merupakan update
            const oldReview = document.getElementById(`review-${data.review_id}`);
            if (oldReview) oldReview.remove();

            // Fungsi escape HTML untuk mencegah DOM XSS dan kerusakan layout UI
            const escapeHTML = str => str.replace(/[&<>'"]/g, tag => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
            }[tag] || tag));
            const safeText = escapeHTML(text).replace(/\n/g, '<br>');

            let starsHtml = '';
            for(let i=0; i<5; i++) { starsHtml += i < currentRating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
            
            const deleteBtnHtml = `<button onclick="deleteReview(${data.review_id}, this)" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:0.85rem; margin-left:15px; transition: 0.3s;" title="<?= translateText('delete_review') ?>"><i class="fas fa-trash"></i></button>`;

            const newReview = document.createElement('div');
            newReview.id = `review-${data.review_id}`;
            newReview.className = 'review-item';
            newReview.style.cssText = 'animation: slideIn 0.3s ease;';
            newReview.innerHTML = `
                <button class="review-like-btn" onclick="likeReview(this, ${data.review_id})">
                    <i class="fas fa-heart"></i>
                    <span class="like-count">0</span>
                </button>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="index.php?page=user_profile&id=${data.user_id}" style="text-decoration: none; display: contents;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold; cursor: pointer;">${data.user_initial}</div>
                        </a>
                        <div>
                            <a href="index.php?page=user_profile&id=${data.user_id}" style="text-decoration: none; color: inherit; transition: color 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color=''">
                                <div style="font-weight: 600;">${data.user_name}</div>
                            </a>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?= translateText('just_now') ?></div>
                        </div>
                    </div>
                    <div style="color: #FCD34D; display: flex; align-items: center; padding-right: 4rem;">${starsHtml}${deleteBtnHtml}</div>
                </div>
                <p style="color: #ddd; line-height: 1.6;">${safeText}</p>
                
                <!-- TOMBOL DAN AREA BALASAN -->
                <div style="margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                    <button onclick="toggleReplies(${data.review_id})" style="background:none; border:none; color:var(--accent); cursor:pointer; font-size:0.9rem; padding:0; display:flex; align-items:center; gap:6px; transition: 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'"><i class="fas fa-comment-alt"></i> <span id="reply-count-${data.review_id}">0</span> Balasan</button>
                    <div id="replies-section-${data.review_id}" style="display:none; margin-top: 1.5rem; padding-left: 1.5rem; border-left: 2px solid rgba(255,255,255,0.05);">
                        <div id="replies-list-${data.review_id}"></div>
                        <div style="display: flex; gap: 10px; margin-top: 10px; align-items: flex-start;">
                            <input type="text" id="reply-input-${data.review_id}" placeholder="Tulis balasan..." style="flex-grow: 1; padding: 10px 15px; border-radius: 20px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; outline: none; font-size: 0.9rem;">
                            <button onclick="submitReply(${data.review_id})" class="btn-primary" style="padding: 10px 18px; border-radius: 20px; font-size: 0.9rem;"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            `;
            
            reviewList.prepend(newReview);
            
            document.getElementById('submitReviewBtn').innerHTML = '<?= translateText('update_review') ?>';
            document.getElementById('reviewFormTitle').innerHTML = '<?= translateText('edit_review') ?>';
        } else {
            alert('Gagal mengirim ulasan: ' + data.error);
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(err => {
        console.error(err);
        alert("Terjadi kesalahan sistem. (Mungkin tabel 'reviews' belum terbuat dengan benar di Database).");
        submitBtn.innerHTML = originalBtnText;
    })
    .finally(() => {
        submitBtn.disabled = false;
    });
});

function deleteReview(reviewId, btn) {
    if(confirm('<?= translateText('delete_review_confirm') ?>')) {
        fetch('index.php?page=ajax_review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete&review_id=${reviewId}`
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                const reviewDiv = document.getElementById('review-' + reviewId) || btn.closest('div[style*="border-radius: 12px"]');
                if (reviewDiv) reviewDiv.remove();
                
                // Kembalikan mode form ke normal
                document.getElementById('reviewText').value = '';
                currentRating = 0;
                document.getElementById('starRating').setAttribute('data-rating', 0);
                document.querySelectorAll('#starRating i').forEach(s => s.className = 'far fa-star');
                document.getElementById('submitReviewBtn').innerHTML = '<?= translateText('submit_review') ?>';
                document.getElementById('reviewFormTitle').innerHTML = '<?= translateText('write_review') ?>';
            } else {
                alert("Gagal menghapus ulasan: " + data.error);
            }
        }).catch(err => {
            console.error(err);
            alert("Terjadi kesalahan sistem/jaringan saat menghapus.");
        });
    }
}

// Fungsi JS untuk menangani balasan
function toggleReplies(reviewId) {
    const section = document.getElementById('replies-section-' + reviewId);
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function submitReply(reviewId) {
    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
        alert('Silakan login terlebih dahulu untuk membalas.');
        window.location.href = 'index.php?page=login';
        return;
    }
    const input = document.getElementById('reply-input-' + reviewId);
    const text = input.value.trim();
    if (!text) return;

    fetch('index.php?page=ajax_review_reply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&review_id=${reviewId}&reply_text=${encodeURIComponent(text)}`
    }).then(r => r.json()).then(data => {
        if (data.success) {
            input.value = '';
            const list = document.getElementById('replies-list-' + reviewId);
            const countSpan = document.getElementById('reply-count-' + reviewId);
            countSpan.innerText = parseInt(countSpan.innerText) + 1;
            
            const safeText = data.reply_text.replace(/\n/g, '<br>');
            const newReply = document.createElement('div');
            newReply.id = 'reply-' + data.reply_id;
            newReply.style.cssText = 'display: flex; gap: 10px; margin-bottom: 15px; align-items: flex-start; animation: slideIn 0.3s ease;';
            newReply.innerHTML = `<div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold; font-size: 0.8rem; flex-shrink: 0;">${data.user_initial}</div><div style="background: rgba(255,255,255,0.03); padding: 10px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); flex-grow: 1;"><div style="display: flex; justify-content: space-between; align-items: flex-start;"><div><span style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">${data.user_name}</span><span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 5px;">Baru saja</span></div><button onclick="deleteReply(${data.reply_id}, ${reviewId})" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:0.8rem; padding: 0;" title="Hapus"><i class="fas fa-times"></i></button></div><div style="color: #ddd; font-size: 0.9rem; margin-top: 5px; line-height: 1.5;">${safeText}</div></div>`;
            list.appendChild(newReply);
        } else alert('Gagal: ' + data.error);
    });
}

function deleteReply(replyId, reviewId) {
    if(confirm('Hapus balasan ini?')) {
        fetch('index.php?page=ajax_review_reply', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete&reply_id=${replyId}`
        }).then(r => r.json()).then(data => {
            if(data.success) { document.getElementById('reply-' + replyId).remove(); document.getElementById('reply-count-' + reviewId).innerText = Math.max(0, parseInt(document.getElementById('reply-count-' + reviewId).innerText) - 1); }
        });
    }
}

function addToPlaylist(playlistId, mediaId, mediaType, title, poster) {
    fetch('index.php?page=ajax_playlist', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&playlist_id=${playlistId}&media_id=${mediaId}&media_type=${mediaType}&media_title=${encodeURIComponent(title)}&media_poster=${encodeURIComponent(poster)}`
    }).then(r => r.json()).then(data => {
        if (data.success) {
            alert('Berhasil ditambahkan ke dalam daftar playlist Anda!');
        } else alert(data.error || 'Gagal menambahkan ke daftar.');
    }).catch(err => console.error(err));
}
</script>