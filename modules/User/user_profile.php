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

<main style="padding-top: 100px; min-height: 80vh;" class="container">
    
    <!-- Form Pencarian Pengguna -->
    <div id="userSearchContainer" style="max-width: 600px; margin: 0 auto 2rem auto; position: relative;">
        <form action="index.php" method="GET" style="display: flex; gap: 10px;">
            <input type="hidden" name="page" value="user_profile">
            <input type="text" id="userSearchInput" name="search_user" value="<?= htmlspecialchars($search_user) ?>" autocomplete="off" placeholder="Cari teman atau pengguna lain..." style="flex-grow: 1; padding: 12px 20px; border-radius: 30px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; outline: none; font-size: 1rem; transition: 0.3s;">
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

    <!-- Profil Header (Showcase) -->
    <div class="user-profile-header review-box" style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 3rem; padding: 3rem 2rem;">
        <?php if(!empty($user_info['avatar'])): ?>
            <img src="<?= htmlspecialchars($user_info['avatar']) ?>" alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
        <?php else: ?>
            <div style="width: 100px; height: 100px; border-radius: 50%; background: <?= $activeAvatarBg ?>; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; font-weight: 800; color: white; margin-bottom: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
                <?= $initial ?>
            </div>
        <?php endif; ?>
        <h1 style="font-size: 2.2rem; margin-bottom: 0.5rem; color: var(--text-main);"><?= htmlspecialchars($uname) ?></h1>
        <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem;">Anggota sejak <?= $member_since ?></p>
        
        <div style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap;">
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 800; color: var(--accent);"><?= $stats['reviews'] ?></div>
                <div style="color: var(--text-muted); font-size: 0.9rem;">Ulasan</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 800; color: var(--accent);"><?= $stats['watchlist'] ?></div>
                <div style="color: var(--text-muted); font-size: 0.9rem;">Di Watchlist</div>
            </div>
            <a href="index.php?page=user_follows&id=<?= $uid ?>&tab=followers" style="text-align: center; text-decoration: none; display: block; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <div style="font-size: 2rem; font-weight: 800; color: var(--accent);"><?= $stats['followers'] ?></div>
                <div style="color: var(--text-muted); font-size: 0.9rem;">Pengikut</div>
            </a>
            <a href="index.php?page=user_follows&id=<?= $uid ?>&tab=following" style="text-align: center; text-decoration: none; display: block; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <div style="font-size: 2rem; font-weight: 800; color: var(--accent);"><?= $stats['following'] ?></div>
                <div style="color: var(--text-muted); font-size: 0.9rem;">Mengikuti</div>
            </a>
        </div>
        
        <?php if ($uid == $current_user_id): // Jika ini profil kita sendiri, tampilkan tombol Edit ?>
            <a href="index.php?page=profile" class="btn-secondary" style="margin-top: 2.5rem; text-decoration: none; border-radius: 30px; padding: 0.6rem 1.5rem;"><i class="fas fa-edit"></i> Edit Akun</a>
        <?php else: // Jika ini profil orang lain, tampilkan tombol Follow/Unfollow
            $follow_btn_class = $is_following ? 'btn-secondary active' : 'btn-primary';
            $follow_btn_icon = $is_following ? 'fa-user-check' : 'fa-user-plus';
            $follow_btn_text = $is_following ? 'Mengikuti' : 'Ikuti';
        ?>
            <button class="<?= $follow_btn_class ?>" onclick="toggleFollow(this, <?= $uid ?>)" style="margin-top: 2.5rem; text-decoration: none; border-radius: 30px; padding: 0.8rem 1.8rem; min-width: 150px; justify-content: center; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas <?= $follow_btn_icon ?>"></i>
                <span><?= $follow_btn_text ?></span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Favorite Cast -->
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2><i class="fas fa-star" style="color: #FCD34D;"></i> Pemeran Favorit</h2>
        <p>Aktor dan aktris favorit <?= htmlspecialchars($uname) ?>.</p>
    </div>
    
    <?php if (count($favorite_casts) > 0): ?>
        <div class="movie-row" style="display: flex; overflow-x: auto; gap: 15px; padding-bottom: 15px; scrollbar-width: thin; margin-bottom: 3.5rem;">
            <?php foreach($favorite_casts as $cast): 
                $castImg = !empty($cast['cast_image']) ? $cast['cast_image'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22300%22%20fill%3D%22%23222%22%3E%3Crect%20width%3D%22200%22%20height%3D%22300%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23666%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20text-anchor%3D%22middle%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fsvg%3E";
            ?>
            <div style="flex: 0 0 140px; text-align: center; position: relative;">
                <img src="<?= htmlspecialchars((string)$castImg) ?>" alt="<?= htmlspecialchars((string)$cast['cast_name']) ?>" style="width: 140px; height: 210px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); margin-bottom: 10px; background: var(--card-bg);">
                <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars((string)$cast['cast_name']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state" style="margin-bottom: 3.5rem; padding: 40px 20px;">
            <i class="fas fa-users" style="font-size: 2.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem; color: var(--text-main);">Belum Ada Pemeran Favorit</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Pengguna ini belum menambahkan aktor atau aktris favorit ke dalam daftarnya.</p>
        </div>
    <?php endif; ?>

    <!-- Feed Aktivitas Ulasan -->
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2><i class="fas fa-comment-dots" style="color: #4facfe;"></i> Aktivitas Terbaru</h2>
        <p>Ulasan yang baru saja dibagikan oleh <?= htmlspecialchars($uname) ?>.</p>
    </div>
    
    <div style="max-width: 900px; margin: 0 auto;">
        <?php if (count($reviews) > 0): ?>
            <?php foreach($reviews as $rev): 
                $title = $rev['media_title'] ?? 'Unknown Media';
                $poster = !empty($rev['media_poster']) ? $rev['media_poster'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22300%22%20viewBox%3D%220%200%20200%20300%22%3E%3Crect%20width%3D%22200%22%20height%3D%22300%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E";
                $starsHtml = '';
                for($i=0; $i<5; $i++) { $starsHtml .= $i < $rev['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'; }
                
                $activeClass = !empty($rev['is_liked_by_user']) ? 'active' : '';
            ?>
            <div class="review-item" style="margin-bottom: 1.5rem; display: flex; gap: 1.5rem; align-items: flex-start;">
                <!-- Link Poster ke Detail Film -->
                <a href="index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>" style="flex-shrink: 0;">
                    <img src="<?= $poster ?>" alt="Poster" style="width: 100px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); background: var(--card-bg);">
                </a>
                
                <div style="flex-grow: 1;">
                    <!-- Header Ulasan -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <a href="index.php?page=details&type=<?= $rev['media_type'] ?>&id=<?= $rev['media_id'] ?>" style="color: var(--text-main); text-decoration: none; font-size: 1.2rem; font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-main)'">
                            <?= htmlspecialchars($title) ?>
                        </a>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                    </div>
                    
                    <!-- Bintang -->
                    <div style="color: #FCD34D; font-size: 0.95rem; margin-bottom: 1rem;"><?= $starsHtml ?></div>
                    
                    <!-- Isi Teks Ulasan -->
                    <p style="line-height: 1.6; margin-bottom: 1rem;"><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
                    
                    <!-- Tombol Aksi (Like) -->
                    <div style="display: flex; gap: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; align-items: center;">
                        <button class="review-like-btn <?= $activeClass ?>" onclick="likeReview(this, <?= $rev['id'] ?>)" style="position: static; margin: 0; padding: 6px 12px;">
                            <i class="fas fa-heart"></i>
                            <span class="like-count"><?= $rev['like_count'] ?></span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-comment-slash" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-main);">Belum ada ulasan</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Pengguna ini belum pernah mengulas media apapun.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>

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