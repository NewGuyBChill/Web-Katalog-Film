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
            const watchTxt = typeof langStrings !== 'undefined' ? langStrings.watchTrailer : 'Watch Trailer';
            const noTrailerTxt = typeof langStrings !== 'undefined' ? langStrings.noTrailer : 'Tidak Ada Trailer';
            if (data.trailer && data.trailer !== '#') {
                trailerBtn.style.opacity = '1';
                trailerBtn.style.cursor = 'pointer';
                trailerBtn.disabled = false;
                trailerBtn.innerHTML = `<i class="fas fa-play"></i> ${watchTxt}`;
                trailerBtn.setAttribute('onclick', `openTrailerModal('${data.trailer}')`);
            } else {
                trailerBtn.style.opacity = '0.5';
                trailerBtn.style.cursor = 'not-allowed';
                trailerBtn.disabled = true;
                trailerBtn.innerHTML = `<i class="fas fa-play"></i> ${noTrailerTxt}`;
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

// Cek cookie bahasa saat ini (default: en-US)
const getSiteLang = () => {
    const match = document.cookie.match(/(^| )site_lang=([^;]+)/);
    return match ? match[2] : 'en-US';
};
const currentSiteLang = getSiteLang();

if (currentLangText) {
    currentLangText.innerText = currentSiteLang === 'id-ID' ? 'ID' : 'EN';
}

if (langDropdown) {
    // Render ulang pilihan bahasa hanya untuk EN dan ID
    langDropdown.innerHTML = `
        <div class="lang-option ${currentSiteLang === 'en-US' ? 'active' : ''}" data-lang="EN" data-value="en-US">English</div>
        <div class="lang-option ${currentSiteLang === 'id-ID' ? 'active' : ''}" data-lang="ID" data-value="id-ID">Indonesia</div>
    `;
}

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
        const selectedLang = e.target.getAttribute('data-value');
        // Simpan preferensi bahasa ke Cookie (berlaku 30 hari)
        document.cookie = "site_lang=" + selectedLang + "; path=/; max-age=" + (60*60*24*30);
        // Reload halaman agar PHP mengambil data TMDB dalam bahasa yang baru
        window.location.reload();
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

// Tangani semua broken image secara global (misal koneksi ke server TMDB terputus)
document.addEventListener("error", function (e) {
    if (e.target.tagName && e.target.tagName.toLowerCase() === "img") {
        // Cegah infinite loop jika fallback gagal dimuat
        if (!e.target.dataset.fallbackApplied) {
            e.target.dataset.fallbackApplied = "true";
            e.target.src = "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22500%22%20height%3D%22750%22%20viewBox%3D%220%200%20500%20750%22%3E%3Crect%20width%3D%22500%22%20height%3D%22750%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E";
        }
    }
}, true);

// --- Fitur Watchlist (Database) ---
function toggleWatchlist(e, btn) {
    e.preventDefault(); // Mencegah pindah ke halaman detail saat klik ikon hati
    e.stopPropagation();

    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
        alert("Silakan login terlebih dahulu untuk menyimpan ke Watchlist!");
        window.location.href = 'index.php?page=login';
        return;
    }

    const movieId = btn.getAttribute('data-id');
    const title = btn.getAttribute('data-title');
    
    let posterPath = '';
    const imgEl = btn.previousElementSibling;
    if (imgEl && imgEl.tagName === 'IMG') {
        posterPath = imgEl.src;
    }
    
    let mediaType = 'movie';
    if (window.location.href.includes('tvshows') || window.location.href.includes('type=tv')) {
        mediaType = 'tv';
    }

    const isActive = btn.classList.contains('active');
    const action = isActive ? 'remove' : 'add';

    if (isActive) {
        btn.classList.remove('active');
    } else {
        btn.classList.add('active');
        btn.style.transform = 'scale(1.3)'; // Efek interaktif detak jantung
        setTimeout(() => btn.style.transform = '', 200);
    }

    fetch('index.php?page=ajax_watchlist', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=${action}&media_id=${movieId}&media_type=${mediaType}&title=${encodeURIComponent(title)}&poster_path=${encodeURIComponent(posterPath)}`
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            console.error("Gagal", data.error);
            if (isActive) btn.classList.add('active');
            else btn.classList.remove('active');
        }
    });
}

function toggleWatchlistDetail(e, btn, movieId, mediaType, title, posterPath) {
    e.preventDefault();
    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
        alert("Silakan login terlebih dahulu untuk menyimpan ke Watchlist!");
        window.location.href = 'index.php?page=login';
        return;
    }

    const icon = btn.querySelector('i');
    const isActive = btn.classList.contains('active-fav');
    const action = isActive ? 'remove' : 'add';

    if (isActive) {
        btn.classList.remove('active-fav');
        if(icon) icon.style.color = '';
    } else {
        btn.classList.add('active-fav');
        if(icon) icon.style.color = '#ff3b3b';
        btn.style.transform = 'scale(1.05)';
        setTimeout(() => btn.style.transform = '', 200);
    }

    fetch('index.php?page=ajax_watchlist', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=${action}&media_id=${movieId}&media_type=${mediaType}&title=${encodeURIComponent(title)}&poster_path=${encodeURIComponent(posterPath)}`
    }).then(r => r.json()).catch(err => console.error(err));
}

function initWatchlistButtons() {
    if (typeof isLoggedIn === 'undefined' || !isLoggedIn || typeof userWatchlist === 'undefined') return;
    
    document.querySelectorAll('.watchlist-btn').forEach(btn => {
        const id = btn.getAttribute('data-id');
        if (userWatchlist.some(watchId => String(watchId) === String(id))) {
            btn.classList.add('active');
        }
    });
    
    document.querySelectorAll('.watchlist-btn-detail').forEach(btn => {
        const id = btn.getAttribute('data-id');
        if (id && userWatchlist.some(watchId => String(watchId) === String(id))) {
            btn.classList.add('active-fav');
            const icon = btn.querySelector('i');
            if(icon) icon.style.color = '#ff3b3b';
        }
    });
}
document.addEventListener('DOMContentLoaded', initWatchlistButtons);

// --- Fitur Trailer Modal (Global) ---
const modalHTML = `
    <div id="trailerModal" class="trailer-modal" onclick="closeTrailerModal()">
        <div class="trailer-modal-content" onclick="event.stopPropagation()">
            <div class="trailer-close" onclick="closeTrailerModal()">&times;</div>
            <div class="trailer-iframe-container">
                <iframe id="trailerIframe" src="" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
    </div>
`;
document.body.insertAdjacentHTML('beforeend', modalHTML);

function openTrailerModal(url) {
    const modal = document.getElementById('trailerModal');
    const iframe = document.getElementById('trailerIframe');
    if(modal && iframe) {
        let embedUrl = url;
        if(url.includes('watch?v=')) {
            embedUrl = url.replace('watch?v=', 'embed/') + '?autoplay=1';
        }
        iframe.src = embedUrl;
        modal.classList.add('show');
    }
}

function closeTrailerModal() {
    const modal = document.getElementById('trailerModal');
    const iframe = document.getElementById('trailerIframe');
    if(modal && iframe) {
        modal.classList.remove('show');
        iframe.src = ''; // Hentikan video saat modal ditutup
    }
}