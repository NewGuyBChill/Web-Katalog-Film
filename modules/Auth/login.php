<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = $conn->query("SELECT id, name, password FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user'] = $row['name'];
            echo "<script>window.location.href='index.php';</script>";
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>
<style>
.split-card-wrapper {
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
.split-card {
    background: var(--nav-bg);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
    width: 100%;
    max-width: 500px;
    display: flex;
    flex-direction: column;
    backdrop-filter: blur(16px);
}
.split-right {
    width: 100%;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
@media (min-width: 768px) {
    .split-right { padding: 4rem; }
}
.auth-input-modern {
    width: 100%;
    padding: 0.85rem 1.2rem;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.2);
    color: white;
    font-size: 0.95rem;
    transition: 0.3s;
    outline: none;
}
.auth-input-modern:focus {
    border-color: var(--accent);
    background: rgba(0,0,0,0.4);
    box-shadow: 0 0 0 3px rgba(0,210,255,0.15);
}
.auth-btn-modern {
    width: 100%;
    padding: 1rem;
    border-radius: 10px;
    background: var(--accent);
    color: #000;
    font-weight: 800;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: 0.3s;
}
.auth-btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,210,255,0.3);
}
.social-btn {
    flex: 1;
    padding: 0.8rem;
    border-radius: 10px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: 0.3s;
}
.social-btn:hover {
    background: rgba(255,255,255,0.1);
    transform: translateY(-2px);
}
</style>

<main class="split-card-wrapper">
    <div class="split-card">
        <!-- Form Panel -->
        <div class="split-right">
            <div style="color: var(--accent); font-size: 1.8rem; margin-bottom: 1.5rem;">
                <i class="fas fa-asterisk"></i>
            </div>
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.8rem; line-height: 1.2;"><?= translateText('welcome_back') ?></h2>
            <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 2.5rem; line-height: 1.5;">Access your tasks, notes, and projects anytime, anywhere - and keep everything flowing in one place.</p>
            
            <?php if($error): ?>
                <p style="color: #ff5c5c; background: rgba(255,92,92,0.1); border: 1px solid rgba(255,92,92,0.2); padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </p>
            <?php endif; ?>
            
            <form method="POST" style="display: flex; flex-direction: column; gap: 1.2rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">Your email</label>
                    <input type="email" name="email" class="auth-input-modern" required placeholder="farazhaidet786@gmail.com">
                </div>
                <div>
                    <label for="password" style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.6rem;">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="auth-input-modern" required placeholder="••••••••••" style="padding-right: 45px;">
                        <i class="fas fa-eye" onclick="togglePassword('password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted); transition: 0.3s;"></i>
                    </div>
                    <div style="text-align: right; margin-top: 0.5rem;">
                        <a href="index.php?page=forgot_password" style="font-size: 0.8rem; color: var(--text-muted); text-decoration: none; font-weight: 600; transition: 0.3s;">Forgot Password?</a>
                    </div>
                </div>
                
                <button type="submit" class="auth-btn-modern">Get Started</button>
            </form>
            
            <!-- Divider -->
            <div style="display: flex; align-items: center; justify-content: center; margin: 2rem 0; gap: 1rem;">
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.1);"></div>
                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">or continue with</span>
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.1);"></div>
            </div>
            
            <!-- Social Auth -->
            <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                <button class="social-btn"><i class="fab fa-behance"></i></button>
                <button class="social-btn"><i class="fab fa-google" style="color: #DB4437;"></i></button>
                <button class="social-btn"><i class="fab fa-facebook-f" style="color: #4267B2;"></i></button>
            </div>
            
            <!-- Footer Nav -->
            <div style="text-align: center; font-size: 0.9rem; color: var(--text-muted); font-weight: 500;">
                Don't have an account? <a href="index.php?page=signup" style="color: var(--accent); font-weight: 700; text-decoration: none; transition: 0.3s;">Sign up</a>
            </div>
        </div>
    </div>
</main>