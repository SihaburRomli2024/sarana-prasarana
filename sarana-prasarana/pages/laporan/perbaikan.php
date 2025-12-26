<?php
// FILE: pages/laporan/perbaikan.php
// Laporan Perbaikan/Maintenance

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil parameter filter
$bulan_filter = $_GET['bulan'] ?? date('Y-m');
$status_filter = $_GET['status'] ?? '';

// Build query dengan filter
$where = "WHERE DATE_FORMAT(m.tanggal_maintenance, '%Y-%m') = '$bulan_filter'";

if (!empty($status_filter)) {
    $where .= " AND m.status = '" . escape($koneksi, $status_filter) . "'";
}

// Ambil daftar maintenance dengan filter
$maintenance_list = fetch_all($koneksi, "
    SELECT m.*, s.nama_sarana, l.nama_lokasi, u.nama_lengkap
    FROM maintenance m
    JOIN sarana_prasarana s ON m.id_sarana = s.id_sarana
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    LEFT JOIN users u ON m.id_user = u.id_user
    $where
    ORDER BY m.tanggal_maintenance DESC
");

// Hitung statistik
$total_maintenance = count($maintenance_list);
$total_rencana = count_rows($koneksi, "SELECT id_maintenance FROM maintenance WHERE status = 'rencana' AND DATE_FORMAT(tanggal_maintenance, '%Y-%m') = '$bulan_filter'");
$total_sedang_dikerjakan = count_rows($koneksi, "SELECT id_maintenance FROM maintenance WHERE status = 'sedang dikerjakan' AND DATE_FORMAT(tanggal_maintenance, '%Y-%m') = '$bulan_filter'");
$total_selesai = count_rows($koneksi, "SELECT id_maintenance FROM maintenance WHERE status = 'selesai' AND DATE_FORMAT(tanggal_maintenance, '%Y-%m') = '$bulan_filter'");

// Hitung total biaya
$biaya_result = fetch_row($koneksi, "
    SELECT SUM(biaya) as total_biaya 
    FROM maintenance 
    $where
");
$total_biaya = $biaya_result['total_biaya'] ?? 0;

// Ambil statistik tipe maintenance
$tipe_stats = fetch_all($koneksi, "
    SELECT tipe_maintenance, COUNT(*) as jumlah, SUM(biaya) as total_biaya
    FROM maintenance
    $where
    GROUP BY tipe_maintenance
");

// Ambil sarana yang sering diperbaiki
$sarana_sering_diperbaiki = fetch_all($koneksi, "
    SELECT s.nama_sarana, COUNT(m.id_maintenance) as jumlah_perbaikan, SUM(m.biaya) as total_biaya
    FROM maintenance m
    JOIN sarana_prasarana s ON m.id_sarana = s.id_sarana
    WHERE DATE_FORMAT(m.tanggal_maintenance, '%Y-%m') = '$bulan_filter'
    GROUP BY m.id_sarana
    ORDER BY jumlah_perbaikan DESC
    LIMIT 10
");
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <h1>Laporan Perbaikan & Pemeliharaan</h1>
    <p>Detail aktivitas perbaikan dan pemeliharaan sarana</p>
</div>

<?php show_alert(); ?>

<!-- Statistik -->
<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-icon bg-blue">
            <i class="fas fa-wrench"></i>
        </div>
        <div class="stat-content">
            <h3>Total Perbaikan</h3>
            <p class="stat-number"><?php echo $total_maintenance; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-content">
            <h3>Rencana</h3>
            <p class="stat-number"><?php echo $total_rencana; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-yellow">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="stat-content">
            <h3>Sedang Dikerjakan</h3>
            <p class="stat-number"><?php echo $total_sedang_dikerjakan; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3>Selesai</h3>
            <p class="stat-number"><?php echo $total_selesai; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-red">
            <i class="fas fa-money-bill"></i>
        </div>
        <div class="stat-content">
            <h3>Total Biaya</h3>
            <p class="stat-number" style="font-size: 20px;"><?php echo format_rupiah($total_biaya); ?></p>
        </div>
    </div>
</div>

<!-- Filter -->
<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <form method="GET" action="">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label for="bulan">Bulan</label>
                <input type="month" id="bulan" name="bulan" value="<?php echo htmlspecialchars($bulan_filter); ?>">
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="rencana" <?php echo $status_filter == 'rencana' ? 'selected' : ''; ?>>Rencana</option>
                    <option value="sedang dikerjakan" <?php echo $status_filter == 'sedang dikerjakan' ? 'selected' : ''; ?>>Sedang Dikerjakan</option>
                    <option value="selesai" <?php echo $status_filter == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="perbaikan.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
            <button type="button" class="btn btn-info" onclick="exportTableToExcel('tabelPerbaikan', 'laporan_perbaikan.xlsx')">
                <i class="fas fa-download"></i> Export Excel
            </button>
            <button type="button" class="btn btn-warning" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </form>
</div>

<!-- Statistik Tipe Maintenance -->
<div class="table-section" style="margin-bottom: 20px;">
    <h3>Statistik Tipe Perbaikan</h3>
    <?php if (count($tipe_stats) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tipe Perbaikan</th>
                <th>Jumlah</th>
                <th>Total Biaya</th>
                <th>Rata-Rata Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tipe_stats as $tipe): ?>
            <tr>
                <td><?php echo ucfirst(htmlspecialchars($tipe['tipe_maintenance'])); ?></td>
                <td><?php echo $tipe['jumlah']; ?></td>
                <td><?php echo format_rupiah($tipe['total_biaya']); ?></td>
                <td><?php echo format_rupiah($tipe['total_biaya'] / $tipe['jumlah']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #666; padding: 20px;">Tidak ada data perbaikan untuk bulan ini</p>
    <?php endif; ?>
</div>

<!-- Sarana Sering Diperbaiki -->
<div class="table-section" style="margin-bottom: 20px;">
    <h3>Sarana yang Sering Diperbaiki (Top 10)</h3>
    <?php if (count($sarana_sering_diperbaiki) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sarana</th>
                <th>Jumlah Perbaikan</th>
                <th>Total Biaya</th>
                <th>Rata-Rata Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sarana_sering_diperbaiki as $idx => $sarana): ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><?php echo htmlspecialchars($sarana['nama_sarana']); ?></td>
                <td><?php echo $sarana['jumlah_perbaikan']; ?></td>
                <td><?php echo format_rupiah($sarana['total_biaya']); ?></td>
                <td><?php echo format_rupiah($sarana['total_biaya'] / $sarana['jumlah_perbaikan']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #666; padding: 20px;">Tidak ada data perbaikan</p>
    <?php endif; ?>
</div>

<!-- Daftar Detail Maintenance -->
<div class="table-section">
    <h3>Detail Perbaikan Bulan <?php echo date('F Y', strtotime($bulan_filter . '-01')); ?></h3>
    <?php if (count($maintenance_list) > 0): ?>
    <table class="table table-striped" id="tabelPerbaikan">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Sarana</th>
                <th>Lokasi</th>
                <th>Tipe</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maintenance_list as $idx => $item): ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><?php echo format_tanggal($item['tanggal_maintenance']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_sarana']); ?></td>
                <td><?php echo htmlspecialchars($item['nama_lokasi']); ?></td>
                <td><?php echo ucfirst($item['tipe_maintenance']); ?></td>
                <td><?php echo format_rupiah($item['biaya']); ?></td>
                <td>
                    <?php 
                    $badge_class = match($item['status']) {
                        'selesai' => 'badge-success',
                        'sedang dikerjakan' => 'badge-warning',
                        'rencana' => 'badge-info',
                        default => 'badge-secondary'
                    };
                    ?>
                    <span class="badge <?php echo $badge_class; ?>">
                        <?php echo ucfirst($item['status']); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($item['nama_lengkap'] ?? '-'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #666; padding: 20px;">Tidak ada data perbaikan untuk periode ini</p>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>