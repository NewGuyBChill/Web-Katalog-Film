<p align="center">
  <strong style="font-size: 2rem;">Celes<span style="font-weight: 200;">View</span></strong>
  <br>
  <em>Katalog Film & TV Show Berbasis Web — Powered by TMDB API</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-Native-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mariadb&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/API-TMDB-01B4E4?logo=themoviedatabase&logoColor=white" alt="TMDB">
  <img src="https://img.shields.io/badge/Server-XAMPP-FB7A24?logo=xampp&logoColor=white" alt="XAMPP">
</p>

---

## 📖 Deskripsi

**CelesView** adalah aplikasi web katalog film dan TV show yang dibangun menggunakan **PHP Native** (tanpa framework) dengan arsitektur **modular MVC-like**. Aplikasi ini mengintegrasikan **TMDB (The Movie Database) API** untuk data film real-time dan menggunakan **MySQL/MariaDB** untuk menyimpan data pengguna, ulasan, watchlist, dan fitur sosial.

### ✨ Fitur Utama

| Kategori | Fitur |
|----------|-------|
| **Katalog** | Hero banner auto-slide, trending, top picks, upcoming, discover movies/TV shows dengan filter & sorting |
| **Pencarian** | Live search real-time (AJAX) + halaman hasil pencarian dengan pagination |
| **Detail Media** | Poster, sinopsis, rating, trailer YouTube, daftar pemeran, rekomendasi AI |
| **Autentikasi** | Login, signup, logout, forgot password, session management |
| **Watchlist** | Simpan/hapus film favorit ke database (per akun) |
| **Review & Rating** | Beri rating bintang (1-5) dan tulis ulasan, edit, hapus |
| **Sosial** | Like ulasan, balas ulasan, follow/unfollow pengguna, activity feed |
| **Playlist** | Buat playlist kustom, tambah film ke playlist |
| **Notifikasi** | Notifikasi real-time (like, follow, reply) dengan badge counter |
| **Profil** | Profil publik, avatar, statistik, pemeran favorit, pencarian pengguna |
| **Admin** | Dashboard moderasi, statistik, hapus ulasan (role-based) |
| **i18n** | Dukungan bilingual (English / Bahasa Indonesia) via cookie |
| **Tema** | Dark mode (default) + Light mode toggle |
| **Performa** | File-based API caching, cURL multi-exec parallel, garbage collection otomatis |

---

## 🏗️ Arsitektur Proyek

```
Web-Katalog-Film/
│
├── index.php                    # 🚪 Entry point & router utama (Front Controller)
├── .gitignore                   # Mengabaikan config/db.php dari Git
│
├── config/                      # ⚙️ Konfigurasi & Service Layer
│   ├── db.php                   # Koneksi MySQL (mysqli) — DI-GITIGNORE
│   ├── data.php                 # TMDB API service, helper functions, i18n translations
│   └── db/
│       └── if0_42011841_kinema_db.sql  # SQL dump schema + sample data
│
├── includes/                    # 🧩 Template Global (Header/Footer)
│   ├── header.php               # HTML head, navbar, SEO meta, session logic
│   └── footer.php               # Footer + load script.js
│
├── assets/                      # 🎨 Frontend Assets
│   ├── css/
│   │   ├── base.css             # CSS variables, reset, typography
│   │   ├── style.css            # Stylesheet utama (25KB)
│   │   ├── components/
│   │   │   ├── navbar.css       # Navbar responsive + dropdowns
│   │   │   ├── hero.css         # Hero banner section
│   │   │   ├── cards.css        # Movie cards & grid
│   │   │   └── auth.css         # Login/signup forms
│   │   └── themes/
│   │       └── light-mode.css   # Override variabel untuk mode terang
│   └── js/
│       └── script.js            # JavaScript utama (41KB) — semua interaksi client
│
├── modules/                     # 📦 Modul Fitur (Halaman & AJAX endpoints)
│   ├── Catalog/                 # Halaman katalog film
│   │   ├── home.php             # Homepage (hero, trending, upcoming, top picks)
│   │   ├── movies.php           # Discover movies + filter
│   │   ├── tvshows.php          # Discover TV shows + filter
│   │   ├── details.php          # Detail film/TV (review, cast, similar)
│   │   ├── search.php           # Halaman hasil pencarian
│   │   └── person.php           # Detail aktor/aktris
│   │
│   ├── Auth/                    # Autentikasi
│   │   ├── login.php            # Halaman login
│   │   ├── signup.php           # Halaman registrasi
│   │   ├── profile.php          # Edit akun (username, password, hapus akun)
│   │   └── forgot_password.php  # Reset password
│   │
│   ├── User/                    # Fitur sosial & profil pengguna
│   │   ├── user_profile.php     # Profil publik + pencarian pengguna
│   │   ├── watchlist.php        # Daftar watchlist user
│   │   ├── my_reviews.php       # Riwayat ulasan user
│   │   ├── notifications.php    # Halaman notifikasi lengkap
│   │   ├── user_follows.php     # Daftar followers/following
│   │   ├── activity_feed.php    # Feed aktivitas
│   │   ├── ajax_watchlist.php   # API: tambah/hapus watchlist
│   │   ├── ajax_review.php      # API: CRUD ulasan
│   │   ├── ajax_follow_user.php # API: follow/unfollow
│   │   ├── ajax_favorite_cast.php # API: favorit pemeran
│   │   ├── ajax_search_user.php # API: pencarian pengguna live
│   │   └── ajax_activity_feed.php # API: load activity feed
│   │
│   ├── Api/                     # AJAX endpoints global
│   │   ├── ajax_search.php      # API: live search film (navbar)
│   │   ├── ajax_notifications.php # API: fetch & mark-read notifikasi
│   │   ├── ajax_like_review.php # API: like/unlike ulasan
│   │   └── ajax_review_reply.php # API: balasan ulasan
│   │
│   ├── admin/                   # Panel admin
│   │   ├── dashboard.php        # Dashboard moderasi
│   │   └── ajax_admin.php       # API: hapus ulasan (admin)
│   │
│   └── playlists/               # Playlist kustom
│       ├── my_lists.php         # Daftar playlist user
│       ├── view_list.php        # Detail playlist
│       └── ajax_playlist.php    # API: CRUD playlist
│
├── cache/                       # 📁 Cache file JSON dari TMDB API (auto-generated)
│
└── reference/
    └── HOMEPAGE.png             # Screenshot referensi desain
```

---

## 🔄 Alur Kerja Aplikasi (Application Flow)

### 1. Request Lifecycle

```
Browser Request
      │
      ▼
  index.php (Front Controller)
      │
      ├─ Baca parameter ?page=xxx dari URL
      ├─ Cocokkan dengan array $routes
      │
      ├─ Jika AJAX endpoint → require file, lalu exit (tanpa HTML wrapper)
      ├─ Jika logout → session_destroy(), redirect
      │
      └─ Jika halaman biasa:
           ├─ require config/data.php (TMDB service + i18n)
           ├─ require includes/header.php (HTML head + navbar)
           ├─ require modules/Xxx/halaman.php (konten)
           └─ require includes/footer.php (footer + script.js)
```

### 2. Alur Data TMDB API

```
Module membutuhkan data film
      │
      ▼
  Panggil fungsi helper (getTrendingMovies, discoverMovies, dll.)
      │
      ▼
  fetchTMDB($endpoint)
      │
      ├─ Cek cache file (cache/*.json)
      │   ├─ Cache valid (< 1 jam) → Return data dari cache
      │   └─ Cache expired / tidak ada → Lanjut ke API
      │
      ├─ HTTP request via cURL ke api.themoviedb.org
      │   ├─ Sukses (200) → Simpan ke cache, return data
      │   └─ Gagal → Gunakan cache lama jika ada (stale-while-error)
      │
      └─ 5% chance: Garbage Collection hapus cache expired (max 30 file)
```

### 3. Alur Autentikasi

```
Signup → password_hash() → INSERT ke tabel users → Set session → Redirect
Login  → SELECT user by email → password_verify() → Set session → Redirect
Logout → session_destroy() → Redirect ke homepage
```

---

## 🗄️ Skema Database

Database: **`kinema_db`** — 9 tabel:

| Tabel | Fungsi | Relasi |
|-------|--------|--------|
| `users` | Data akun (name, email, password hash, avatar, role) | PK: `id` |
| `reviews` | Ulasan & rating film/TV | FK → `users.id` (CASCADE) |
| `review_likes` | Like pada ulasan | FK → `reviews.id`, `users.id` (CASCADE) |
| `review_replies` | Balasan pada ulasan | FK → `reviews.id`, `users.id` |
| `watchlist` | Daftar tontonan favorit user | FK → `users.id` |
| `notifications` | Notifikasi (like, follow, reply) | FK → `users.id` (CASCADE) |
| `user_follows` | Relasi follow antar pengguna | FK → `users.id` |
| `favorite_casts` | Pemeran favorit user | UNIQUE(`user_id`, `cast_id`) |
| `custom_playlists` | Playlist kustom | FK → `users.id` |
| `playlist_items` | Item dalam playlist | FK → `custom_playlists.id` |

---

## 🚀 Cara Menjalankan

### Prasyarat

- **XAMPP** (PHP 7.4+ dan MySQL/MariaDB)
- Ekstensi PHP: `curl`, `mysqli`, `json`, `mbstring` (semua sudah aktif di XAMPP default)
- Koneksi internet (untuk mengambil data dari TMDB API)

### Langkah-langkah Instalasi

**1. Clone atau Download Repository**

```bash
cd C:\xampp\htdocs
git clone https://github.com/NewGuyBChill/Web-Katalog-Film.git celesview/Web-Katalog-Film
```

**2. Buat File Konfigurasi Database**

Karena `config/db.php` di-gitignore, buat file ini secara manual:

```php
<?php
// config/db.php
$host = "localhost";
$user = "root";
$pass = "";           // Password default XAMPP (kosong)
$dbname = "kinema_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```

**3. Buat Database dan Import Schema**

Buka **phpMyAdmin** (`http://localhost/phpmyadmin`):

1. Buat database baru bernama **`kinema_db`**
2. Pilih tab **Import**
3. Upload file `config/db/if0_42011841_kinema_db.sql`
4. Klik **Go/Execute**

Atau via terminal:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS kinema_db"
mysql -u root kinema_db < config/db/if0_42011841_kinema_db.sql
```

**4. Jalankan XAMPP**

1. Buka **XAMPP Control Panel**
2. Start **Apache** dan **MySQL**
3. Buka browser ke:

```
http://localhost/celesview/Web-Katalog-Film/
```

---

## 🔑 Akun Demo

Dari SQL dump, beberapa akun yang tersedia (password asli tidak diketahui karena di-hash):

| Email | Nama | Role |
|-------|------|------|
| `selby@gmail.com` | ehehhe | user |
| `fathur@gmail.com` | Fathur | user |

Untuk **akses admin**, login dengan akun apapun, lalu kunjungi:
```
index.php?page=admin&make_me_admin=1
```

Atau daftar akun baru via halaman **Sign Up**.

---

## 🛠️ Troubleshooting

### ❌ Halaman Blank / Error 500

| Kemungkinan Penyebab | Solusi |
|---|---|
| File `config/db.php` belum dibuat | Buat file sesuai instruksi di atas |
| Database `kinema_db` belum ada | Import SQL schema via phpMyAdmin |
| Apache/MySQL belum running | Start keduanya di XAMPP Control Panel |

### ❌ "Koneksi database gagal"

| Kemungkinan Penyebab | Solusi |
|---|---|
| MySQL belum start | Buka XAMPP → Start MySQL |
| Port MySQL bentrok | Ubah port di `my.ini` atau matikan service MySQL lain |
| Credential salah | Pastikan `$user` dan `$pass` di `db.php` sesuai XAMPP Anda |

### ❌ Film/Poster Tidak Muncul

| Kemungkinan Penyebab | Solusi |
|---|---|
| Tidak ada koneksi internet | Pastikan PC terhubung ke internet (TMDB API butuh akses online) |
| API key TMDB expired/invalid | Ganti `$tmdbApiKey` di `config/data.php` dengan key baru dari [themoviedb.org](https://www.themoviedb.org/settings/api) |
| Cache corrupt | Hapus semua file `.json` di folder `cache/` |
| cURL extension nonaktif | Buka `php.ini`, pastikan `extension=curl` tidak diawali titik koma (`;`) |
| SSL error pada cURL | Sudah ditangani (`CURLOPT_SSL_VERIFYPEER = false`), jika masih error pastikan file `cacert.pem` tersedia |

### ❌ 404 — Halaman Tidak Ditemukan

| Kemungkinan Penyebab | Solusi |
|---|---|
| Case-sensitive folder di Linux | Pastikan nama folder `modules/Catalog`, `modules/Auth`, `modules/User`, `modules/Api` huruf kapitalnya sesuai. Router sudah memiliki fallback case-insensitive otomatis. |
| Parameter `?page=` salah | Cek daftar route di `index.php` array `$routes` |

### ❌ Review/Watchlist Tidak Bisa Disimpan

| Kemungkinan Penyebab | Solusi |
|---|---|
| Belum login | Login terlebih dahulu |
| Tabel belum ada di database | Import ulang SQL schema, atau biarkan fitur auto-create table di `details.php` berjalan |
| Session timeout | Refresh halaman dan login ulang |

### ❌ Lag / Lambat saat Loading

| Kemungkinan Penyebab | Solusi |
|---|---|
| DNS IPv6 lag di Windows | Sudah ditangani (`CURLOPT_IPRESOLVE = CURL_IPRESOLVE_V4` dan `$host = "localhost"`) |
| Cache kosong (cold start) | Request pertama selalu lebih lambat karena harus fetch dari TMDB. Request berikutnya akan cepat karena cache aktif (TTL 1 jam) |
| `curl_multi_exec` diblokir hosting | Aplikasi otomatis fallback ke sequential `fetchTMDB()` jika hosting tidak mendukung `curl_multi` |

---

## ⚙️ Konfigurasi Lanjutan

### Mengubah API Key TMDB

Edit `config/data.php` baris 2:

```php
$tmdbApiKey = "YOUR_NEW_API_KEY_HERE";
```

Dapatkan API key gratis di: https://www.themoviedb.org/settings/api

### Mengubah TTL Cache

Default: **3600 detik (1 jam)**. Ubah parameter kedua pada pemanggilan `fetchTMDB()`:

```php
$data = fetchTMDB("trending/movie/day", 1800); // 30 menit
```

### Deploy ke Hosting (InfinityFree, dll.)

1. Upload semua file via FTP/File Manager
2. Buat database MySQL di panel hosting
3. Sesuaikan `config/db.php` dengan credential hosting
4. Pastikan folder `cache/` memiliki permission **writable** (`chmod 777`)
5. Jika hosting memblokir `curl_multi_exec`, aplikasi otomatis fallback

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan edukasi dan portofolio.
Data film bersumber dari [TMDB API](https://www.themoviedb.org/) — bukan afiliasi resmi.
