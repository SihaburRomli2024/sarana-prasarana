<?php
// FILE: pages/maintenance/hapus.php
// Proses Hapus Maintenance

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil ID dari URL
$id_maintenance = intval($_GET['id'] ?? 0);

if ($id_maintenance == 0) {
    set_alert('danger', 'ID maintenance tidak valid!');
    header('Location: index.php');
    exit;
}

// Ambil data maintenance
$maintenance = fetch_row($koneksi, "SELECT * FROM maintenance WHERE id_maintenance = $id_maintenance");

if (!$maintenance) {
    set_alert('danger', 'Maintenance tidak ditemukan!');
    header('Location: index.php');
    exit;
}

// Hapus dari database
$query = "DELETE FROM maintenance WHERE id_maintenance = $id_maintenance";

if (mysqli_query($koneksi, $query)) {
    set_alert('success', 'Maintenance berhasil dihapus!');
} else {
    set_alert('danger', 'Gagal menghapus maintenance: ' . mysqli_error($koneksi));
}

// Redirect ke halaman daftar
header('Location: index.php');
exit;
?>