<?php
// FILE: pages/kategori/index.php
// Halaman Daftar Kategori Sarana

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
    $where .= " AND nama_kategori LIKE '%" . escape($koneksi, $search) . "%'";
}

// Query ambil data kategori
$kategori_list = fetch_all($koneksi, "SELECT * FROM kategori_sarana $where ORDER BY nama_kategori");

// Hitung total
$total_kategori = count($kategori_list);
?>
<?php
// SEMBUNYIKAN SIDEBAR & TOPBAR
$hideSidebar = true;
$hideTopbar  = true;
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Kategori Sarana</h1>
            <p>Total: <strong><?php echo $total_kategori; ?></strong> kategori</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    </div>
</div>

<?php show_alert(); ?>

<!-- Filter Section -->
<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <form method="GET" action="" class="filter-form">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label for="search">Cari Kategori</label>
                <input type="text" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nama kategori...">
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
            <a href="../dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            
        </div>
    </form>
</div>

<!-- Tabel Kategori -->
<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">
            <?php if (count($kategori_list) > 0): ?>
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                        <th width="15%">Jumlah Sarana</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategori_list as $idx => $item): ?>
                    <?php
                        $jumlah_sarana = count_rows(
                            $koneksi,
                            "SELECT id_sarana FROM sarana_prasarana 
                             WHERE id_kategori = " . $item['id_kategori']
                        );
                    ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1; ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($item['nama_kategori']); ?></td>
                        <td><?= htmlspecialchars(potong_teks($item['keterangan'] ?? '-', 50)); ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary"><?= $jumlah_sarana; ?></span>
                        </td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $item['id_kategori']; ?>" 
                               class="btn btn-sm btn-warning me-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <?php if ($jumlah_sarana == 0): ?>
                            <a href="hapus.php?id=<?= $item['id_kategori']; ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirmDelete(
                                   <?= $item['id_kategori']; ?>,
                                   '<?= htmlspecialchars($item['nama_kategori']); ?>'
                               );"
                               title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled
                                    title="Tidak bisa dihapus (ada sarana)">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">Tidak ada data kategori</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
<?php include '../../includes/footer.php'; ?>