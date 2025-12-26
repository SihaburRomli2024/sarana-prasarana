<?php
// FILE: pages/maintenance/edit.php
// Halaman Edit Maintenance/Pemeliharaan

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil ID dari URL
$id_maintenance = intval($_GET['id'] ?? 0);

if ($id_maintenance == 0) {
    header('Location: index.php');
    exit;
}

// Ambil data maintenance
$maintenance = fetch_row($koneksi, "SELECT * FROM maintenance WHERE id_maintenance = $id_maintenance");

if (!$maintenance) {
    set_alert('danger', 'Maintenance tidak ditemukan!');
    header('Location: index.php');
    exit;
}

$error = '';

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi input
    $id_sarana = intval($_POST['id_sarana'] ?? 0);
    $tanggal_maintenance = $_POST['tanggal_maintenance'] ?? '';
    $tipe_maintenance = sanitasi($_POST['tipe_maintenance'] ?? '');
    $deskripsi_perbaikan = sanitasi($_POST['deskripsi_perbaikan'] ?? '');
    $biaya = !empty($_POST['biaya']) ? str_replace(['Rp ', '.', ','], '', $_POST['biaya']) : '0';
    $status = sanitasi($_POST['status'] ?? 'rencana');
    
    // Validasi input
    $errors = [];
    
    if ($id_sarana == 0) {
        $errors[] = 'Sarana harus dipilih';
    }
    
    if (empty($tanggal_maintenance)) {
        $errors[] = 'Tanggal maintenance harus diisi';
    } elseif (!validasi_tanggal($tanggal_maintenance)) {
        $errors[] = 'Format tanggal tidak valid (YYYY-MM-DD)';
    }
    
    if (empty($tipe_maintenance)) {
        $errors[] = 'Tipe maintenance harus dipilih';
    }
    
    if (empty($errors)) {
        // Escape string
        $deskripsi_escaped = escape($koneksi, $deskripsi_perbaikan);
        $status_escaped = escape($koneksi, $status);
        $tipe_escaped = escape($koneksi, $tipe_maintenance);
        
        // Query update
        $query = "UPDATE maintenance SET 
                  id_sarana = $id_sarana,
                  tanggal_maintenance = '$tanggal_maintenance',
                  tipe_maintenance = '$tipe_escaped',
                  deskripsi_perbaikan = '$deskripsi_escaped',
                  biaya = $biaya,
                  status = '$status_escaped'
                  WHERE id_maintenance = $id_maintenance";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Maintenance berhasil diperbarui!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal memperbarui maintenance: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Ambil data untuk dropdown
$sarana_list = fetch_all($koneksi, "SELECT s.id_sarana, s.nama_sarana, l.nama_lokasi 
                                       FROM sarana_prasarana s
                                       JOIN lokasi l ON s.id_lokasi = l.id_lokasi
                                       ORDER BY s.nama_sarana");
?>

<?php
// SEMBUNYIKAN SIDEBAR & TOPBAR
$hideSidebar = true;
$hideTopbar  = true;
?>
<?php include '../../includes/header.php'; ?>

<div class="text-center mt-4 mb-4">
    <h3 class="fw-semibold mb-1">Edit Maintenance / Pemeliharaan</h3>
    <p class="text-muted mb-0">Perbarui data pemeliharaan sarana</p>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <form method="POST" id="formEditMaintenance">

                    <!-- Sarana -->
                    <div class="mb-3">
                        <label for="id_sarana" class="form-label">
                            Sarana <span class="text-danger">*</span>
                        </label>
                        <select id="id_sarana" name="id_sarana" class="form-select" required>
                            <option value="">-- Pilih Sarana --</option>
                            <?php foreach ($sarana_list as $sar): ?>
                                <option value="<?= $sar['id_sarana']; ?>"
                                    <?= $maintenance['id_sarana'] == $sar['id_sarana'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($sar['nama_sarana']); ?>
                                    (<?= htmlspecialchars($sar['nama_lokasi']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <!-- Tanggal -->
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_maintenance" class="form-label">
                                Tanggal Maintenance <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control"
                                   id="tanggal_maintenance"
                                   name="tanggal_maintenance"
                                   value="<?= htmlspecialchars($maintenance['tanggal_maintenance']); ?>"
                                   required>
                        </div>

                        <!-- Tipe -->
                        <div class="col-md-6 mb-3">
                            <label for="tipe_maintenance" class="form-label">
                                Tipe Maintenance <span class="text-danger">*</span>
                            </label>
                            <select id="tipe_maintenance" name="tipe_maintenance" class="form-select" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="pemeliharaan rutin" <?= $maintenance['tipe_maintenance'] == 'pemeliharaan rutin' ? 'selected' : ''; ?>>
                                    Pemeliharaan Rutin
                                </option>
                                <option value="perbaikan" <?= $maintenance['tipe_maintenance'] == 'perbaikan' ? 'selected' : ''; ?>>
                                    Perbaikan
                                </option>
                                <option value="penggantian" <?= $maintenance['tipe_maintenance'] == 'penggantian' ? 'selected' : ''; ?>>
                                    Penggantian
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Biaya -->
                        <div class="col-md-6 mb-3">
                            <label for="biaya" class="form-label">Biaya (Rp)</label>
                            <input type="text"
                                   class="form-control"
                                   id="biaya"
                                   name="biaya"
                                   value="<?= htmlspecialchars($maintenance['biaya'] ?? ''); ?>">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="rencana" <?= $maintenance['status'] == 'rencana' ? 'selected' : ''; ?>>Rencana</option>
                                <option value="sedang dikerjakan" <?= $maintenance['status'] == 'sedang dikerjakan' ? 'selected' : ''; ?>>Sedang Dikerjakan</option>
                                <option value="selesai" <?= $maintenance['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label for="deskripsi_perbaikan" class="form-label">Deskripsi Perbaikan</label>
                        <textarea id="deskripsi_perbaikan"
                                  name="deskripsi_perbaikan"
                                  rows="4"
                                  class="form-control"><?= htmlspecialchars($maintenance['deskripsi_perbaikan'] ?? ''); ?></textarea>
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex justify-content-between">
                        <!-- <a href="index.php" class="btn btn-secondary">
                         <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a> -->

                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>


<?php