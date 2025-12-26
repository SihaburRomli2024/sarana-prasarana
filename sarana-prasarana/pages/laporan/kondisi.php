<?php
// FILE: pages/laporan/kondisi.php
// Laporan Kondisi Sarana

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil parameter filter
$kategori_filter = $_GET['kategori'] ?? '';
$lokasi_filter = $_GET['lokasi'] ?? '';

// Build query dengan filter
$where = "WHERE 1=1";

if (!empty($kategori_filter)) {
    $where .= " AND s.id_kategori = " . intval($kategori_filter);
}

if (!empty($lokasi_filter)) {
    $where .= " AND s.id_lokasi = " . intval($lokasi_filter);
}

// Ambil statistik keseluruhan
$total_sarana = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana");
$total_baik = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_kondisi = 1");
$total_rusak_ringan = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_kondisi = 2");
$total_rusak_berat = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_kondisi = 3");

// Hitung persentase
$persen_baik = $total_sarana > 0 ? round(($total_baik / $total_sarana) * 100, 2) : 0;
$persen_rusak_ringan = $total_sarana > 0 ? round(($total_rusak_ringan / $total_sarana) * 100, 2) : 0;
$persen_rusak_berat = $total_sarana > 0 ? round(($total_rusak_berat / $total_sarana) * 100, 2) : 0;

// Ambil daftar sarana dengan filter
$sarana_baik = fetch_all($koneksi, "
    SELECT s.*, k.nama_kategori, l.nama_lokasi
    FROM sarana_prasarana s
    JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    $where AND s.id_kondisi = 1
    ORDER BY s.nama_sarana
");

$sarana_rusak_ringan = fetch_all($koneksi, "
    SELECT s.*, k.nama_kategori, l.nama_lokasi
    FROM sarana_prasarana s
    JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    $where AND s.id_kondisi = 2
    ORDER BY s.nama_sarana
");

$sarana_rusak_berat = fetch_all($koneksi, "
    SELECT s.*, k.nama_kategori, l.nama_lokasi
    FROM sarana_prasarana s
    JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    $where AND s.id_kondisi = 3
    ORDER BY s.nama_sarana
");

// Ambil data untuk filter dropdown
$kategori_list = fetch_all($koneksi, "SELECT * FROM kategori_sarana ORDER BY nama_kategori");
$lokasi_list = fetch_all($koneksi, "SELECT * FROM lokasi ORDER BY nama_lokasi");
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <h1>Laporan Kondisi Sarana</h1>
    <p>Informasi kondisi dan status semua sarana di sekolah</p>
</div>

<?php show_alert(); ?>

<!-- Statistik Keseluruhan -->
<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon bg-blue">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <h3>Total Sarana</h3>
            <p class="stat-number"><?php echo $total_sarana; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3>Baik</h3>
            <p class="stat-number"><?php echo $total_baik; ?></p>
            <small style="color: #666;"><?php echo $persen_baik; ?>%</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-yellow">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <h3>Rusak Ringan</h3>
            <p class="stat-number"><?php echo $total_rusak_ringan; ?></p>
            <small style="color: #666;"><?php echo $persen_rusak_ringan; ?>%</small>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-red">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-content">
            <h3>Rusak Berat</h3>
            <p class="stat-number"><?php echo $total_rusak_berat; ?></p>
            <small style="color: #666;"><?php echo $persen_rusak_berat; ?>%</small>
        </div>
    </div>
</div>

<!-- Filter -->
<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <form method="GET" action="">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($kategori_list as $kat): ?>
                    <option value="<?php echo $kat['id_kategori']; ?>" 
                            <?php echo $kategori_filter == $kat['id_kategori'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <select id="lokasi" name="lokasi">
                    <option value="">-- Semua Lokasi --</option>
                    <?php foreach ($lokasi_list as $lok): ?>
                    <option value="<?php echo $lok['id_lokasi']; ?>" 
                            <?php echo $lokasi_filter == $lok['id_lokasi'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($lok['nama_lokasi']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="kondisi.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
            <a href="export.php?laporan=kondisi&tipe=excel&kategori=<?php echo htmlspecialchars($kategori_filter); ?>&lokasi=<?php echo htmlspecialchars($lokasi_filter); ?>" class="btn btn-info">
    <i class="fas fa-download"></i> Export Excel
</a>
            </button>
            <button type="button" class="btn btn-warning" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </form>
</div>

<!-- Tabel Sarana Baik -->
<div class="table-section" style="margin-bottom: 20px;">
    <h3><span class="badge badge-success">Kondisi Baik (<?php echo count($sarana_baik); ?>)</span></h3>
    <?php if (count($sarana_baik) > 0): ?>
    <table class="table table-striped" id="tabelLaporan">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sarana</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>No. Inventaris</th>
                <th>Tanggal Perolehan</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sarana_baik as $idx => $item): ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><?php echo htmlspecialchars($item['nama_sarana']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_lokasi']); ?></td>
                <td><?php echo htmlspecialchars($item['nomor_inventaris'] ?? '-'); ?></td>
                <td><?php echo $item['tanggal_perolehan'] ? format_tanggal($item['tanggal_perolehan']) : '-'; ?></td>
                <td><?php echo $item['harga_perolehan'] ? format_rupiah($item['harga_perolehan']) : '-'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #666; padding: 20px;">Tidak ada sarana dengan kondisi baik</p>
    <?php endif; ?>
</div>

<!-- Tabel Sarana Rusak Ringan -->
<div class="table-section" style="margin-bottom: 20px;">
    <h3><span class="badge badge-warning">Kondisi Rusak Ringan (<?php echo count($sarana_rusak_ringan); ?>)</span></h3>
    <?php if (count($sarana_rusak_ringan) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sarana</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>No. Inventaris</th>
                <th>Tanggal Perolehan</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sarana_rusak_ringan as $idx => $item): ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><?php echo htmlspecialchars($item['nama_sarana']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_lokasi']); ?></td>
                <td><?php echo htmlspecialchars($item['nomor_inventaris'] ?? '-'); ?></td>
                <td><?php echo $item['tanggal_perolehan'] ? format_tanggal($item['tanggal_perolehan']) : '-'; ?></td>
                <td><?php echo $item['harga_perolehan'] ? format_rupiah($item['harga_perolehan']) : '-'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #666; padding: 20px;">Tidak ada sarana dengan kondisi rusak ringan</p>
    <?php endif; ?>
</div>

<!-- Tabel Sarana Rusak Berat -->
<div class="table-section">
    <h3><span class="badge badge-danger">Kondisi Rusak Berat (<?php echo count($sarana_rusak_berat); ?>)</span></h3>
    <?php if (count($sarana_rusak_berat) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sarana</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>No. Inventaris</th>
                <th>Tanggal Perolehan</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sarana_rusak_berat as $idx => $item): ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><?php echo htmlspecialchars($item['nama_sarana']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_lokasi']); ?></td>
                <td><?php echo htmlspecialchars($item['nomor_inventaris'] ?? '-'); ?></td>
                <td><?php echo $item['tanggal_perolehan'] ? format_tanggal($item['tanggal_perolehan']) : '-'; ?></td>
                <td><?php echo $item['harga_perolehan'] ? format_rupiah($item['harga_perolehan']) : '-'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #666; padding: 20px;">Tidak ada sarana dengan kondisi rusak berat</p>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>