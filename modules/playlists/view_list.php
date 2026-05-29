<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$list_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($list_id === 0) {
    echo "<script>window.location.href='index.php?page=home';</script>";
    exit;
}

$playlistInfo = null;
$res = $conn->query("SELECT cp.*, u.name as creator_name FROM custom_playlists cp JOIN users u ON cp.user_id = u.id WHERE cp.id = $list_id");
if ($res && $res->num_rows > 0) {
    $playlistInfo = $res->fetch_assoc();
} else {
    echo "<main class='container' style='padding-top: 150px; text-align: center;'><h2>Daftar tidak ditemukan</h2></main>";
    return;
}

$is_owner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $playlistInfo['user_id']);

$items = [];
$resItems = $conn->query("SELECT * FROM playlist_items WHERE playlist_id = $list_id ORDER BY added_at DESC");
if ($resItems) {
    while($row = $resItems->fetch_assoc()) $items[] = $row;
}
?>
<main class="container" style="padding-top: 120px; min-height: 80vh;">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
        <div style="margin-bottom: 0;">
            <h2 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 0.5rem;"><i class="fas fa-list" style="color: var(--accent);"></i> <?= htmlspecialchars($playlistInfo['name']) ?></h2>
            <p style="color: #ccc; margin-bottom: 0.5rem; font-size: 1.05rem;"><?= htmlspecialchars($playlistInfo['description']) ?></p>
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Dibuat oleh <a href="index.php?page=user_profile&id=<?= $playlistInfo['user_id'] ?>" style="color: var(--accent); text-decoration: none;"><?= htmlspecialchars($playlistInfo['creator_name']) ?></a> &bull; <?= count($items) ?> item
            </div>
        </div>
        
        <?php if($is_owner): ?>
            <button onclick="deletePlaylist(<?= $list_id ?>)" class="btn-secondary" style="background: rgba(255, 59, 59, 0.1); color: #ff3b3b; border-color: rgba(255, 59, 59, 0.3);"><i class="fas fa-trash"></i> Hapus Daftar Ini</button>
        <?php endif; ?>
    </div>

    <div class="movies-grid" style="gap: 20px;">
        <?php if(count($items) > 0): ?>
            <?php foreach($items as $item): ?>
            <div class="grid-movie-card" id="list-item-<?= $item['id'] ?>" style="position: relative; transition: all 0.3s ease;">
                <a href="index.php?page=details&type=<?= $item['media_type'] ?>&id=<?= $item['media_id'] ?>" style="text-decoration: none; color: inherit; display: block;">
                    <div class="grid-movie-img-wrap">
                        <img src="<?= htmlspecialchars((string)$item['media_poster']) ?>" alt="<?= htmlspecialchars((string)$item['media_title']) ?>">
                    </div>
                    <div class="grid-movie-info">
                        <div class="grid-movie-title"><?= htmlspecialchars((string)$item['media_title']) ?></div>
                        <div class="grid-movie-meta" style="text-transform: capitalize;"><?= $item['media_type'] === 'tv' ? 'TV Show' : 'Movie' ?></div>
                    </div>
                </a>
                <?php if($is_owner): ?>
                    <button onclick="removePlaylistItem(<?= $item['id'] ?>)" style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.8); color: #ff3b3b; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s; z-index: 10;" onmouseover="this.style.background='#ff3b3b'; this.style.color='#fff';" onmouseout="this.style.background='rgba(0,0,0,0.8)'; this.style.color='#ff3b3b';" title="Hapus dari daftar">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1; padding: 60px 20px;">
                <i class="fas fa-film" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.5rem; color: white; margin-bottom: 0.5rem;">Daftar masih kosong</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Cari film atau acara TV dan tambahkan ke daftar ini.</p>
                <a href="index.php?page=movies" class="btn-primary" style="margin-top: 1.5rem; display: inline-block; text-decoration: none;">Eksplorasi Film</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php if($is_owner): ?>
<script>
function removePlaylistItem(itemId) {
    if(confirm('Hapus item ini dari daftar?')) {
        fetch('index.php?page=ajax_playlist', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'action=remove&item_id=' + itemId })
        .then(r => r.json()).then(data => { if(data.success) { let el = document.getElementById('list-item-' + itemId); if(el) { el.style.transform = 'scale(0.8)'; el.style.opacity = '0'; setTimeout(() => { el.remove(); if(document.querySelectorAll('.grid-movie-card').length === 0) window.location.reload(); }, 300); } } else alert('Gagal: ' + data.error); });
    }
}
function deletePlaylist(playlistId) {
    if(confirm('Apakah Anda yakin ingin menghapus seluruh daftar kustom ini beserta isinya? Tindakan ini tidak bisa dibatalkan.')) {
        fetch('index.php?page=ajax_playlist', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'action=delete_playlist&playlist_id=' + playlistId })
        .then(r => r.json()).then(data => { if(data.success) window.location.href = 'index.php?page=my_lists'; else alert('Gagal: ' + data.error); });
    }
}
</script>
<?php endif; ?>