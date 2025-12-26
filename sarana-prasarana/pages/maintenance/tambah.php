<?php
// FILE: pages/maintenance/tambah.php
// Halaman Tambah Maintenance/Pemeliharaan

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$id_sarana_default = intval($_GET['id_sarana'] ?? 0);

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_sarana = intval($_POST['id_sarana'] ?? 0);
    $tanggal_maintenance = $_POST['tanggal_maintenance'] ?? '';
    $tipe_maintenance = sanitasi($_POST['tipe_maintenance'] ?? '');
    $deskripsi_perbaikan = sanitasi($_POST['deskripsi_perbaikan'] ?? '');
    $biaya = !empty($_POST['biaya']) ? str_replace(['Rp ', '.', ','], '', $_POST['biaya']) : '0';
    $status = sanitasi($_POST['status'] ?? 'rencana');

    $errors = [];

    if ($id_sarana == 0) $errors[] = 'Sarana harus dipilih';
    if (empty($tanggal_maintenance)) $errors[] = 'Tanggal maintenance harus diisi';
    elseif (!validasi_tanggal($tanggal_maintenance)) $errors[] = 'Format tanggal tidak valid';
    if (empty($tipe_maintenance)) $errors[] = 'Tipe maintenance harus dipilih';

    if (empty($errors)) {

        $query = "INSERT INTO maintenance 
            (id_sarana, tanggal_maintenance, tipe_maintenance, deskripsi_perbaikan, biaya, status, id_user)
            VALUES (
                $id_sarana,
                '$tanggal_maintenance',
                '".escape($koneksi, $tipe_maintenance)."',
                '".escape($koneksi, $deskripsi_perbaikan)."',
                $biaya,
                '".escape($koneksi, $status)."',
                ".$_SESSION['id_user']."
            )";

        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Maintenance berhasil ditambahkan!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal menambahkan maintenance: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Data sarana
$sarana_list = fetch_all($koneksi, "
    SELECT s.id_sarana, s.nama_sarana, l.nama_lokasi
    FROM sarana_prasarana s
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    ORDER BY s.nama_sarana
");
?>

<?php
$hideSidebar = true;
$hideTopbar  = true;
include '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Tambah Maintenance / Pemeliharaan
                    </h5>
                </div>

                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">
                                Sarana <span class="text-danger">*</span>
                            </label>
                            <select name="id_sarana" class="form-select" required>
                                <option value="">-- Pilih Sarana --</option>
                                <?php foreach ($sarana_list as $sar): ?>
                                    <option value="<?= $sar['id_sarana']; ?>"
                                        <?= ($id_sarana_default == $sar['id_sarana']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($sar['nama_sarana']); ?>
                                        (<?= htmlspecialchars($sar['nama_lokasi']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Tanggal Maintenance <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal_maintenance"
                                       class="form-control"
                                       value="<?= date('Y-m-d'); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Tipe Maintenance <span class="text-danger">*</span>
                                </label>
                                <select name="tipe_maintenance" class="form-select" required>
                                    <option value="">-- Pilih Tipe --</option>
                                    <option value="pemeliharaan rutin">Pemeliharaan Rutin</option>
                                    <option value="perbaikan">Perbaikan</option>
                                    <option value="penggantian">Penggantian</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Biaya (Rp)</label>
                                <input type="number" name="biaya" class="form-control" placeholder="0">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-select" required>
                                    <option value="rencana" selected>Rencana</option>
                                    <option value="sedang dikerjakan">Sedang Dikerjakan</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Perbaikan</label>
                            <textarea name="deskripsi_perbaikan"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Jelaskan detail perbaikan atau pemeliharaan..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
