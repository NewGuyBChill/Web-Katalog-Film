<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

// Auto-create tabel OTP jika belum ada di database
$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL,
    otp VARCHAR(10) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$step = isset($_GET['step']) ? $_GET['step'] : '1';
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_otp'])) {
        $email = $conn->real_escape_string(trim($_POST['email']));
        $check = $conn->query("SELECT id, name FROM users WHERE email = '$email'");
        if ($check && $check->num_rows > 0) {
            $user = $check->fetch_assoc();
            $otp = sprintf("%06d", mt_rand(100000, 999999));
            
            // Simpan ke database
            $conn->query("DELETE FROM password_resets WHERE email = '$email'");
            $conn->query("INSERT INTO password_resets (email, otp) VALUES ('$email', '$otp')");
            
            // Mengirim email sungguhan (via native mail server PHP)
            $to = $email;
            $subject = "Kode OTP Reset Password - Kinema";
            $message = "Halo " . $user['name'] . ",\n\nKode OTP Anda untuk mengatur ulang kata sandi adalah: " . $otp . "\n\nJika Anda tidak meminta ini, abaikan pesan ini.";
            $header = "From: noreply@celesview.com";
            @mail($to, $subject, $message, $header);
            
            // Menampilkan kode OTP di layar untuk keperluan testing di komputer lokal
            $success = "Kode OTP telah dikirim ke email Anda. (MODE DEMO: Kode Anda adalah <b>$otp</b>)";
            $_SESSION['reset_email'] = $email;
            $step = '2';
        } else {
            $error = "Alamat email tidak terdaftar di sistem kami.";
        }
    } elseif (isset($_POST['verify_otp'])) {
        $otp = $conn->real_escape_string(trim($_POST['otp']));
        $new_password = $_POST['new_password'];
        $email = $_SESSION['reset_email'] ?? '';
        
        if (empty($email)) {
            $error = "Sesi telah habis. Silakan ulangi dari awal.";
            $step = '1';
        } else {
            // Cek OTP dengan batas waktu berlaku 15 menit
            $checkOTP = $conn->query("SELECT id FROM password_resets WHERE email = '$email' AND otp = '$otp' AND created_at >= NOW() - INTERVAL 15 MINUTE");
            if ($checkOTP && $checkOTP->num_rows > 0) {
                $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password = '$hashed_pw' WHERE email = '$email'");
                
                $conn->query("DELETE FROM password_resets WHERE email = '$email'");
                unset($_SESSION['reset_email']);
                echo "<script>alert('Kata sandi berhasil diatur ulang! Silakan login.'); window.location.href='index.php?page=login';</script>";
                exit;
            } else {
                $error = "Kode OTP salah atau sudah kedaluwarsa.";
                $step = '2';
            }
        }
    }
}
?>
<main style="padding-top: 100px; min-height: 80vh;" class="container auth-wrapper">
    <div class="auth-card" style="margin: 0 auto;">
        <h2><i class="fas fa-lock" style="color: var(--accent); margin-right: 10px;"></i> Lupa Kata Sandi</h2>
        
        <?php if($error): ?><div style="background: rgba(255, 59, 59, 0.1); color: #ff3b3b; border: 1px solid #ff3b3b; padding: 10px; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if($success): ?><div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid #2ed573; padding: 10px; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;"><?= $success ?></div><?php endif; ?>

        <?php if ($step === '1'): ?>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 2rem;">Masukkan alamat email yang terdaftar untuk menerima kode OTP pemulihan kata sandi.</p>
            <form method="POST" class="auth-form">
                <input type="hidden" name="request_otp" value="1">
                <div class="auth-input-group"><label>Alamat Email</label><input type="email" name="email" class="auth-input" required placeholder="email@contoh.com"></div>
                <button type="submit" class="auth-btn" style="margin-top: 1rem;"><i class="fas fa-paper-plane"></i> Kirim Kode OTP</button>
            </form>
            <div class="auth-links" style="margin-top: 1.5rem;">Ingat kata sandi Anda? <a href="index.php?page=login">Login di sini</a></div>
            
        <?php elseif ($step === '2'): ?>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 2rem;">Masukkan kode 6 digit yang telah kami kirimkan ke <strong><?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?></strong>.</p>
            <form method="POST" class="auth-form">
                <input type="hidden" name="verify_otp" value="1">
                <div class="auth-input-group"><label>Kode OTP</label><input type="text" name="otp" class="auth-input" required placeholder="123456" maxlength="6" style="letter-spacing: 5px; font-size: 1.2rem; text-align: center; font-weight: bold;"></div>
                <div class="auth-input-group" style="margin-top: 10px;"><label>Kata Sandi Baru</label>
                    <div style="position: relative;"><input type="password" name="new_password" id="new_password" class="auth-input" required minlength="6" placeholder="Buat kata sandi baru" style="padding-right: 40px;"><i class="fas fa-eye" onclick="togglePassword('new_password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted); transition: 0.3s;"></i></div>
                </div>
                <button type="submit" class="auth-btn" style="margin-top: 1rem;"><i class="fas fa-check-circle"></i> Simpan Kata Sandi Baru</button>
            </form>
            <div class="auth-links" style="margin-top: 1.5rem;"><a href="index.php?page=forgot_password"><i class="fas fa-arrow-left"></i> Kembali masukkan email</a></div>
        <?php endif; ?>
    </div>
</main>