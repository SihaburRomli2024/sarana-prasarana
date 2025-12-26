
<?php
// FILE: pages/maintenance/index.php
// Halaman Daftar Maintenance / Pemeliharaan (Bootstrap 5)

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

$search         = sanitasi($_GET['search'] ?? '');
$status_filter  = $_GET['status'] ?? '';
$sarana_filter  = $_GET['sarana'] ?? '';
$bulan_filter   = $_GET['bulan'] ?? '';

$where = "WHERE 1=1";

if ($search) {
    $where .= " AND (s.nama_sarana LIKE '%" . escape($koneksi, $search) . "%'
                 OR m.deskripsi_perbaikan LIKE '%" . escape($koneksi, $search) . "%')";
}
if ($status_filter) {
    $where .= " AND m.status = '" . escape($koneksi, $status_filter) . "'";
}
if ($sarana_filter) {
    $where .= " AND m.id_sarana = " . intval($sarana_filter);
}
if ($bulan_filter) {
    $where .= " AND DATE_FORMAT(m.tanggal_maintenance, '%Y-%m') = '" . escape($koneksi, $bulan_filter) . "'";
}

$query = "
    SELECT m.*, s.nama_sarana, l.nama_lokasi, u.nama_lengkap
    FROM maintenance m
    JOIN sarana_prasarana s ON m.id_sarana = s.id_sarana
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    LEFT JOIN users u ON m.id_user = u.id_user
    $where
    ORDER BY m.tanggal_maintenance DESC
";

$maintenance_list = fetch_all($koneksi, $query);
$sarana_list      = fetch_all($koneksi, "SELECT id_sarana, nama_sarana FROM sarana_prasarana ORDER BY nama_sarana");

$total_maintenance = count($maintenance_list);
$total_rencana    = count_rows($koneksi, "SELECT id_maintenance FROM maintenance WHERE status='rencana'");
$total_proses     = count_rows($koneksi, "SELECT id_maintenance FROM maintenance WHERE status='sedang dikerjakan'");
$total_selesai    = count_rows($koneksi, "SELECT id_maintenance FROM maintenance WHERE status='selesai'");
?>
<?php
// SEMBUNYIKAN SIDEBAR & TOPBAR
$hideSidebar = true;
$hideTopbar  = true;
?>

<?php include '../../includes/header.php'; ?>

<div class="container-fluid">

  <!-- Header -->
 <div class="d-flex justify-content-between align-items-center mb-4"> 
    <div>
      <h4 class="fw-bold mb-2">Pemeliharaan Sarana</h4>
      <small class="text-muted">Total: <strong><?= $total_maintenance ?></strong> data</small>
    </div>
    <div class="d-flex gap-2">
        <a href="../dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Batal
        </a>
        <a href="tambah.php" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Maintenance
        </a>
    </div>
  </div>
          <style>
        .sidebar,
        .navbar,
        .topbar {
          display: none !important;
        }
        .main-content {
          margin-left: 0 !important;
        }
        </style>


  <?php show_alert(); ?>

  <!-- Statistik -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body d-flex align-items-center">
          <span class="badge bg-info p-3 me-3"><i class="fas fa-list"></i></span>
          <div>
            <small class="text-muted">Rencana</small>
            <h5 class="mb-0 fw-bold"><?= $total_rencana ?></h5>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body d-flex align-items-center">
          <span class="badge bg-warning p-3 me-3"><i class="fas fa-spinner"></i></span>
          <div>
            <small class="text-muted">Sedang Dikerjakan</small>
            <h5 class="mb-0 fw-bold"><?= $total_proses ?></h5>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body d-flex align-items-center">
          <span class="badge bg-success p-3 me-3"><i class="fas fa-check"></i></span>
          <div>
            <small class="text-muted">Selesai</small>
            <h5 class="mb-0 fw-bold"><?= $total_selesai ?></h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="GET">
        <div class="row g-3">
          <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Cari sarana / deskripsi" value="<?= htmlspecialchars($search) ?>">
          </div>
          <div class="col-md-3">
            <select name="sarana" class="form-select">
              <option value="">Semua Sarana</option>
              <?php foreach ($sarana_list as $sar): ?>
                <option value="<?= $sar['id_sarana'] ?>" <?= $sarana_filter == $sar['id_sarana'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($sar['nama_sarana']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <select name="status" class="form-select">
              <option value="">Semua Status</option>
              <option value="rencana" <?= $status_filter=='rencana'?'selected':'' ?>>Rencana</option>
              <option value="sedang dikerjakan" <?= $status_filter=='sedang dikerjakan'?'selected':'' ?>>Proses</option>
              <option value="selesai" <?= $status_filter=='selesai'?'selected':'' ?>>Selesai</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="month" name="bulan" class="form-control" value="<?= htmlspecialchars($bulan_filter) ?>">
          </div>
          <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
            <a href="index.php" class="btn btn-secondary w-100"><i class="fas fa-undo"></i></a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Table -->
  <div class="card shadow-sm">
    <div class="card-body table-responsive">
      <?php if ($maintenance_list): ?>
      <table class="table table-hover align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Sarana</th>
            <th>Lokasi</th>
            <th>Tipe</th>
            <th>Biaya</th>
            <th>Status</th>
            <th>Petugas</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($maintenance_list as $i => $m): ?>
          <tr>
            <td class="text-center"><?= $i+1 ?></td>
            <td><?= format_tanggal($m['tanggal_maintenance']) ?></td>
            <td><?= htmlspecialchars($m['nama_sarana']) ?></td>
            <td><?= htmlspecialchars($m['nama_lokasi']) ?></td>
            <td><?= ucfirst($m['tipe_maintenance']) ?></td>
            <td><?= $m['biaya'] ? format_rupiah($m['biaya']) : '-' ?></td>
            <td class="text-center">
              <?php $bg = match($m['status']){
                'rencana'=>'info','sedang dikerjakan'=>'warning','selesai'=>'success',default=>'secondary'}; ?>
              <span class="badge bg-<?= $bg ?>"><?= ucfirst($m['status']) ?></span>
            </td>
            <td><?= htmlspecialchars($m['nama_lengkap'] ?? '-') ?></td>
            <td class="text-center">
              <a href="edit.php?id=<?= $m['id_maintenance'] ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
              <a href="hapus.php?id=<?= $m['id_maintenance'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <div class="text-center text-muted py-5">Tidak ada data maintenance</div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php
$hideSidebar = true;
$hideTopbar  = true;
include '../../includes/header.php';
?>


