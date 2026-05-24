// --- Animasi Top Progress Bar Loader ---
const topProgressBar = document.getElementById('topProgressBar');
if (topProgressBar) {
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 10 + 5; // Tambah 5-15% secara acak
        if (progress > 85) progress = 85; // Menahan di 85% sebelum halaman load total
        topProgressBar.style.width = progress + '%';
    }, 100);

    window.addEventListener('load', () => {
        clearInterval(interval);
        topProgressBar.style.width = '100%'; // Selesai
        setTimeout(() => {
            topProgressBar.style.opacity = '0';
            setTimeout(() => topProgressBar.remove(), 400);
        }, 300);
    });
}

// --- Smart Navbar (Auto-Hide on Scroll) ---
const navbar = document.querySelector('.navbar');
let lastScrollY = window.scrollY;
window.addEventListener('scroll', () => {
    const currentScrollY = window.scrollY;
    
    // Tampilkan/sembunyikan tombol Scroll to Top
    const scrollToTopBtn = document.getElementById('scrollToTop');
    if (scrollToTopBtn) {
        if (currentScrollY > 400) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    }
        
    if (navbar) {
        // Abaikan scroll negatif (bouncing effect di Mac/iOS) agar navbar tidak glitch
        if (currentScrollY <= 0) {
            navbar.classList.remove('navbar-hidden');
            lastScrollY = currentScrollY;
            return;
        }
        
        // Jika scroll ke bawah dan sudah melewati 80px (tinggi navbar), sembunyikan
        if (currentScrollY > lastScrollY && currentScrollY > 80) {
            navbar.classList.add('navbar-hidden'); 
        } else {
            navbar.classList.remove('navbar-hidden'); // Scroll ke atas: Tampilkan
        }
    }
    lastScrollY = currentScrollY;
});

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
    
    // Jeda otomatis saat kursor berada di area hero agar pengguna bisa membaca sinopsis
    heroSlider.addEventListener('mouseenter', () => clearInterval(slideInterval));
    heroSlider.addEventListener('mouseleave', startAutoSlide);
}

// Mengambil elemen HTML untuk search bar & logika dropdown bahasa
const searchTrigger = document.getElementById('searchTrigger');
const searchContainer = document.getElementById('searchContainer');
const searchInput = document.getElementById('searchInput');
const clearSearch = document.getElementById('clearSearch');
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

const liveSearchResults = document.getElementById('liveSearchResults');
let debounceTimer;

if (searchInput && clearSearch) {
    // Tampilkan tombol X jika ada teks, sembunyikan jika kosong
    searchInput.addEventListener('input', () => {
        if (searchInput.value.length > 0) {
            clearSearch.classList.add('show');
            if (liveSearchResults) {
                liveSearchResults.classList.add('show');
                const isIndo = document.cookie.includes("site_lang=id-ID");
                const searchingText = isIndo ? "Mencari..." : "Searching...";
                liveSearchResults.innerHTML = `<div style="padding: 15px; text-align: center; color: var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> ${searchingText}</div>`;
                
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetch(`index.php?page=ajax_search&q=${encodeURIComponent(searchInput.value)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.length > 0) {
                                let html = '';
                                data.forEach(item => {
                                    html += `
                                        <a href="index.php?page=details&id=${item.id}" class="live-search-item">
                                            <img src="${item.image}" alt="Poster" class="live-search-poster">
                                            <div class="live-search-info">
                                                <div class="live-search-title">${item.title}</div>
                                                <div class="live-search-meta"><i class="fas fa-star" style="color: #FCD34D;"></i> ${item.rating} &bull; ${item.year}</div>
                                            </div>
                                        </a>
                                    `;
                                });
                                const seeAllText = isIndo ? "Lihat semua hasil" : "See all results";
                                html += `
                                    <a href="index.php?page=search&q=${encodeURIComponent(searchInput.value)}" style="display: block; text-align: center; padding: 10px; color: var(--accent); font-size: 0.85rem; font-weight: 600; text-decoration: none; border-top: 1px solid rgba(255,255,255,0.1);">
                                        ${seeAllText} <i class="fas fa-arrow-right" style="font-size: 0.8rem; margin-left: 4px;"></i>
                                    </a>
                                `;
                                liveSearchResults.innerHTML = html;
                            } else {
                                const noResultText = isIndo ? "Tidak ada hasil ditemukan." : "No results found.";
                                liveSearchResults.innerHTML = `<div style="padding: 15px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">${noResultText}</div>`;
                            }
                        }).catch(err => {
                            const errorText = isIndo ? "Gagal memuat data." : "Failed to load data.";
                            liveSearchResults.innerHTML = `<div style="padding: 15px; text-align: center; color: #ff3b3b; font-size: 0.9rem;">${errorText}</div>`;
                        });
                }, 500); // Jeda 500ms setelah selesai ngetik
            }
        } else {
            clearSearch.classList.remove('show');
            if (liveSearchResults) liveSearchResults.classList.remove('show');
            clearTimeout(debounceTimer);
        }
    });
    
    // Munculkan langsung jika input sudah ada isinya dari awal (saat berada di halaman hasil pencarian)
    if (searchInput.value.length > 0) {
        clearSearch.classList.add('show');
    }
    
    // Tampilkan kembali hasil pencarian jika kotak pencarian diklik ulang (fokus)
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.length > 0 && liveSearchResults && liveSearchResults.innerHTML.trim() !== '') {
            liveSearchResults.classList.add('show');
        }
    });
    
    // Tutup live search jika pengguna mengklik di luar area kotak pencarian
    document.addEventListener('click', (e) => {
        if (searchContainer && !searchContainer.contains(e.target)) {
            if (liveSearchResults) liveSearchResults.classList.remove('show');
        }
    });
    
    // Kosongkan isi text box saat tombol X diklik
    clearSearch.addEventListener('click', () => {
        searchInput.value = '';
        clearSearch.classList.remove('show');
        if (liveSearchResults) {
            liveSearchResults.classList.remove('show');
            liveSearchResults.innerHTML = '';
        }
        searchInput.focus();
    });
}

// --- Animasi Ketikan (Typing Effect) pada Placeholder Pencarian ---
if (searchInput) {
    const isIndo = document.cookie.includes("site_lang=id-ID");
    const placeholderTexts = isIndo ? [
        "Cari film...",
        "Cari TV show...",
        "Temukan favorit baru...",
        "Cari tontonan selanjutnya..."
    ] : [
        "Search movies...",
        "Search TV shows...",
        "Discover new favorites...",
        "Find your next watch..."
    ];
    let textIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let typingDelay = 100;
    let isPaused = false; // Flag untuk menjeda animasi

    // Berhenti saat diklik (fokus) dan tampilkan teks default
    searchInput.addEventListener('focus', () => {
        isPaused = true;
        searchInput.setAttribute('placeholder', isIndo ? 'Cari film...' : 'Search movies...'); 
    });

    // Lanjutkan animasi dari awal saat kursor keluar (blur)
    searchInput.addEventListener('blur', () => {
        isPaused = false;
        textIndex = 0;
        charIndex = 0;
        isDeleting = false;
    });

    function typePlaceholder() {
        if (isPaused) {
            setTimeout(typePlaceholder, 300); // Cek secara berkala (tunggu) sampai fokus hilang
            return;
        }
        
        const currentText = placeholderTexts[textIndex];
        
        if (isDeleting) {
            searchInput.setAttribute('placeholder', currentText.substring(0, charIndex - 1));
            charIndex--;
            typingDelay = 40; // Kecepatan saat menghapus teks
        } else {
            searchInput.setAttribute('placeholder', currentText.substring(0, charIndex + 1));
            charIndex++;
            typingDelay = 80; // Kecepatan saat mengetik teks
        }

        if (!isDeleting && charIndex === currentText.length) {
            isDeleting = true;
            typingDelay = 2000; // Jeda (pause) setelah satu kalimat selesai diketik
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % placeholderTexts.length;
            typingDelay = 500; // Jeda sebelum mulai mengetik kalimat baru
        }
        setTimeout(typePlaceholder, typingDelay);
    }
    
    // Mulai animasi
    setTimeout(typePlaceholder, 1000);
}

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

// --- Animasi Fade-In saat gambar berhasil dimuat ---
document.addEventListener("load", function (e) {
    if (e.target.tagName && e.target.tagName.toLowerCase() === "img") {
        e.target.classList.add("img-loaded");
        // Kunci gambar: hapus animasi setelah selesai agar tidak berkedip saat ganti tema
        setTimeout(() => { e.target.style.animation = 'none'; }, 1000);
    }
}, true);

document.querySelectorAll('img').forEach(img => {
    if (img.complete) {
        img.classList.add('img-loaded');
        // Kunci gambar: hapus animasi setelah selesai agar tidak berkedip saat ganti tema
        setTimeout(() => { img.style.animation = 'none'; }, 1000);
    }
});

// --- Fitur Watchlist (Database) ---
function toggleWatchlist(e, btn) {
    e.preventDefault(); // Mencegah pindah ke halaman detail saat klik ikon hati
    e.stopPropagation();

    if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
        const isIndo = document.cookie.includes("site_lang=id-ID");
        alert(isIndo ? "Silakan login terlebih dahulu untuk menyimpan ke Watchlist!" : "Please login to save to Watchlist!");
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
        const isIndo = document.cookie.includes("site_lang=id-ID");
        alert(isIndo ? "Silakan login terlebih dahulu untuk menyimpan ke Watchlist!" : "Please login to save to Watchlist!");
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

// --- Fitur Scroll to Top ---
const scrollTopBtnHTML = `
    <div id="scrollToTop" class="scroll-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-arrow-up"></i>
    </div>
`;
document.body.insertAdjacentHTML('beforeend', scrollTopBtnHTML);

// --- Animasi Staggered Fade In untuk Movie Grid (Pagination) ---
document.querySelectorAll('.grid-movie-card').forEach((card, index) => {
    // Berikan jeda bertingkat: 0s, 0.05s, 0.1s, dst. Max delay 1.5 detik
    const delay = Math.min(index * 0.05, 1.5);
    card.style.animationDelay = `${delay}s`;
    
    // Kunci kartu: Hapus paksa properti animasi setelah selesai (animasi 0.6s + jeda). 
    // Ini secara permanen mencegah browser me-restart animasi (efek tawuran/melompat) saat tombol Tema ditekan!
    setTimeout(() => {
        card.style.animation = 'none';
    }, (delay + 0.8) * 1000);
});

// --- Theme Switcher (Dark/Light Mode) ---
const themeSwitch = document.getElementById('themeSwitch');
const themeIcon = document.getElementById('themeIcon');

if (themeSwitch && themeIcon) {
    if (document.documentElement.classList.contains('light-mode')) {
        themeIcon.classList.replace('fa-sun', 'fa-moon');
    }

    themeSwitch.addEventListener('click', () => {
        // Tambahkan class animasi transisi tema sementara
        document.documentElement.classList.add('theme-transition');
        setTimeout(() => document.documentElement.classList.remove('theme-transition'), 500);
        
        document.documentElement.classList.toggle('light-mode');
        
        if (document.documentElement.classList.contains('light-mode')) {
            localStorage.setItem('kinema_theme', 'light');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        } else {
            localStorage.setItem('kinema_theme', 'dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        }
    });
}

// --- Fitur Show/Hide Password ---
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
            icon.style.color = 'var(--accent)';
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
            icon.style.color = 'var(--text-muted)';
        }
    }
}