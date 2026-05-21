<?php
require_once __DIR__ . '/../config/data.php';
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$movie = getMovieDetails($id);

if (!$movie) {
    echo "<div style='padding: 150px 20px; text-align: center; height: 60vh;'><h2>Film Tidak Ditemukan / Tidak ada koneksi.</h2></div>";
    return;
}

$backdrop = !empty($movie['backdrop_path']) ? "https://image.tmdb.org/t/p/original" . $movie['backdrop_path'] : "";
$poster = !empty($movie['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $movie['poster_path'] : "https://via.placeholder.com/500x750?text=No+Poster";
$title = $movie['title'] ?? $movie['original_title'];
$overview = $movie['overview'] ?? 'Tidak ada sinopsis tersedia.';
$rating = isset($movie['vote_average']) ? round($movie['vote_average'], 1) : 0;
$release_date = isset($movie['release_date']) ? $movie['release_date'] : "-";
$runtime = isset($movie['runtime']) && $movie['runtime'] > 0 ? $movie['runtime'] . " min" : "N/A";

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
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 1.5rem; font-weight: 500;"><?= $release_date ?> &bull; <?= $runtime ?></p>
            
            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="background: #FCD34D; color: black; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 800; font-size: 1.2rem;">
                    <i class="fas fa-star"></i> <?= $rating ?> / 10
                </div>
                <?php if ($trailerUrl !== "#"): ?>
                    <a href="<?= $trailerUrl ?>" target="_blank" class="btn-primary" style="padding: 0.7rem 1.5rem; text-decoration: none;"><i class="fas fa-play"></i> Trailer</a>
                <?php else: ?>
                    <button class="btn-primary" style="padding: 0.7rem 1.5rem; opacity: 0.5; cursor: not-allowed;" disabled><i class="fas fa-play"></i> Tidak Ada Trailer</button>
                <?php endif; ?>
                <button class="btn-secondary" style="padding: 0.7rem 1.5rem;"><i class="fas fa-heart"></i> Add to Favorite</button>
            </div>
            
            <h3 style="font-size: 1.2rem; margin-bottom: 0.8rem; color: var(--accent);">Sinopsis</h3>
            <p style="color: #ddd; line-height: 1.7; font-size: 1.05rem;"><?= htmlspecialchars($overview) ?></p>
        </div>
    </div>

    <!-- Bagian User Review -->
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding-top: 2rem; padding-bottom: 4rem;">
        <div class="section-header" style="margin-bottom: 2rem;">
            <h2>User Reviews</h2>
            <p>Bagikan pendapatmu tentang film ini</p>
        </div>
        
        <div style="background: rgba(255,255,255,0.02); padding: 2rem; border-radius: 12px; border: 1px solid #262626; margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1.2rem; font-size: 1.1rem; font-weight: 600;">Tulis Review Kamu</h3>
            <form id="reviewForm" style="display: flex; flex-direction: column; gap: 1.2rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem;">Rating:</label>
                    <div id="starRating" style="color: #FCD34D; font-size: 1.5rem; cursor: pointer; letter-spacing: 5px; transition: 0.2s;">
                        <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                    </div>
                </div>
                <textarea id="reviewText" placeholder="Apa yang kamu pikirkan tentang film ini?" rows="4" style="width: 100%; padding: 1.2rem; border-radius: 8px; background: #080808; border: 1px solid #333; color: white; resize: vertical; font-family: inherit; font-size: 1rem; outline: none;"></textarea>
                <button type="submit" class="btn-primary" style="align-self: flex-start; padding: 0.8rem 2rem; border-radius: 30px;">Kirim Review</button>
            </form>
        </div>
        
        <div id="reviewsList" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Review yang disubmit akan muncul di sini -->
        </div>
    </div>
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
    const text = document.getElementById('reviewText').value;
    if (!text || currentRating === 0) {
        alert('Mohon isi rating dan review terlebih dahulu.');
        return;
    }

    const reviewList = document.getElementById('reviewsList');
    
    let starsHtml = '';
    for(let i=0; i<5; i++) {
        starsHtml += i < currentRating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
    }

    const newReview = document.createElement('div');
    newReview.style.cssText = 'background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: 12px; border: 1px solid #262626; animation: slideIn 0.3s ease;';
    newReview.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold;">U</div>
                <div><div style="font-weight: 600;">User</div><div style="font-size: 0.8rem; color: var(--text-muted);">Baru saja</div></div>
            </div>
            <div style="color: #FCD34D;">${starsHtml}</div>
        </div>
        <p style="color: #ddd; line-height: 1.6;">${text.replace(/\n/g, '<br>')}</p>
    `;
    
    reviewList.prepend(newReview);
    
    document.getElementById('reviewText').value = '';
    currentRating = 0;
    document.querySelectorAll('#starRating i').forEach(s => s.className = 'far fa-star');
});
</script>