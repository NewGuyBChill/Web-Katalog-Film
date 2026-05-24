```structure
movie-rating-platform/
│
├── app/
│   │
│   ├── Core/   --->  otak utama sistem  
│   │   ├── App.php (memulai aplikasi & memuat konfigurasi)
│   │   ├── Router.php  (mengatur rute & mencocokkan URL dengan controller)
│   │   ├── Database.php    (menangani koneksi database & query)
│   │   ├── Controller.php  (menangani logika controller & memuat view)
│   │   ├── Model.php   (model data) 
│   │   ├── Request.php (memproses permintaan HTTP)
│   │   ├── Response.php  (mengembalikan hasil ke browser)
│   │   ├── Session.php (mengelola sesi pengguna)
│   │   ├── Cache.php (mengelola cache untuk mempercepat loading)
│   │   ├── Validator.php   (memvalidasi input)
│   │   ├── Auth.php  (mengelola autentikasi)
│   │   └── Helpers.php (fungsi bantuan)
│   │
│   ├── Middleware/   ---> lapisan keamanan & kontrol akses
│   │   ├── AuthMiddleware.php  (memeriksa apakah pengguna login)
│   │   ├── GuestMiddleware.php (memeriksa apakah pengguna belum login)
│   │   ├── AdminMiddleware.php (memeriksa apakah pengguna adalah admin)
│   │   ├── CSRFMiddleware.php  (melindungi dari serangan CSRF)
│   │   └── RateLimitMiddleware.php (melindungi dari serangan rate limiting)
│   │
│   ├── Services/   ---> logika bisnis & integrasi API external
│   │   │
│   │   ├── TMDB/   ---> API external
│   │   │   ├── TMDBClient.php  (client API TMDB)
│   │   │   ├── MovieService.php (mengambil data film)
│   │   │   ├── SearchService.php (mencari film)
│   │   │   ├── TrendingService.php (mengambil film trending)
│   │   │   ├── ActorService.php  (mengambil data aktor)
│   │   │   ├── GenreService.php  (mengambil data genre)
│   │   │   ├── RecommendationService.php (memberikan rekomendasi film)
│   │   │   └── DiscoverService.php (menemukan film berdasarkan kriteria)
│   │   │
│   │   ├── Cache/  ---> cache system
│   │   │   ├── CacheService.php (mengelola cache)
│   │   │   ├── FileCache.php    (cache berbasis file)
│   │   │   └── RedisCache.php   (cache berbasis Redis)
│   │   │
│   │   ├── Auth/   ---> authentication
│   │   │   ├── LoginService.php (logika login)
│   │   │   ├── RegisterService.php (logika registrasi)
│   │   │   ├── PasswordResetService.php (logika reset password)
│   │   │   └── SessionService.php (logika sesi)
│   │   │
│   │   ├── Upload/   ---> upload system
│   │   │   ├── AvatarUploadService.php (mengunggah avatar)
│   │   │   ├── BannerUploadService.php (mengunggah banner)
│   │   │   ├── ImageOptimizer.php (mengoptimalkan gambar)
│   │   │   └── StorageService.php (menyimpan gambar)
│   │   │
│   │   ├── Review/   ---> review system
│   │   │   ├── ReviewService.php (logika review)
│   │   │   ├── ReviewLikeService.php (logika like review)
│   │   │   ├── ReviewCommentService.php (logika komentar review)
│   │   │   └── SpoilerService.php (logika spoiler)
│   │   │
│   │   ├── Rating/   ---> rating system
│   │   │   ├── RatingService.php (logika rating)
│   │   │   ├── WeightedRatingService.php (logika weighted rating)
│   │   │   └── TrendingScoreService.php (logika trending score)
│   │   │
│   │   ├── Watchlist/   ---> watchlist system
│   │   │   ├── WatchlistService.php (logika watchlist)
│   │   │   └── FavoriteService.php (logika favorite)
│   │   │
│   │   ├── Notification/   ---> notification system
│   │   │   ├── NotificationService.php (logika notifikasi)
│   │   │   ├── PushNotificationService.php (logika push notifikasi)
│   │   │   └── EmailNotificationService.php (logika email notifikasi)
│   │   │
│   │   ├── Recommendation/   ---> recommendation system
│   │   │   ├── CollaborativeFiltering.php (logika collaborative filtering)
│   │   │   ├── ContentBasedFiltering.php (logika content based filtering)
│   │   │   ├── GenreSimilarity.php (logika genre similarity)
│   │   │   └── UserPreferenceService.php (logika user preference)
│   │   │
│   │   └── Activity/   ---> activity system
│   │       ├── ActivityFeedService.php (logika activity feed)
│   │       ├── UserActivityService.php (logika user activity)
│   │       └── TimelineService.php     (logika timeline)
│   │
│   └── Shared/   ---> file-file yang digunakan bersama
│       │
│       ├── Helpers/   ---> helpers
│       │   ├── ImageHelper.php (memformat gambar)
│       │   ├── DateHelper.php  (memformat tanggal)
│       │   ├── SlugHelper.php  (membuat slug)
│       │   ├── RatingHelper.php (memformat rating)
│       │   └── NumberHelper.php (memformat number)
│       │
│       ├── Traits/   ---> traits
│       │   ├── ApiResponseTrait.php (membuat response)
│       │   ├── UploadTrait.php (mengunggah file)
│       │   └── CacheTrait.php  (mengelola cache)
│       │
│       ├── Constants/   ---> constants
│       │   ├── Roles.php (role pengguna)
│       │   ├── Genres.php (genre film)
│       │   ├── NotificationType.php (tipe notifikasi)
│       │   └── WatchlistStatus.php (status watchlist)
│       │
│       ├── Exceptions/   ---> exceptions
│       │   ├── TMDBException.php (error API TMDB)
│       │   ├── ValidationException.php (error validasi)
│       │   ├── UploadException.php (error upload)
│       │   └── AuthenticationException.php (error autentikasi)
│       │
│       └── Interfaces/   ---> interfaces
│           ├── CacheInterface.php (interface cache)
│           └── UploadInterface.php (interface upload)
│
├── bootstrap/  ---> bootstrap aplikasi
│   ├── app.php (menjalankan aplikasi)
│   ├── routes.php (memuat rute)
│   ├── config.php (memuat konfigurasi)
│   └── middleware.php (memuat middleware)
│
├── config/   ---> file konfigurasi aplikasi
│   ├── app.php (konfigurasi aplikasi)
│   ├── database.php (konfigurasi database)
│   ├── tmdb.php (konfigurasi API TMDB)
│   ├── cache.php (konfigurasi cache)
│   ├── upload.php (konfigurasi upload)
│   └── auth.php (konfigurasi autentikasi)
│
├── database/   ---> database aplikasi
│   │
│   ├── migrations/
│   │   ├── create_users_table.php (membuat tabel users)
│   │   ├── create_reviews_table.php (membuat tabel reviews)
│   │   ├── create_review_comments_table.php (membuat tabel komentar review)
│   │   ├── create_review_likes_table.php (membuat tabel like review)
│   │   ├── create_ratings_table.php (membuat tabel rating)
│   │   ├── create_watchlists_table.php (membuat tabel watchlist)
│   │   ├── create_notifications_table.php (membuat tabel notifikasi)
│   │   ├── create_followers_table.php (membuat tabel follower)
│   │   ├── create_activities_table.php (membuat tabel aktivitas)
│   │   ├── create_user_preferences_table.php (membuat tabel preferensi pengguna)
│   │   └── create_cache_logs_table.php (membuat tabel log cache)
│   │
│   ├── seeders/    ---> seeders database
│   │   ├── AdminSeeder.php (membuat admin)
│   │   ├── UserSeeder.php (membuat user)
│   │   └── DemoReviewSeeder.php (membuat demo review)
│   │
│   └── schema.sql (schema database)
│
├── modules/    ---> modul aplikasi
│   │
│   ├── Home/   ---> modul home
│   │   ├── Controllers/
│   │   │   └── HomeController.php (controller home)
│   │   ├── Views/
│   │   │   └── home.php (view home)
│   │   └── Routes/
│   │       └── web.php (route home)
│   │
│   ├── Movies/   ---> modul movie
│   │   ├── Controllers/   ---> controller movie
│   │   │   └── MovieController.php (controller movie)
│   │   │   └── SearchController.php (controller search)
│   │   ├── Services/   ---> service movie
│   │   │   ├── MovieDetailService.php (mengambil detail movie)
│   │   │   ├── TrendingMovieService.php (mengambil movie trending)
│   │   │   ├── PopularMovieService.php (mengambil movie populer)
│   │   │   └── UpcomingMovieService.php (mengambil movie mendatang)
│   │   ├── Models/   ---> model movie
│   │   │   └── Movie.php (model movie)
│   │   ├── Views/
│   │   │   ├── detail.php (view detail movie)
│   │   │   ├── trending.php (view trending movie)
│   │   │   ├── popular.php (view popular movie)
│   │   │   ├── discover.php (view discover movie)
│   │   │   └── upcoming.php (view upcoming movie)
│   │   └── Routes/   ---> route movie
│   │       └── web.php
│   │
│   ├── Search/   ---> modul search
│   │   ├── Controllers/   ---> controller search
│   │   │   └── SearchController.php (controller search)
│   │   ├── Services/   ---> service search
│   │   │   ├── SearchMovieService.php (service search movie)
│   │   │   ├── SearchActorService.php (service search actor)
│   │   │   └── DiscoverSearchService.php (service discover search)
│   │   ├── Views/   ---> view search
│   │   │   └── search.php
│   │   └── Routes/   ---> route search
│   │       └── web.php
│   │
│   ├── Reviews/   ---> modul review
│   │   ├── Controllers/   ---> controller review
│   │   │   └── ReviewController.php
│   │   ├── Models/   ---> model review
│   │   │   ├── Review.php (model review)
│   │   │   ├── ReviewLike.php (model like review)
│   │   │   └── ReviewComment.php (model komentar review)
│   │   ├── Services/   ---> service review
│   │   │   ├── CreateReviewService.php (service membuat review)
│   │   │   ├── LikeReviewService.php (service like review)
│   │   │   ├── CommentReviewService.php (service komentar review)
│   │   │   └── DeleteReviewService.php (service delete review)
│   │   ├── Views/  ---> view review
│   │   │   ├── review-list.php (view list review)
│   │   │   └── review-form.php (view form review)
│   │   └── Routes/   ---> route review
│   │       └── web.php
│   │
│   ├── Ratings/   ---> modul rating
│   │   ├── Controllers/   ---> controller rating
│   │   │   └── RatingController.php
│   │   ├── Models/   ---> model rating
│   │   │   └── Rating.php (model rating)
│   │   ├── Services/   ---> service rating
│   │   │   ├── RatingCalculatorService.php
│   │   │   ├── StoreRatingService.php
│   │   │   └── AverageRatingService.php
│   │   └── Routes/   ---> route rating
│   │       └── web.php
│   │
│   ├── Watchlist/   ---> modul watchlist
│   │   ├── Controllers/   ---> controller watchlist
│   │   │   └── WatchlistController.php
│   │   ├── Models/   ---> model watchlist
│   │   │   └── Watchlist.php (model watchlist)
│   │   ├── Services/   ---> service watchlist
│   │   │   ├── AddWatchlistService.php (service add watchlist)
│   │   │   ├── RemoveWatchlistService.php (service remove watchlist)
│   │   │   └── FavoriteMovieService.php (service favorite movie)
│   │   ├── Views/  ---> view watchlist
│   │   │   └── watchlist.php (view watchlist)
│   │   └── Routes/   ---> route watchlist
│   │       └── web.php
│   │
│   ├── User/   ---> modul user
│   │   ├── Controllers/   ---> controller user
│   │   │   └── UserController.php
│   │   ├── Models/   ---> model user
│   │   │   └── User.php (model user)
│   │   ├── Services/   ---> service user
│   │   │   ├── ProfileService.php (service profile)
│   │   │   ├── AvatarService.php (service avatar)
│   │   │   ├── BannerService.php (service banner)
│   │   │   ├── PasswordService.php (service password)
│   │   │   └── PreferenceService.php (service preference)
│   │   ├── Views/  ---> view user
│   │   │   ├── profile.php (view profile)
│   │   │   ├── edit-profile.php (view edit profile)
│   │   │   ├── settings.php (view settings)
│   │   │   └── favorites.php (view favorites)
│   │   └── Routes/   ---> route user
│   │       └── web.php
│   │
│   ├── Notifications/   ---> modul notification
│   │   ├── Controllers/   ---> controller notification
│   │   │   └── NotificationController.php
│   │   ├── Models/   ---> model notification
│   │   │   └── Notification.php
│   │   ├── Services/   ---> service notification
│   │   │   ├── SendNotificationService.php (service send notification)
│   │   │   ├── MarkAsReadService.php (service mark as read)
│   │   │   └── NotificationFeedService.php (service notification feed)
│   │   ├── Views/   ---> view notification
│   │   │   └── notifications.php (view notification)
│   │   └── Routes/   ---> route notification
│   │       └── web.php
│   │
│   ├── Recommendation/   ---> modul recommendation
│   │   ├── Controllers/   ---> controller recommendation
│   │   │   └── RecommendationController.php
│   │   ├── Services/   ---> service recommendation
│   │   │   ├── MovieRecommendationService.php (service movie recommendation)
│   │   │   ├── SimilarMovieService.php (service similar movie)
│   │   │   ├── TrendingRecommendationService.php (service trending recommendation)
│   │   │   └── PersonalizedRecommendationService.php (service personalized recommendation)
│   │   ├── Algorithms/   ---> algorithm recommendation
│   │   │   ├── CollaborativeFiltering.php (algorithm collaborative filtering)
│   │   │   ├── GenreSimilarity.php (algorithm genre similarity)
│   │   │   ├── UserSimilarity.php (algorithm user similarity)
│   │   │   └── TrendingScore.php (algorithm trending score)
│   │   ├── Views/  ---> view recommendation
│   │   │   └── recommendation.php (view recommendation)
│   │   └── Routes/   ---> route recommendation
│   │       └── web.php
│   │
│   ├── Activity/   ---> modul activity
│   │   ├── Controllers/   ---> controller activity
│   │   │   └── ActivityController.php
│   │   ├── Models/   ---> model activity
│   │   │   └── Activity.php (model activity)
│   │   ├── Services/   ---> service activity
│   │   │   ├── FeedService.php (service feed)
│   │   │   ├── TimelineService.php (service timeline)
│   │   │   └── UserActivityService.php (service user activity)
│   │   ├── Views/  ---> view activity
│   │   │   └── activity-feed.php (view activity)
│   │   └── Routes/   ---> route activity
│   │       └── web.php
│   │
│   ├── Social/   ---> modul social
│   │   ├── Controllers/   ---> controller social
│   │   │   └── FollowController.php
│   │   ├── Models/   ---> model social
│   │   │   └── Follow.php (model social)
│   │   ├── Services/   ---> service social
│   │   │   ├── FollowService.php (service follow)
│   │   │   ├── UnfollowService.php (service unfollow)
│   │   │   └── FollowerService.php (service follower)
│   │   ├── Views/  ---> view social
│   │   │   └── followers.php (view social)
│   │   └── Routes/   ---> route social
│   │       └── web.php
│   │
│   ├── Upload/   ---> modul upload
│   │   ├── Controllers/   ---> controller upload
│   │   │   └── UploadController.php
│   │   ├── Services/   ---> service upload
│   │   │   ├── UploadAvatarService.php (service upload avatar)
│   │   │   ├── UploadBannerService.php (service upload banner)
│   │   │   ├── UploadReviewImageService.php (service upload review image)
│   │   │   └── CompressImageService.php (service compress image)
│   │   ├── Views/  ---> view upload
│   │   │   └── upload.php (view upload)
│   │   └── Routes/   ---> route upload
│   │       └── web.php
│   │
│   ├── Admin/   ---> modul admin
│   │   ├── Controllers/   ---> controller admin
│   │   │   ├── DashboardController.php (controller dashboard)
│   │   │  ├── UserManagementController.php (controller user management)
│   │   │  ├── ReviewModerationController.php (controller review moderation)
│   │   │  └── AnalyticsController.php (controller analytics)
│   │   ├── Services/   ---> service admin
│   │   │  ├── AnalyticsService.php (service analytics)
│   │   │  ├── ModerationService.php (service moderation)
│   │   │  └── BanUserService.php (service ban user)
│   │   ├── Views/  ---> view admin
│   │   │  ├── dashboard.php (view dashboard)
│   │   │  ├── analytics.php (view analytics)
│   │   │  ├── reviews.php (view reviews)
│   │   │  └── users.php (view users)
│   │   └── Routes/   ---> route admin
│   │       └── web.php
│   │
│   └── TMDB/   ---> modul TMDB
│       ├── Controllers/   ---> controller TMDB
│       │   └── TMDBController.php (controller TMDB)
│       ├── Services/   ---> service TMDB
│       │   ├── FetchMovieService.php (service fetch movie)
│       │   ├── FetchActorService.php (service fetch actor)
│       │   ├── FetchGenreService.php (service fetch genre)
│       │   ├── FetchTrailerService.php (service fetch trailer)
│       │   └── FetchTrendingService.php (service fetch trending)
│       ├── Cache/   ---> cache TMDB
│       │   ├── MovieCache.php (cache movie)
│       │   └── TrendingCache.php (cache trending)
│       └── Routes/   ---> route TMDB
│           └── web.php
│
├── public/   ---> halaman web
│   │
│   ├── assets/   ---> aset website
│   │   ├── css/   ---> css website
│   │   │   ├── app.css (css app)
│   │   │   ├── tailwind.css (css tailwind)
│   │   │   ├── movie.css (css movie)
│   │   │   ├── profile.css (css profile)
│   │   │   └── admin.css (css admin)
│   │   │
│   │   ├── js/   ---> js website
│   │   │   ├── app.js (js app)
│   │   │   ├── search.js (js search)
│   │   │   ├── watchlist.js (js watchlist)
│   │   │   ├── notification.js (js notification)
│   │   │   └── recommendation.js (js recommendation)
│   │   │
│   │   ├── icons/   ---> icon website
│   │   └── images/   ---> gambar website
│   │
│   ├── uploads/   ---> folder upload
│   │   │   ├── app.css (css app)
│   │   │   ├── tailwind.css (css tailwind)
│   │   │   ├── movie.css (css movie)
│   │   │   ├── profile.css (css profile)
│   │   │   └── admin.css (css admin)
│   │   │
│   │   ├── js/   ---> js website
│   │   │   ├── app.js (js app)
│   │   │   ├── search.js (js search)
│   │   │   ├── watchlist.js (js watchlist)
│   │   │   ├── notification.js (js notification)
│   │   │   └── recommendation.js (js recommendation)
│   │   │
│   │   ├── icons/   ---> icon website
│   │   └── images/   ---> gambar website
│   │
│   ├── uploads/   ---> folder upload
│   │   ├── avatars/   ---> folder avatar
│   │   ├── banners/   ---> folder banner
│   │   ├── reviews/   ---> folder review
│   │   └── cache/   ---> folder cache
│   │
│   ├── storage/   ---> folder storage
│   │   ├── cache/   ---> folder cache
│   │   └── tmp/   ---> folder tmp
│   │
│   ├── .htaccess   ---> file .htaccess
│   └── index.php   ---> file index.php
│
├── resources/   ---> resources website
│   ├── views/   ---> views website
│   │   ├── layouts/   ---> layouts website
│   │   │   ├── app.php   ---> file app.php
│   │   │   ├── navbar.php   ---> file navbar.php
│   │   │   ├── footer.php   ---> file footer.php
│   │   │   ├── sidebar.php   ---> file sidebar.php
│   │   │   └── mobile-nav.php   ---> file mobile-nav.php
│   │   │
│   │   ├── components/   ---> components website
│   │   │   ├── movie-card.php   ---> file movie-card.php
│   │   │   ├── review-card.php   ---> file review-card.php
│   │   │   ├── actor-card.php   ---> file actor-card.php
│   │   │   ├── rating-circle.php   ---> file rating-circle.php
│   │   │   ├── search-bar.php   ---> file search-bar.php
│   │   │   ├── notification-dropdown.php   ---> file notification-dropdown.php
│   │   │   ├── recommendation-slider.php   ---> file recommendation-slider.php
│   │   │   └── hero-banner.php   ---> file hero-banner.php
│   │   │
│   │   └── errors/   ---> errors website
│   │       ├── 404.php   ---> file 404.php
│   │       └── 500.php   ---> file 500.php
│   │
│   └── tailwind/   ---> tailwind website
│       ├── input.css   ---> file input.css
│       └── output.css   ---> file output.css
│
├── routes/   ---> routes website
│   ├── web.php   ---> file web.php
│   ├── api.php   ---> file api.php
│   └── admin.php   ---> file admin.php
│
├── storage/   ---> folder storage
│   ├── cache/   ---> folder cache
│   ├── logs/   ---> folder logs
│   ├── sessions/   ---> folder sessions
│   └── tmp/   ---> folder tmp
│
├── vendor/   ---> folder vendor
│
├── .env   ---> file .env
├── .gitignore   ---> file .gitignore
├── composer.json   ---> file composer.json
├── package.json   ---> file package.json
├── tailwind.config.js   ---> file tailwind.config.js
├── vite.config.js   ---> file vite.config.js
├── README.md   ---> file README.md
└── LICENSE   ---> file LICENSE
