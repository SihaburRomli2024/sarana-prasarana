<?php
// FILE: pages/sarana/edit.php
// Halaman Edit Sarana Prasarana

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

// Ambil data sarana
$sarana = fetch_row($koneksi, "SELECT * FROM sarana_prasarana WHERE id_sarana = $id_sarana");

if (!$sarana) {
    set_alert('danger', 'Sarana tidak ditemukan!');
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi input
    $nama_sarana = sanitasi($_POST['nama_sarana'] ?? '');
    $id_kategori = intval($_POST['id_kategori'] ?? 0);
    $id_lokasi = intval($_POST['id_lokasi'] ?? 0);
    $id_kondisi = intval($_POST['id_kondisi'] ?? 1);
    $deskripsi = sanitasi($_POST['deskripsi'] ?? '');
    $tanggal_perolehan = $_POST['tanggal_perolehan'] ?? '';
    $nomor_inventaris = sanitasi($_POST['nomor_inventaris'] ?? '');
    $harga_perolehan = str_replace(['Rp ', '.', ','], '', $_POST['harga_perolehan'] ?? '0');
    $spesifikasi = sanitasi($_POST['spesifikasi'] ?? '');
    
    // Validasi input
    $errors = [];
    
    if (!validasi_tidak_kosong($nama_sarana)) {
        $errors[] = 'Nama sarana harus diisi';
    }
    
    if ($id_kategori == 0) {
        $errors[] = 'Kategori harus dipilih';
    }
    
    if ($id_lokasi == 0) {
        $errors[] = 'Lokasi harus dipilih';
    }
    
    if (!empty($tanggal_perolehan) && !validasi_tanggal($tanggal_perolehan)) {
        $errors[] = 'Format tanggal tidak valid (YYYY-MM-DD)';
    }
    
    // Proses upload foto jika ada
    $foto_sarana = $sarana['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_result = upload_gambar($_FILES['foto'], '../../uploads/foto_sarana/');
        if ($upload_result['status']) {
            // Hapus foto lama jika ada
            if (!empty($sarana['foto'])) {
                $old_file = '../../uploads/foto_sarana/' . $sarana['foto'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            $foto_sarana = $upload_result['file'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        // Query update
        $query = "UPDATE sarana_prasarana SET 
                  nama_sarana = '" . escape($koneksi, $nama_sarana) . "',
                  id_kategori = $id_kategori,
                  id_lokasi = $id_lokasi,
                  id_kondisi = $id_kondisi,
                  deskripsi = '" . escape($koneksi, $deskripsi) . "',
                  tanggal_perolehan = " . (!empty($tanggal_perolehan) ? "'" . $tanggal_perolehan . "'" : "NULL") . ",
                  nomor_inventaris = '" . escape($koneksi, $nomor_inventaris) . "',
                  harga_perolehan = $harga_perolehan,
                  spesifikasi = '" . escape($koneksi, $spesifikasi) . "',
                  foto = '" . escape($koneksi, $foto_sarana) . "'
                  WHERE id_sarana = $id_sarana";
        
        if (mysqli_query($koneksi, $query)) {
            set_alert('success', 'Sarana berhasil diperbarui!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal memperbarui sarana: ' . mysqli_error($koneksi);
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Ambil data untuk dropdown
$kategori_list = fetch_all($koneksi, "SELECT * FROM kategori_sarana ORDER BY nama_kategori");
$lokasi_list = fetch_all($koneksi, "SELECT * FROM lokasi ORDER BY nama_lokasi");
$kondisi_list = fetch_all($koneksi, "SELECT * FROM kondisi_sarana");
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <h1>Edit Sarana Prasarana</h1>
    <p>Perbarui data sarana: <?php echo htmlspecialchars($sarana['nama_sarana']); ?></p>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div style="max-width: 800px;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <form method="POST" action="" enctype="multipart/form-data" id="formEditSarana">
            
            <div class="form-group">
                <label for="nama_sarana">Nama Sarana <span style="color: red;">*</span></label>
                <input type="text" id="nama_sarana" name="nama_sarana" 
                       value="<?php echo htmlspecialchars($sarana['nama_sarana']); ?>" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="id_kategori">Kategori <span style="color: red;">*</span></label>
                    <select id="id_kategori" name="id_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori_list as $kat): ?>
                        <option value="<?php echo $kat['id_kategori']; ?>" 
                                <?php echo $sarana['id_kategori'] == $kat['id_kategori'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="id_kondisi">Kondisi <span style="color: red;">*</span></label>
                    <select id="id_kondisi" name="id_kondisi" required>
                        <?php foreach ($kondisi_list as $kond): ?>
                        <option value="<?php echo $kond['id_kondisi']; ?>" 
                                <?php echo $sarana['id_kondisi'] == $kond['id_kondisi'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kond['nama_kondisi']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="id_lokasi">Lokasi <span style="color: red;">*</span></label>
                    <select id="id_lokasi" name="id_lokasi" required>
                        <option value="">-- Pilih Lokasi --</option>
                        <?php foreach ($lokasi_list as $lok): ?>
                        <option value="<?php echo $lok['id_lokasi']; ?>" 
                                <?php echo $sarana['id_lokasi'] == $lok['id_lokasi'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lok['nama_lokasi']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nomor_inventaris">Nomor Inventaris</label>
                    <input type="text" id="nomor_inventaris" name="nomor_inventaris" 
                           value="<?php echo htmlspecialchars($sarana['nomor_inventaris'] ?? ''); ?>">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="tanggal_perolehan">Tanggal Perolehan</label>
                    <input type="date" id="tanggal_perolehan" name="tanggal_perolehan"
                           value="<?php echo htmlspecialchars($sarana['tanggal_perolehan'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="harga_perolehan">Harga Perolehan (Rp)</label>
                    <input type="text" id="harga_perolehan" name="harga_perolehan" 
                           value="<?php echo htmlspecialchars($sarana['harga_perolehan'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi"><?php echo htmlspecialchars($sarana['deskripsi'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="spesifikasi">Spesifikasi</label>
                <textarea id="spesifikasi" name="spesifikasi"><?php echo htmlspecialchars($sarana['spesifikasi'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="foto">Foto Sarana</label>
                <?php if (!empty($sarana['foto'])): ?>
                <div style="margin-bottom: 15px;">
                    <img src="../../uploads/foto_sarana/<?php echo htmlspecialchars($sarana['foto']); ?>" 
                         alt="Foto Sarana" style="max-width: 200px; border-radius: 5px;">
                </div>
                <?php endif; ?>
                <input type="file" id="foto" name="foto" accept="image/*">
                <small style="color: #666;">Format: JPG, PNG, GIF (Max 5MB)</small>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>