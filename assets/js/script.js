const heroSlider = document.querySelector('.hero');
const heroContent = document.querySelector('.hero-content');
const dotsContainer = document.getElementById('heroDots');

// Gunakan data API dari PHP, jika gagal pakai array kosong
const banners = typeof dynamicBanners !== 'undefined' ? dynamicBanners : [];

let currentIndex = 0;
let slideInterval = 10;

function updateSlider(index) {
    heroContent.classList.add('fade-out');
    setTimeout(() => {
        currentIndex = index;
        const data = banners[currentIndex];
        heroSlider.style.backgroundImage = data.bg;
        document.querySelector('.hero h1').innerHTML = data.title;
        document.querySelector('.hero .meta').innerHTML = data.meta;
        if (document.querySelector('.hero .synopsis')) {
            document.querySelector('.hero .synopsis').innerHTML = data.synopsis.substring(0, 150) + "...";
        }
        
        document.querySelectorAll('.dot').forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });
        heroContent.classList.remove('fade-out');
    }, 400);
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

// Tombol Panah
document.getElementById('nextSlide').addEventListener('click', () => {
    let next = (currentIndex + 1) % banners.length;
    updateSlider(next);
    startAutoSlide();
});

document.getElementById('prevSlide').addEventListener('click', () => {
    let prev = (currentIndex - 1 + banners.length) % banners.length;
    updateSlider(prev);
    startAutoSlide();
});

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
    if (!searchContainer.classList.contains('active')) {
        e.preventDefault(); // Mencegah pencarian kosong ter-submit
        e.stopPropagation();
        searchContainer.classList.add('active');
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
    if (!searchContainer.contains(e.target) && searchInput.value === "") searchContainer.classList.remove('active');
    if (!langContainer.contains(e.target)) langDropdown.classList.remove('show');
});