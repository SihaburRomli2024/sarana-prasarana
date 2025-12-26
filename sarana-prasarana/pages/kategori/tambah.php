<?php
// FILE: pages/kategori/tambah.php
// Halaman Tambah Kategori

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
    $nama_kategori = sanitasi($_POST['nama_kategori'] ?? '');
    $keterangan = sanitasi($_POST['keterangan'] ?? '');
    
    // Validasi input
    $errors = [];
    
    if (!validasi_tidak_kosong($nama_kategori)) {
        $errors[] = 'Nama kategori harus diisi';
    }
    
    if (empty($errors)) {
        // Escape string
        $nama_escaped = escape($koneksi, $nama_kategori);
        $keterangan_escaped = escape($koneksi, $keterangan);
        
        // Query insert
        $query = "INSERT INTO kategori_sarana (nama_kategori, keterangan)
                  VALUES ('$nama_escaped', '$keterangan_escaped')";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Kategori berhasil ditambahkan!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal menambahkan kategori: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
<?php
// SEMBUNYIKAN SIDEBAR & TOPBAR
$hideSidebar = true;
$hideTopbar  = true;
?>
<?php include '../../includes/header.php'; ?>

<div class="text-center mt-4 mb-4">
    <h3 class="fw-semibold mb-1">Tambah Kategori Sarana</h3>
    <p class="text-muted mb-0">Buat kategori sarana baru</p>
</div>


<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <form method="POST">

                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control"
                               id="nama_kategori"
                               name="nama_kategori"
                               value="<?= isset($_POST['nama_kategori']) ? htmlspecialchars($_POST['nama_kategori']) : ''; ?>"
                               placeholder="Contoh: Meja, Kursi, Komputer"
                               required>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control"
                                  id="keterangan"
                                  name="keterangan"
                                  rows="4"
                                  placeholder="Deskripsi kategori (opsional)"><?= isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : ''; ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                       
                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

