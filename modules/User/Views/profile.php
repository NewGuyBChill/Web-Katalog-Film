<?php
$initial = strtoupper(substr($user['username'], 0, 1));
$avatarColor = $user['avatar_color'] ?? '#0072ff';
?>
<main style="padding-top: 100px; min-height: 80vh;" class="container">
    <div class="max-w-4xl mx-auto">
        
        <!-- Profile Header -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 flex flex-col md:flex-row items-center md:items-start gap-8 shadow-lg mb-8">
            <div class="w-32 h-32 rounded-full flex items-center justify-center text-5xl font-bold shadow-2xl" style="background: <?= $avatarColor ?>; color: #fff;">
                <?= $initial ?>
            </div>
            
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl font-bold text-white mb-2"><?= htmlspecialchars($user['username']) ?></h1>
                <p class="text-gray-400 mb-6 flex items-center justify-center md:justify-start gap-2">
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                </p>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-4">
                    <div class="bg-black/40 border border-white/5 rounded-2xl p-4 min-w-[120px]">
                        <div class="text-3xl font-bold text-[#00d2ff] mb-1"><?= $stats['total_reviews'] ?? 0 ?></div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider">Reviews</div>
                    </div>
                    <div class="bg-black/40 border border-white/5 rounded-2xl p-4 min-w-[120px]">
                        <div class="text-3xl font-bold text-[#ff00a0] mb-1"><?= $stats['total_watchlist'] ?? 0 ?></div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider">Watchlist</div>
                    </div>
                    <div class="bg-black/40 border border-white/5 rounded-2xl p-4 min-w-[120px]">
                        <div class="text-3xl font-bold text-[#FCD34D] mb-1 flex items-center gap-2">
                            <?= $stats['avg_rating'] ?? '0.0' ?> <i class="fas fa-star text-sm"></i>
                        </div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider">Avg Rating</div>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col gap-3 w-full md:w-auto">
                <a href="<?= url('profile/reviews') ?>" class="btn-primary text-center px-6 py-3 rounded-xl">
                    <i class="fas fa-star mr-2"></i> My Reviews
                </a>
                <a href="<?= url('watchlist') ?>" class="btn-secondary text-center px-6 py-3 rounded-xl">
                    <i class="fas fa-heart mr-2"></i> My Watchlist
                </a>
                <a href="<?= url('logout') ?>" class="text-red-400 border border-red-500/30 hover:bg-red-500/10 text-center px-6 py-3 rounded-xl transition-all">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Member Since -->
        <div class="text-center text-sm text-gray-500">
            Member since <?= date('F Y', strtotime($user['created_at'])) ?>
        </div>
        
    </div>
</main>
