<?php
// FILE: pages/user/tambah.php
// Halaman Tambah User

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

$error = '';

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi input
    $username = sanitasi($_POST['username'] ?? '');
    $email = sanitasi($_POST['email'] ?? '');
    $nama_lengkap = sanitasi($_POST['nama_lengkap'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_konfirmasi = $_POST['password_konfirmasi'] ?? '';
    $role = sanitasi($_POST['role'] ?? '');
    $status = sanitasi($_POST['status'] ?? 'aktif');
    
    // Validasi input
    $errors = [];
    
    if (!validasi_tidak_kosong($username)) {
        $errors[] = 'Username harus diisi';
    }
    
    if (!validasi_email($email)) {
        $errors[] = 'Email tidak valid';
    }
    
    if (!validasi_tidak_kosong($nama_lengkap)) {
        $errors[] = 'Nama lengkap harus diisi';
    }
    
    if (!validasi_tidak_kosong($password)) {
        $errors[] = 'Password harus diisi';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter';
    }
    
    if ($password !== $password_konfirmasi) {
        $errors[] = 'Password dan konfirmasi password tidak cocok';
    }
    
    if (empty($role)) {
        $errors[] = 'Role harus dipilih';
    }
    
    // Cek username sudah ada
    if (empty($errors)) {
        $cek_username = count_rows($koneksi, "SELECT id_user FROM users WHERE username = '" . escape($koneksi, $username) . "'");
        if ($cek_username > 0) {
            $errors[] = 'Username sudah terdaftar';
        }
    }
    
    // Cek email sudah ada
    if (empty($errors)) {
        $cek_email = count_rows($koneksi, "SELECT id_user FROM users WHERE email = '" . escape($koneksi, $email) . "'");
        if ($cek_email > 0) {
            $errors[] = 'Email sudah terdaftar';
        }
    }
    
    if (empty($errors)) {
        // Hash password
        $password_hash = hash_password($password);
        
        // Escape string
        $username_escaped = escape($koneksi, $username);
        $email_escaped = escape($koneksi, $email);
        $nama_escaped = escape($koneksi, $nama_lengkap);
        $role_escaped = escape($koneksi, $role);
        $status_escaped = escape($koneksi, $status);
        
        // Query insert
        $query = "INSERT INTO users 
                  (username, password, email, nama_lengkap, role, status)
                  VALUES 
                  ('$username_escaped', '$password_hash', '$email_escaped', '$nama_escaped', 
                   '$role_escaped', '$status_escaped')";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'User berhasil ditambahkan!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal menambahkan user: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <h1>Tambah User</h1>
    <p>Buat akun pengguna baru</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div style="max-width: 600px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <form method="POST" action="">
            
            <div class="form-group">
                <label for="username">Username <span style="color: red;">*</span></label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       placeholder="username" required>
            </div>
            
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" 
                       value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>"
                       placeholder="Nama lengkap" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       placeholder="email@example.com" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="role">Role <span style="color: red;">*</span></label>
                    <select id="role" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" <?php echo isset($_POST['role']) && $_POST['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="guru" <?php echo isset($_POST['role']) && $_POST['role'] == 'guru' ? 'selected' : ''; ?>>Guru</option>
                        <option value="staff" <?php echo isset($_POST['role']) && $_POST['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status <span style="color: red;">*</span></label>
                    <select id="status" name="status" required>
                        <option value="aktif" <?php echo !isset($_POST['status']) || $_POST['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="nonaktif" <?php echo isset($_POST['status']) && $_POST['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="password">Password <span style="color: red;">*</span></label>
                    <input type="password" id="password" name="password" 
                           placeholder="Minimal 6 karakter" required>
                </div>
                
                <div class="form-group">
                    <label for="password_konfirmasi">Konfirmasi Password <span style="color: red;">*</span></label>
                    <input type="password" id="password_konfirmasi" name="password_konfirmasi" 
                           placeholder="Ulangi password" required>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>