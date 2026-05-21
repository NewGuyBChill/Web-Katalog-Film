<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinema - Katalog Film</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-left">
            <div class="logo">KINEMA</div>
            <ul class="nav-links">
                <li><a href="index.php?page=home" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>">Homepage</a></li>
                <li><a href="#">Movies</a></li>
                <li><a href="#">TV Shows</a></li>
                <li><a href="#">Games</a></li>
            </ul>
        </div>
        <form action="index.php" method="GET" class="search-container" id="searchContainer">
            <input type="hidden" name="page" value="search">
            <button type="submit" class="search-trigger" id="searchTrigger">
                <i class="fas fa-search"></i>
            </button>
            <input type="text" name="q" class="search-input" id="searchInput" placeholder="Search movies...">
        </form>

        <div class="lang-container" id="langContainer">
            <div class="lang-trigger" id="langTrigger">
                <i class="fas fa-globe"></i>
                <span class="lang-text" id="currentLang">ID</span>
                <i class="fas fa-chevron-down" style="font-size: 0.6rem; margin-left: 3px;"></i>
            </div>
            
            <div class="lang-dropdown" id="langDropdown">
                <div class="lang-option active" data-lang="ID">Indonesia</div>
                <div class="lang-option" data-lang="EN">English</div>
                <div class="lang-option" data-lang="KR">한국어</div>
                <div class="lang-option" data-lang="JP">日本語</div>
            </div>
        </div>
    </nav>