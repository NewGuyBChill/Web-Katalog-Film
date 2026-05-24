<?php
/**
 * Movie Card Component
 * 
 * Expected variables:
 * - $movie : array containing id, title, poster_path, vote_average, release_date
 */

$title = $movie['title'] ?? $movie['name'] ?? 'Unknown Title';
$poster = !empty($movie['poster_path']) 
    ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] 
    : url('assets/images/no-poster.png');
$rating = isset($movie['vote_average']) ? round($movie['vote_average'] * 10) : 0;
$year = !empty($movie['release_date']) ? substr($movie['release_date'], 0, 4) : (!empty($movie['first_air_date']) ? substr($movie['first_air_date'], 0, 4) : 'N/A');
$id = $movie['id'] ?? 0;
?>

<div class="group relative rounded-2xl overflow-hidden glass-panel transform transition duration-300 hover:-translate-y-2 hover:shadow-2xl hover:shadow-brand/20 cursor-pointer">
    
    <!-- Poster Image -->
    <a href="<?= url("movies/{$id}") ?>" class="block relative aspect-[2/3] overflow-hidden bg-gray-900">
        <img src="<?= $poster ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy" 
             class="w-full h-full object-cover transition duration-500 group-hover:scale-110 opacity-90 group-hover:opacity-100">
             
        <!-- Overlay on Hover -->
        <div class="absolute inset-0 bg-gradient-to-t from-dark/90 via-dark/40 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
            <span class="btn-primary text-center text-sm py-1.5 w-full bg-brand/90 hover:bg-brand backdrop-blur-sm">
                View Details
            </span>
        </div>
    </a>

    <!-- Rating Badge (Absolute positioned overlapping poster) -->
    <div class="absolute top-2 right-2 z-10">
        <?php include __DIR__ . '/rating-circle.php'; ?>
    </div>

    <!-- Movie Info -->
    <div class="p-4 relative">
        <a href="<?= url("movies/{$id}") ?>" class="block">
            <h3 class="text-white font-bold text-base truncate transition duration-200 group-hover:text-brand" title="<?= htmlspecialchars($title) ?>">
                <?= htmlspecialchars($title) ?>
            </h3>
        </a>
        <div class="flex items-center justify-between mt-1">
            <span class="text-gray-400 text-sm font-medium"><?= $year ?></span>
            
            <!-- Quick Action: Add to Watchlist -->
            <?php if(auth()->check()): ?>
            <button onclick="toggleWatchlist(event, <?= $id ?>, 'movie')" class="text-gray-500 hover:text-accent transition duration-200 z-20 relative tooltip" data-tip="Add to Watchlist">
                <i class="fas fa-bookmark"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>
