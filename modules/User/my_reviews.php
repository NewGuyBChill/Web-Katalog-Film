<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$uid = (int)$_SESSION['user_id'];
$reviews = [];
// Dibatasi 30 ulasan agar tidak membuat antrean (throttle) request ke TMDB API terlalu panjang
$res = $conn->query("SELECT * FROM reviews WHERE user_id = $uid ORDER BY created_at DESC LIMIT 30");
if ($res) {
    while($row = $res->fetch_assoc()) { $reviews[] = $row; }
}
?>
<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header">
        <div class="header-titles">
            <h2><?= translateText('my_reviews') ?></h2>
            <p><?= translateText('my_reviews_desc') ?></p>
        </div>
    </div>

    <div style="max-width: 900px; margin: 0 auto;">
        <?php if(count($reviews) > 0): ?>
            <?php foreach($reviews as $rev): 
                $title = $rev['media_title'] ?? 'Unknown Media';
                $poster = !empty($rev['media_poster']) ? $rev['media_poster'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22300%22%20viewBox%3D%220%200%20200%20300%22%3E%3Crect%20width%3D%22200%22%20height%3D%22300%22%20fill%3D%22%231a1a1a%22%2F%3E%3C%2Fsvg%3E";
                $starsHtml = '';
                for($i=0; $i<5; $i++) { $starsHtml .= $i < $rev['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
            ?>
            <a href="index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>" class="review-history-item" title="Lihat selengkapnya">
                <img src="<?= $poster ?>" alt="Poster" class="review-history-poster">
                <div class="review-history-content">
                    <div class="review-history-title">
                        <?= htmlspecialchars($title) ?> 
                        <span class="review-history-date"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                        <button onclick="event.preventDefault(); window.location.href='index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>#reviewFormTitle'" style="background:none; border:none; color:var(--accent); cursor:pointer; font-size:1rem; margin-left:15px; transition: 0.3s; padding: 0;" title="<?= translateText('edit_review') ?>"><i class="fas fa-edit"></i></button>
                        <button onclick="event.preventDefault(); deleteReview(<?= $rev['id'] ?>, this)" style="background:none; border:none; color:#ff3b3b; cursor:pointer; font-size:1rem; margin-left:10px; transition: 0.3s; padding: 0;" title="<?= translateText('delete_review') ?>"><i class="fas fa-trash"></i></button>
                    </div>
                    <div style="color: #FCD34D; font-size: 0.95rem; margin-bottom: 0.5rem;"><?= $starsHtml ?></div>
                    <div class="review-history-text"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state"><i class="fas fa-star" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i><h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;"><?= translateText('empty_reviews') ?></h3><p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;"><?= translateText('empty_reviews_desc') ?></p><a href="index.php?page=movies" class="btn-primary" style="text-decoration: none;"><?= translateText('explore_review_now') ?></a></div>
        <?php endif; ?>
    </div>
</main>

<script>
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
                const reviewItem = btn.closest('.review-history-item');
                if (reviewItem) reviewItem.remove();
                
                // Reload halaman jika ulasan sudah kosong untuk memunculkan pesan "Belum Ada Ulasan"
                if (document.querySelectorAll('.review-history-item').length === 0) {
                    window.location.reload();
                }
            } else {
                alert("Gagal menghapus ulasan: " + data.error);
            }
        }).catch(err => {
            console.error(err);
            alert("Terjadi kesalahan sistem/jaringan saat menghapus.");
        });
    }
}
</script>