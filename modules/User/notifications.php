<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$uid = (int)$_SESSION['user_id'];

// Aksi untuk 'Tandai Semua Dibaca'
if (isset($_GET['action']) && $_GET['action'] === 'read_all') {
    $conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $uid AND is_read = 0");
    echo "<script>window.location.href='index.php?page=notifications';</script>";
    exit;
}

// Aksi untuk 'Hapus Semua Notifikasi'
if (isset($_GET['action']) && $_GET['action'] === 'delete_all') {
    $conn->query("DELETE FROM notifications WHERE user_id = $uid");
    echo "<script>window.location.href='index.php?page=notifications';</script>";
    exit;
}

// Aksi untuk 'Hapus Satu Notifikasi'
if (isset($_GET['action']) && $_GET['action'] === 'delete_single' && isset($_GET['nid'])) {
    $nid = (int)$_GET['nid'];
    $conn->query("DELETE FROM notifications WHERE id = $nid AND user_id = $uid");
    echo "<script>window.location.href='index.php?page=notifications';</script>";
    exit;
}

$notifications = [];
$res = $conn->query("SELECT * FROM notifications WHERE user_id = $uid ORDER BY created_at DESC");
if ($res) {
    while($row = $res->fetch_assoc()) {
        $notifications[] = $row;
    }
}
?>
<main style="padding-top: 120px; min-height: 80vh;" class="container">

    <div style="max-width: 900px; margin: 0 auto;">
        <div class="movies-header" style="display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem; gap: 1rem;">
            <div class="notifications-header" style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 0;">
                <h2 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 0; color: var(--text-main);"><i class="fas fa-bell" style="color: var(--accent);"></i> Riwayat Notifikasi</h2>
                <p style="color: var(--text-muted); margin-top: 0.5rem; margin-bottom: 0;">Semua pembaruan aktivitas dan pemberitahuan dari akun Anda.</p>
            </div>
            <?php if(count($notifications) > 0): ?>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                    <a href="#" onclick="event.preventDefault(); markAllReadPage()" class="btn-secondary" style="text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 20px; font-size: 0.9rem;"><i class="fas fa-check-double"></i> Tandai Semua Dibaca</a>
                    <a href="#" onclick="event.preventDefault(); deleteAllNotifs()" class="btn-secondary" style="background: rgba(255, 59, 59, 0.1); color: #ff3b3b; border-color: rgba(255, 59, 59, 0.3); text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 20px; font-size: 0.9rem;"><i class="fas fa-trash"></i> Hapus Semua</a>
                </div>
            <?php endif; ?>
        </div>
        <?php if(count($notifications) > 0): ?>
            <div id="notificationsContainer" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden;">
            <?php foreach($notifications as $notif): 
                $is_unread = $notif['is_read'] == 0;
                $bg_class = $is_unread ? 'background: rgba(0, 210, 255, 0.05);' : 'background: transparent;';
                $icon = 'fa-bell';
                if ($notif['type'] == 'like') $icon = 'fa-heart';
                elseif ($notif['type'] == 'follow') $icon = 'fa-user-plus';
                elseif ($notif['type'] == 'recommendation') $icon = 'fa-magic';
            ?>
            <div class="notif-row-item <?= $is_unread ? 'unread-row' : '' ?>" style="<?= $bg_class ?> padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; gap: 1.5rem; align-items: flex-start; transition: 0.3s; position: relative;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.04)'" onmouseout="this.style.backgroundColor=this.classList.contains('unread-row') ? 'rgba(0, 210, 255, 0.05)' : 'transparent'">
                <a href="#" onclick="event.preventDefault(); deleteSingleNotif(<?= $notif['id'] ?>, this)" style="position: absolute; top: 1.5rem; right: 1.5rem; color: #ff3b3b; opacity: 0.5; transition: 0.3s; text-decoration: none;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'" title="Hapus Notifikasi"><i class="fas fa-times"></i></a>
                <div style="width: 45px; height: 45px; border-radius: 12px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: var(--accent); flex-shrink: 0;"><i class="fas <?= $icon ?>"></i></div>
                <div style="flex-grow: 1; padding-right: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 10px;">
                        <h4 style="margin: 0; font-size: 1.05rem; color: var(--text-main); font-weight: 600;"><?= htmlspecialchars($notif['title']) ?><?php if($is_unread): ?><span class="new-badge" style="background: var(--accent); color: #000; font-size: 0.65rem; padding: 2px 6px; border-radius: 10px; margin-left: 8px; vertical-align: middle;">Baru</span><?php endif; ?></h4>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="far fa-clock"></i> <?= date('d M Y, H:i', strtotime($notif['created_at'])) ?></span>
                    </div>
                    <p style="margin: 0; color: #ccc; font-size: 0.95rem; line-height: 1.5;"><?= $notif['message'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state"><i class="fas fa-bell-slash" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i><h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;">Belum Ada Notifikasi</h3><p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;">Anda belum memiliki riwayat pemberitahuan atau aktivitas di akun ini.</p></div>
        <?php endif; ?>
    </div>
</main>

<script>
function triggerBadgeUpdate() {
    // Panggil ulang pemuatan notifikasi yang ada di navigation bar!
    if (typeof window.loadNotifications === 'function') {
        window.loadNotifications();
    }
}

function markAllReadPage() {
    fetch('index.php?page=ajax_notifications', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=mark_all_read'
    }).then(r => r.json()).then(data => {
        if(data.success) {
            document.querySelectorAll('.unread-row').forEach(row => {
                row.classList.remove('unread-row');
                row.style.backgroundColor = 'transparent';
                const badge = row.querySelector('.new-badge');
                if (badge) badge.remove();
            });
            triggerBadgeUpdate();
        }
    });
}

function deleteAllNotifs() {
    if(confirm('Apakah Anda yakin ingin menghapus semua notifikasi? Tindakan ini tidak bisa dibatalkan.')) {
        fetch('index.php?page=ajax_notifications', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=delete_all'
        }).then(r => r.json()).then(data => {
            if(data.success) {
                const container = document.getElementById('notificationsContainer');
                if(container) container.innerHTML = '';
                triggerBadgeUpdate();
                window.location.reload(); // Untuk memunculkan UI "Kosong"
            }
        });
    }
}

function deleteSingleNotif(nid, btn) {
    fetch('index.php?page=ajax_notifications', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=delete_single&nid=' + nid
    }).then(r => r.json()).then(data => {
        if(data.success) {
            const row = btn.closest('.notif-row-item');
            if (row) {
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    if (document.querySelectorAll('.notif-row-item').length === 0) {
                        window.location.reload();
                    }
                }, 300);
            }
            triggerBadgeUpdate();
        }
    });
}
</script>