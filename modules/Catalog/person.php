<?php
require_once __DIR__ . '/../../config/data.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data profil orang dan kredit (film) yang dia mainkan sekaligus
$person = fetchTMDB("person/" . $id . "?append_to_response=combined_credits");

if (empty($person) || isset($person['success']) && $person['success'] === false) {
    echo "<main class='container' style='padding-top: 150px; text-align: center; min-height: 70vh;'>
            <i class='fas fa-user-slash' style='font-size: 4rem; color: #ff3b3b; margin-bottom: 20px;'></i>
            <h2>Data Pemeran Tidak Ditemukan</h2>
            <a href='index.php?page=home' class='btn-primary' style='margin-top: 20px; display: inline-flex;'><i class='fas fa-home'></i> Kembali ke Beranda</a>
          </main>";
    return;
}

$name = $person['name'] ?? 'Unknown';
$biography = !empty($person['biography']) ? $person['biography'] : "Biografi tidak tersedia untuk " . htmlspecialchars($name) . ".";
$birthday = !empty($person['birthday']) ? date('d F Y', strtotime($person['birthday'])) : "Tidak diketahui";
$place_of_birth = $person['place_of_birth'] ?? "Tidak diketahui";
$department = $person['known_for_department'] ?? "Acting";

$profile_img = !empty($person['profile_path']) ? "https://image.tmdb.org/t/p/w500" . $person['profile_path'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22300%22%20height%3D%22450%22%20fill%3D%22%23222%22%3E%3Crect%20width%3D%22300%22%20height%3D%22450%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20fill%3D%22%23666%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20text-anchor%3D%22middle%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fsvg%3E";

// Urutkan kredit berdasarkan popularitas atau tanggal rilis
$credits = $person['combined_credits']['cast'] ?? [];
usort($credits, function($a, $b) {
    $popA = $a['vote_count'] ?? 0;
    $popB = $b['vote_count'] ?? 0;
    return $popB <=> $popA;
});
$credits = array_slice($credits, 0, 24); // Ambil 24 karya paling populer
?>

<main class="detail-main container" style="padding-top: 120px;">
    <div style="display: flex; flex-wrap: wrap; gap: 3rem; margin-bottom: 4rem;">
        <!-- Foto & Info Pribadi -->
        <div style="flex: 0 0 300px; width: 100%;">
            <img src="<?= $profile_img ?>" alt="<?= htmlspecialchars($name) ?>" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); margin-bottom: 2rem;">
            
            <h3 style="font-size: 1.2rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">Informasi Pribadi</h3>
            
            <div style="margin-bottom: 1rem;">
                <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Dikenal Sebagai</div>
                <div style="font-size: 1rem;"><?= htmlspecialchars($department) ?></div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Tanggal Lahir</div>
                <div style="font-size: 1rem;"><?= $birthday ?></div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Tempat Lahir</div>
                <div style="font-size: 1rem;"><?= htmlspecialchars($place_of_birth) ?></div>
            </div>
        </div>

        <!-- Biografi & Karya -->
        <div style="flex: 1; min-width: 300px;">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1.5rem; color: var(--text-main);"><?= htmlspecialchars($name) ?></h1>
            
            <h3 style="font-size: 1.3rem; margin-bottom: 0.8rem; color: var(--accent);">Biografi</h3>
            <p style="color: #ccc; line-height: 1.7; font-size: 1.05rem; margin-bottom: 3rem; white-space: pre-line;"><?= htmlspecialchars($biography) ?></p>
            
            <h3 style="font-size: 1.3rem; margin-bottom: 1.5rem;">Dikenal Lewat (Karya Terpopuler)</h3>
            <div class="movies-grid" style="grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px;">
                <?php if(count($credits) > 0): ?>
                    <?php foreach($credits as $credit): 
                        $media_type = $credit['media_type'] ?? 'movie';
                        $c_title = $credit['title'] ?? $credit['name'] ?? 'Unknown';
                        $c_poster = !empty($credit['poster_path']) ? "https://image.tmdb.org/t/p/w300" . $credit['poster_path'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22200%22%20height%3D%22300%22%20fill%3D%22%23222%22%3E%3Crect%20width%3D%22200%22%20height%3D%22300%22%2F%3E%3C%2Fsvg%3E";
                        $c_year = isset($credit['release_date']) && strlen($credit['release_date'])>=4 ? substr($credit['release_date'], 0, 4) : (isset($credit['first_air_date']) && strlen($credit['first_air_date'])>=4 ? substr($credit['first_air_date'], 0, 4) : "-");
                    ?>
                    <a href="index.php?page=details&type=<?= $media_type ?>&id=<?= $credit['id'] ?>" style="text-decoration: none; color: inherit; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <img src="<?= $c_poster ?>" alt="Poster" style="width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); margin-bottom: 10px;">
                        <div style="font-weight: 600; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;"><?= htmlspecialchars($c_title) ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                            <span><?= $c_year ?></span>
                            <span style="color: #FCD34D;"><i class="fas fa-star"></i> <?= isset($credit['vote_average']) ? round($credit['vote_average'], 1) : 0 ?></span>
                        </div>
                        <?php if(!empty($credit['character'])): ?>
                            <div style="font-size: 0.8rem; color: var(--accent); margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Peran: <?= htmlspecialchars($credit['character']) ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted); grid-column: 1 / -1;">Belum ada data karya (film/seri) yang tercatat.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>