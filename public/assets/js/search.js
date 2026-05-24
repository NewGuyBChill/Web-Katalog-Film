document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('liveSearchResults');
    let debounceTimer;

    if (!searchInput || !searchResults) return;

    // Apply tailwind classes dynamically since this might have been a legacy CSS container
    searchResults.className = 'absolute top-full right-0 mt-2 w-80 bg-card/95 backdrop-blur-md rounded-xl shadow-2xl border border-gray-700/50 overflow-hidden z-50 hidden opacity-0 transition-opacity duration-200';

    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();

        if (query.length < 2) {
            hideResults();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`${BASE_URL}/api/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        showResults(data);
                    } else {
                        showEmpty();
                    }
                })
                .catch(err => {
                    console.error('Live search error:', err);
                });
        }, 300); // 300ms debounce
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            hideResults();
        }
    });

    function showResults(data) {
        searchResults.innerHTML = '';
        data.forEach(item => {
            const a = document.createElement('a');
            a.href = `${BASE_URL}/movies/${item.id}`;
            a.className = 'flex items-center gap-3 p-3 hover:bg-white/5 transition border-b border-gray-800 last:border-0';
            
            a.innerHTML = `
                <img src="${item.poster}" alt="" class="w-10 h-14 object-cover rounded shadow-md">
                <div class="flex-1 overflow-hidden">
                    <h4 class="text-white text-sm font-semibold truncate">${item.title}</h4>
                    <span class="text-xs text-gray-400 capitalize">${item.type} &bull; ${item.year}</span>
                </div>
            `;
            searchResults.appendChild(a);
        });

        searchResults.classList.remove('hidden');
        // Small delay to allow display block to apply before opacity transition
        setTimeout(() => searchResults.classList.remove('opacity-0'), 10);
    }

    function showEmpty() {
        searchResults.innerHTML = `
            <div class="p-4 text-center text-gray-400 text-sm">
                No results found
            </div>
        `;
        searchResults.classList.remove('hidden');
        setTimeout(() => searchResults.classList.remove('opacity-0'), 10);
    }

    function hideResults() {
        searchResults.classList.add('opacity-0');
        setTimeout(() => searchResults.classList.add('hidden'), 200);
    }
});
