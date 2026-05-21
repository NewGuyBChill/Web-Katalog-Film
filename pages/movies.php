<?php 
require_once __DIR__ . '/../config/data.php'; 

// Ambil data query dari URL bar (bila ada)
$filters = [
    'genre' => $_GET['genre'] ?? '',
    'year' => $_GET['year'] ?? '',
    'rating' => $_GET['rating'] ?? ''
];

// Panggil fungsi discover dengan meneruskan array filters
$moviesList = discoverMovies($filters, 20); 

// Ambil list variabel global genre untuk ditampilkan di dropdown
global $genreMap;
?>

<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="movies-header">
        <div class="header-titles">
            <h2>Explore Movies</h2>
            <p>Dari mahakarya sinematik legendaris hingga rilis <i>blockbuster</i> terbaru. Sesuaikan filter di bawah dan temukan tontonan sempurna untuk menemani waktu santaimu hari ini.</p>
        </div>
        <div class="filters-section">
            
            <!-- Custom Dropdown untuk Genre -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['genre']) ? 'active-filter' : '' ?>">
                    <span>Genre: <?= isset($genreMap[$filters['genre']]) ? $genreMap[$filters['genre']] : 'Semua' ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= buildFilterUrl($filters, 'genre', '') ?>" class="<?= empty($filters['genre']) ? 'active' : '' ?>">Semua Genre</a>
                    <?php foreach($genreMap as $id => $name): ?>
                        <a href="<?= buildFilterUrl($filters, 'genre', $id) ?>" class="<?= $filters['genre'] == $id ? 'active' : '' ?>"><?= $name ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Custom Dropdown untuk Tahun -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['year']) ? 'active-filter' : '' ?>">
                    <span>Tahun: <?= !empty($filters['year']) ? $filters['year'] : 'Semua' ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= buildFilterUrl($filters, 'year', '') ?>" class="<?= empty($filters['year']) ? 'active' : '' ?>">Semua Tahun</a>
                    <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                        <a href="<?= buildFilterUrl($filters, 'year', $y) ?>" class="<?= $filters['year'] == $y ? 'active' : '' ?>"><?= $y ?></a>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Custom Dropdown untuk Rating -->
            <div class="custom-dropdown">
                <button class="dropdown-toggle <?= !empty($filters['rating']) ? 'active-filter' : '' ?>">
                    <span>Rating: <?= !empty($filters['rating']) ? '⭐ '.$filters['rating'].'.0+' : 'Semua' ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="<?= buildFilterUrl($filters, 'rating', '') ?>" class="<?= empty($filters['rating']) ? 'active' : '' ?>">Semua Rating</a>
                    <?php for($r = 8; $r >= 5; $r--): ?>
                        <a href="<?= buildFilterUrl($filters, 'rating', $r) ?>" class="<?= $filters['rating'] == $r ? 'active' : '' ?>">⭐ <?= $r ?>.0+</a>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Tombol reset filter muncul jika user sedang mem-filter sesuatu -->
            <?php if(!empty($filters['genre']) || !empty($filters['year']) || !empty($filters['rating'])): ?>
                <a href="index.php?page=movies" class="reset-btn">
                    <i class="fas fa-times"></i> Reset Filter
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="movies-grid">
        <?php if(!empty($moviesList) && is_array($moviesList)): ?>
            <?php foreach($moviesList as $movie): ?>
            <a href="index.php?page=details&id=<?= $movie['id'] ?>" class="grid-movie-card" style="text-decoration: none; color: inherit;">
                <div class="grid-movie-img-wrap">
                    <div class="grid-movie-rating"><i class="fas fa-star"></i> <?= htmlspecialchars((string)$movie['rating']) ?></div>
                    <img src="<?= htmlspecialchars((string)$movie['image']) ?>" alt="<?= htmlspecialchars((string)$movie['title']) ?>">
                </div>
                <div class="grid-movie-info">
                    <div class="grid-movie-title"><?= htmlspecialchars((string)$movie['title']) ?></div>
                    <div class="grid-movie-meta"><?= htmlspecialchars((string)$movie['year']) ?> &bull; <?= htmlspecialchars((string)$movie['genre']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search" style="font-size: 3.5rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: white;">Oops, Tidak Ada Film!</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem;">Coba sesuaikan filter pencarianmu untuk melihat hasil lainnya.</p>
                <a href="index.php?page=movies" class="reset-btn" style="margin: 0;">
                    <i class="fas fa-sync-alt"></i> Bersihkan Filter
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>