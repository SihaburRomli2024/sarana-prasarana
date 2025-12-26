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

$tanggal_cetak = date('d F Y');
$penanggung_jawab = $_SESSION['nama_lengkap'];
?>

<?php include '../../includes/header.php'; ?>

<style>
@media print {
    .no-print { display: none; }
    body { background: #fff; }
}

/* KOP SURAT */
.kop {
    border-bottom: 3px solid #000;
    padding-bottom: 10px;
    margin-bottom: 30px;
}
.kop img {
    width: 90px;
}

/* WATERMARK LOGO */
.watermark-logo {
    position: fixed;
    top: 50%;
    left: 50%;
    width: 400px;
    opacity: 0.08;
    transform: translate(-50%, -50%);
    z-index: 0;
}

/* KONTEN */
.content-area {
    position: relative;
    z-index: 2;
}

/* PENGESAHAN */
.pengesahan {
    margin-top: 60px;
}
</style>

<!-- WATERMARK LOGO -->
<img src="../../assets/images/logo-sekolah.png" class="watermark-logo">

<div class="container content-area">

    <!-- TOMBOL -->
    <div class="no-print mb-3 text-end">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print / PDF
        </button>
    </div>

    <!-- KOP SURAT -->
    <div class="kop d-flex align-items-center">
        <div class="me-3">
            <img src="../../assets/images/logo-sekolah.png" alt="Logo Sekolah">
        </div>
        <div class="text-center flex-fill">
            <h5 class="fw-bold mb-0">SMP BINA INSAN MANDIRI</h5>
            <small>
                Jl. Pendidikan No. 123, Kecamatan Dramaga, Kabupaten Bogor<br>
                Telp. (0251) 123456 | Email: smpbinainsan@gmail.com
            </small>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="text-center mb-4">
        <h5 class="fw-bold text-uppercase">Laporan Data Sarana dan Prasarana</h5>
    </div>

    <!-- TABEL -->
    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light text-center">
            <tr>
                <th>No</th>
                <th>No Inventaris</th>
                <th>Nama Sarana</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>Tanggal Perolehan</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sarana_list as $i => $item): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= $item['nomor_inventaris'] ?? '-' ?></td>
                <td><?= $item['nama_sarana'] ?></td>
                <td><?= $item['nama_kategori'] ?></td>
                <td><?= $item['nama_lokasi'] ?></td>
                <td class="text-center"><?= $item['nama_kondisi'] ?></td>
                <td><?= $item['tanggal_perolehan'] ? format_tanggal($item['tanggal_perolehan']) : '-' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN -->
    <div class="pengesahan">
        <div class="row">
            <div class="col-6">
                <p>Mengetahui,<br>
                Kepala Sekolah</p>
                <br><br><br>
                <p class="fw-bold text-decoration-underline">
                    ( ______________________ )
                </p>
            </div>

            <div class="col-6 text-end">
                <p>Bogor, <?= $tanggal_cetak ?><br>
                Penanggung Jawab</p>
                <br><br><br>
                <p class="fw-bold text-decoration-underline">
                    <?= htmlspecialchars($penanggung_jawab) ?>
                </p>
            </div>
        </div>
    </div>

</div>

<?php include '../../includes/footer.php'; ?>


