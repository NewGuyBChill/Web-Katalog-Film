// Mock Data untuk Rekomendasi
const movies = [
    { title: "The Batman", year: "2022", img: "https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?auto=format&fit=crop&w=100&q=80" },
    { title: "Dune: Part Two", year: "2024", img: "https://images.unsplash.com/photo-1542204165-65bf26472b9b?auto=format&fit=crop&w=100&q=80" },
    { title: "Interstellar", year: "2014", img: "https://images.unsplash.com/photo-1419242902214-272b3f66ee7a?auto=format&fit=crop&w=100&q=80" },
    { title: "Oppenheimer", year: "2023", img: "https://images.unsplash.com/photo-1440404653325-ab127d49abc1?auto=format&fit=crop&w=100&q=80" }
];

// 1. Logic Search Bar Dinamis
const searchTrigger = document.getElementById('searchTrigger');
const searchContainer = document.getElementById('searchContainer');
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');
const resultsList = document.getElementById('resultsList');

searchTrigger.addEventListener('click', (e) => {
    e.stopPropagation();
    searchContainer.classList.toggle('active');
    if (searchContainer.classList.contains('active')) {
        searchInput.focus();
    } else {
        searchResults.classList.remove('show');
    }
});

searchInput.addEventListener('input', (e) => {
    const val = e.target.value.toLowerCase();
    if (val.length > 0) {
        searchResults.classList.add('show');
        const filtered = movies.filter(m => m.title.toLowerCase().includes(val));
        
        if (filtered.length > 0) {
            resultsList.innerHTML = filtered.map(m => `
                <div class="result-item">
                    <img src="${m.img}" alt="${m.title}">
                    <div class="result-info">${m.title} <br><small style="color:#777">${m.year}</small></div>
                </div>
            `).join('');
        } else {
            resultsList.innerHTML = '<p style="font-size:0.8rem; color:#777; padding:10px;">No results found</p>';
        }
    } else {
        searchResults.classList.remove('show');
    }
});

// 2. Logic Profile Dropdown
const profileTrigger = document.getElementById('profileTrigger');
const profileDropdown = document.getElementById('profileDropdown');

profileTrigger.addEventListener('click', (e) => {
    e.stopPropagation();
    profileDropdown.classList.toggle('show');
});

// Tutup semua dropdown jika klik di luar
document.addEventListener('click', () => {
    profileDropdown.classList.remove('show');
    // Jika input kosong, tutup search bar saat klik luar
    if (searchInput.value === "") {
        searchContainer.classList.remove('active');
        searchResults.classList.remove('show');
    }
});

// Stop propagation agar klik di dalam dropdown tidak menutup dropdown itu sendiri
profileDropdown.addEventListener('click', (e) => e.stopPropagation());
searchContainer.addEventListener('click', (e) => e.stopPropagation());

// 3. Populate Rows (Dummy data untuk tampilan utama)
const mainMovies = [
    { title: "Dune: Part Two", year: "2024", img: "https://images.unsplash.com/photo-1542204165-65bf26472b9b?auto=format&fit=crop&w=300&q=80" },
    { title: "Oppenheimer", year: "2023", img: "https://images.unsplash.com/photo-1440404653325-ab127d49abc1?auto=format&fit=crop&w=300&q=80" },
    { title: "Interstellar", year: "2014", img: "https://images.unsplash.com/photo-1419242902214-272b3f66ee7a?auto=format&fit=crop&w=300&q=80" },
    { title: "The Batman", year: "2022", img: "https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?auto=format&fit=crop&w=300&q=80" },
    { title: "Inception", year: "2010", img: "https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?auto=format&fit=crop&w=300&q=80" },
    { title: "Joker", year: "2019", img: "https://images.unsplash.com/photo-1559583109-3e7968136c99?auto=format&fit=crop&w=300&q=80" }
];

function populate() {
    const html = mainMovies.map(m => `
        <div class="movie-card">
            <img src="${m.img}" alt="${m.title}">
            <div class="movie-title">${m.title}</div>
        </div>
    `).join('');
    document.getElementById('trending-row').innerHTML = html;
    document.getElementById('top-picks-row').innerHTML = html;
}

populate();
