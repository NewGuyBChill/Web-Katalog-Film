<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

// Jika tidak ada parameter ID di URL, berarti buka profil sendiri (wajib login)
$uid = isset($_GET['id']) ? (int)$_GET['id'] : ($_SESSION['user_id'] ?? 0);

if ($uid === 0) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}

// Handle Pencarian User
$search_user = isset($_GET['search_user']) ? trim($_GET['search_user']) : '';
$search_results = [];
if (!empty($search_user)) {
    $safe_search = $conn->real_escape_string($search_user);
    $res_search = $conn->query("SELECT id, name, avatar, created_at FROM users WHERE name LIKE '%$safe_search%' LIMIT 20");
    if ($res_search) {
        while($row = $res_search->fetch_assoc()) $search_results[] = $row;
    }
}

// 1. Ambil Info Pengguna
$user_info = null;
$res = $conn->query("SELECT name, created_at, avatar FROM users WHERE id = $uid");
if ($res && $res->num_rows > 0) {
    $user_info = $res->fetch_assoc();
} else {
    echo "<main class='container' style='padding-top: 150px; text-align: center; min-height: 70vh;'>
            <i class='fas fa-user-slash' style='font-size: 4rem; color: #ff3b3b; margin-bottom: 20px;'></i>
            <h2>Pengguna Tidak Ditemukan</h2>
            <a href='index.php?page=home' class='btn-primary' style='margin-top: 20px; display: inline-flex;'><i class='fas fa-home'></i> Kembali ke Beranda</a>
          </main>";
    return;
}

$uname = $user_info['name'];
$initial = strtoupper(substr($uname, 0, 1));
$member_since = date('F Y', strtotime($user_info['created_at'] ?? 'now'));

// Warna Avatar Dinamis (Persis seperti di Navbar)
$avatarColors = [
    'linear-gradient(135deg, #f5576c 0%, #f093fb 100%)',
    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
    'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
    'linear-gradient(135deg, #ff0844 0%, #ffb199 100%)'
];
$colorIndex = ord($initial) % count($avatarColors);
$activeAvatarBg = $avatarColors[$colorIndex];

// 2. Ambil Statistik
$stats = ['reviews' => 0, 'watchlist' => 0, 'followers' => 0, 'following' => 0];
$res_rev = $conn->query("SELECT COUNT(id) as count FROM reviews WHERE user_id = $uid");
if ($res_rev) $stats['reviews'] = $res_rev->fetch_assoc()['count'];

$res_watch = $conn->query("SELECT COUNT(id) as count FROM watchlist WHERE user_id = $uid");
if ($res_watch) $stats['watchlist'] = $res_watch->fetch_assoc()['count'];

$res_followers = $conn->query("SELECT COUNT(id) as count FROM user_follows WHERE following_id = $uid");
if ($res_followers) $stats['followers'] = $res_followers->fetch_assoc()['count'];

$res_following = $conn->query("SELECT COUNT(id) as count FROM user_follows WHERE follower_id = $uid");
if ($res_following) $stats['following'] = $res_following->fetch_assoc()['count'];

// 3. Ambil Feed Aktivitas / Ulasan Terbaru
$reviews = [];
$current_user_id = $_SESSION['user_id'] ?? 0;
$is_following = false;
if ($current_user_id > 0 && $uid != $current_user_id) {
    $res_follow_check = $conn->query("SELECT id FROM user_follows WHERE follower_id = $current_user_id AND following_id = $uid");
    if ($res_follow_check && $res_follow_check->num_rows > 0) {
        $is_following = true;
    }
}

$res_reviews = $conn->query("
    SELECT r.*, 
           (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id) as like_count,
           (SELECT COUNT(id) FROM review_likes WHERE review_id = r.id AND user_id = $current_user_id) as is_liked_by_user
    FROM reviews r 
    WHERE user_id = $uid 
    ORDER BY created_at DESC LIMIT 15
");
if ($res_reviews) {
    while($row = $res_reviews->fetch_assoc()) {
        $reviews[] = $row;
    }
}

// 4. Ambil Pemeran Favorit
$favorite_casts = [];
$res_casts = $conn->query("SELECT * FROM favorite_casts WHERE user_id = $uid ORDER BY created_at DESC");
if ($res_casts) {
    while($row = $res_casts->fetch_assoc()) {
        $favorite_casts[] = $row;
    }
}
?>

<div class="dashboard-container">
    <?php require_once __DIR__ . '/../../includes/account_sidebar.php'; ?>
    <main class="dash-main container" style="min-height: 80vh;">
    
    <!-- Form Pencarian Pengguna -->
    <div id="userSearchContainer" style="max-width: 700px; margin: 0 0 2rem 0; position: relative;">
        <form action="index.php" method="GET" style="display: flex; gap: 10px;">
            <input type="hidden" name="page" value="user_profile">
            <input type="text" id="userSearchInput" name="search_user" value="<?= htmlspecialchars($search_user) ?>" autocomplete="off" placeholder="Cari teman atau pengguna lain..." style="flex-grow: 1; padding: 12px 20px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; outline: none; font-size: 1rem; transition: 0.3s;">
            <button type="submit" class="btn-primary" style="padding: 12px 25px; border-radius: 30px;"><i class="fas fa-search"></i></button>
            <?php if(!empty($search_user)): ?>
                <a href="index.php?page=user_profile" class="btn-secondary" style="padding: 12px 20px; border-radius: 30px; text-decoration: none;"><i class="fas fa-times"></i></a>
            <?php endif; ?>
        </form>
        <div id="userLiveSearchResults" class="live-search-results" style="top: 100%; margin-top: 10px; width: 100%; min-width: unset; max-height: 350px;"></div>
    </div>

    <?php if(!empty($search_user)): ?>
    <!-- Hasil Pencarian Pengguna -->
    <div class="section-header" style="margin-bottom: 1.5rem; text-align: center;">
        <h2>Hasil Pencarian untuk "<?= htmlspecialchars($search_user) ?>"</h2>
    </div>
    <div style="max-width: 600px; margin: 0 auto 3rem auto;">
        <?php if(count($search_results) > 0): ?>
            <?php foreach($search_results as $u): 
                $u_init = strtoupper(substr($u['name'], 0, 1));
            ?>
            <a href="index.php?page=user_profile&id=<?= $u['id'] ?>" class="review-box" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; margin-bottom: 1rem; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <?php if(!empty($u['avatar'])): ?>
                    <img src="<?= htmlspecialchars($u['avatar']) ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: #000;"><?= $u_init ?></div>
                <?php endif; ?>
                <div>
                    <div style="font-size: 1.1rem; font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($u['name']) ?></div>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">Anggota sejak <?= date('M Y', strtotime($u['created_at'])) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state"><i class="fas fa-search" style="font-size: 3rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i><h3 style="font-size: 1.2rem; color: white;">Pengguna tidak ditemukan</h3></div>
        <?php endif; ?>
    </div>
    <?php else: ?>

    <style>
    .profile-split-grid {
        display: grid;
        grid-template-columns: 1.8fr 1.2fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .profile-split-grid {
            grid-template-columns: 1fr;
        }
    }
    .list-card-grid {
        display: grid; 
        grid-template-columns: 1fr 2fr 1.5fr 1fr; 
        background: rgba(0,0,0,0.2); 
        border-radius: 12px; 
        padding: 1rem; 
        align-items: center; 
        gap: 1rem;
        transition: transform 0.2s;
    }
    .list-card-grid:hover {
        transform: scale(1.01);
        background: rgba(0,0,0,0.3);
    }
    @media (max-width: 768px) {
        .list-card-grid {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        .list-card-grid > div {
            text-align: left !important;
        }
        .list-card-badge {
            float: none !important;
            display: inline-block !important;
        }
    }
    .profile-action-btn {
        padding: 0.5rem 1.2rem;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: 0.3s;
        cursor: pointer;
    }
    .btn-outline-accent {
        border: 1px solid var(--accent);
        color: var(--accent);
        background: transparent;
    }
    .btn-outline-accent:hover {
        background: rgba(0, 210, 255, 0.1);
    }
    </style>

    <!-- Tahap 1: Header Halaman -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
            User profile
        </h1>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
            <div style="display: flex; gap: 10px;">
                <?php if ($uid == $current_user_id): ?>
                    <a href="javascript:window.print()" class="profile-action-btn btn-outline-accent">PRINT</a>
                    <a href="index.php?page=profile" class="profile-action-btn btn-primary" style="border: none;">EDIT</a>
                <?php else: 
                    $follow_btn_class = $is_following ? 'btn-secondary active' : 'btn-primary';
                    $follow_btn_icon = $is_following ? 'fa-user-check' : 'fa-user-plus';
                    $follow_btn_text = $is_following ? 'FOLLOWING' : 'FOLLOW';
                ?>
                    <button class="profile-action-btn <?= $follow_btn_class ?>" onclick="toggleFollow(this, <?= $uid ?>)" style="border: none; display: flex; align-items: center; gap: 6px;">
                        <i class="fas <?= $follow_btn_icon ?>"></i> <?= $follow_btn_text ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tahap 2: Grid Atas (3 Kartu Informasi) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Kartu 1: Identitas Utama (Kiri) -->
        <div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 2.5rem 1.5rem; text-align: center; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <?php if(!empty($user_info['avatar'])): ?>
                <img src="<?= htmlspecialchars($user_info['avatar']) ?>" alt="Avatar" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; margin-bottom: 1.2rem; border: 4px solid rgba(255,255,255,0.1);">
            <?php else: ?>
                <div style="width: 110px; height: 110px; border-radius: 50%; background: <?= $activeAvatarBg ?>; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; font-weight: 800; color: white; margin-bottom: 1.2rem; border: 4px solid rgba(255,255,255,0.1);">
                    <?= $initial ?>
                </div>
            <?php endif; ?>
            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;"><?= htmlspecialchars($uname) ?></h2>
            <p style="color: var(--accent); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.2rem;">ID: #<?= str_pad($uid, 5, '0', STR_PAD_LEFT) ?></p>
            <p style="color: var(--text-muted); font-size: 0.8rem;"><?= strtolower(str_replace(' ', '', htmlspecialchars($uname))) ?>@celesview.com</p>
        </div>

        <!-- Kartu 2: Informasi Umum (Tengah) -->
        <div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 1.8rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.8rem;">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 8px;">General information <i class="fas fa-pen" style="color: var(--accent); font-size: 0.75rem; cursor: pointer;"></i></h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 1.2rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Registration Date:</span>
                    <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;"><?= $member_since ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Account Status:</span>
                    <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;">Active Member</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Favorite Casts:</span>
                    <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;"><?= count($favorite_casts) ?> Actors</span>
                </div>
            </div>
        </div>

        <!-- Kartu 3: Informasi Spesifik (Kanan) -->
        <div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 1.8rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.8rem;">
                <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text-main); display: flex; align-items: center; gap: 8px;">Platform Statistics <i class="fas fa-pen" style="color: var(--accent); font-size: 0.75rem; cursor: pointer;"></i></h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 1.2rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Reviews Written:</span>
                    <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;"><?= $stats['reviews'] ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Watchlist Items:</span>
                    <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;"><?= $stats['watchlist'] ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Followers:</span>
                    <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;"><?= $stats['followers'] ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Following:</span>
                    <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 600;"><?= $stats['following'] ?></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Tahap 3 & 4: Grid Bawah (Split Layout 60% : 40%) -->
    <div class="profile-split-grid">
        
        <!-- Area Kiri Bawah (Navigasi Tab & Daftar Aktivitas) -->
        <div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 1.8rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <!-- Navigasi Tab -->
            <div style="display: flex; gap: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 1.5rem;">
                <div style="padding-bottom: 0.8rem; border-bottom: 2px solid var(--accent); color: var(--accent); font-weight: 600; font-size: 0.9rem; cursor: pointer;">Recent Reviews (<?= count($reviews) ?>)</div>
                <a href="index.php?page=watchlist" style="padding-bottom: 0.8rem; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; cursor: pointer; text-decoration: none;">Watchlist (<?= $stats['watchlist'] ?>)</a>
            </div>

            <!-- Daftar Aktivitas (List Cards) -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (count($reviews) > 0): ?>
                    <?php foreach($reviews as $rev): 
                        $title = $rev['media_title'] ?? 'Unknown Media';
                        $starsHtml = '';
                        for($i=0; $i<5; $i++) { $starsHtml .= $i < $rev['rating'] ? '<i class="fas fa-star" style="color: #FCD34D;"></i>' : '<i class="far fa-star" style="color: #FCD34D;"></i>'; }
                        $activeClass = !empty($rev['is_liked_by_user']) ? 'active' : '';
                    ?>
                    <!-- Internal Card Grid (4 Kolom) -->
                    <div class="list-card-grid">
                        <!-- [Tanggal/Waktu] -->
                        <div>
                            <div style="color: var(--text-muted); font-size: 0.7rem; margin-bottom: 0.3rem;">Time: <?= date('H:i', strtotime($rev['created_at'])) ?></div>
                            <div style="color: var(--text-main); font-weight: 700; font-size: 0.9rem;"><?= date('d M Y', strtotime($rev['created_at'])) ?></div>
                        </div>
                        <!-- [Nama Layanan/Aktivitas] -> Judul Film -->
                        <div>
                            <div style="color: var(--text-muted); font-size: 0.7rem; margin-bottom: 0.3rem;">Service:</div>
                            <a href="index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>" style="color: var(--text-main); font-weight: 600; font-size: 0.9rem; text-decoration: none; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90%;"><?= htmlspecialchars($title) ?></a>
                        </div>
                        <!-- [Nama Penanggung Jawab] -> Rating Bintang -->
                        <div>
                            <div style="color: var(--text-muted); font-size: 0.7rem; margin-bottom: 0.3rem;">Rating Given:</div>
                            <div style="font-size: 0.75rem; letter-spacing: 2px;"><?= $starsHtml ?></div>
                        </div>
                        <!-- [Badge Status] -> Status Likes -->
                        <div style="text-align: right;">
                            <div style="color: var(--text-muted); font-size: 0.7rem; margin-bottom: 0.3rem; text-align: left;">Status:</div>
                            <button onclick="likeReview(this, <?= $rev['id'] ?>)" class="list-card-badge <?= $activeClass ?>" style="display: inline-block; background: rgba(46, 213, 115, 0.15); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.3); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; float: left; cursor: pointer; transition: 0.3s;">
                                <i class="fas fa-heart"></i> <span class="like-count"><?= $rev['like_count'] ?></span> Likes
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted); font-size: 0.9rem;">
                        <i class="fas fa-comment-slash" style="font-size: 2.5rem; opacity: 0.5; margin-bottom: 1rem; display: block;"></i>
                        No recent activity found.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tahap 5: Area Kanan Bawah (Dokumen & Catatan) -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Kartu Atas (Files/Documents -> Favorite Casts) -->
            <div style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 1.8rem; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--text-main);">Favorite Casts</h3>
                    <a href="#" class="profile-action-btn btn-outline-accent" style="padding: 4px 12px; font-size: 0.75rem;">DOWNLOAD</a>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1.2rem;">
                    <?php if (count($favorite_casts) > 0): ?>
                        <?php foreach(array_slice($favorite_casts, 0, 4) as $cast): 
                            $castImg = !empty($cast['cast_image']) ? $cast['cast_image'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2240%22%20height%3D%2240%22%20fill%3D%22%23222%22%3E%3Crect%20width%3D%2240%22%20height%3D%2240%22%2F%3E%3C%2Fsvg%3E";
                        ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="<?= htmlspecialchars((string)$castImg) ?>" style="width: 35px; height: 35px; border-radius: 8px; object-fit: cover;">
                                <span style="color: var(--text-main); font-size: 0.85rem; font-weight: 500;"><?= htmlspecialchars((string)$cast['cast_name']) ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <span style="color: var(--text-muted); font-size: 0.8rem;">123kb</span>
                                <i class="fas fa-download" style="color: var(--text-muted); font-size: 0.8rem; cursor: pointer;"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="color: var(--text-muted); font-size: 0.85rem; text-align: center;">No favorite casts yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>
    </main>
</div>

<script>
const userSearchInput = document.getElementById('userSearchInput');
const userLiveSearchResults = document.getElementById('userLiveSearchResults');
const userSearchContainer = document.getElementById('userSearchContainer');
let userDebounceTimer;

if (userSearchInput && userLiveSearchResults) {
    userSearchInput.addEventListener('input', () => {
        const val = userSearchInput.value.trim();
        if (val.length > 0) {
            userLiveSearchResults.classList.add('show');
            userLiveSearchResults.innerHTML = '<div style="padding: 15px; text-align: center; color: var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Mencari pengguna...</div>';
            
            clearTimeout(userDebounceTimer);
            userDebounceTimer = setTimeout(() => {
                fetch(`index.php?page=ajax_search_user&q=${encodeURIComponent(val)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(u => {
                                const avatarHtml = u.avatar 
                                    ? `<img src="${u.avatar}" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">` 
                                    : `<div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; color: #000; flex-shrink: 0;">${u.initial}</div>`;
                                    
                                html += `
                                    <a href="index.php?page=user_profile&id=${u.id}" class="live-search-item" style="align-items: center; padding: 12px 15px;">
                                        ${avatarHtml}
                                        <div class="live-search-info">
                                            <div class="live-search-title">${u.name}</div>
                                            <div class="live-search-meta">Anggota sejak ${u.member_since}</div>
                                        </div>
                                    </a>
                                `;
                            });
                            html += `
                                <a href="index.php?page=user_profile&search_user=${encodeURIComponent(val)}" style="display: block; text-align: center; padding: 12px; color: var(--accent); font-size: 0.85rem; font-weight: 600; text-decoration: none; border-top: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02);">
                                    Lihat semua hasil <i class="fas fa-arrow-right" style="font-size: 0.8rem; margin-left: 4px;"></i>
                                </a>
                            `;
                            userLiveSearchResults.innerHTML = html;
                        } else {
                            userLiveSearchResults.innerHTML = '<div style="padding: 15px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">Pengguna tidak ditemukan.</div>';
                        }
                    }).catch(err => {
                        userLiveSearchResults.innerHTML = '<div style="padding: 15px; text-align: center; color: #ff3b3b; font-size: 0.9rem;">Gagal memuat data.</div>';
                    });
            }, 400); // 400ms delay debouncing
        } else {
            userLiveSearchResults.classList.remove('show');
            userLiveSearchResults.innerHTML = '';
        }
    });

    // Animasi glow ketika form difokuskan
    userSearchInput.addEventListener('focus', () => {
        if (userSearchInput.value.trim().length > 0 && userLiveSearchResults.innerHTML.trim() !== '') {
            userLiveSearchResults.classList.add('show');
        }
        userSearchInput.style.borderColor = 'var(--accent)';
        userSearchInput.style.boxShadow = '0 0 15px rgba(0, 210, 255, 0.15)';
    });

    userSearchInput.addEventListener('blur', () => {
        userSearchInput.style.borderColor = 'rgba(255,255,255,0.1)';
        userSearchInput.style.boxShadow = 'none';
    });

    // Sembunyikan hasil live search jika kursor meng-klik area luar
    document.addEventListener('click', (e) => {
        if (userSearchContainer && !userSearchContainer.contains(e.target)) {
            userLiveSearchResults.classList.remove('show');
        }
    });
}
</script>