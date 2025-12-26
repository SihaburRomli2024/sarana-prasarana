<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}
$hideSidebar = true;
$hideTopbar  = true;

$query = "
    SELECT s.*, k.nama_kategori, l.nama_lokasi, kond.nama_kondisi
    FROM sarana_prasarana s
    JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    JOIN kondisi_sarana kond ON s.id_kondisi = kond.id_kondisi
    ORDER BY s.tanggal_dibuat DESC
";

$sarana_list = fetch_all($koneksi, $query);
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
        
        <h4 class="fw-bold mb-0">Data Sarana & Prasarana</h4>
    </div>
    <div>
            
            <a href="tambah.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah
            </a>
            <a href="laporan.php" target="_blank" class="btn btn-secondary">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>
            <a href="../dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>Nama Sarana</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sarana_list as $i => $row): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= $row['nama_sarana'] ?></td>
                <td><?= $row['nama_kategori'] ?></td>
                <td><?= $row['nama_lokasi'] ?></td>
                <td><?= $row['nama_kondisi'] ?></td>
                <td class="text-center">
                    <a href="detail.php?id=<?= $row['id_sarana'] ?>" class="btn btn-info btn-sm">Detail</a>
                    <a href="edit.php?id=<?= $row['id_sarana'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus.php?id=<?= $row['id_sarana'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Yakin hapus data?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php include '../../includes/footer.php'; ?>
