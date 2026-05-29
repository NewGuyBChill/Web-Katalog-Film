<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

if (!isset($_SESSION['user_id'])) {
    echo "<main class='container' style='padding-top: 120px;'><div class='empty-state'>Silakan login untuk melihat feed aktivitas.</div></main>";
    return;
}

$current_user_id = (int)$_SESSION['user_id'];
$feed_reviews = [];

// Ambil ulasan terbaru dari orang yang kita ikuti menggunakan JOIN SQL
$res_feed = $conn->query("
    SELECT r.*, u.name as author_name,
           (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id) as like_count,
           (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id AND user_id = $current_user_id) as is_liked_by_user
    FROM reviews r
    JOIN user_follows uf ON r.user_id = uf.following_id
    JOIN users u ON r.user_id = u.id
    WHERE uf.follower_id = $current_user_id
    ORDER BY r.created_at DESC LIMIT 10
");

if ($res_feed) {
    while($row = $res_feed->fetch_assoc()) {
        $feed_reviews[] = $row;
    }
}
?>
<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="section-header" style="margin-bottom: 2rem;">
        <h2><i class="fas fa-stream" style="color: var(--accent);"></i> Feed Aktivitas</h2>
        <p>Ulasan terbaru dari orang-orang yang Anda ikuti.</p>
    </div>
    
    <div id="feedContainer" style="max-width: 900px; margin: 0 auto;">
        <?php if (count($feed_reviews) > 0): ?>
            <?php foreach($feed_reviews as $rev): 
                $title = $rev['media_title'] ?? 'Unknown Media';
                $poster = !empty($rev['media_poster']) ? $rev['media_poster'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22300%22%20viewBox%3D%220%200%20200%20300%22%3E%3Crect%20width%3D%22200%22%20height%3D%22300%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E";
                $starsHtml = '';
                for($i=0; $i<5; $i++) { $starsHtml .= $i < $rev['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
                
                $activeClass = !empty($rev['is_liked_by_user']) ? 'active' : '';
                $initial = strtoupper(substr($rev['author_name'], 0, 1));
            ?>
            <div class="review-item" style="margin-bottom: 2rem; display: flex; gap: 1.5rem; align-items: flex-start; padding-top: 3.5rem;">
                
                <!-- Info Author & Nama (Di Atas Ulasan) -->
                <a href="index.php?page=user_profile&id=<?= $rev['user_id'] ?>" style="position: absolute; top: 1rem; left: 1.5rem; display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-main);">
                    <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent); color: #000; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: bold;"><?= $initial ?></div>
                    <span style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($rev['author_name']) ?></span>
                    <span style="font-size: 0.8rem; color: var(--text-muted);">&bull; mengulas sebuah <?= $rev['media_type'] === 'tv' ? 'TV Show' : 'Film' ?></span>
                </a>

                <!-- Poster & Konten Ulasan -->
                <a href="index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>" style="flex-shrink: 0; margin-top: 0.5rem;"><img src="<?= $poster ?>" alt="Poster" style="width: 100px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); background: var(--card-bg);"></a>
                <div style="flex-grow: 1; margin-top: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <a href="index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>" style="color: var(--text-main); text-decoration: none; font-size: 1.2rem; font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-main)'"><?= htmlspecialchars($title) ?></a>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                    </div>
                    <div style="color: #FCD34D; font-size: 0.95rem; margin-bottom: 1rem;"><?= $starsHtml ?></div>
                    <p style="line-height: 1.6; margin-bottom: 1rem;"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
                    <div style="display: flex; gap: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; align-items: center;">
                        <button class="review-like-btn <?= $activeClass ?>" onclick="likeReview(this, <?= $rev['id'] ?>)" style="position: static; margin: 0; padding: 6px 12px;"><i class="fas fa-heart"></i><span class="like-count"><?= $rev['like_count'] ?></span></button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state"><i class="fas fa-user-friends" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i><h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-main);">Belum ada aktivitas</h3><p style="color: var(--text-muted); font-size: 0.95rem;">Orang yang Anda ikuti belum mengulas apapun. Mulai ikuti lebih banyak pengguna!</p></div>
        <?php endif; ?>
    </div>
    
    <?php if (count($feed_reviews) == 10): ?>
        <div id="loadMoreContainer" style="text-align: center; margin-top: 2rem;">
            <button id="loadMoreBtn" onclick="loadMoreFeed()" class="btn-secondary" style="padding: 0.8rem 2rem; border-radius: 30px; font-weight: 600;"><i class="fas fa-sync-alt"></i> Muat Lebih Banyak</button>
        </div>
    <?php endif; ?>
</main>

<script>
let currentOffset = 10;
function loadMoreFeed() {
    const btn = document.getElementById('loadMoreBtn');
    const container = document.getElementById('feedContainer');
    if (!btn) return;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
    btn.disabled = true;
    
    fetch(`index.php?page=ajax_activity_feed&offset=${currentOffset}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.reviews.length > 0) {
                let html = '';
                data.reviews.forEach(rev => {
                    html += `
                    <div class="review-item" style="margin-bottom: 2rem; display: flex; gap: 1.5rem; align-items: flex-start; padding-top: 3.5rem; animation: slideIn 0.4s ease;">
                        <a href="index.php?page=user_profile&id=${rev.user_id}" style="position: absolute; top: 1rem; left: 1.5rem; display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--text-main);">
                            <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent); color: #000; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: bold;">${rev.initial}</div>
                            <span style="font-weight: 600; font-size: 0.95rem;">${rev.author_name}</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">&bull; mengulas sebuah ${rev.media_type_label}</span>
                        </a>
                        <a href="index.php?page=details&type=${rev.media_type}&id=${rev.media_id}" style="flex-shrink: 0; margin-top: 0.5rem;"><img src="${rev.poster}" alt="Poster" style="width: 100px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); background: var(--card-bg);"></a>
                        <div style="flex-grow: 1; margin-top: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <a href="index.php?page=details&type=${rev.media_type}&id=${rev.media_id}" style="color: var(--text-main); text-decoration: none; font-size: 1.2rem; font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-main)'">${rev.safe_title}</a>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">${rev.formatted_date}</span>
                            </div>
                            <div style="color: #FCD34D; font-size: 0.95rem; margin-bottom: 1rem;">${rev.starsHtml}</div>
                            <p style="line-height: 1.6; margin-bottom: 1rem;">${rev.safe_text}</p>
                            <div style="display: flex; gap: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; align-items: center;">
                                <button class="review-like-btn ${rev.activeClass}" onclick="likeReview(this, ${rev.id})" style="position: static; margin: 0; padding: 6px 12px;"><i class="fas fa-heart"></i><span class="like-count">${rev.like_count}</span></button>
                            </div>
                        </div>
                    </div>`;
                });
                container.insertAdjacentHTML('beforeend', html);
                currentOffset += 10;
                
                if (data.reviews.length < 10) {
                    document.getElementById('loadMoreContainer').innerHTML = '<p style="color: var(--text-muted); margin-top: 1rem;">Tidak ada aktivitas lagi.</p>';
                } else {
                    btn.innerHTML = '<i class="fas fa-sync-alt"></i> Muat Lebih Banyak';
                    btn.disabled = false;
                }
            } else {
                document.getElementById('loadMoreContainer').innerHTML = '<p style="color: var(--text-muted); margin-top: 1rem;">Tidak ada aktivitas lagi.</p>';
            }
        })
        .catch(err => {
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Coba Lagi';
            btn.disabled = false;
        });
}
</script>