<?php
// FILE: pages/user/index.php
// Halaman Daftar User

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Cek role (hanya admin yang bisa akses)
if ($_SESSION['role'] != 'admin') {
    set_alert('danger', 'Anda tidak memiliki akses ke halaman ini!');
    header('Location: ../dashboard.php');
    exit;
}

// Ambil parameter search dan filter
$search = sanitasi($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query dengan filter
$where = "WHERE 1=1";

if (!empty($search)) {
    $where .= " AND (nama_lengkap LIKE '%" . escape($koneksi, $search) . "%' 
                     OR email LIKE '%" . escape($koneksi, $search) . "%'
                     OR username LIKE '%" . escape($koneksi, $search) . "%')";
}

if (!empty($role_filter)) {
    $where .= " AND role = '" . escape($koneksi, $role_filter) . "'";
}

if (!empty($status_filter)) {
    $where .= " AND status = '" . escape($koneksi, $status_filter) . "'";
}

// Query ambil data user
$user_list = fetch_all($koneksi, "SELECT * FROM users $where ORDER BY nama_lengkap");

// Hitung total
$total_user = count($user_list);
$total_admin = count_rows($koneksi, "SELECT id_user FROM users WHERE role = 'admin'");
$total_guru = count_rows($koneksi, "SELECT id_user FROM users WHERE role = 'guru'");
$total_staff = count_rows($koneksi, "SELECT id_user FROM users WHERE role = 'staff'");
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Kelola User</h1>
            <p>Total: <strong><?php echo $total_user; ?></strong> user</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah User
        </a>
    </div>
</div>

<?php show_alert(); ?>

<!-- Statistik -->
<div class="stats-grid" style="margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-icon bg-blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3>Total User</h3>
            <p class="stat-number"><?php echo $total_user; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-purple">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-content">
            <h3>Admin</h3>
            <p class="stat-number"><?php echo $total_admin; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-green">
            <i class="fas fa-chalkboard-user"></i>
        </div>
        <div class="stat-content">
            <h3>Guru</h3>
            <p class="stat-number"><?php echo $total_guru; ?></p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-orange">
            <i class="fas fa-toolbox"></i>
        </div>
        <div class="stat-content">
            <h3>Staff</h3>
            <p class="stat-number"><?php echo $total_staff; ?></p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <form method="GET" action="" class="filter-form">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label for="search">Cari User</label>
                <input type="text" id="search" name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Nama, email, atau username...">
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" <?php echo $role_filter == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="guru" <?php echo $role_filter == 'guru' ? 'selected' : ''; ?>>Guru</option>
                    <option value="staff" <?php echo $role_filter == 'staff' ? 'selected' : ''; ?>>Staff</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="aktif" <?php echo $status_filter == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="nonaktif" <?php echo $status_filter == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset
            </a>
            <button type="button" class="btn btn-info" onclick="exportTableToExcel('tabelUser', 'data_user.xlsx')">
                <i class="fas fa-download"></i> Export Excel
            </button>
        </div>
    </form>
</div>

<!-- Tabel User -->
<div class="table-section">
    <?php if (count($user_list) > 0): ?>
    <table class="table table-striped" id="tabelUser">
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($user_list as $idx => $item): ?>
            <tr>
                <td><?php echo $idx + 1; ?></td>
                <td><strong><?php echo htmlspecialchars($item['username']); ?></strong></td>
                <td><?php echo htmlspecialchars($item['nama_lengkap']); ?></td>
                <td><?php echo htmlspecialchars($item['email']); ?></td>
                <td>
                    <?php 
                    $role_badge = match($item['role']) {
                        'admin' => 'badge-danger',
                        'guru' => 'badge-success',
                        'staff' => 'badge-warning',
                        default => 'badge-secondary'
                    };
                    ?>
                    <span class="badge <?php echo $role_badge; ?>">
                        <?php echo ucfirst($item['role']); ?>
                    </span>
                </td>
                <td>
                    <?php 
                    $status_badge = $item['status'] == 'aktif' ? 'badge-success' : 'badge-danger';
                    ?>
                    <span class="badge <?php echo $status_badge; ?>">
                        <?php echo ucfirst($item['status']); ?>
                    </span>
                </td>
                <td><?php echo format_tanggal($item['tanggal_dibuat']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $item['id_user']; ?>" 
                       class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <?php if ($item['id_user'] != $_SESSION['id_user']): ?>
                    <a href="hapus.php?id=<?php echo $item['id_user']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirmDelete(<?php echo $item['id_user']; ?>, '<?php echo htmlspecialchars($item['username']); ?>');"
                       title="Hapus">
                        <i class="fas fa-trash"></i>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align: center; padding: 40px;">
        <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
        <p style="color: #666; font-size: 16px;">Tidak ada data user</p>
        <a href="tambah.php" class="btn btn-primary" style="margin-top: 15px;">Tambah User Baru</a>
    </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>