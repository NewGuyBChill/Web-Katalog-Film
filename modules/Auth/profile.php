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
$res = $conn->query("SELECT name, email, avatar FROM users WHERE id = $uid");
if ($res && $res->num_rows > 0) {
    $user_data = $res->fetch_assoc();
}

$user_data = array_merge([
    'first_name' => '', 'last_name' => '', 'country' => 'Indonesia', 'bio' => '', 'gender' => 'Male',
    'dob_month' => 'May', 'dob_day' => '27', 'dob_year' => '2000', 'timezone' => 'Automatically',
    'twitter' => '', 'facebook' => '', 'discord' => '', 'youtube' => '', 'twitch' => '', 'instagram' => ''
], $user_data ?? []);

if (!empty($user_data['name'])) {
    $parts = explode(' ', $user_data['name'], 2);
    $user_data['first_name'] = $parts[0];
    $user_data['last_name'] = $parts[1] ?? '';
}
?>

<style>
/* Dashboard Tab Specific */
.dash-tab {
    padding: 1rem 0;
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    cursor: pointer;
    position: relative;
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.dash-tab.active {
    color: #fff;
}
.dash-tab.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--accent);
    border-radius: 3px 3px 0 0;
}

/* Form & Grid */
.dash-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
}
.dash-form-group {
    margin-bottom: 1.5rem;
}
.dash-form-group label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}
.dash-input {
    width: 100%;
    background: rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.05);
    color: #fff;
    padding: 0.9rem 1.2rem;
    border-radius: 8px;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.3s;
}
.dash-input:focus {
    border-color: var(--accent);
}
.dash-select {
    appearance: none;
    background: rgba(0,0,0,0.2) url("data:image/svg+xml;charset=UTF-8,%3csvg fill='%23ffffff' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e") no-repeat right 10px center;
}
.dash-row {
    display: flex;
    gap: 1.5rem;
}
.dash-row .dash-form-group {
    flex: 1;
}

/* Specific elements */
.social-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.social-input-wrap i {
    position: absolute;
    left: 1.2rem;
    color: var(--text-muted);
    font-size: 1.1rem;
}
.social-input-wrap .dash-input {
    padding-left: 3.2rem;
}

.dash-avatar-sec {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
}
.dash-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    background: var(--accent);
    box-shadow: 0 5px 15px rgba(0,0,0,0.5);
}
.dash-username {
    font-size: 1.2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #fff;
}
.dash-username i {
    font-size: 0.9rem;
    color: var(--text-muted);
    cursor: pointer;
    transition: color 0.3s;
}
.dash-username i:hover { color: var(--accent); }

.dash-btn-outline {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    padding: 0.6rem 1.2rem;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: 0.3s;
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.05);
}
.dash-btn-primary {
    background: var(--accent);
    color: #000;
    border: none;
    padding: 0.8rem 2.5rem;
    border-radius: 6px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 1rem;
    font-size: 1rem;
}
.dash-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 210, 255, 0.4);
}
</style>

<div class="dashboard-container">
    <?php require_once __DIR__ . '/../../includes/account_sidebar.php'; ?>

    <!-- Main Content Kanan -->
    <main class="dash-main">
        <div class="dash-header">
            <h1>My Account</h1>
        </div>

        <?php if(!empty($message)): ?>
            <div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid #2ed573; padding: 15px; border-radius: 8px; margin-bottom: 2rem;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error)): ?>
            <div style="background: rgba(255, 59, 59, 0.1); color: #ff3b3b; border: 1px solid #ff3b3b; padding: 15px; border-radius: 8px; margin-bottom: 2rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?page=profile" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            
            <div class="dash-grid">
                <!-- Kolom Kiri: Form Utama -->
                <div class="dash-left-col">
                    <div class="dash-avatar-sec">
                        <?php if(!empty($user_data['avatar'])): ?>
                            <img src="<?= htmlspecialchars($user_data['avatar']) ?>" alt="Avatar" class="dash-avatar">
                        <?php else: ?>
                            <div class="dash-avatar" style="display:flex; align-items:center; justify-content:center; font-size: 2.5rem; font-weight:bold; color:#000;">
                                <?= strtoupper(substr($user_data['name'] ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="dash-username">
                            <?= htmlspecialchars($user_data['name'] ?? 'Username') ?> 
                            <label for="avatarUpload" style="cursor:pointer; margin:0;"><i class="fas fa-pen"></i></label>
                            <input type="file" id="avatarUpload" name="avatar" accept=".jpg,.jpeg,.png,.webp" style="display:none;">
                        </div>
                    </div>

                    <div class="dash-row">
                        <div class="dash-form-group">
                            <label>First Name</label>
                            <input type="text" id="firstNameInput" class="dash-input" value="<?= htmlspecialchars($user_data['first_name']) ?>" required>
                        </div>
                        <div class="dash-form-group">
                            <label>Last Name</label>
                            <input type="text" id="lastNameInput" class="dash-input" value="<?= htmlspecialchars($user_data['last_name']) ?>">
                        </div>
                        <!-- Hidden input to keep existing logic working -->
                    <input type="hidden" name="username" id="realUsername" value="<?= htmlspecialchars($user_data['name'] ?? '') ?>">
                    </div>

                    <div class="dash-form-group">
                        <label>Country</label>
                    <select name="country" class="dash-input dash-select">
                            <option value="Serbia" <?= ($user_data['country'] == 'Serbia') ? 'selected' : '' ?>>Serbia</option>
                            <option value="Indonesia" <?= ($user_data['country'] == 'Indonesia') ? 'selected' : '' ?>>Indonesia</option>
                            <option value="USA" <?= ($user_data['country'] == 'USA') ? 'selected' : '' ?>>United States</option>
                        </select>
                    </div>

                    <div class="dash-form-group">
                        <label>Bio</label>
                        <textarea name="bio" class="dash-input" rows="4" placeholder="A passionate creator..."><?= htmlspecialchars($user_data['bio']) ?></textarea>
                    </div>

                    <div class="dash-form-group">
                        <label>Gender</label>
                        <select name="gender" class="dash-input dash-select">
                            <option value="Male" <?= ($user_data['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($user_data['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= ($user_data['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <div class="dash-form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="dash-input" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" readonly style="opacity: 0.7;">
                    </div>
                    
                    <div style="display:flex; gap:1rem; margin-bottom:1.5rem;">
                        <button type="button" class="dash-btn-outline" onclick="alert('Change email modal here')">Change Email</button>
                        <button type="button" class="dash-btn-outline" onclick="alert('Set mobile number modal here')">Set mobile number</button>
                    </div>

                    <div class="dash-form-group">
                        <label>Birthday</label>
                        <div class="dash-row" style="gap:1rem;">
                        <select name="dob_month" class="dash-input dash-select">
                                <option value="May">May</option>
                                <option value="June">June</option>
                            </select>
                        <select name="dob_day" class="dash-input dash-select">
                                <option value="27">27</option>
                                <option value="28">28</option>
                            </select>
                        <select name="dob_year" class="dash-input dash-select">
                                <option value="2000">2000</option>
                                <option value="2001">2001</option>
                            </select>
                        </div>
                    </div>

                    <div class="dash-form-group">
                        <label>Update Timezone</label>
                    <select name="timezone" class="dash-input dash-select">
                            <option value="Automatically">Automatically</option>
                        </select>
                    </div>

                    <!-- Change Password (dipindahkan ke sini agar fungsinya tetap jalan) -->
                    <div class="dash-row" style="margin-top: 2rem;">
                        <div class="dash-form-group">
                            <label>New Password (Opsional)</label>
                            <input type="password" name="password" id="password" class="dash-input" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="dash-form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="dash-input" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <button type="submit" class="dash-btn-primary" onclick="
                        const first = document.getElementById('firstNameInput').value;
                        const last = document.getElementById('lastNameInput').value;
                        document.getElementById('realUsername').value = (first + ' ' + last).trim();
                    ">Update</button>
                </div>

                <!-- Kolom Kanan: Social Profiles -->
                <div class="dash-right-col">
                    <h3 style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; font-weight: normal; text-transform: uppercase; letter-spacing: 0.5px;">Social Profiles</h3>
                    
                    <div class="dash-form-group social-input-wrap">
                        <i class="fab fa-twitter"></i>
                        <input type="text" name="twitter" class="dash-input" value="<?= htmlspecialchars($user_data['twitter']) ?>" placeholder="https://twitter.com/...">
                    </div>
                    <div class="dash-form-group social-input-wrap">
                        <i class="fab fa-facebook-f"></i>
                        <input type="text" name="facebook" class="dash-input" value="<?= htmlspecialchars($user_data['facebook']) ?>">
                    </div>
                    <div class="dash-form-group social-input-wrap">
                        <i class="fab fa-discord"></i>
                        <input type="text" name="discord" class="dash-input" value="<?= htmlspecialchars($user_data['discord']) ?>">
                    </div>
                    <div class="dash-form-group social-input-wrap">
                        <i class="fab fa-youtube"></i>
                        <input type="text" name="youtube" class="dash-input" value="<?= htmlspecialchars($user_data['youtube']) ?>">
                    </div>
                    <div class="dash-form-group social-input-wrap">
                        <i class="fab fa-twitch"></i>
                        <input type="text" name="twitch" class="dash-input" value="<?= htmlspecialchars($user_data['twitch']) ?>">
                    </div>
                    <div class="dash-form-group social-input-wrap">
                        <i class="fab fa-instagram"></i>
                        <input type="text" name="instagram" class="dash-input" value="<?= htmlspecialchars($user_data['instagram']) ?>">
                    </div>
                </div>
            </div>
        </form>

        <!-- Delete Account Section -->
        <div style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); max-width: 65%;">
            <h3 style="color: #ff3b3b; margin-bottom: 0.5rem; font-size: 1.1rem;"><i class="fas fa-exclamation-triangle"></i> <?= translateText('delete_account_permanent') ?></h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;"><?= translateText('delete_account_desc') ?></p>
            
            <form action="index.php?page=profile" method="POST" onsubmit="return confirm('<?= translateText('delete_account_confirm') ?>');">
                <input type="hidden" name="action" value="delete">
                <div class="dash-form-group">
                    <label style="color: #ff3b3b;"><?= translateText('confirm_password') ?></label>
                    <input type="password" name="delete_password" class="dash-input" placeholder="<?= translateText('enter_password_verify') ?>" required style="border-color: rgba(255,59,59,0.3);">
                </div>
                <button type="submit" class="dash-btn-outline" style="border-color: #ff3b3b; color: #ff3b3b;"><?= translateText('delete_account_btn') ?></button>
            </form>
        </div>
    </main>
</div>