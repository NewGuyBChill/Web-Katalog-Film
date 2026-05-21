<?php
$tmdbApiKey = "ac2e690e071692fe9f8e181d6370f6c7"; // API Key TMDB kamu

// Mapping ID Genre dari TMDB ke nama aslinya
$genreMap = [
    28 => "Action", 12 => "Adventure", 16 => "Animation", 35 => "Comedy", 80 => "Crime",
    99 => "Documentary", 18 => "Drama", 10751 => "Family", 14 => "Fantasy", 36 => "History",
    27 => "Horror", 10402 => "Music", 9648 => "Mystery", 10749 => "Romance", 878 => "Sci-Fi",
    10770 => "TV Movie", 53 => "Thriller", 10752 => "War", 37 => "Western"
];

// Mapping ID Genre khusus TV Show
$tvGenreMap = [
    10759 => "Action & Adventure", 16 => "Animation", 35 => "Comedy", 80 => "Crime",
    99 => "Documentary", 18 => "Drama", 10751 => "Family", 10762 => "Kids",
    9648 => "Mystery", 10763 => "News", 10764 => "Reality", 10765 => "Sci-Fi & Fantasy",
    10766 => "Soap", 10767 => "Talk", 10768 => "War & Politics", 37 => "Western"
];

// Mapping Bahasa Film
$langMap = [
    'en' => 'Inggris (Barat)',
    'ko' => 'Korea',
    'ja' => 'Jepang (Anime)',
    'id' => 'Indonesia',
    'zh' => 'Mandarin',
    'th' => 'Thailand'
];

// Cek bahasa situs yang dipilih user melalui cookie (default: English)
$siteLang = isset($_COOKIE['site_lang']) && $_COOKIE['site_lang'] === 'id-ID' ? 'id-ID' : 'en-US';

// Mapping Bahasa Situs untuk Teks Antarmuka / UI
$siteTranslations = [
    'en-US' => [
        'now_playing' => 'NOW PLAYING',
        'watch_trailer' => 'Watch Trailer',
        'no_trailer' => 'No Trailer Available',
        'details' => 'Details',
        'featured_today' => 'Featured Today',
        'highlight_day' => 'Highlight of the day',
        'trending_1' => 'Trending #1 Today',
        'read_more' => 'Read More',
        'watchlist' => 'Watchlist',
        'my_profile' => 'My Profile',
        'my_reviews' => 'My Reviews',
        'profile_settings' => 'Account Settings',
        'update_profile' => 'Update Profile',
        'all_media' => 'All Media',
        'movies_only' => 'Movies Only',
        'tv_only' => 'TV Shows Only',
        'trending_now' => 'Trending Now',
        'top_picks' => 'Top Picks',
        'upcoming_movies' => 'Upcoming Movies',
        'similar_movies' => 'Recommended For You',
        'recommended_for_you' => 'Recommended For You',
        'based_on_rating' => 'Based on movies you rated highly',
        'synopsis' => 'Synopsis',
        'no_synopsis' => 'Synopsis is not available for this movie.',
        'explore_movies' => 'Explore Movies',
        'explore_tv' => 'Explore TV Shows',
        'explore_desc' => 'From legendary cinematic masterpieces to the latest blockbuster releases. Adjust the filters below and find the perfect watch for your day.',
        'explore_tv_desc' => 'From epic series to the latest reality shows. Adjust the filters below and find the perfect binge-watch for today.',
        'genre' => 'Genre',
        'all_genre' => 'All Genres',
        'year' => 'Year',
        'all_year' => 'All Years',
        'rating' => 'Rating',
        'all_rating' => 'All Ratings',
        'language' => 'Language',
        'all_language' => 'All Languages',
        'sort_by' => 'Sort By',
        'sort_pop_desc' => 'Most Popular',
        'sort_rating_desc' => 'Highest Rating',
        'sort_date_desc' => 'Newest Release',
        'sort_date_asc' => 'Oldest Release',
        'reset_filter' => 'Reset Filter',
        'clear_filter' => 'Clear Filters',
        'prev' => 'Previous',
        'next' => 'Next',
        'no_movies' => 'Oops, No Movies Found!',
        'no_movies_desc' => 'Try adjusting your search filters to see other results.',
        'movie_not_found' => 'Movie Not Found / No Connection.',
        'add_favorite' => 'Add to Favorite',
        'user_reviews' => 'User Reviews',
        'share_opinion' => 'Share your thoughts about this movie',
        'write_review' => 'Write Your Review',
        'review_placeholder' => 'What do you think about this movie?',
        'submit_review' => 'Submit Review',
        'just_now' => 'Just now',
        'alert_review' => 'Please provide a rating and review first.',
        'all' => 'All',
        'user' => 'User'
    ],
    'id-ID' => [
        'now_playing' => 'SEDANG TAYANG',
        'watch_trailer' => 'Tonton Trailer',
        'no_trailer' => 'Tidak Ada Trailer',
        'details' => 'Detail',
        'featured_today' => 'Pilihan Hari Ini',
        'highlight_day' => 'Sorotan hari ini',
        'trending_1' => 'Trending #1 Hari Ini',
        'read_more' => 'Selengkapnya',
        'watchlist' => 'Watchlist',
        'my_profile' => 'Profil Saya',
        'my_reviews' => 'Riwayat Ulasan',
        'profile_settings' => 'Pengaturan Akun',
        'update_profile' => 'Perbarui Profil',
        'all_media' => 'Semua',
        'movies_only' => 'Hanya Movie',
        'tv_only' => 'Hanya TV Show',
        'trending_now' => 'Sedang Trending',
        'top_picks' => 'Pilihan Teratas',
        'upcoming_movies' => 'Segera Tayang',
        'similar_movies' => 'Rekomendasi Terkait',
        'recommended_for_you' => 'Rekomendasi Untukmu',
        'based_on_rating' => 'Berdasarkan film yang Anda beri rating tinggi',
        'synopsis' => 'Sinopsis',
        'no_synopsis' => 'Sinopsis belum tersedia untuk film ini.',
        'explore_movies' => 'Jelajahi Film',
        'explore_tv' => 'Jelajahi TV Show',
        'explore_desc' => 'Dari mahakarya sinematik legendaris hingga rilis blockbuster terbaru. Sesuaikan filter di bawah dan temukan tontonan sempurna untuk menemani waktu santaimu hari ini.',
        'explore_tv_desc' => 'Dari serial epik hingga acara realitas terbaru. Sesuaikan filter di bawah dan temukan tontonan maraton sempurna untuk hari ini.',
        'genre' => 'Genre',
        'all_genre' => 'Semua Genre',
        'year' => 'Tahun',
        'all_year' => 'Semua Tahun',
        'rating' => 'Rating',
        'all_rating' => 'Semua Rating',
        'language' => 'Bahasa',
        'all_language' => 'Semua Bahasa',
        'sort_by' => 'Urutkan',
        'sort_pop_desc' => 'Terpopuler',
        'sort_rating_desc' => 'Rating Tertinggi',
        'sort_date_desc' => 'Rilis Terbaru',
        'sort_date_asc' => 'Rilis Terlama',
        'reset_filter' => 'Reset Filter',
        'clear_filter' => 'Bersihkan Filter',
        'prev' => 'Sebelumnya',
        'next' => 'Selanjutnya',
        'no_movies' => 'Oops, Tidak Ada Film!',
        'no_movies_desc' => 'Coba sesuaikan filter pencarianmu untuk melihat hasil lainnya.',
        'movie_not_found' => 'Film Tidak Ditemukan / Tidak ada koneksi.',
        'add_favorite' => 'Tambah ke Favorit',
        'user_reviews' => 'Ulasan Pengguna',
        'share_opinion' => 'Bagikan pendapatmu tentang film ini',
        'write_review' => 'Tulis Ulasan Kamu',
        'review_placeholder' => 'Apa yang kamu pikirkan tentang film ini?',
        'submit_review' => 'Kirim Ulasan',
        'just_now' => 'Baru saja',
        'alert_review' => 'Mohon isi rating dan ulasan terlebih dahulu.',
        'all' => 'Semua',
        'user' => 'Pengguna'
    ]
];

// Fungsi pembantu untuk menerjemahkan statis teks UI
function translateText($key) {
    global $siteTranslations, $siteLang;
    $lang = isset($siteTranslations[$siteLang]) ? $siteLang : 'en-US';
    return isset($siteTranslations[$lang][$key]) ? $siteTranslations[$lang][$key] : $key;
}

// Fungsi Pembantu untuk Memanggil TMDB
function fetchTMDB($endpoint, $cache_ttl = 3600) {
    global $tmdbApiKey, $siteLang;
    $separator = strpos($endpoint, '?') !== false ? "&" : "?";
    $url = "https://api.themoviedb.org/3/" . $endpoint . $separator . "api_key=" . $tmdbApiKey . "&language=" . $siteLang;
    
    // Setup direktori cache sementara
    $cacheDir = __DIR__ . '/../cache/';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
    
    // Buat nama file cache unik berdasarkan URL API
    $cacheFile = $cacheDir . md5($url) . '.json';
    
    // Cek apakah cache valid (belum expired / umurnya di bawah 1 jam)
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cache_ttl) {
        return json_decode(@file_get_contents($cacheFile), true);
    }
    
    // Gunakan cURL karena jauh lebih stabil dan cepat dibanding file_get_contents
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Mencegah error SSL Certificate di XAMPP
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // Mencegah Lag DNS IPv6 di Windows
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Simpan data respons ke dalam file cache jika sukses (HTTP 200)
    if ($response && $httpCode === 200) {
        @file_put_contents($cacheFile, $response);
        return json_decode($response, true);
    }
    
    // Jika koneksi TMDB gagal/putus, ambil data dari cache lama jika file nya ada
    if (file_exists($cacheFile)) {
        return json_decode(@file_get_contents($cacheFile), true);
    }
    
    return null;
}

// Fungsi untuk memformat array hasil TMDB
function formatMovies($results, $limit = 8, $type = 'movie') {
    global $genreMap, $tvGenreMap;
    $movies = [];
    if (!$results) return $movies;
    
    $map = $type === 'tv' ? $tvGenreMap : $genreMap;
    
    foreach (array_slice($results, 0, $limit) as $item) {
        $genre = $type === 'tv' ? "TV Show" : "Movie";
        if (!empty($item['genre_ids'])) {
            $genre = $map[$item['genre_ids'][0]] ?? $genre;
        }
        $movies[] = [
            "id" => $item['id'] ?? 0,
            "title" => $item['title'] ?? $item['original_title'] ?? $item['name'] ?? "Unknown",
            "year" => isset($item['release_date']) && strlen($item['release_date']) >= 4 ? substr($item['release_date'], 0, 4) : (isset($item['first_air_date']) && strlen($item['first_air_date']) >= 4 ? substr($item['first_air_date'], 0, 4) : "-"),
            "genre" => $genre,
            "rating" => isset($item['vote_average']) ? round($item['vote_average'], 1) : 0,
            "image" => !empty($item['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $item['poster_path'] : "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22500%22%20height%3D%22750%22%20viewBox%3D%220%200%20500%20750%22%3E%3Crect%20width%3D%22500%22%20height%3D%22750%22%20fill%3D%22%231a1a1a%22%2F%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20font-family%3D%22sans-serif%22%20font-size%3D%2230%22%20fill%3D%22%23555555%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENo%20Poster%3C%2Ftext%3E%3C%2Fsvg%3E",
            "backdrop" => !empty($item['backdrop_path']) ? "https://image.tmdb.org/t/p/original" . $item['backdrop_path'] : "",
            "overview" => $item['overview'] ?? ""
        ];
    }
    return $movies;
}

function getTrendingMovies() {
    $data = fetchTMDB("trending/movie/day");
    return formatMovies($data['results'] ?? []);
}

function getTopPicks() {
    $data = fetchTMDB("movie/top_rated");
    return formatMovies($data['results'] ?? []);
}

function getPopularMovies($limit = 20) {
    $data = fetchTMDB("movie/popular");
    return formatMovies($data['results'] ?? [], $limit);
}

function getUpcomingMovies() {
    $data = fetchTMDB("movie/upcoming");
    return formatMovies($data['results'] ?? []);
}

function discoverMovies($filters = [], $limit = 20, $page = 1) {
    $sortBy = !empty($filters['sort']) ? $filters['sort'] : "popularity.desc";
    $endpoint = "discover/movie?sort_by=" . urlencode($sortBy) . "&page=" . intval($page);
    
    if (!empty($filters['genre'])) {
        $endpoint .= "&with_genres=" . urlencode($filters['genre']);
    }
    if (!empty($filters['year'])) {
        $endpoint .= "&primary_release_year=" . urlencode($filters['year']);
    }
    if (!empty($filters['rating'])) {
        $endpoint .= "&vote_average.gte=" . urlencode($filters['rating']);
    }
    if (!empty($filters['lang'])) {
        $endpoint .= "&with_original_language=" . urlencode($filters['lang']);
    }
    
    $data = fetchTMDB($endpoint);
    return formatMovies($data['results'] ?? [], $limit);
}

function discoverTVShows($filters = [], $limit = 20, $page = 1) {
    $sortBy = !empty($filters['sort']) ? $filters['sort'] : "popularity.desc";
    $endpoint = "discover/tv?sort_by=" . urlencode($sortBy) . "&page=" . intval($page);
    
    if (!empty($filters['genre'])) {
        $endpoint .= "&with_genres=" . urlencode($filters['genre']);
    }
    if (!empty($filters['year'])) {
        $endpoint .= "&first_air_date_year=" . urlencode($filters['year']);
    }
    if (!empty($filters['rating'])) {
        $endpoint .= "&vote_average.gte=" . urlencode($filters['rating']);
    }
    if (!empty($filters['lang'])) {
        $endpoint .= "&with_original_language=" . urlencode($filters['lang']);
    }
    
    $data = fetchTMDB($endpoint);
    return formatMovies($data['results'] ?? [], $limit, 'tv');
}

function getHeroBanners() {
    $data = fetchTMDB("movie/now_playing");
    $movies = formatMovies($data['results'] ?? [], 4);
    $banners = [];
    foreach($movies as $m) {
        if(!empty($m['backdrop'])) {
            // Cari video trailer dari YouTube
            $trailerUrl = "#";
            $vidData = fetchTMDB("movie/" . $m['id'] . "/videos");
            if (!empty($vidData['results'])) {
                foreach ($vidData['results'] as $video) {
                    if ($video['site'] === 'YouTube' && ($video['type'] === 'Trailer' || $video['type'] === 'Teaser')) {
                        $trailerUrl = "https://www.youtube.com/watch?v=" . $video['key'];
                        break;
                    }
                }
            }

            $banners[] = [
                "id" => $m['id'],
                "bg" => "url('" . $m['backdrop'] . "')",
                "title" => $m['title'],
                "meta" => $m['year'] . " • " . $m['genre'],
                "synopsis" => $m['overview'],
                "rating" => $m['rating'],
                "trailer" => $trailerUrl
            ];
        }
    }
    return $banners;
}

function getMovieDetails($id) {
    if(empty($id)) return null;
    return fetchTMDB("movie/" . intval($id) . "?append_to_response=videos");
}

function getMediaDetails($id, $type = 'movie') {
    if(empty($id)) return null;
    return fetchTMDB($type . "/" . intval($id) . "?append_to_response=videos");
}

function getPersonalizedRecommendations($limit = 10) {
    if (session_status() === PHP_SESSION_NONE) { @session_start(); }
    if (!isset($_SESSION['user_id'])) return [];
    
    $dbPath = __DIR__ . '/db.php';
    if(file_exists($dbPath)) {
        require_once $dbPath;
        global $conn;
        if($conn) {
            $uid = (int)$_SESSION['user_id'];
            try {
                // Cari 1 film paling terakhir yang di-rating 4 atau 5 oleh akun ini di MySQL
                $res = $conn->query("SELECT media_id, media_type FROM reviews WHERE user_id = $uid AND rating >= 4 ORDER BY created_at DESC LIMIT 1");
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $data = fetchTMDB($row['media_type'] . "/" . intval($row['media_id']) . "/recommendations");
                    if (!empty($data['results'])) {
                        return formatMovies($data['results'], $limit, $row['media_type']);
                    }
                }
            } catch (Exception $e) {}
        }
    }
    return [];
}

function getSimilarMedia($id, $type = 'movie', $limit = 10) {
    if(empty($id)) return [];
    
    // Prioritaskan endpoint 'recommendations' karena menggunakan AI & User Behavior (Sangat Akurat)
    $data = fetchTMDB($type . "/" . intval($id) . "/recommendations");
    
    // Jika tidak ada rekomendasi (misal film terlalu baru/indie), fallback ke 'similar' (berdasarkan keyword)
    if (empty($data['results'])) {
        $data = fetchTMDB($type . "/" . intval($id) . "/similar");
    }
    
    return formatMovies($data['results'] ?? [], $limit, $type);
}

function searchMovies($query, $page = 1) {
    if(empty($query)) return [];
    $data = fetchTMDB("search/movie?query=" . urlencode($query) . "&page=" . intval($page));
    return formatMovies($data['results'] ?? [], 20); // Tampilkan max 20 pencarian
}

function buildFilterUrl($currentFilters, $keyToChange, $newValue, $pageName = 'movies') {
    $params = $currentFilters;
    $params[$keyToChange] = $newValue;
    $params['page'] = $pageName;
    
    // Hapus filter yang kosong agar URL bersih
    $params = array_filter($params);
    
    return 'index.php?' . http_build_query($params);
}
?>