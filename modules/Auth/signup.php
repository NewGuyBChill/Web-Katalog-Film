<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email sudah digunakan!";
    } else {
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_pw')");
        
        session_regenerate_id(true);
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user'] = $name; 
        echo "<script>window.location.href='index.php';</script>";
        exit;
    }
}
?>
<main style="padding-top: 40px;">
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2><?= translateText('create_account') ?></h2>
            <p><?= translateText('signup_desc') ?></p>
            <?php if($error): ?><p style="color: #ff3b3b; margin-bottom: 1rem; font-size: 0.9rem;"><?= $error ?></p><?php endif; ?>
            <form method="POST" class="auth-form">
                <div class="auth-input-group">
                    <label><?= translateText('full_name') ?></label>
                    <input type="text" name="name" class="auth-input" required placeholder="<?= translateText('enter_name') ?>">
                </div>
                <div class="auth-input-group">
                    <label><?= translateText('email_address') ?></label>
                    <input type="email" name="email" class="auth-input" required placeholder="<?= translateText('enter_email') ?>">
                </div>
                <div class="auth-input-group">
                    <label for="password"><?= translateText('password') ?></label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="auth-input" required minlength="6" placeholder="<?= translateText('create_password') ?>" style="padding-right: 40px;">
                        <i class="fas fa-eye" onclick="togglePassword('password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted); transition: 0.3s;"></i>
                    </div>
                </div>
                <button type="submit" class="auth-btn"><?= translateText('signup_btn') ?></button>
            </form>
            <div class="auth-links">
                <?= translateText('already_account') ?> <a href="index.php?page=login"><?= translateText('login_here') ?></a>
            </div>
        </div>
    </div>
</main>