<?php
$current_page = $_GET['page'] ?? '';
?>
<style>
/* Dashboard Layout Base */
.dashboard-container {
    display: flex;
    align-items: stretch; /* Memaksa anak untuk sama tinggi */
    min-height: calc(100vh - 80px);
    margin-top: 80px; /* Offset for navbar */
    background: var(--bg-main);
    color: var(--text-main);
}
.dash-sidebar {
    width: 260px;
    background: rgba(0,0,0,0.3);
    border-right: 1px solid rgba(255,255,255,0.05);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    align-self: stretch; /* Memastikan sidebar selalu setinggi konten utama */
}
.dash-sidebar-inner {
    padding: 2rem 1.5rem;
    position: sticky;
    top: 80px;
}
.dash-nav-list {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
}
.dash-nav-item {
    margin-bottom: 0.5rem;
}
.dash-nav-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.8rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.dash-nav-link:hover, .dash-nav-link.active {
    background: rgba(255,255,255,0.05);
    color: #fff;
}
.dash-nav-link.active i {
    color: var(--accent);
}
.dash-main {
    flex: 1;
    padding: 3rem 4rem;
}
.dash-header h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #fff;
}
@media (max-width: 992px) {
    .dash-sidebar { display: none; }
    .dash-main { padding: 2rem; }
}
</style>

<aside class="dash-sidebar">
    <div class="dash-sidebar-inner">
        <ul class="dash-nav-list" style="margin-top: 1rem;">
            <li class="dash-nav-item"><a href="index.php?page=user_profile" class="dash-nav-link <?= ($current_page == 'user_profile') ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
            <li class="dash-nav-item"><a href="index.php?page=watchlist" class="dash-nav-link <?= ($current_page == 'watchlist') ? 'active' : '' ?>"><i class="fas fa-bookmark"></i> Watchlist</a></li>
            <li class="dash-nav-item"><a href="index.php?page=my_reviews" class="dash-nav-link <?= ($current_page == 'my_reviews') ? 'active' : '' ?>"><i class="fas fa-star"></i> My Reviews</a></li>
            <li class="dash-nav-item"><a href="index.php?page=my_lists" class="dash-nav-link <?= ($current_page == 'my_lists') ? 'active' : '' ?>"><i class="fas fa-list"></i> My Playlists</a></li>
            <li class="dash-nav-item"><a href="index.php?page=profile" class="dash-nav-link <?= ($current_page == 'profile') ? 'active' : '' ?>"><i class="fas fa-cog"></i> Settings</a></li>
            <li class="dash-nav-item" style="margin-top: 2rem;"><a href="index.php?page=logout" class="dash-nav-link" style="color: #ff5c5c;"><i class="fas fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
</aside>
