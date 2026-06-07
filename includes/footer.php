    <!-- Footer Utama -->
    <style>
    .custom-footer { padding: 4rem; background: #080808; border-top: 1px solid rgba(255,255,255,0.05); margin-top: 4rem; font-family: 'Inter', sans-serif; color: white; }
    .footer-row { display: flex; justify-content: space-between; max-width: 1600px; margin: 0 auto; gap: 2rem; flex-wrap: wrap; }
    .footer-col-1 { flex: 0 0 250px; }
    .footer-col-1 h2 { font-size: 1.5rem; font-weight: 800; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 10px; color: white; }
    .footer-col-1 p { color: #555; font-size: 0.8rem; margin-top: 1rem; }
    .footer-col-2 { flex: 1; display: flex; justify-content: center; gap: 6rem; flex-wrap: wrap; }
    .footer-links-group h4 { font-size: 0.95rem; font-weight: 600; margin-bottom: 1.5rem; color: white; }
    .footer-links-group a { display: block; color: #888; text-decoration: none; font-size: 0.85rem; margin-bottom: 0.8rem; transition: color 0.3s; }
    .footer-links-group a:hover { color: white; }
    .footer-col-3 { flex: 0 0 350px; display: flex; flex-direction: column; align-items: flex-end; }
    .mailing-box { background: rgba(255,255,255,0.05); border-radius: 8px; padding: 1.5rem; display: flex; align-items: center; gap: 15px; width: 100%; box-sizing: border-box; margin-bottom: 1.5rem; }
    .mailing-box img { width: 50px; height: 50px; border-radius: 4px; object-fit: cover; }
    .mailing-info { flex: 1; }
    .mailing-info h4 { margin: 0 0 0.5rem 0; font-size: 0.9rem; color: white; }
    .mailing-input-wrapper { display: flex; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 5px; }
    .mailing-input-wrapper input { background: transparent; border: none; color: white; font-size: 0.85rem; outline: none; width: 100%; }
    .mailing-input-wrapper i { color: #aaa; cursor: pointer; }
    .btn-support { display: inline-flex; align-items: center; gap: 10px; background: transparent; border: 1px solid rgba(255,255,255,0.2); padding: 0.5rem 1.2rem; border-radius: 30px; color: white; text-decoration: none; font-size: 0.85rem; margin-bottom: 1rem; }
    .footer-phone { font-size: 0.9rem; color: #aaa; margin-bottom: 1rem; display: flex; align-items: center; gap: 10px; }
    .footer-socials { display: flex; gap: 15px; }
    .footer-socials a { color: #aaa; font-size: 1.2rem; transition: 0.3s; background: rgba(255,255,255,0.1); width: 35px; height: 35px; display: flex; justify-content: center; align-items: center; border-radius: 50%; text-decoration: none; }
    .footer-socials a:hover { color: white; background: rgba(255,255,255,0.2); }
    
    @media (max-width: 900px) {
        .footer-row { flex-direction: column; align-items: center; text-align: center; }
        .footer-col-3 { align-items: center; }
        .mailing-box { flex-direction: column; text-align: center; }
    }
    </style>
    
    <footer class="site-footer custom-footer">
        <div class="footer-row">
            <!-- Kiri -->
            <div class="footer-col-1">
                <h2> CelesView</h2>
                <p>&copy; <?= date('Y') ?> CelesView Inc. All rights reserved.</p>
            </div>
            
            <!-- Tengah -->
            <div class="footer-col-2">
                <div class="footer-links-group">
                    <h4>About Us</h4>
                    <a href="#">Contact Us</a>
                    <a href="#">Careers</a>
                    <a href="#">Blog</a>
                    <a href="#">FAQ</a>
                </div>
                <div class="footer-links-group">
                    <h4>For Users</h4>
                    <a href="index.php?page=login">Sign Up</a>
                    <a href="#">Support Center</a>
                    <a href="#">Request Movie</a>
                    <a href="#">Mobile Phone</a>
                </div>
            </div>
            
            <!-- Kanan -->
            <div class="footer-col-3">
                <div class="mailing-box">
                    <img src="https://image.tmdb.org/t/p/w200/8tZYtuWezp8JbcsvHYO0O46tFbo.jpg" alt="Thumb">
                    <div class="mailing-info">
                        <h4>Join Our Mailing List</h4>
                        <div class="mailing-input-wrapper">
                            <input type="email" placeholder="Enter Your Mail">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn-support"><i class="fas fa-headset"></i> Support</a>
                <div class="footer-phone"><i class="fas fa-phone-alt"></i> +1 (800) 595-2611</div>
                <div class="footer-socials">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-google-plus-g"></i></a>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="assets/js/script.js"></script>
</body>
</html>