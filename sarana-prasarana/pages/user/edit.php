<?php
// FILE: pages/user/edit.php
// Halaman Edit User

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil ID dari URL
$id_user = intval($_GET['id'] ?? 0);

if ($id_user == 0) {
    header('Location: index.php');
    exit;
}

// Ambil data user
$user = fetch_row($koneksi, "SELECT * FROM users WHERE id_user = $id_user");

if (!$user) {
    set_alert('danger', 'User tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// Cek role (hanya admin, atau user yang mengedit dirinya sendiri)
if ($_SESSION['role'] != 'admin' && $_SESSION['id_user'] != $id_user) {
    set_alert('danger', 'Anda tidak memiliki akses!');
    header('Location: ../dashboard.php');
    exit;
}

$error = '';

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = sanitasi($_POST['nama_lengkap'] ?? '');
    $email = sanitasi($_POST['email'] ?? '');
    $status = sanitasi($_POST['status'] ?? 'aktif');
    $role = $_SESSION['role'] == 'admin' ? sanitasi($_POST['role'] ?? '') : $user['role'];
    
    // Validasi input
    $errors = [];
    
    if (!validasi_tidak_kosong($nama_lengkap)) {
        $errors[] = 'Nama lengkap harus diisi';
    }
    
    if (!validasi_email($email)) {
        $errors[] = 'Email tidak valid';
    }
    
    // Cek email sudah ada (selain user ini)
    $cek_email = count_rows($koneksi, "SELECT id_user FROM users WHERE email = '" . escape($koneksi, $email) . "' AND id_user != $id_user");
    if ($cek_email > 0) {
        $errors[] = 'Email sudah terdaftar';
    }
    
    if (empty($errors)) {
        // Escape string
        $nama_escaped = escape($koneksi, $nama_lengkap);
        $email_escaped = escape($koneksi, $email);
        $status_escaped = escape($koneksi, $status);
        $role_escaped = escape($koneksi, $role);
        
        // Query update
        $query = "UPDATE users SET 
                  nama_lengkap = '$nama_escaped',
                  email = '$email_escaped',
                  status = '$status_escaped'";
        
        // Hanya admin yang bisa ubah role
        if ($_SESSION['role'] == 'admin') {
            $query .= ", role = '$role_escaped'";
        }
        
        $query .= " WHERE id_user = $id_user";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'User berhasil diperbarui!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal memperbarui user: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <h1>Edit User</h1>
    <p>Perbarui data user: <?php echo htmlspecialchars($user['username']); ?></p>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div style="max-width: 600px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <h3>Informasi User</h3>
        <form method="POST" action="">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                <small style="color: #666;">Username tidak dapat diubah</small>
            </div>
            
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" 
                       value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="role">Role <span style="color: red;">*</span></label>
                    <select id="role" name="role" required>
                        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="guru" <?php echo $user['role'] == 'guru' ? 'selected' : ''; ?>>Guru</option>
                        <option value="staff" <?php echo $user['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status <span style="color: red;">*</span></label>
                    <select id="status" name="status" required>
                        <option value="aktif" <?php echo $user['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?php echo $user['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled>
                <small style="color: #666;">Role tidak dapat diubah</small>
            </div>
            <?php endif; ?>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
    
    <!-- Form Ubah Password -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3>Ubah Password</h3>
        <form method="POST" action="reset_password.php">
            <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">
            
            <div class="form-group">
                <label for="password_baru">Password Baru <span style="color: red;">*</span></label>
                <input type="password" id="password_baru" name="password_baru" 
                       placeholder="Minimal 6 karakter" required>
            </div>
            
            <div class="form-group">
                <label for="password_konfirmasi">Konfirmasi Password <span style="color: red;">*</span></label>
                <input type="password" id="password_konfirmasi" name="password_konfirmasi" 
                       placeholder="Ulangi password" required>
            </div>
            
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-key"></i> Reset Password
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
?>

<?php
// FILE: pages/user/reset_password.php
// Proses Reset Password

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil ID dari form
$id_user = intval($_POST['id_user'] ?? 0);

// Cek role dan akses
if ($_SESSION['role'] != 'admin' && $_SESSION['id_user'] != $id_user) {
    set_alert('danger', 'Anda tidak memiliki akses!');
    header('Location: index.php');
    exit;
}

if ($id_user == 0) {
    set_alert('danger', 'User tidak valid!');
    header('Location: index.php');
    exit;
}

// Ambil data user
$user = fetch_row($koneksi, "SELECT * FROM users WHERE id_user = $id_user");
if (!$user) {
    set_alert('danger', 'User tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_baru = $_POST['password_baru'] ?? '';
    $password_konfirmasi = $_POST['password_konfirmasi'] ?? '';
    
    // Validasi
    if (empty($password_baru) || empty($password_konfirmasi)) {
        set_alert('danger', 'Password tidak boleh kosong!');
    } elseif (strlen($password_baru) < 6) {
        set_alert('danger', 'Password minimal 6 karakter!');
    } elseif ($password_baru !== $password_konfirmasi) {
        set_alert('danger', 'Password tidak cocok!');
    } else {
        // Hash password baru
        $password_hash = hash_password($password_baru);
        $password_hash_escaped = escape($koneksi, $password_hash);
        
        // Update password
        $query = "UPDATE users SET password = '$password_hash_escaped' WHERE id_user = $id_user";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Password berhasil direset!');
        } else {
            set_alert('danger', 'Gagal reset password: ' . mysqli_error($koneksi));
        }
    }
}

header('Location: edit.php?id=' . $id_user);
exit;
?>

<?php
// FILE: pages/user/hapus.php
// Proses Hapus User

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Cek role (hanya admin)
if ($_SESSION['role'] != 'admin') {
    set_alert('danger', 'Anda tidak memiliki akses!');
    header('Location: ../dashboard.php');
    exit;
}

// Ambil ID dari URL
$id_user = intval($_GET['id'] ?? 0);

if ($id_user == 0) {
    set_alert('danger', 'ID user tidak valid!');
    header('Location: index.php');
    exit;
}

// Cek user tidak menghapus dirinya sendiri
if ($_SESSION['id_user'] == $id_user) {
    set_alert('danger', 'Anda tidak dapat menghapus akun sendiri!');
    header('Location: index.php');
    exit;
}

// Ambil data user
$user = fetch_row($koneksi, "SELECT * FROM users WHERE id_user = $id_user");

if (!$user) {
    set_alert('danger', 'User tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// Hapus dari database
$query = "DELETE FROM users WHERE id_user = $id_user";

if (mysqli_query($koneksi, $query)) {
    set_alert('success', 'User berhasil dihapus!');
} else {
    set_alert('danger', 'Gagal menghapus user: ' . mysqli_error($koneksi));
}

// Redirect ke halaman daftar
header('Location: index.php');
exit;
?>