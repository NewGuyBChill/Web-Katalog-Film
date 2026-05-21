<?php
require_once __DIR__ . '/../config/data.php';
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$type = isset($_GET['type']) && $_GET['type'] === 'tv' ? 'tv' : 'movie';
$movie = getMediaDetails($id, $type);

if (!$movie) {
    echo "<div style='padding: 150px 20px; text-align: center; height: 60vh;'><h2>" . translateText('movie_not_found') . "</h2></div>";
    return;
}

$backdrop = !empty($movie['backdrop_path']) ? "https://image.tmdb.org/t/p/original" . $movie['backdrop_path'] : "";
$poster = !empty($movie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22500%22%20height%3D%22750%22%20viewBox%3D%220%200%20500%20750%22%3E%3Crect%20width%3D%22500%22%20height%3D%22750%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E";
$title = $movie['title'] ?? $movie['original_title'] ?? $movie['name'];
$overview = $movie['overview'] ?? translateText('no_synopsis');
$rating = isset($movie['vote_average']) ? round($movie['vote_average'], 1) : 0;
$release_date = isset($movie['release_date']) ? $movie['release_date'] : ($movie['first_air_date'] ?? "-");
$runtime = isset($movie['runtime']) && $movie['runtime'] > 0 ? $movie['runtime'] . " min" : (isset($movie['episode_run_time'][0]) ? $movie['episode_run_time'][0] . " min/ep" : "N/A");

$extraInfo = "";
if ($type === 'tv') {
    $seasons = $movie['number_of_seasons'] ?? 0;
    $episodes = $movie['number_of_episodes'] ?? 0;
    if ($seasons > 0) $extraInfo = " &bull; $seasons Seasons ($episodes Episodes)";
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

<main style="padding-top: 80px; min-height: 80vh;">
    <!-- Bagian Header (Poster, Judul, Info) -->
    <div style="position: relative; background: url('<?= $backdrop ?>') center/cover; padding: 4rem; display: flex; gap: 3rem; align-items: center; justify-content: center; min-height: 60vh; flex-wrap: wrap;">
        <div style="position: absolute; inset: 0; background: linear-gradient(to right, #121212 20%, rgba(18, 18, 18, 0.8) 50%, transparent 100%), linear-gradient(to top, #121212 0%, transparent 30%);"></div>
        
        <div style="position: relative; z-index: 10; max-width: 300px; flex-shrink: 0;">
            <img src="<?= $poster ?>" alt="<?= htmlspecialchars($title) ?>" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.8);">
        </div>
        
        <div style="position: relative; z-index: 10; max-width: 700px;">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem; line-height: 1.1;"><?= htmlspecialchars($title) ?></h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 1.5rem; font-weight: 500;"><?= $release_date ?> &bull; <?= $runtime ?><?= $extraInfo ?></p>
            
            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="background: #FCD34D; color: black; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 800; font-size: 1.2rem;">
                    <i class="fas fa-star"></i> <?= $rating ?> / 10
                </div>
                <?php if ($trailerUrl !== "#"): ?>
                    <a href="javascript:void(0)" onclick="openTrailerModal('<?= $trailerUrl ?>')" class="btn-primary" style="padding: 0.7rem 1.5rem; text-decoration: none;"><i class="fas fa-play"></i> <?= translateText('watch_trailer') ?></a>
                <?php else: ?>
                    <button class="btn-primary" style="padding: 0.7rem 1.5rem; opacity: 0.5; cursor: not-allowed;" disabled><i class="fas fa-play"></i> <?= translateText('no_trailer') ?></button>
                <?php endif; ?>
                <button class="btn-secondary watchlist-btn-detail" data-id="<?= $id ?>" onclick="toggleWatchlistDetail(event, this, '<?= $id ?>', '<?= $type ?>', '<?= addslashes(htmlspecialchars($title)) ?>', '<?= $poster ?>')" style="padding: 0.7rem 1.5rem; transition: 0.3s;"><i class="fas fa-heart"></i> <?= translateText('add_favorite') ?></button>
            </div>
            
            <h3 style="font-size: 1.2rem; margin-bottom: 0.8rem; color: var(--accent);"><?= translateText('synopsis') ?></h3>
            <p style="color: #ddd; line-height: 1.7; font-size: 1.05rem;"><?= htmlspecialchars($overview) ?></p>
        </div>
    </div>

    <!-- Bagian User Review -->
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding-top: 2rem; padding-bottom: 4rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <h2><?= translateText('user_reviews') ?></h2>
            <p><?= translateText('share_opinion') ?></p>
        </div>
        
        <div style="background: rgba(255,255,255,0.02); padding: 2rem; border-radius: 12px; border: 1px solid #262626; margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; font-weight: 600;"><?= translateText('write_review') ?></h3>
            <form id="reviewForm" style="display: flex; flex-direction: column; gap: 1.2rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;"><?= translateText('rating') ?>:</label>
                    <div id="starRating" style="color: #FCD34D; font-size: 1.5rem; cursor: pointer; letter-spacing: 5px; transition: 0.2s;">
                        <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                    </div>
                </div>
                <textarea id="reviewText" placeholder="<?= translateText('review_placeholder') ?>" rows="4" style="width: 100%; padding: 1.2rem; border-radius: 8px; background: #080808; border: 1px solid #333; color: white; resize: vertical; font-family: inherit; font-size: 1rem; outline: none;"></textarea>
                <button type="submit" class="btn-primary" style="align-self: flex-start; padding: 0.8rem 2rem; border-radius: 30px;"><?= translateText('submit_review') ?></button>
            </form>
        </div>
        
        <div id="reviewsList" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php
            $dbPath = __DIR__ . '/../config/db.php';
            if (file_exists($dbPath)) {
                require_once $dbPath;
                global $conn;
                if ($conn) {
                    try {
                        $revSql = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.media_id = " . intval($id) . " AND r.media_type = '" . $conn->real_escape_string($type) . "' ORDER BY r.created_at DESC";
                        $revRes = $conn->query($revSql);
                        if ($revRes && $revRes->num_rows > 0) {
                            while($rev = $revRes->fetch_assoc()) {
                                $starsHtml = '';
                                for($i=0; $i<5; $i++) { $starsHtml .= $i < $rev['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
                                $initial = strtoupper(substr($rev['name'], 0, 1));
                                $date = date('d M Y', strtotime($rev['created_at']));
                                $deleteBtn = '';
                                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $rev['user_id']) {
                                    $deleteBtn = '<button onclick="deleteReview('.$rev['id'].', this)" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:0.85rem; margin-left:15px; transition: 0.3s;" title="Hapus Ulasan"><i class="fas fa-trash"></i></button>';
                                }
                                echo '
                                <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: 12px; border: 1px solid #262626;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                        <div style="display: flex; gap: 1rem; align-items: center;">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold;">'.$initial.'</div>
                                            <div><div style="font-weight: 600;">'.htmlspecialchars($rev['name']).'</div><div style="font-size: 0.8rem; color: var(--text-muted);">'.$date.'</div></div>
                                        </div>
                                        <div style="color: #FCD34D; display: flex; align-items: center;">'.$starsHtml.$deleteBtn.'</div>
                                    </div>
                                    <p style="color: #ddd; line-height: 1.6;">'.nl2br(htmlspecialchars($rev['review_text'])).'</p>
                                </div>';
                            }
                        } else {
                            echo '<p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">Belum ada ulasan. Jadilah yang pertama!</p>';
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
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding-top: 0; padding-bottom: 4rem;">
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
let currentRating = 0;
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
        alert("Silakan login terlebih dahulu untuk mengirim ulasan!");
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
    submitBtn.innerHTML = 'Mengirim...';
    submitBtn.disabled = true;

    fetch('index.php?page=ajax_review', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `media_id=<?= $id ?>&media_type=<?= $type ?>&rating=${currentRating}&review_text=${encodeURIComponent(text)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const reviewList = document.getElementById('reviewsList');
            
            // Hapus teks "Belum ada ulasan" jika itu review pertama
            if (reviewList.innerHTML.includes('Belum ada ulasan')) reviewList.innerHTML = '';

            let starsHtml = '';
            for(let i=0; i<5; i++) { starsHtml += i < currentRating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
            
            const deleteBtnHtml = `<button onclick="deleteReview(${data.review_id}, this)" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:0.85rem; margin-left:15px; transition: 0.3s;" title="Hapus Ulasan"><i class="fas fa-trash"></i></button>`;

            const newReview = document.createElement('div');
            newReview.style.cssText = 'background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: 12px; border: 1px solid #262626; animation: slideIn 0.3s ease;';
            newReview.innerHTML = `
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold;">${data.user_initial}</div>
                        <div><div style="font-weight: 600;">${data.user_name}</div><div style="font-size: 0.8rem; color: var(--text-muted);"><?= translateText('just_now') ?></div></div>
                    </div>
                    <div style="color: #FCD34D; display: flex; align-items: center;">${starsHtml}${deleteBtnHtml}</div>
                </div>
                <p style="color: #ddd; line-height: 1.6;">${text.replace(/\n/g, '<br>')}</p>
            `;
            
            reviewList.prepend(newReview);
            
            document.getElementById('reviewText').value = '';
            currentRating = 0;
            document.querySelectorAll('#starRating i').forEach(s => s.className = 'far fa-star');
        } else {
            alert('Gagal mengirim ulasan: ' + data.error);
        }
    })
    .catch(err => console.error(err))
    .finally(() => {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
});

function deleteReview(reviewId, btn) {
    if(confirm("Apakah Anda yakin ingin menghapus ulasan ini?")) {
        fetch('index.php?page=ajax_review', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete&review_id=${reviewId}`
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                btn.closest('div[style*="border-radius: 12px"]').remove();
            } else {
                alert("Gagal menghapus ulasan.");
            }
        });
    }
}
</script>