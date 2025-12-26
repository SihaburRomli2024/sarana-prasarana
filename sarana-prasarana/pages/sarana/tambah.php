<?php
// FILE: pages/sarana/tambah.php
// Halaman Tambah Sarana Prasarana

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = false;

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi input
    $nama_sarana = sanitasi($_POST['nama_sarana'] ?? '');
    $id_kategori = intval($_POST['id_kategori'] ?? 0);
    $id_lokasi = intval($_POST['id_lokasi'] ?? 0);
    $id_kondisi = intval($_POST['id_kondisi'] ?? 1);
    $deskripsi = sanitasi($_POST['deskripsi'] ?? '');
    $tanggal_perolehan = $_POST['tanggal_perolehan'] ?? '';
    $nomor_inventaris = sanitasi($_POST['nomor_inventaris'] ?? '');
    $harga_perolehan = !empty($_POST['harga_perolehan']) ? str_replace(['Rp ', '.', ','], '', $_POST['harga_perolehan']) : '0';
    $spesifikasi = sanitasi($_POST['spesifikasi'] ?? '');
    
    // Validasi input
    $errors = [];
    
    if (!validasi_tidak_kosong($nama_sarana)) {
        $errors[] = 'Nama sarana harus diisi';
    }
    
    if ($id_kategori == 0) {
        $errors[] = 'Kategori harus dipilih';
    }
    
    if ($id_lokasi == 0) {
        $errors[] = 'Lokasi harus dipilih';
    }
    
    if (!empty($tanggal_perolehan) && !validasi_tanggal($tanggal_perolehan)) {
        $errors[] = 'Format tanggal tidak valid (YYYY-MM-DD)';
    }
    
    // Proses upload foto jika ada
    $foto_sarana = NULL;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_result = upload_gambar($_FILES['foto'], '../../uploads/foto_sarana/');
        if ($upload_result['status']) {
            $foto_sarana = $upload_result['file'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        // Escape semua string
        $nama_sarana_escaped = escape($koneksi, $nama_sarana);
        $deskripsi_escaped = escape($koneksi, $deskripsi);
        $nomor_inventaris_escaped = escape($koneksi, $nomor_inventaris);
        $spesifikasi_escaped = escape($koneksi, $spesifikasi);
        $foto_escaped = !empty($foto_sarana) ? escape($koneksi, $foto_sarana) : 'NULL';
        
        // Build query dengan hati-hati terhadap NULL values
        $tanggal_part = !empty($tanggal_perolehan) ? "'" . $tanggal_perolehan . "'" : "NULL";
        $foto_part = !empty($foto_sarana) ? "'" . $foto_escaped . "'" : "NULL";
        $harga_part = !empty($_POST['harga_perolehan']) ? $harga_perolehan : "0";
        
        // Query insert
        $query = "INSERT INTO sarana_prasarana 
                  (nama_sarana, id_kategori, id_lokasi, id_kondisi, deskripsi, 
                   tanggal_perolehan, nomor_inventaris, harga_perolehan, spesifikasi, foto)
                  VALUES 
                  ('$nama_sarana_escaped', $id_kategori, $id_lokasi, $id_kondisi, 
                   '$deskripsi_escaped', $tanggal_part, '$nomor_inventaris_escaped', 
                   $harga_part, '$spesifikasi_escaped', $foto_part)";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Sarana berhasil ditambahkan!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal menambahkan sarana: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Ambil data untuk dropdown
$kategori_list = fetch_all($koneksi, "SELECT * FROM kategori_sarana ORDER BY nama_kategori");
$lokasi_list = fetch_all($koneksi, "SELECT * FROM lokasi ORDER BY nama_lokasi");
$kondisi_list = fetch_all($koneksi, "SELECT * FROM kondisi_sarana");
?>

<?php
$hideSidebar = true;
$hideTopbar  = true;
include '../../includes/header.php';
?>

<!-- <div class="page-header">
    <h1>Tambah Sarana Prasarana</h1>
    <p>Masukkan data sarana baru ke dalam sistem</p>
</div> -->

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- JUDUL TENGAH -->
<div class="text-center my-4">
    <h2 class="fw-bold">Tambah Sarana Prasarana</h2>
    <p class="text-muted mb-0">
        Masukkan data sarana baru ke dalam sistem
    </p>
</div>

<div class="container mt-4" style="max-width: 900px;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-plus-circle"></i> Form Tambah Sarana Prasarana
            </h5>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <table class="table table-borderless align-middle">
                    <tr>
                        <th width="30%">Nama Sarana <span class="text-danger">*</span></th>
                        <td>
                            <input type="text" name="nama_sarana" class="form-control"
                                   value="<?= htmlspecialchars($_POST['nama_sarana'] ?? '') ?>"
                                   placeholder="Contoh: Meja Belajar" required>
                        </td>
                    </tr>

                    <tr>
                        <th>Kategori <span class="text-danger">*</span></th>
                        <td>
                            <select name="id_kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori_list as $kat): ?>
                                    <option value="<?= $kat['id_kategori']; ?>"
                                        <?= ($_POST['id_kategori'] ?? '') == $kat['id_kategori'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Kondisi Awal <span class="text-danger">*</span></th>
                        <td>
                            <select name="id_kondisi" class="form-select">
                                <?php foreach ($kondisi_list as $kond): ?>
                                    <option value="<?= $kond['id_kondisi']; ?>"
                                        <?= $kond['id_kondisi'] == 1 ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($kond['nama_kondisi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Lokasi <span class="text-danger">*</span></th>
                        <td>
                            <select name="id_lokasi" class="form-select" required>
                                <option value="">-- Pilih Lokasi --</option>
                                <?php foreach ($lokasi_list as $lok): ?>
                                    <option value="<?= $lok['id_lokasi']; ?>"
                                        <?= ($_POST['id_lokasi'] ?? '') == $lok['id_lokasi'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($lok['nama_lokasi']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Nomor Inventaris</th>
                        <td>
                            <input type="text" name="nomor_inventaris" class="form-control"
                                   value="<?= htmlspecialchars($_POST['nomor_inventaris'] ?? '') ?>"
                                   placeholder="INV-2024-001">
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Perolehan</th>
                        <td>
                            <input type="date" name="tanggal_perolehan" class="form-control"
                                   value="<?= $_POST['tanggal_perolehan'] ?? '' ?>">
                        </td>
                    </tr>

                    <tr>
                        <th>Harga Perolehan (Rp)</th>
                        <td>
                            <input type="text" name="harga_perolehan" class="form-control"
                                   value="<?= htmlspecialchars($_POST['harga_perolehan'] ?? '') ?>"
                                   placeholder="0">
                        </td>
                    </tr>

                    <tr>
                        <th>Deskripsi</th>
                        <td>
                            <textarea name="deskripsi" class="form-control" rows="3"
                                      placeholder="Keterangan tambahan..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th>Spesifikasi</th>
                        <td>
                            <textarea name="spesifikasi" class="form-control" rows="3"
                                      placeholder="Spesifikasi teknis..."><?= htmlspecialchars($_POST['spesifikasi'] ?? '') ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <th>Foto Sarana</th>
                        <td>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <small class="text-muted">Format JPG/PNG, Maks 5MB</small>
                        </td>
                    </tr>
                </table>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>