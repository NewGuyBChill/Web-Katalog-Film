<?php

/**
 * TMDB API Configuration
 */
return [
    'api_key'    => env('TMDB_API_KEY', ''),
    'base_url'   => env('TMDB_BASE_URL', 'https://api.themoviedb.org/3'),
    'image_base' => env('TMDB_IMAGE_BASE', 'https://image.tmdb.org/t/p'),

    'poster_size'   => 'w500',
    'backdrop_size'  => 'w1280',
    'profile_size'   => 'w185',

    'cache_ttl' => 3600, // Cache TMDB responses for 1 hour
];
