<?php
// FILE: config/functions.php
// Fungsi-fungsi umum yang digunakan di seluruh aplikasi

// ============================================
// FUNGSI VALIDASI & SANITASI
// ============================================

/**
 * Sanitasi input data
 * Menghilangkan karakter berbahaya
 */
function sanitasi($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validasi input tidak boleh kosong
 */
function validasi_tidak_kosong($data) {
    if (empty($data) || trim($data) == '') {
        return false;
    }
    return true;
}

/**
 * Validasi email
 */
function validasi_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validasi tanggal format YYYY-MM-DD
 */
function validasi_tanggal($tanggal) {
    $pattern = '/^\d{4}-\d{2}-\d{2}$/';
    return preg_match($pattern, $tanggal);
}

// ============================================
// FUNGSI KEAMANAN & AUTENTIKASI
// ============================================

/**
 * Hash password menggunakan bcrypt
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verifikasi password
 */
function verifikasi_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate token CSRF untuk keamanan form
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi token CSRF
 */
function validasi_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// ============================================
// FUNGSI DATABASE
// ============================================

/**
 * Query SELECT
 */
function query_select($koneksi, $query) {
    $result = mysqli_query($koneksi, $query);
    if (!$result) {
        die("Query Error: " . mysqli_error($koneksi));
    }
    return $result;
}

/**
 * Query INSERT/UPDATE/DELETE
 */
function query_execute($koneksi, $query) {
    $result = mysqli_query($koneksi, $query);
    if (!$result) {
        die("Query Error: " . mysqli_error($koneksi));
    }
    return $result;
}

/**
 * Ambil satu baris data
 */
function fetch_row($koneksi, $query) {
    $result = query_select($koneksi, $query);
    return mysqli_fetch_assoc($result);
}

/**
 * Ambil semua baris data
 */
function fetch_all($koneksi, $query) {
    $result = query_select($koneksi, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Hitung jumlah baris
 */
function count_rows($koneksi, $query) {
    $result = query_select($koneksi, $query);
    return mysqli_num_rows($result);
}

/**
 * Escape string untuk keamanan SQL
 */
function escape($koneksi, $data) {
    return mysqli_real_escape_string($koneksi, $data);
}

// ============================================
// FUNGSI NOTIFIKASI
// ============================================

/**
 * Set notifikasi alert
 */
function set_alert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,  // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Tampilkan notifikasi
 */
function show_alert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        $type = $alert['type'];
        $message = $alert['message'];
        
        echo "<div class='alert alert-$type' role='alert'>
                $message
              </div>";
        
        unset($_SESSION['alert']);
    }
}

// ============================================
// FUNGSI UPLOAD FILE
// ============================================

/**
 * Upload dan validasi file gambar
 */
function upload_gambar($file, $folder = 'uploads/foto_sarana/') {
    $nama_file = $file['name'];
    $ukuran_file = $file['size'];
    $tmp_file = $file['tmp_name'];
    $error = $file['error'];
    
    // Cek error
    if ($error !== 0) {
        return ['status' => false, 'message' => 'Terjadi error saat upload'];
    }
    
    // Cek ukuran file (max 5MB)
    if ($ukuran_file > 5000000) {
        return ['status' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    }
    
    // Cek tipe file
    $tipe_diperbolehkan = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $tipe_diperbolehkan)) {
        return ['status' => false, 'message' => 'Tipe file tidak diperbolehkan'];
    }
    
    // Generate nama file baru
    $nama_baru = time() . '_' . rand(1000, 9999) . '.' . $ext;
    $path_lengkap = $folder . $nama_baru;
    
    // Buat folder jika belum ada
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }
    
    // Pindahkan file
    if (move_uploaded_file($tmp_file, $path_lengkap)) {
        return ['status' => true, 'file' => $nama_baru, 'path' => $path_lengkap];
    } else {
        return ['status' => false, 'message' => 'Gagal upload file'];
    }
}

// ============================================
// FUNGSI FORMAT DATA
// ============================================

/**
 * Format tanggal Indonesia
 */
function format_tanggal($tanggal) {
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    $tanggal_obj = new DateTime($tanggal);
    $day = $tanggal_obj->format('w');
    $date = $tanggal_obj->format('d');
    $month = $tanggal_obj->format('n');
    $year = $tanggal_obj->format('Y');
    
    return $hari[$day] . ', ' . $date . ' ' . $bulan[$month] . ' ' . $year;
}

/**
 * Format mata uang Rupiah
 */
function format_rupiah($nominal) {
    return 'Rp ' . number_format($nominal, 0, ',', '.');
}

/**
 * Potong teks panjang
 */
function potong_teks($teks, $panjang = 100) {
    if (strlen($teks) > $panjang) {
        return substr($teks, 0, $panjang) . '...';
    }
    return $teks;
}

?>