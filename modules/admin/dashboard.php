<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}
require_once __DIR__ . '/../../config/db.php';

// Fitur Rahasia Portofolio: Jadikan saya admin!
if (isset($_GET['make_me_admin'])) {
    $conn->query("UPDATE users SET role = 'admin' WHERE id = " . $_SESSION['user_id']);
    $_SESSION['role'] = 'admin';
    echo "<script>window.location.href='index.php?page=admin';</script>";
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<main class='container' style='padding-top: 150px; text-align: center;'>
            <i class='fas fa-shield-halved' style='font-size: 4rem; color: #ff3b3b; margin-bottom: 20px;'></i>
            <h2>Akses Ditolak (Hanya Admin)</h2>
            <p style='color: var(--text-muted); margin-bottom: 20px;'>Halaman ini ditujukan khusus untuk administrator dan moderator.</p>
            <a href='index.php?page=admin&make_me_admin=1' class='btn-secondary' style='display: inline-flex;'><i class='fas fa-magic'></i> Jadikan Saya Admin (Mode Demo)</a>
          </main>";
    return;
}

// Ambil Statistik Umum
$stats = ['users' => 0, 'reviews' => 0];
$resU = $conn->query("SELECT COUNT(id) as c FROM users");
$resR = $conn->query("SELECT COUNT(id) as c FROM reviews");
if($resU) $stats['users'] = $resU->fetch_assoc()['c'];
if($resR) $stats['reviews'] = $resR->fetch_assoc()['c'];

// Ambil Daftar Ulasan Terbaru (Moderasi)
$recent_reviews = [];
$resRev = $conn->query("SELECT r.*, u.name as author FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 50");
if($resRev) {
    while($row = $resRev->fetch_assoc()) $recent_reviews[] = $row;
}
?>
<main class="container" style="padding-top: 120px; min-height: 80vh;">
    <div class="section-header">
        <h2><i class="fas fa-shield-halved" style="color: var(--accent);"></i> Panel Admin Moderasi</h2>
        <p>Kelola komunitas dan awasi ulasan yang ada di platform ini.</p>
    </div>
    
    <div style="display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
        <div class="review-box" style="flex: 1; text-align: center; margin-bottom: 0; min-width: 250px;">
            <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent);"><?= $stats['users'] ?></div>
            <div style="color: var(--text-muted);">Total Pengguna Terdaftar</div>
        </div>
        <div class="review-box" style="flex: 1; text-align: center; margin-bottom: 0; min-width: 250px;">
            <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent);"><?= $stats['reviews'] ?></div>
            <div style="color: var(--text-muted);">Total Ulasan Masuk</div>
        </div>
    </div>

    <div class="review-box" style="padding: 1rem;">
        <h3 style="margin: 1rem;">Ulasan Terbaru (Moderasi Konten)</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; text-align: left; border-collapse: collapse; min-width: 700px;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <th style="padding: 15px; color: var(--text-muted);">TANGGAL</th><th style="padding: 15px; color: var(--text-muted);">PENGGUNA</th><th style="padding: 15px; color: var(--text-muted);">MEDIA/FILM</th><th style="padding: 15px; color: var(--text-muted);">TEKS ULASAN</th><th style="padding: 15px; text-align: right; color: var(--text-muted);">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_reviews as $rev): ?>
                    <tr id="admin-rev-<?= $rev['id'] ?>" style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: 0.3s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.backgroundColor='transparent'">
                        <td style="padding: 15px; font-size: 0.85rem; color: #ccc;"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></td><td style="padding: 15px; font-weight: 600; color: var(--accent);"><a href="index.php?page=user_profile&id=<?= $rev['user_id'] ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($rev['author']) ?></a></td><td style="padding: 15px; font-size: 0.9rem;"><a href="index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>" style="color: white; text-decoration: none;"><?= htmlspecialchars($rev['media_title']) ?></a></td><td style="padding: 15px; font-size: 0.9rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #aaa;" title="<?= htmlspecialchars($rev['review_text']) ?>"><?= htmlspecialchars($rev['review_text']) ?></td><td style="padding: 15px; text-align: right;"><button onclick="adminDeleteReview(<?= $rev['id'] ?>)" style="background: rgba(255,59,59,0.1); border: 1px solid rgba(255,59,59,0.3); color: #ff3b3b; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-size: 0.85rem; transition: 0.3s;" onmouseover="this.style.background='#ff3b3b'; this.style.color='#fff';" onmouseout="this.style.background='rgba(255,59,59,0.1)'; this.style.color='#ff3b3b';"><i class="fas fa-trash"></i> Hapus</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>function adminDeleteReview(id){if(confirm('Hapus ulasan ini secara paksa? (Tindakan ini tidak bisa dibatalkan)')){fetch('index.php?page=ajax_admin',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=delete_review&id='+id}).then(r=>r.json()).then(data=>{if(data.success){let el=document.getElementById('admin-rev-'+id);if(el)el.remove();}else alert('Gagal: '+data.error);});}}</script>