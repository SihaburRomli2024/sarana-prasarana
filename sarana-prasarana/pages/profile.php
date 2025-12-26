<?php
// FILE: pages/profile.php
session_start();
require_once '../config/koneksi.php';
require_once '../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

// Ambil data user dari database
$id_user = (int) $_SESSION['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = $id_user");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    die("Data user tidak ditemukan");
}

// Tentukan avatar
$avatar = !empty($user['avatar']) ? $user['avatar'] : 'default.png';


$hideSidebar = true;
$hideTopbar  = true;

include '../includes/header.php';
?>

<div class="container mt-4" style="max-width: 720px;">
    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fas fa-user me-2"></i>
            <h5 class="mb-0">Profil Pengguna</h5>
        </div>

        <div class="card-body">
            <!-- FOTO PROFIL -->
            <div class="text-center mb-4">
                <img src="../uploads/avatars/<?= htmlspecialchars($avatar); ?>"
                     class="rounded-circle shadow"
                     style="width:120px;height:120px;object-fit:cover;"
                     alt="Foto Profil">

                <h5 class="mt-3 mb-0">
                    <?= htmlspecialchars($user['nama_lengkap']); ?>
                </h5>
                <small class="text-muted">
                    @<?= htmlspecialchars($user['username']); ?>
                </small>
            </div>

            <hr>

            <!-- DATA PROFIL -->
            <table class="table table-borderless mb-0">
                <tr>
                    <th width="35%">Nama Lengkap</th>
                    <td><?= htmlspecialchars($user['nama_lengkap']); ?></td>
                </tr>

                <tr>
                    <th>Username</th>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                </tr>

                <tr>
                    <th>Role</th>
                    <td>
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary'; ?>">
                            <?= ucfirst($user['role']); ?>
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Status Akun</th>
                    <td>
                        <span class="badge bg-success">Aktif</span>
                    </td>
                </tr>

                <tr>
                    <th>Tanggal Dibuat</th>
                    <td>
                        <?= !empty($user['created_at']) 
                            ? date('d M Y', strtotime($user['created_at'])) 
                            : '-'; ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card-footer text-end">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
