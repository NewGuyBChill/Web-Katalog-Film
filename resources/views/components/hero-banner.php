<?php
/**
 * Hero Banner Component (Slider)
 * 
 * Expected variables:
 * - $featured : array of movie objects
 */
$featured = $featured ?? [];
if (empty($featured)) return;
?>

<div class="relative w-full h-[60vh] md:h-[80vh] overflow-hidden" id="hero-slider">
    <!-- Slider Container -->
    <div class="relative w-full h-full">
        <?php foreach ($featured as $index => $movie): ?>
            <?php 
                $backdrop = !empty($movie['backdrop_path']) 
                    ? 'https://image.tmdb.org/t/p/original' . $movie['backdrop_path'] 
                    : url('assets/images/no-backdrop.jpg');
                $title = $movie['title'] ?? $movie['name'] ?? 'Unknown';
                $overview = $movie['overview'] ?? '';
                // Limit overview to ~150 chars
                if (strlen($overview) > 150) {
                    $overview = substr($overview, 0, 150) . '...';
                }
                $id = $movie['id'] ?? 0;
            ?>
            <div class="slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out <?= $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' ?>">
                
                <!-- Background Image -->
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= $backdrop ?>');"></div>
                
                <!-- Overlay Gradients (Darken bottom and left) -->
                <div class="absolute inset-0 bg-gradient-to-t from-background via-background/40 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-background via-background/60 to-transparent"></div>
                
                <!-- Content -->
                <div class="absolute inset-0 flex items-center">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="max-w-2xl transform translate-y-8 md:translate-y-0">
                            <!-- Title -->
                            <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-bold text-white leading-tight mb-4 drop-shadow-lg">
                                <?= htmlspecialchars($title) ?>
                            </h1>
                            
                            <!-- Meta Info -->
                            <div class="flex items-center space-x-4 mb-6">
                                <?php $rating = isset($movie['vote_average']) ? round($movie['vote_average'] * 10) : 0; ?>
                                <div class="scale-110 origin-left">
                                    <?php include __DIR__ . '/rating-circle.php'; ?>
                                </div>
                                <span class="px-2 py-1 bg-white/10 backdrop-blur-md rounded text-sm text-gray-200 border border-white/20">
                                    Action
                                </span>
                                <span class="text-gray-300 text-sm">
                                    <?= !empty($movie['release_date']) ? substr($movie['release_date'], 0, 4) : 'N/A' ?>
                                </span>
                            </div>
                            
                            <!-- Overview -->
                            <p class="text-gray-300 text-base md:text-lg mb-8 max-w-xl drop-shadow-md hidden md:block">
                                <?= htmlspecialchars($overview) ?>
                            </p>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-4">
                                <a href="<?= url("movies/{$id}") ?>" class="px-8 py-3 bg-brand text-white font-bold rounded-full hover:bg-blue-600 transition shadow-[0_0_20px_rgba(0,114,255,0.4)] hover:shadow-[0_0_30px_rgba(0,114,255,0.6)] transform hover:-translate-y-1">
                                    <i class="fas fa-play mr-2"></i> View Details
                                </a>
                                <?php if(auth()->check()): ?>
                                <button onclick="toggleWatchlist(event, <?= $id ?>, 'movie')" class="w-12 h-12 rounded-full bg-white/10 backdrop-blur-md text-white flex items-center justify-center hover:bg-white/20 border border-white/20 transition transform hover:-translate-y-1 tooltip" data-tip="Add to Watchlist">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Slider Controls -->
    <?php if (count($featured) > 1): ?>
    <div class="absolute bottom-8 right-8 z-30 flex space-x-3">
        <button id="hero-prev" class="w-10 h-10 rounded-full bg-black/50 text-white backdrop-blur flex items-center justify-center hover:bg-brand transition border border-white/10">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button id="hero-next" class="w-10 h-10 rounded-full bg-black/50 text-white backdrop-blur flex items-center justify-center hover:bg-brand transition border border-white/10">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    
    <!-- JS for Slider -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('#hero-slider .slide');
            let current = 0;
            let timer;
            
            function showSlide(index) {
                slides[current].classList.remove('opacity-100', 'z-10');
                slides[current].classList.add('opacity-0', 'z-0');
                current = (index + slides.length) % slides.length;
                slides[current].classList.remove('opacity-0', 'z-0');
                slides[current].classList.add('opacity-100', 'z-10');
            }
            
            function nextSlide() { showSlide(current + 1); }
            function prevSlide() { showSlide(current - 1); }
            
            function startTimer() {
                clearInterval(timer);
                timer = setInterval(nextSlide, 5000);
            }
            
            document.getElementById('hero-next').addEventListener('click', () => { nextSlide(); startTimer(); });
            document.getElementById('hero-prev').addEventListener('click', () => { prevSlide(); startTimer(); });
            
            startTimer();
        });
    </script>
    <?php endif; ?>
</div>
