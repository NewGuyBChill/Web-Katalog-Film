<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$uid = (int)$_SESSION['user_id'];
$filter = $_GET['filter'] ?? 'all';
$filterSql = "";
if ($filter === 'movie') $filterSql = " AND media_type = 'movie'";
elseif ($filter === 'tv') $filterSql = " AND media_type = 'tv'";

$items = [];
$res = $conn->query("SELECT * FROM watchlist WHERE user_id = $uid $filterSql ORDER BY created_at DESC");
if ($res) {
    while($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
}
?>
<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header">
        <div class="header-titles">
            <h2><?= translateText('my_watchlist') ?></h2>
            <p><?= translateText('watchlist_desc') ?></p>
        </div>
    </div>
    
    <div class="filter-tabs">
        <a href="index.php?page=watchlist&filter=all" class="tab-btn <?= $filter === 'all' ? 'active' : '' ?>"><?= translateText('all_media') ?></a>
        <a href="index.php?page=watchlist&filter=movie" class="tab-btn <?= $filter === 'movie' ? 'active' : '' ?>"><?= translateText('movies_only') ?></a>
        <a href="index.php?page=watchlist&filter=tv" class="tab-btn <?= $filter === 'tv' ? 'active' : '' ?>"><?= translateText('tv_only') ?></a>
    </div>

    <div class="movies-grid">
        <?php if(count($items) > 0): ?>
            <?php foreach($items as $item): ?>
            <a href="index.php?page=details&type=<?= $item['media_type'] ?>&id=<?= $item['media_id'] ?>" class="grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <img src="<?= htmlspecialchars((string)$item['poster_path']) ?>" alt="<?= htmlspecialchars((string)$item['title']) ?>" style="width: 100%; aspect-ratio: 2/3; object-fit: cover;">
                    <div class="watchlist-btn active" data-id="<?= $item['media_id'] ?>" data-title="<?= htmlspecialchars((string)$item['title']) ?>" onclick="toggleWatchlist(event, this)">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars((string)$item['title']) ?></div>
                    <div class="grid-movie-meta" style="text-transform: capitalize;"><?= $item['media_type'] === 'tv' ? 'TV Show' : 'Movie' ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bookmark" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;"><?= translateText('empty_watchlist') ?></h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;"><?= translateText('empty_watchlist_desc') ?></p>
                <a href="index.php?page=movies" class="btn-primary" style="text-decoration: none;"><?= translateText('explore_now') ?></a>
            </div>
        <?php endif; ?>
    </div>
</main>