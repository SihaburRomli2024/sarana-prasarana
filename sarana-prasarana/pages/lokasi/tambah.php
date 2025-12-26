<?php
// FILE: pages/lokasi/tambah.php
// Halaman Tambah Lokasi

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login & role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    set_alert('danger', 'Anda tidak memiliki akses!');
    header('Location: ../dashboard.php');
    exit;
}

$error = '';

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lokasi = sanitasi($_POST['nama_lokasi'] ?? '');
    $tipe_ruangan = sanitasi($_POST['tipe_ruangan'] ?? '');
    $keterangan = sanitasi($_POST['keterangan'] ?? '');
    
    // Validasi input
    $errors = [];
    
    if (!validasi_tidak_kosong($nama_lokasi)) {
        $errors[] = 'Nama lokasi harus diisi';
    }
    
    if (empty($errors)) {
        // Escape string
        $nama_escaped = escape($koneksi, $nama_lokasi);
        $tipe_escaped = escape($koneksi, $tipe_ruangan);
        $keterangan_escaped = escape($koneksi, $keterangan);
        
        // Query insert
        $query = "INSERT INTO lokasi (nama_lokasi, tipe_ruangan, keterangan)
                  VALUES ('$nama_escaped', '$tipe_escaped', '$keterangan_escaped')";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Lokasi berhasil ditambahkan!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal menambahkan lokasi: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <h1>Tambah Lokasi/Ruangan</h1>
    <p>Buat lokasi ruangan baru</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div style="max-width: 600px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <form method="POST" action="">
            
            <div class="form-group">
                <label for="nama_lokasi">Nama Lokasi <span style="color: red;">*</span></label>
                <input type="text" id="nama_lokasi" name="nama_lokasi" 
                       value="<?php echo isset($_POST['nama_lokasi']) ? htmlspecialchars($_POST['nama_lokasi']) : ''; ?>"
                       placeholder="Contoh: Ruang Kelas 7A, Lab IPA" required>
            </div>
            
            <div class="form-group">
                <label for="tipe_ruangan">Tipe Ruangan</label>
                <input type="text" id="tipe_ruangan" name="tipe_ruangan" 
                       value="<?php echo isset($_POST['tipe_ruangan']) ? htmlspecialchars($_POST['tipe_ruangan']) : ''; ?>"
                       placeholder="Contoh: Kelas, Laboratorium, Kantor">
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" 
                          placeholder="Deskripsi lokasi (opsional)">
<?php echo isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : ''; ?></textarea>
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
?>

<?php
// FILE: pages/lokasi/edit.php
// Halaman Edit Lokasi

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login & role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    set_alert('danger', 'Anda tidak memiliki akses!');
    header('Location: ../dashboard.php');
    exit;
}

// Ambil ID dari URL
$id_lokasi = intval($_GET['id'] ?? 0);

if ($id_lokasi == 0) {
    header('Location: index.php');
    exit;
}

// Ambil data lokasi
$lokasi = fetch_row($koneksi, "SELECT * FROM lokasi WHERE id_lokasi = $id_lokasi");

if (!$lokasi) {
    set_alert('danger', 'Lokasi tidak ditemukan!');
    header('Location: index.php');
    exit;
}

$error = '';

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lokasi = sanitasi($_POST['nama_lokasi'] ?? '');
    $tipe_ruangan = sanitasi($_POST['tipe_ruangan'] ?? '');
    $keterangan = sanitasi($_POST['keterangan'] ?? '');
    
    // Validasi input
    $errors = [];
    
    if (!validasi_tidak_kosong($nama_lokasi)) {
        $errors[] = 'Nama lokasi harus diisi';
    }
    
    if (empty($errors)) {
        // Escape string
        $nama_escaped = escape($koneksi, $nama_lokasi);
        $tipe_escaped = escape($koneksi, $tipe_ruangan);
        $keterangan_escaped = escape($koneksi, $keterangan);
        
        // Query update
        $query = "UPDATE lokasi SET 
                  nama_lokasi = '$nama_escaped',
                  tipe_ruangan = '$tipe_escaped',
                  keterangan = '$keterangan_escaped'
                  WHERE id_lokasi = $id_lokasi";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Lokasi berhasil diperbarui!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal memperbarui lokasi: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <h1>Edit Lokasi/Ruangan</h1>
    <p>Perbarui data lokasi: <?php echo htmlspecialchars($lokasi['nama_lokasi']); ?></p>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div style="max-width: 600px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <form method="POST" action="">
            
            <div class="form-group">
                <label for="nama_lokasi">Nama Lokasi <span style="color: red;">*</span></label>
                <input type="text" id="nama_lokasi" name="nama_lokasi" 
                       value="<?php echo htmlspecialchars($lokasi['nama_lokasi']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="tipe_ruangan">Tipe Ruangan</label>
                <input type="text" id="tipe_ruangan" name="tipe_ruangan" 
                       value="<?php echo htmlspecialchars($lokasi['tipe_ruangan'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan">
<?php echo htmlspecialchars($lokasi['keterangan'] ?? ''); ?></textarea>
            </div>
            
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
</div>

<?php include '../../includes/footer.php'; ?>
?>

<?php
// FILE: pages/lokasi/hapus.php
// Proses Hapus Lokasi

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login & role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    set_alert('danger', 'Anda tidak memiliki akses!');
    header('Location: ../dashboard.php');
    exit;
}

// Ambil ID dari URL
$id_lokasi = intval($_GET['id'] ?? 0);

if ($id_lokasi == 0) {
    set_alert('danger', 'ID lokasi tidak valid!');
    header('Location: index.php');
    exit;
}

// Ambil data lokasi
$lokasi = fetch_row($koneksi, "SELECT * FROM lokasi WHERE id_lokasi = $id_lokasi");

if (!$lokasi) {
    set_alert('danger', 'Lokasi tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// Cek apakah ada sarana dengan lokasi ini
$jumlah_sarana = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_lokasi = $id_lokasi");

if ($jumlah_sarana > 0) {
    set_alert('danger', 'Lokasi tidak bisa dihapus karena masih ada sarana!');
    header('Location: index.php');
    exit;
}

// Hapus dari database
$query = "DELETE FROM lokasi WHERE id_lokasi = $id_lokasi";

if (mysqli_query($koneksi, $query)) {
    set_alert('success', 'Lokasi berhasil dihapus!');
} else {
    set_alert('danger', 'Gagal menghapus lokasi: ' . mysqli_error($koneksi));
}

// Redirect ke halaman daftar
header('Location: index.php');
exit;
?>