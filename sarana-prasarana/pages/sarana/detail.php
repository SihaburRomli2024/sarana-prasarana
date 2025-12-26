<?php
// FILE: pages/sarana/detail.php
// Halaman Detail Sarana Prasarana

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil ID dari URL
$id_sarana = intval($_GET['id'] ?? 0);

if ($id_sarana == 0) {
    header('Location: index.php');
    exit;
}

// Ambil data sarana dengan JOIN
$sarana = fetch_row($koneksi, "
    SELECT s.*, k.nama_kategori, l.nama_lokasi, kond.nama_kondisi
    FROM sarana_prasarana s
    JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    JOIN kondisi_sarana kond ON s.id_kondisi = kond.id_kondisi
    WHERE s.id_sarana = $id_sarana
");

if (!$sarana) {
    set_alert('danger', 'Sarana tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// Ambil riwayat maintenance
$maintenance_list = fetch_all($koneksi, "
    SELECT m.*, u.nama_lengkap
    FROM maintenance m
    LEFT JOIN users u ON m.id_user = u.id_user
    WHERE m.id_sarana = $id_sarana
    ORDER BY m.tanggal_maintenance DESC
");
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><?php echo htmlspecialchars($sarana['nama_sarana']); ?></h1>
            <p>Nomor Inventaris: <strong><?php echo htmlspecialchars($sarana['nomor_inventaris'] ?? '-'); ?></strong></p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="edit.php?id=<?php echo $sarana['id_sarana']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <!-- Foto & Info Dasar -->
    <div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <?php if (!empty($sarana['foto'])): ?>
            <img src="../../uploads/foto_sarana/<?php echo htmlspecialchars($sarana['foto']); ?>" 
                 alt="Foto Sarana" style="width: 100%; border-radius: 5px; margin-bottom: 20px;">
            <?php else: ?>
            <div style="width: 100%; height: 200px; background: #f0f0f0; border-radius: 5px; 
                        display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <i class="fas fa-image" style="font-size: 48px; color: #ccc;"></i>
            </div>
            <?php endif; ?>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                <h4 style="margin-top: 0;">Kondisi</h4>
                <?php 
                $badge_class = match($sarana['id_kondisi']) {
                    1 => 'badge-success',
                    2 => 'badge-warning',
                    3 => 'badge-danger',
                    default => 'badge-secondary'
                };
                ?>
                <span class="badge <?php echo $badge_class; ?>" style="font-size: 14px; padding: 8px 15px;">
                    <?php echo htmlspecialchars($sarana['nama_kondisi']); ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Informasi Detail -->
    <div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <h3>Informasi Sarana</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <p style="margin: 0 0 5px 0; color: #666; font-size: 12px; text-transform: uppercase;">Kategori</p>
                    <p style="margin: 0; font-weight: 600;"><?php echo htmlspecialchars($sarana['nama_kategori']); ?></p>
                </div>
                
                <div>
                    <p style="margin: 0 0 5px 0; color: #666; font-size: 12px; text-transform: uppercase;">Lokasi</p>
                    <p style="margin: 0; font-weight: 600;"><?php echo htmlspecialchars($sarana['nama_lokasi']); ?></p>
                </div>
                
                <div>
                    <p style="margin: 0 0 5px 0; color: #666; font-size: 12px; text-transform: uppercase;">Tanggal Perolehan</p>
                    <p style="margin: 0; font-weight: 600;">
                        <?php echo $sarana['tanggal_perolehan'] ? format_tanggal($sarana['tanggal_perolehan']) : '-'; ?>
                    </p>
                </div>
                
                <div>
                    <p style="margin: 0 0 5px 0; color: #666; font-size: 12px; text-transform: uppercase;">Harga Perolehan</p>
                    <p style="margin: 0; font-weight: 600;">
                        <?php echo $sarana['harga_perolehan'] ? format_rupiah($sarana['harga_perolehan']) : '-'; ?>
                    </p>
                </div>
            </div>
            
            <?php if (!empty($sarana['deskripsi'])): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <h4 style="margin: 0 0 10px 0;">Deskripsi</h4>
                <p style="margin: 0; color: #666;"><?php echo htmlspecialchars($sarana['deskripsi']); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($sarana['spesifikasi'])): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <h4 style="margin: 0 0 10px 0;">Spesifikasi</h4>
                <p style="margin: 0; color: #666; white-space: pre-wrap;">
                    <?php echo htmlspecialchars($sarana['spesifikasi']); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Riwayat Maintenance -->
<div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;">Riwayat Pemeliharaan</h3>
        <a href="../maintenance/tambah.php?id_sarana=<?php echo $id_sarana; ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Maintenance
        </a>
    </div>
    
    <?php if (count($maintenance_list) > 0): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Deskripsi</th>
                <th>Biaya</th>
                <th>Status</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maintenance_list as $maint): ?>
            <tr>
                <td><?php echo format_tanggal($maint['tanggal_maintenance']); ?></td>
                <td><?php echo ucfirst($maint['tipe_maintenance']); ?></td>
                <td><?php echo htmlspecialchars(potong_teks($maint['deskripsi_perbaikan'] ?? '', 50)); ?></td>
                <td><?php echo $maint['biaya'] ? format_rupiah($maint['biaya']) : '-'; ?></td>
                <td>
                    <?php 
                    $status_class = match($maint['status']) {
                        'selesai' => 'badge-success',
                        'sedang dikerjakan' => 'badge-warning',
                        'rencana' => 'badge-info',
                        default => 'badge-secondary'
                    };
                    ?>
                    <span class="badge <?php echo $status_class; ?>">
                        <?php echo ucfirst($maint['status']); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($maint['nama_lengkap'] ?? '-'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align: center; padding: 30px; color: #666;">
        <p>Belum ada riwayat pemeliharaan</p>
    </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>