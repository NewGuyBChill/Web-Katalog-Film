<?php
// Layout: Recommendation View
?>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl md:text-4xl font-display font-bold text-transparent bg-clip-text bg-gradient-to-r from-accent to-brand">
            Recommended For You
        </h1>
        <p class="text-gray-400 text-sm hidden md:block">Based on your reviews & watchlist</p>
    </div>

    <?php if (empty($movies)): ?>
        <div class="glass-panel p-10 text-center rounded-2xl border border-gray-800">
            <i class="fas fa-magic text-4xl text-gray-500 mb-4"></i>
            <h2 class="text-xl font-semibold mb-2">No recommendations yet</h2>
            <p class="text-gray-400">Start adding movies to your watchlist and reviewing them so we can learn your taste!</p>
            <a href="<?= url('movies') ?>" class="inline-block mt-6 btn-primary">
                Explore Movies
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php foreach ($movies as $movie): ?>
                <?php include dirname(__DIR__, 3) . '/resources/views/components/movie-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
