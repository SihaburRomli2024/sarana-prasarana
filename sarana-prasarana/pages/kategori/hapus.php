<?php
// FILE: pages/kategori/hapus.php
// Proses Hapus Kategori

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
$id_kategori = intval($_GET['id'] ?? 0);

if ($id_kategori == 0) {
    set_alert('danger', 'ID kategori tidak valid!');
    header('Location: index.php');
    exit;
}

// Ambil data kategori
$kategori = fetch_row($koneksi, "SELECT * FROM kategori_sarana WHERE id_kategori = $id_kategori");

if (!$kategori) {
    set_alert('danger', 'Kategori tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// Cek apakah ada sarana dengan kategori ini
$jumlah_sarana = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_kategori = $id_kategori");

if ($jumlah_sarana > 0) {
    set_alert('danger', 'Kategori tidak bisa dihapus karena masih ada sarana!');
    header('Location: index.php');
    exit;
}

// Hapus dari database
$query = "DELETE FROM kategori_sarana WHERE id_kategori = $id_kategori";

if (mysqli_query($koneksi, $query)) {
    set_alert('success', 'Kategori berhasil dihapus!');
} else {
    set_alert('danger', 'Gagal menghapus kategori: ' . mysqli_error($koneksi));
}

// Redirect ke halaman daftar
header('Location: index.php');
exit;
?>

<?php
// FILE: pages/lokasi/index.php
// Halaman Daftar Lokasi

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
    set_alert('danger', 'Anda tidak memiliki akses ke halaman ini!');
    header('Location: ../dashboard.php');
    exit;
}

// Ambil parameter search
$search = sanitasi($_GET['search'] ?? '');

// Build query dengan filter
$where = "WHERE 1=1";

if (!empty($search)) {
    $where .= " AND nama_lokasi LIKE '%" . escape($koneksi, $search) . "%'";
}

// Query ambil data lokasi
$lokasi_list = fetch_all($koneksi, "SELECT * FROM lokasi $where ORDER BY nama_lokasi");

// Hitung total
$total_lokasi = count($lokasi_list);
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Lokasi/Ruangan</h1>
            <p>Total: <strong><?php echo $total_lokasi; ?></strong> lokasi</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Lokasi
        </a>
    </div>
</div>

<?php show_alert(); ?>

<!-- Filter Section -->
<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <form method="GET" action="" class="filter-form">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label for="search">Cari Lokasi</label>
                <input type="text" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nama lokasi...">
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Lokasi -->
<div class="table-section">
    <?php if (count($lokasi_list) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lokasi</th>
                <th>Tipe Ruangan</th>
                <th>Keterangan</th>
                <th>Jumlah Sarana</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lokasi_list as $idx => $item): ?>
            <?php 
            // Hitung jumlah sarana per lokasi
            $jumlah_sarana = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_lokasi = " . $item['id_lokasi']);
            ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><strong><?php echo htmlspecialchars($item['nama_lokasi']); ?></strong></td>
                <td><?php echo htmlspecialchars($item['tipe_ruangan'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars(potong_teks($item['keterangan'] ?? '-', 40)); ?></td>
                <td>
                    <span class="badge badge-info">
                        <?php echo $jumlah_sarana; ?> sarana
                    </span>
                </td>
                <td>
                    <a href="edit.php?id=<?php echo $item['id_lokasi']; ?>" 
                       class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <?php if ($jumlah_sarana == 0): ?>
                    <a href="hapus.php?id=<?php echo $item['id_lokasi']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirmDelete(<?php echo $item['id_lokasi']; ?>, '<?php echo htmlspecialchars($item['nama_lokasi']); ?>');"
                       title="Hapus">
                        <i class="fas fa-trash"></i>
                    </a>
                    <?php else: ?>
                    <button class="btn btn-sm btn-secondary" disabled title="Tidak bisa dihapus (ada sarana)">
                        <i class="fas fa-trash"></i>
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align: center; padding: 40px;">
        <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
        <p style="color: #666; font-size: 16px;">Tidak ada data lokasi</p>
        <a href="tambah.php" class="btn btn-primary" style="margin-top: 15px;">Tambah Lokasi Baru</a>
    </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>