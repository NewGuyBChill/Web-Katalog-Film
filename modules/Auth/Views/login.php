<style>
.auth-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 150px);
    padding: 20px;
}
.auth-card {
    background-color: var(--card-bg, #111);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 40px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    text-align: center;
    margin: 0 auto;
}
.auth-card h2 {
    color: var(--text-main, #fff);
    margin-bottom: 10px;
    font-size: 24px;
}
.auth-card p {
    color: var(--text-muted, #a0a0a0);
    margin-bottom: 25px;
    font-size: 14px;
}
.auth-input-group {
    margin-bottom: 20px;
    text-align: left;
}
.auth-input-group label {
    display: block;
    color: var(--text-main, #fff);
    margin-bottom: 8px;
    font-size: 14px;
}
.auth-input {
    width: 100%;
    padding: 12px 15px;
    background-color: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: var(--text-main, #fff);
    font-size: 14px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}
.auth-input:focus {
    outline: none;
    border-color: var(--accent, #00d2ff);
    background-color: rgba(255, 255, 255, 0.1);
}
.auth-btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, var(--accent, #00d2ff), #0072ff);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin-top: 10px;
}
.auth-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 210, 255, 0.3);
}
.auth-links {
    margin-top: 25px;
    font-size: 14px;
    color: var(--text-muted, #a0a0a0);
}
.auth-links a {
    color: var(--accent, #00d2ff);
    text-decoration: none;
    font-weight: 600;
}
.auth-links a:hover {
    text-decoration: underline;
}
</style>

<main style="padding-top: 40px;">
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2><?= translateText('welcome_back') ?></h2>
            <p><?= translateText('login_desc') ?></p>
            
            <?php if ($error = \App\Core\Session::getFlash('error')): ?>
                <p style="color: #ff3b3b; margin-bottom: 1rem; font-size: 0.9rem; text-align: center;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            
            <form action="index.php?page=login" method="POST" class="auth-form">
                <?= csrf_field() ?>
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
            <div class="auth-links">
                <?= translateText('no_account') ?> <a href="index.php?page=register"><?= translateText('signup_here') ?></a>
            </div>
        </div>
    </div>
</main>
