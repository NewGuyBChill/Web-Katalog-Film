<?php
$tmdbApiKey = "ac2e690e071692fe9f8e181d6370f6c7"; // API Key TMDB kamu

// Mapping ID Genre dari TMDB ke nama aslinya
$genreMap = [
    28 => "Action", 12 => "Adventure", 16 => "Animation", 35 => "Comedy", 80 => "Crime",
    99 => "Documentary", 18 => "Drama", 10751 => "Family", 14 => "Fantasy", 36 => "History",
    27 => "Horror", 10402 => "Music", 9648 => "Mystery", 10749 => "Romance", 878 => "Sci-Fi",
    10770 => "TV Movie", 53 => "Thriller", 10752 => "War", 37 => "Western"
];

// Fungsi Pembantu untuk Memanggil TMDB
function fetchTMDB($endpoint) {
    global $tmdbApiKey;
    $separator = strpos($endpoint, '?') !== false ? "&" : "?";
    $url = "https://api.themoviedb.org/3/" . $endpoint . $separator . "api_key=" . $tmdbApiKey . "&language=en-US";
    // @ mencegah tampilan error muncul jika komputer mati internet
    $response = @file_get_contents($url);
    return $response ? json_decode($response, true) : null;
}

// Fungsi untuk memformat array hasil TMDB
function formatMovies($results, $limit = 8) {
    global $genreMap;
    $movies = [];
    if (!$results) return $movies;
    
    foreach (array_slice($results, 0, $limit) as $item) {
        $genre = "Movie";
        if (!empty($item['genre_ids'])) {
            $genre = $genreMap[$item['genre_ids'][0]] ?? "Movie";
        }
        $movies[] = [
            "title" => $item['title'] ?? $item['original_title'] ?? $item['name'] ?? "Unknown",
            "year" => isset($item['release_date']) && strlen($item['release_date']) >= 4 ? substr($item['release_date'], 0, 4) : "-",
            "genre" => $genre,
            "rating" => isset($item['vote_average']) ? round($item['vote_average'], 1) : 0,
            "image" => !empty($item['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $item['poster_path'] : "https://via.placeholder.com/500x750?text=No+Poster",
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

function getHeroBanners() {
    $data = fetchTMDB("movie/now_playing");
    $movies = formatMovies($data['results'] ?? [], 4);
    $banners = [];
    foreach($movies as $m) {
        if(!empty($m['backdrop'])) {
            $banners[] = [
                "bg" => "url('" . $m['backdrop'] . "')",
                "title" => $m['title'],
                "meta" => $m['year'] . " • " . $m['genre'],
                "synopsis" => $m['overview']
            ];
        }
    }
    return $banners;
}

function searchMovies($query) {
    if(empty($query)) return [];
    $data = fetchTMDB("search/movie?query=" . urlencode($query));
    return formatMovies($data['results'] ?? [], 20); // Tampilkan max 20 pencarian
}
?>