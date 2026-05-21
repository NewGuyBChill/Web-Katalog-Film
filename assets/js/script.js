const heroSlider = document.querySelector('.hero');
const heroContent = document.querySelector('.hero-content');
const dotsContainer = document.getElementById('heroDots');

// Gunakan data API dari PHP, jika gagal pakai array kosong
const banners = typeof dynamicBanners !== 'undefined' ? dynamicBanners : [];

let currentIndex = 0;
let slideInterval = 10;

function updateSlider(index) {
    // 1. Simpan background saat ini
    const currentBg = heroSlider.style.backgroundImage || getComputedStyle(heroSlider).backgroundImage;
    
    // 2. Buat layer bayangan untuk menahan gambar lama (efek crossfade)
    const tempBg = document.createElement('div');
    tempBg.style.position = 'absolute';
    tempBg.style.inset = '0';
    tempBg.style.backgroundImage = currentBg;
    tempBg.style.backgroundSize = 'cover';
    tempBg.style.backgroundPosition = 'center';
    tempBg.style.zIndex = '0'; // Biarkan di bawah hero-overlay
    tempBg.style.transition = 'opacity 0.8s ease-in-out';
    heroSlider.insertBefore(tempBg, heroSlider.firstChild);

    heroContent.classList.add('fade-out');
    
    setTimeout(() => {
        currentIndex = index;
        const data = banners[currentIndex];
        
        // 3. Ganti gambar background utama (kini ada di belakang layer bayangan)
        heroSlider.style.backgroundImage = data.bg;
        
        document.querySelector('.hero h1').innerHTML = data.title;
        document.querySelector('.hero .meta').innerHTML = data.meta;
        if (document.querySelector('.hero .synopsis')) {
            document.querySelector('.hero .synopsis').innerHTML = data.synopsis.substring(0, 150) + "...";
        }
        const heroRating = document.getElementById('heroRating');
        if (heroRating) {
            heroRating.innerHTML = `<i class="fas fa-star"></i> ${data.rating}`;
        }
        const detailsBtn = document.querySelector('.hero .btn-secondary');
        if (detailsBtn) {
            detailsBtn.setAttribute('onclick', `window.location.href='index.php?page=details&id=${data.id}'`);
        }
        const trailerBtn = document.querySelector('.hero .btn-primary');
        if (trailerBtn) {
            if (data.trailer && data.trailer !== '#') {
                trailerBtn.style.opacity = '1';
                trailerBtn.style.cursor = 'pointer';
                trailerBtn.disabled = false;
                trailerBtn.innerHTML = '<i class="fas fa-play"></i> Watch Trailer';
                trailerBtn.setAttribute('onclick', `window.open('${data.trailer}', '_blank')`);
            } else {
                trailerBtn.style.opacity = '0.5';
                trailerBtn.style.cursor = 'not-allowed';
                trailerBtn.disabled = true;
                trailerBtn.innerHTML = '<i class="fas fa-play"></i> Tidak Ada Trailer';
                trailerBtn.removeAttribute('onclick');
            }
        }
        
        document.querySelectorAll('.dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });
        
        // 4. Mulai memudarkan layer bayangan perlahan
        setTimeout(() => tempBg.style.opacity = '0', 50); 
        
        // 5. Bersihkan elemen layer bayangan setelah animasinya selesai
        setTimeout(() => tempBg.remove(), 850);
        
        heroContent.classList.remove('fade-out');
    }, 600);
}

// Buat Dots
if (banners.length > 0) {
    banners.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => {
            updateSlider(i);
            startAutoSlide(); 
        });
        dotsContainer.appendChild(dot);
    });
}

function startAutoSlide() {
    clearInterval(slideInterval);
    slideInterval = setInterval(() => {
        let next = (currentIndex + 1) % banners.length;
        updateSlider(next);
    }, 5000);
}

if (banners.length > 0) {
    startAutoSlide();
}

// Mengambil elemen HTML untuk search bar & logika dropdown bahasa
const searchTrigger = document.getElementById('searchTrigger');
const searchContainer = document.getElementById('searchContainer');
const searchInput = document.getElementById('searchInput');
const langContainer = document.getElementById('langContainer');
const langTrigger = document.getElementById('langTrigger');
const langDropdown = document.getElementById('langDropdown');
const currentLangText = document.getElementById('currentLang');
const langOptions = document.querySelectorAll('.lang-option');

searchTrigger.addEventListener('click', (e) => {
    if (searchInput.value.trim() === "") {
        e.preventDefault(); // Mencegah pencarian kosong ter-submit
        searchInput.focus();
    }
});

langTrigger.addEventListener('click', (e) => {
    e.stopPropagation();
    langDropdown.classList.toggle('show');
});

langOptions.forEach(option => {
    option.addEventListener('click', (e) => {
        e.stopPropagation();
        langOptions.forEach(opt => opt.classList.remove('active'));
        e.target.classList.add('active');
        currentLangText.innerText = e.target.getAttribute('data-lang');
        langDropdown.classList.remove('show');
    });
});

document.addEventListener('click', (e) => {
    if (!langContainer.contains(e.target)) langDropdown.classList.remove('show');
});

// Fitur Drag to Scroll untuk baris film (Trending & Top Picks)
const movieRows = document.querySelectorAll('.movie-row');
movieRows.forEach(row => {
    let isDown = false;
    let startX;
    let scrollLeft;
    let isDragging = false;

    row.addEventListener('mousedown', (e) => {
        isDown = true;
        isDragging = false;
        row.classList.add('active');
        startX = e.pageX - row.offsetLeft;
        scrollLeft = row.scrollLeft;
    });
    row.addEventListener('mouseleave', () => {
        isDown = false;
        row.classList.remove('active');
    });
    row.addEventListener('mouseup', () => {
        isDown = false;
        row.classList.remove('active');
    });
    row.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        isDragging = true;
        const x = e.pageX - row.offsetLeft;
        const walk = (x - startX) * 2; // Kecepatan scroll
        row.scrollLeft = scrollLeft - walk;
    });

    // Mencegah klik link masuk ke halaman detail jika user sebenarnya sedang menggeser (drag)
    row.querySelectorAll('.movie-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (isDragging) {
                e.preventDefault();
            }
        });
    });
});

// Custom Dropdown Logic untuk halaman Movies
document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
    const toggle = dropdown.querySelector('.dropdown-toggle');
    const menu = dropdown.querySelector('.dropdown-menu');

    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        // Tutup dropdown lain yang mungkin terbuka
        document.querySelectorAll('.custom-dropdown .dropdown-menu').forEach(otherMenu => {
            if (otherMenu !== menu) {
                otherMenu.classList.remove('show');
            }
        });
        menu.classList.toggle('show');
    });
});

// Tutup dropdown jika user mengklik di luar area dropdown
window.addEventListener('click', function(e) {
    if (!e.target.closest('.custom-dropdown')) {
        document.querySelectorAll('.custom-dropdown .dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});