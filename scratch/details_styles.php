<?php
// Top of details.php will have the PHP block intact.

?>
<style>
/* Reset and hide global header/footer */
#mainNavbar, .site-footer { display: none !important; }

/* Custom Base Styles */
body {
    background-color: #0d0d0d;
    color: #fff;
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
}

/* Tahap 1: Navbar */
.detail-custom-nav {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 100;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 3rem;
    box-sizing: border-box;
    background: linear-gradient(to bottom, rgba(0,0,0,0.8) 0%, transparent 100%);
}
.detail-custom-nav a { color: white; text-decoration: none; }
.nav-left-col { display: flex; align-items: center; gap: 2rem; }
.btn-back { font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: opacity 0.3s; }
.btn-back:hover { opacity: 0.7; }
.nav-brand { font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; }
.nav-brand i { background: white; color: black; padding: 5px; border-radius: 4px; font-size: 0.8rem; }
.nav-center-col { flex: 1; display: flex; justify-content: center; }
.nav-search { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 30px; padding: 0.5rem 1.5rem; display: flex; align-items: center; gap: 10px; width: 400px; backdrop-filter: blur(10px); }
.nav-search input { background: transparent; border: none; color: white; outline: none; width: 100%; font-size: 0.9rem; }
.nav-right-col { display: flex; align-items: center; gap: 1.5rem; }
.btn-signin { background: rgba(255,255,255,0.15); padding: 0.5rem 1.5rem; border-radius: 30px; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
.btn-signin:hover { background: rgba(255,255,255,0.3); }
.nav-profile { width: 35px; height: 35px; background: white; color: black; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.1rem; }

/* Tahap 2: Hero Banner */
.detail-hero-wrapper {
    position: relative;
    width: 100%;
    height: 70vh;
    min-height: 500px;
}
.detail-hero-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
.detail-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 0%, transparent 60%, #0d0d0d 100%);
}
.detail-hero-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 3rem 4rem;
}
.detail-play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    transition: all 0.3s ease;
    z-index: 10;
}
.detail-play-btn:hover { transform: translate(-50%, -50%) scale(1.1); background: white; }
.detail-play-btn i { color: #ff3b3b; font-size: 2rem; margin-left: 5px; }

.hero-bottom-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    z-index: 10;
    position: relative;
    padding-bottom: 2rem;
}
.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 10px rgba(0,0,0,0.8);
}
.hero-year {
    font-size: 1.5rem;
    font-weight: 400;
    opacity: 0.8;
}
.hero-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}
.action-btn {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    backdrop-filter: blur(5px);
    transition: 0.3s;
    text-decoration: none;
}
.action-btn:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }
.imdb-badge { background: #f5c518; color: black; padding: 2px 6px; border-radius: 4px; font-weight: 900; font-size: 0.75rem; letter-spacing: 1px; }

/* Tahap 3: Pembagian Layout Utama */
.detail-grid-container {
    display: flex;
    gap: 3rem;
    padding: 0 4rem 4rem 4rem;
    max-width: 1600px;
    margin: 0 auto;
    position: relative;
    z-index: 20;
    margin-top: -3rem;
}
.detail-col-left {
    flex: 0 0 250px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.detail-poster-img {
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    margin-bottom: 1rem;
}
.vertical-btn {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
    padding: 0.8rem;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: 0.3s;
}
.vertical-btn:hover { background: rgba(255,255,255,0.15); }
.comments-btn {
    width: 100%;
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    padding: 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: 0.3s;
    margin-top: 1rem;
}
.comments-btn:hover { background: rgba(255,255,255,0.2); }

.detail-col-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding-top: 4rem;
}
.movie-title-small { font-size: 1.8rem; font-weight: 700; display: flex; align-items: center; gap: 15px; margin-bottom: 0.5rem; }
.movie-tagline { font-size: 1.1rem; color: #ccc; margin-bottom: 1.5rem; }
.movie-metadata-row { display: flex; align-items: center; gap: 15px; font-size: 0.85rem; color: #aaa; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1.5rem; }
.movie-metadata-row i.fa-star { color: #f5c518; }
.award-badge { border: 1px solid rgba(255,255,255,0.3); padding: 3px 8px; border-radius: 4px; letter-spacing: 1px; font-size: 0.75rem; color: #fff; }

.split-content {
    display: flex;
    gap: 3rem;
}
.details-list, .cast-list { flex: 1; }
.section-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; }
.detail-item { font-size: 0.9rem; margin-bottom: 0.8rem; color: #aaa; }
.detail-item strong { color: #fff; font-weight: 500; display: inline-block; width: 110px; }
.detail-item a { color: #aaa; text-decoration: none; }
.detail-item a:hover { color: white; }

.cast-item { display: flex; align-items: center; gap: 15px; margin-bottom: 1rem; }
.cast-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.cast-info { display: flex; flex-direction: column; }
.cast-name { font-size: 0.95rem; font-weight: 500; color: #fff; }
.cast-role { font-size: 0.8rem; color: #aaa; }
.show-more-text { color: #aaa; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px; margin-top: 1rem; }

.media-column { flex: 0 0 250px; }
.gallery-main { width: 100%; height: 140px; border-radius: 8px; position: relative; margin-bottom: 10px; overflow: hidden; }
.gallery-main img { width: 100%; height: 100%; object-fit: cover; }
.gallery-play { position: absolute; inset: 0; display: flex; justify-content: center; align-items: center; background: rgba(0,0,0,0.4); cursor: pointer; }
.gallery-play i { font-size: 2rem; color: white; opacity: 0.8; transition: 0.3s; }
.gallery-play:hover i { opacity: 1; transform: scale(1.1); }
.gallery-thumbs { display: flex; gap: 10px; margin-bottom: 1rem; }
.gallery-thumbs img { width: calc(33.333% - 6.66px); height: 50px; border-radius: 4px; object-fit: cover; cursor: pointer; }
.soundtrack-btn { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 1rem; text-align: center; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: 0.3s; width: 100%; display: block; color: white; text-decoration: none; }
.soundtrack-btn:hover { background: rgba(255,255,255,0.1); }

.storyline-section { margin-top: 3rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; }
.storyline-text { font-size: 0.95rem; line-height: 1.8; color: #aaa; }

/* Tahap 4: Similar Movies */
.similar-section { padding: 0 4rem 4rem 4rem; max-width: 1600px; margin: 0 auto; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 2rem; padding-top: 3rem; }
.similar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.similar-title { font-size: 1.2rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
.similar-filter { font-size: 0.85rem; color: #aaa; cursor: pointer; }
.similar-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.5rem; }
.similar-card { display: flex; flex-direction: column; gap: 0.8rem; text-decoration: none; }
.similar-card img { width: 100%; aspect-ratio: 2/3; object-fit: cover; border-radius: 8px; transition: transform 0.3s; }
.similar-card:hover img { transform: scale(1.05); }
.similar-info { display: flex; flex-direction: column; gap: 4px; }
.similar-name { color: white; font-size: 0.95rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.similar-sub { color: #aaa; font-size: 0.8rem; }
.similar-rating { display: flex; align-items: center; gap: 5px; color: #aaa; font-size: 0.8rem; margin-top: 5px; }
.similar-rating i { color: #f5c518; }
.similar-rating span { font-size: 0.7rem; letter-spacing: 2px; }
.btn-load-more { width: 100%; padding: 1rem; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); color: white; font-size: 0.9rem; font-weight: 600; border-radius: 8px; margin-top: 3rem; cursor: pointer; transition: 0.3s; }
.btn-load-more:hover { background: rgba(255,255,255,0.05); }

/* Tahap 5: Footer */
.custom-footer { padding: 4rem; background: #080808; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 4rem; }
.footer-row { display: flex; justify-content: space-between; max-width: 1600px; margin: 0 auto; }
.footer-col-1 { flex: 0 0 250px; }
.footer-col-1 h2 { font-size: 1.5rem; font-weight: 800; margin: 0 0 0.5rem 0; }
.footer-col-1 p { color: #555; font-size: 0.8rem; }
.footer-col-2 { flex: 1; display: flex; justify-content: center; gap: 6rem; }
.footer-links-group h4 { font-size: 0.95rem; font-weight: 600; margin-bottom: 1.5rem; }
.footer-links-group a { display: block; color: #888; text-decoration: none; font-size: 0.85rem; margin-bottom: 0.8rem; transition: color 0.3s; }
.footer-links-group a:hover { color: white; }
.footer-col-3 { flex: 0 0 350px; display: flex; flex-direction: column; align-items: flex-end; }
.mailing-box { background: rgba(255,255,255,0.05); border-radius: 8px; padding: 1.5rem; display: flex; align-items: center; gap: 15px; width: 100%; box-sizing: border-box; margin-bottom: 1.5rem; }
.mailing-box img { width: 50px; height: 50px; border-radius: 4px; object-fit: cover; }
.mailing-info h4 { margin: 0 0 0.5rem 0; font-size: 0.9rem; }
.mailing-info input { background: transparent; border: none; border-bottom: 1px solid rgba(255,255,255,0.2); color: white; font-size: 0.85rem; padding: 5px 0; outline: none; width: 150px; }
.btn-support { display: inline-flex; align-items: center; gap: 10px; background: transparent; border: 1px solid rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 30px; color: white; text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; }
.footer-phone { font-size: 0.9rem; color: #aaa; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px; }
.footer-socials { display: flex; gap: 15px; }
.footer-socials a { color: #aaa; font-size: 1.2rem; transition: 0.3s; }
.footer-socials a:hover { color: white; }
</style>
