<main style="padding-top: 120px; min-height: 80vh;" class="container">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold">My Reviews</h2>
            <a href="<?= url('profile') ?>" class="text-[#00d2ff] hover:text-white transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Profile
            </a>
        </div>

        <?php if (!empty($reviews)): ?>
            <div class="space-y-6">
                <?php foreach ($reviews as $rev): 
                    $movie = $rev['movie'];
                    if (!$movie) continue;
                    
                    $starsHtml = '';
                    for($i=0; $i<5; $i++) { 
                        $starsHtml .= $i < $rev['rating'] ? '<i class="fas fa-star text-[#FCD34D]"></i>' : '<i class="far fa-star text-gray-600"></i>'; 
                    }
                ?>
                
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 flex flex-col md:flex-row gap-6 transition-all hover:bg-white/10">
                    <!-- Movie Poster -->
                    <a href="<?= url("movies/{$movie['id']}") ?>" class="w-full md:w-32 flex-shrink-0 block">
                        <img src="<?= htmlspecialchars($movie['image']) ?>" alt="Poster" class="w-full h-auto rounded-xl shadow-lg border border-white/10">
                    </a>
                    
                    <!-- Review Content -->
                    <div class="flex-1">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-2 gap-2">
                            <a href="<?= url("movies/{$movie['id']}") ?>" class="text-xl font-bold hover:text-[#00d2ff] transition-colors">
                                <?= htmlspecialchars($movie['title']) ?> <span class="text-gray-400 text-sm font-normal">(<?= htmlspecialchars($movie['year']) ?>)</span>
                            </a>
                            <div class="flex items-center gap-4">
                                <div class="flex gap-1 text-sm tracking-widest"><?= $starsHtml ?></div>
                                <div class="text-xs text-gray-500"><?= date('M d, Y', strtotime($rev['created_at'])) ?></div>
                            </div>
                        </div>
                        
                        <div class="bg-black/30 rounded-xl p-4 mt-4 border border-white/5">
                            <p class="text-gray-300 leading-relaxed italic">"<?= nl2br(htmlspecialchars($rev['review_text'])) ?>"</p>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <a href="<?= url("movies/{$movie['id']}") ?>#review-<?= $rev['id'] ?>" class="text-xs text-[#00d2ff] hover:text-white transition-colors">
                                View / Edit Review <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white/5 border border-white/10 border-dashed rounded-3xl p-12 text-center">
                <i class="fas fa-comment-slash text-5xl text-gray-600 mb-4"></i>
                <h3 class="text-2xl font-semibold mb-2">No Reviews Yet</h3>
                <p class="text-gray-400 mb-6">You haven't shared your thoughts on any movies or TV shows.</p>
                <a href="<?= url('movies') ?>" class="btn-primary inline-block px-8 py-3 rounded-full">
                    Find Movies to Review
                </a>
            </div>
        <?php endif; ?>
        
    </div>
</main>
