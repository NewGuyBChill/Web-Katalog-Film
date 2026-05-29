<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$uid = isset($_GET['id']) ? (int)$_GET['id'] : ($_SESSION['user_id'] ?? 0);
$tab = isset($_GET['tab']) && $_GET['tab'] === 'following' ? 'following' : 'followers';

if ($uid === 0) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}

// Info User
$user_info = null;
$res = $conn->query("SELECT name FROM users WHERE id = $uid");
if ($res && $res->num_rows > 0) {
    $user_info = $res->fetch_assoc();
} else {
    echo "<main class='container' style='padding-top: 150px; text-align: center;'><h2>Pengguna Tidak Ditemukan</h2></main>";
    return;
}

$users = [];
if ($tab === 'followers') {
    $sql = "SELECT u.id, u.name, u.created_at FROM user_follows uf JOIN users u ON uf.follower_id = u.id WHERE uf.following_id = $uid ORDER BY uf.created_at DESC";
} else {
    $sql = "SELECT u.id, u.name, u.created_at FROM user_follows uf JOIN users u ON uf.following_id = u.id WHERE uf.follower_id = $uid ORDER BY uf.created_at DESC";
}

$res_users = $conn->query($sql);
if ($res_users) {
    while($row = $res_users->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="section-header" style="margin-bottom: 2rem; text-align: center;">
        <h2><?= htmlspecialchars($user_info['name']) ?> - <?= $tab === 'followers' ? 'Pengikut' : 'Mengikuti' ?></h2>
        <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1rem;">
            <a href="index.php?page=user_follows&id=<?= $uid ?>&tab=followers" class="btn-<?= $tab === 'followers' ? 'primary' : 'secondary' ?>" style="padding: 0.5rem 1.5rem; text-decoration: none; border-radius: 20px;">Pengikut</a>
            <a href="index.php?page=user_follows&id=<?= $uid ?>&tab=following" class="btn-<?= $tab === 'following' ? 'primary' : 'secondary' ?>" style="padding: 0.5rem 1.5rem; text-decoration: none; border-radius: 20px;">Mengikuti</a>
        </div>
    </div>
    
    <div style="max-width: 600px; margin: 0 auto;">
        <?php if(count($users) > 0): ?>
            <?php foreach($users as $u): 
                $initial = strtoupper(substr($u['name'], 0, 1));
            ?>
            <a href="index.php?page=user_profile&id=<?= $u['id'] ?>" class="review-box" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; margin-bottom: 1rem; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: #000;"><?= $initial ?></div>
                <div>
                    <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($u['name']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">Anggota sejak <?= date('M Y', strtotime($u['created_at'])) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state"><i class="fas fa-users" style="font-size: 3rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i><h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: var(--text-main);">Tidak ada pengguna</h3></div>
        <?php endif; ?>
    </div>
</main>