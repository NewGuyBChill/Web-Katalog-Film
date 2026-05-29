<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='index.php?page=login';</script>";
    exit;
}
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/data.php';

$uid = (int)$_SESSION['user_id'];
$message = isset($_GET['success']) ? translateText('profile_updated') : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update';

    if ($action === 'delete') {
        $del_password = $_POST['delete_password'] ?? '';
        $res = $conn->query("SELECT password FROM users WHERE id = $uid");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if (password_verify($del_password, $row['password'])) {
                $conn->query("DELETE FROM users WHERE id = $uid");
                session_unset();
                session_destroy();
                echo "<script>alert('Akun Anda berhasil dihapus secara permanen.'); window.location.href='index.php';</script>";
                exit;
            } else {
                $error = 'Password salah, gagal menghapus akun.';
            }
        }
    } else {
        $new_username = $conn->real_escape_string(trim($_POST['username'] ?? ''));
        $new_password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($new_username)) {
            $error = 'Username tidak boleh kosong.';
        } else {
            // Cek apakah nama sudah dipakai orang lain
            $check = $conn->query("SELECT id FROM users WHERE name = '$new_username' AND id != $uid");
            if ($check && $check->num_rows > 0) {
                $error = 'Username sudah digunakan oleh pengguna lain.';
            } else {
                $update_query = "UPDATE users SET name = '$new_username'";
                
                if (!empty($new_password)) {
                    if ($new_password !== $confirm_password) {
                        $error = 'Konfirmasi password tidak cocok.';
                    } else {
                        // Jika password diubah, lakukan hashing
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_query .= ", password = '$hashed_password'";
                    }
                }

                // Handle Upload Avatar
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $fileName = $_FILES['avatar']['name'];
                    $fileTmp = $_FILES['avatar']['tmp_name'];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    if (in_array($fileExt, $allowed)) {
                        $uploadDir = __DIR__ . '/../../assets/uploads/avatars/';
                        if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
                        $newFileName = 'avatar_' . $uid . '_' . time() . '.' . $fileExt;
                        if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                            $avatarPath = 'assets/uploads/avatars/' . $newFileName;
                            $update_query .= ", avatar = '$avatarPath'";
                        }
                    } else {
                        $error = "Format foto tidak didukung (Gunakan JPG, PNG, atau WEBP).";
                    }
                }

                if (empty($error)) {
                    $update_query .= " WHERE id = $uid";
                    if ($conn->query($update_query)) {
                        $_SESSION['user'] = $new_username; // Perbarui session
                        // Alihkan dengan parameter GET agar pembaruan nama di Navbar langsung terlihat
                        echo "<script>window.location.href='index.php?page=profile&success=1';</script>";
                        exit;
                    } else {
                        $error = 'Terjadi kesalahan saat memperbarui profil.';
                    }
                }
            }
        }
    }
}

// Ambil data user saat ini dari database
$user_data = null;
$res = $conn->query("SELECT name, avatar FROM users WHERE id = $uid");
if ($res && $res->num_rows > 0) {
    $user_data = $res->fetch_assoc();
}
?>
<main class="container auth-wrapper" style="align-items: flex-start; padding-top: 140px;">
    <div class="auth-card" style="margin: 0 auto;">
        <h2><i class="fas fa-user-circle" style="color: var(--accent); margin-right: 10px;"></i> <?= translateText('profile_settings') ?></h2>
        <p><?= translateText('profile_update_desc') ?></p>
        
        <?php if(!empty($message)): ?>
            <div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid #2ed573; padding: 10px; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if(!empty($error)): ?>
            <div style="background: rgba(255, 59, 59, 0.1); color: #ff3b3b; border: 1px solid #ff3b3b; padding: 10px; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?page=profile" method="POST" class="auth-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <div class="auth-input-group">
                <label>Foto Profil (Opsional)</label>
                <input type="file" name="avatar" class="auth-input" accept=".jpg,.jpeg,.png,.webp" style="padding: 10px; background: rgba(255,255,255,0.02);">
            </div>

            <div class="auth-input-group">
                <label for="username"><?= translateText('new_username') ?></label>
                <input type="text" name="username" id="username" class="auth-input" value="<?= htmlspecialchars($user_data['name'] ?? $_SESSION['user']) ?>" required>
            </div>
            
            <div class="auth-input-group" style="margin-top: 10px;">
                <label for="password"><?= translateText('new_password') ?> <span style="color: var(--text-muted); font-size: 0.8rem;"><?= translateText('leave_blank') ?></span></label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="auth-input" placeholder="<?= translateText('enter_new_password') ?>" style="padding-right: 40px;">
                    <i class="fas fa-eye" onclick="togglePassword('password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted); transition: 0.3s;"></i>
                </div>
            </div>
            
            <div class="auth-input-group">
                <label for="confirm_password"><?= translateText('confirm_new_password') ?></label>
                <div style="position: relative;">
                    <input type="password" name="confirm_password" id="confirm_password" class="auth-input" placeholder="<?= translateText('retype_new_password') ?>" style="padding-right: 40px;">
                    <i class="fas fa-eye" onclick="togglePassword('confirm_password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted); transition: 0.3s;"></i>
                </div>
            </div>
            
            <button type="submit" class="auth-btn" style="margin-top: 1.5rem;"><i class="fas fa-save"></i> <?= translateText('update_profile') ?></button>
        </form>

        <!-- Bagian Hapus Akun -->
        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); width: 100%; text-align: left;">
            <h3 style="color: #ff3b3b; margin-bottom: 0.5rem; font-size: 1.1rem;"><i class="fas fa-exclamation-triangle"></i> <?= translateText('delete_account_permanent') ?></h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;"><?= translateText('delete_account_desc') ?></p>
            
            <form action="index.php?page=profile" method="POST" class="auth-form" onsubmit="return confirm('<?= translateText('delete_account_confirm') ?>');">
                <input type="hidden" name="action" value="delete">
                <div class="auth-input-group">
                    <label for="delete_password" style="color: #ff3b3b;"><?= translateText('confirm_password') ?></label>
                    <div style="position: relative;">
                        <input type="password" name="delete_password" id="delete_password" class="auth-input" placeholder="<?= translateText('enter_password_verify') ?>" required style="border-color: rgba(255,59,59,0.3); padding-right: 40px;">
                        <i class="fas fa-eye" onclick="togglePassword('delete_password', this)" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted); transition: 0.3s;"></i>
                    </div>
                </div>
                <button type="submit" class="auth-btn" style="background: rgba(255, 59, 59, 0.1); color: #ff3b3b; border: 1px solid #ff3b3b; box-shadow: none; margin-top: 1rem; width: 100%;"><?= translateText('delete_account_btn') ?></button>
            </form>
        </div>
    </div>
</main>