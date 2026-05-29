<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$uid = (int)$_SESSION['user_id'];

// Auto-create tabel Playlists (Daftar) & Relasi (Item) jika belum ada di database
$conn->query("CREATE TABLE IF NOT EXISTS custom_playlists (
    id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, name VARCHAR(255) NOT NULL,
    description TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

$conn->query("CREATE TABLE IF NOT EXISTS playlist_items (
    id INT AUTO_INCREMENT PRIMARY KEY, playlist_id INT NOT NULL, media_id INT NOT NULL, media_type VARCHAR(20) NOT NULL,
    media_title VARCHAR(255), media_poster VARCHAR(255), added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (playlist_id) REFERENCES custom_playlists(id) ON DELETE CASCADE
)");

// Proses Permintaan Form Tambah Playlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['list_name'])) {
    $name = $conn->real_escape_string(trim($_POST['list_name']));
    $desc = $conn->real_escape_string(trim($_POST['list_desc']));
    if (!empty($name)) {
        $conn->query("INSERT INTO custom_playlists (user_id, name, description) VALUES ($uid, '$name', '$desc')");
        echo "<script>window.location.href='index.php?page=my_lists';</script>";
        exit;
    }
}

// Ambil Daftar Playlist Milik Pengguna Saat Ini
$playlists = [];
$res = $conn->query("SELECT p.*, (SELECT COUNT(id) FROM playlist_items WHERE playlist_id = p.id) as item_count FROM custom_playlists p WHERE user_id = $uid ORDER BY created_at DESC");
if ($res) {
    while($row = $res->fetch_assoc()) $playlists[] = $row;
}
?>
<main class="container" style="padding-top: 120px; min-height: 80vh;">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
        <div style="margin-bottom: 0;">
            <h2 style="font-size: 2.2rem; font-weight: 800;"><i class="fas fa-list" style="color: var(--accent);"></i> Daftar Kustom Saya</h2>
            <p style="margin-bottom: 0;">Kelompokkan koleksi filmmu (Contoh: "Film Horor Terbaik", "Tonton Nanti Bersama Keluarga").</p>
        </div>
        <button onclick="document.getElementById('createListModal').style.display='flex'" class="btn-primary" style="padding: 0.8rem 1.8rem; border-radius: 30px; font-weight: 600;"><i class="fas fa-plus"></i> Buat Daftar Baru</button>
    </div>

    <div class="movies-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php if(count($playlists) > 0): ?>
            <?php foreach($playlists as $list): ?>
            <a href="index.php?page=view_list&id=<?= $list['id'] ?>" style="text-decoration: none; color: inherit; display: block;">
                <div class="review-box" style="margin-bottom: 0; position: relative; overflow: hidden; transition: transform 0.3s; padding: 1.5rem;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: var(--accent);"></div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 700;"><?= htmlspecialchars($list['name']) ?></h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5; height: 2.85rem;"><?= htmlspecialchars($list['description'] ?: 'Tidak ada deskripsi.') ?></p>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                        <span style="font-size: 0.85rem; color: #aaa; background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 10px;"><i class="fas fa-film"></i> <?= $list['item_count'] ?> Item</span>
                        <span style="font-size: 0.85rem; color: var(--accent); cursor: pointer; font-weight: 600;">Lihat Isi Daftar <i class="fas fa-arrow-right"></i></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1 / -1; padding: 60px 20px;"><i class="fas fa-folder-open" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i><h3 style="font-size: 1.5rem; color: white; margin-bottom: 0.5rem;">Belum Ada Daftar</h3><p style="color: var(--text-muted); font-size: 0.95rem;">Mulai kumpulkan dan atur film favoritmu ke dalam daftar kustom.</p></div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Form Tambah Daftar (Popup) -->
<div id="createListModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="auth-card" style="width: 90%; max-width: 450px; position: relative; margin: 0; box-shadow: 0 20px 60px rgba(0,0,0,0.8);">
        <i class="fas fa-times" onclick="document.getElementById('createListModal').style.display='none'" style="position: absolute; top: 20px; right: 20px; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; transition: 0.3s;" onmouseover="this.style.color='#ff3b3b'" onmouseout="this.style.color='var(--text-muted)'"></i>
        <h2 style="margin-bottom: 0.5rem;">Buat Daftar Baru</h2>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Berikan nama untuk daftar kustom (playlist) film Anda yang baru.</p>
        <form method="POST" style="display: flex; flex-direction: column; gap: 1.2rem;"><div class="auth-input-group"><label style="font-size: 0.85rem; color: #aaa;">Nama Daftar</label><input type="text" name="list_name" class="auth-input" placeholder="Contoh: Film Horor Terseram..." required></div><div class="auth-input-group"><label style="font-size: 0.85rem; color: #aaa;">Deskripsi Singkat (Opsional)</label><textarea name="list_desc" class="auth-input" rows="3" placeholder="Jelaskan tentang daftar ini..."></textarea></div><button type="submit" class="auth-btn" style="margin-top: 1rem;"><i class="fas fa-save"></i> Simpan Daftar</button></form>
    </div>
</div>