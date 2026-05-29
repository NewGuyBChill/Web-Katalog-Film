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
<main style="padding-top: 40px;">
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2><?= translateText('welcome_back') ?></h2>
            <p><?= translateText('login_desc') ?></p>
            <?php if($error): ?><p style="color: #ff3b3b; margin-bottom: 1rem; font-size: 0.9rem;"><?= $error ?></p><?php endif; ?>
            <form method="POST" class="auth-form">
                <div class="auth-input-group">
                    <label><?= translateText('email_address') ?></label>
                    <input type="email" name="email" class="auth-input" required placeholder="<?= translateText('enter_email') ?>">
                </div>
                <div class="auth-input-group">
                    <label for="password"><?= translateText('password') ?></label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="auth-input" required placeholder="<?= translateText('enter_password') ?>" style="padding-right: 40px;">
                        <i class="fas fa-eye" onclick="togglePassword('password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted); transition: 0.3s;"></i>
                    </div>
                </div>
                <button type="submit" class="auth-btn"><?= translateText('login_btn') ?></button>
            </form>
            <div class="auth-links" style="margin-top: 1rem; margin-bottom: 0;">
                <a href="index.php?page=forgot_password" style="font-weight: 500;">Lupa Kata Sandi?</a>
            </div>
            <div class="auth-links">
                <?= translateText('no_account') ?> <a href="index.php?page=signup"><?= translateText('signup_here') ?></a>
            </div>
        </div>
    </div>
</main>