<?php
// FILE: pages/laporan/export.php
// Export Laporan ke Excel & PDF

session_start();
require_once '../../config/koneksi.php';
require_once '../../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil tipe export
$tipe = $_GET['tipe'] ?? '';
$laporan = $_GET['laporan'] ?? '';

// ============================================
// EXPORT KONDISI SARANA
// ============================================

if ($laporan == 'kondisi' && $tipe == 'excel') {
    // Ambil filter
    $kategori_filter = $_GET['kategori'] ?? '';
    $lokasi_filter = $_GET['lokasi'] ?? '';
    
    $where = "WHERE 1=1";
    if (!empty($kategori_filter)) {
        $where .= " AND s.id_kategori = " . intval($kategori_filter);
    }
    if (!empty($lokasi_filter)) {
        $where .= " AND s.id_lokasi = " . intval($lokasi_filter);
    }
    
    // Ambil data
    $sarana_baik = fetch_all($koneksi, "
        SELECT s.*, k.nama_kategori, l.nama_lokasi
        FROM sarana_prasarana s
        JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
        JOIN lokasi l ON s.id_lokasi = l.id_lokasi
        $where AND s.id_kondisi = 1
        ORDER BY s.nama_sarana
    ");
    
    $sarana_rusak_ringan = fetch_all($koneksi, "
        SELECT s.*, k.nama_kategori, l.nama_lokasi
        FROM sarana_prasarana s
        JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
        JOIN lokasi l ON s.id_lokasi = l.id_lokasi
        $where AND s.id_kondisi = 2
        ORDER BY s.nama_sarana
    ");
    
    $sarana_rusak_berat = fetch_all($koneksi, "
        SELECT s.*, k.nama_kategori, l.nama_lokasi
        FROM sarana_prasarana s
        JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
        JOIN lokasi l ON s.id_lokasi = l.id_lokasi
        $where AND s.id_kondisi = 3
        ORDER BY s.nama_sarana
    ");
    
    // Generate Excel
    $filename = 'Laporan_Kondisi_Sarana_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM untuk Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
    // Header
    fputcsv($output, ['LAPORAN KONDISI SARANA', '', '', '', '', '', '']);
    fputcsv($output, ['Tanggal:', date('d-m-Y'), '', '', '', '', '']);
    fputcsv($output, ['', '', '', '', '', '', '']);
    
    // Sarana Baik
    fputcsv($output, ['SARANA DALAM KONDISI BAIK', '', '', '', '', '', '']);
    fputcsv($output, ['No', 'Nama Sarana', 'Kategori', 'Lokasi', 'No. Inventaris', 'Tanggal Perolehan', 'Harga']);
    
    $no = 1;
    foreach ($sarana_baik as $item) {
        fputcsv($output, [
            $no++,
            $item['nama_sarana'],
            $item['nama_kategori'],
            $item['nama_lokasi'],
            $item['nomor_inventaris'] ?? '-',
            $item['tanggal_perolehan'] ?? '-',
            $item['harga_perolehan'] ?? '-'
        ]);
    }
    
    fputcsv($output, ['', '', '', '', '', '', '']);
    
    // Sarana Rusak Ringan
    fputcsv($output, ['SARANA DALAM KONDISI RUSAK RINGAN', '', '', '', '', '', '']);
    fputcsv($output, ['No', 'Nama Sarana', 'Kategori', 'Lokasi', 'No. Inventaris', 'Tanggal Perolehan', 'Harga']);
    
    $no = 1;
    foreach ($sarana_rusak_ringan as $item) {
        fputcsv($output, [
            $no++,
            $item['nama_sarana'],
            $item['nama_kategori'],
            $item['nama_lokasi'],
            $item['nomor_inventaris'] ?? '-',
            $item['tanggal_perolehan'] ?? '-',
            $item['harga_perolehan'] ?? '-'
        ]);
    }
    
    fputcsv($output, ['', '', '', '', '', '', '']);
    
    // Sarana Rusak Berat
    fputcsv($output, ['SARANA DALAM KONDISI RUSAK BERAT', '', '', '', '', '', '']);
    fputcsv($output, ['No', 'Nama Sarana', 'Kategori', 'Lokasi', 'No. Inventaris', 'Tanggal Perolehan', 'Harga']);
    
    $no = 1;
    foreach ($sarana_rusak_berat as $item) {
        fputcsv($output, [
            $no++,
            $item['nama_sarana'],
            $item['nama_kategori'],
            $item['nama_lokasi'],
            $item['nomor_inventaris'] ?? '-',
            $item['tanggal_perolehan'] ?? '-',
            $item['harga_perolehan'] ?? '-'
        ]);
    }
    
    fclose($output);
    exit;
}

// ============================================
// EXPORT PERBAIKAN
// ============================================

if ($laporan == 'perbaikan' && $tipe == 'excel') {
    // Ambil filter
    $bulan_filter = $_GET['bulan'] ?? date('Y-m');
    $status_filter = $_GET['status'] ?? '';
    
    $where = "WHERE DATE_FORMAT(m.tanggal_maintenance, '%Y-%m') = '$bulan_filter'";
    if (!empty($status_filter)) {
        $where .= " AND m.status = '" . escape($koneksi, $status_filter) . "'";
    }
    
    // Ambil data
    $maintenance_list = fetch_all($koneksi, "
        SELECT m.*, s.nama_sarana, l.nama_lokasi, u.nama_lengkap
        FROM maintenance m
        JOIN sarana_prasarana s ON m.id_sarana = s.id_sarana
        JOIN lokasi l ON s.id_lokasi = l.id_lokasi
        LEFT JOIN users u ON m.id_user = u.id_user
        $where
        ORDER BY m.tanggal_maintenance DESC
    ");
    
    // Generate Excel
    $filename = 'Laporan_Perbaikan_' . $bulan_filter . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
    // Header
    fputcsv($output, ['LAPORAN PERBAIKAN & PEMELIHARAAN SARANA', '', '', '', '', '', '', '']);
    fputcsv($output, ['Bulan:', date('F Y', strtotime($bulan_filter . '-01')), '', '', '', '', '', '']);
    fputcsv($output, ['Tanggal Export:', date('d-m-Y H:i:s'), '', '', '', '', '', '']);
    fputcsv($output, ['', '', '', '', '', '', '', '']);
    
    // Data detail
    fputcsv($output, ['No', 'Tanggal', 'Sarana', 'Lokasi', 'Tipe', 'Biaya', 'Status', 'Petugas']);
    
    $no = 1;
    $total_biaya = 0;
    foreach ($maintenance_list as $item) {
        $total_biaya += $item['biaya'];
        fputcsv($output, [
            $no++,
            $item['tanggal_maintenance'],
            $item['nama_sarana'],
            $item['nama_lokasi'],
            $item['tipe_maintenance'],
            $item['biaya'],
            $item['status'],
            $item['nama_lengkap'] ?? '-'
        ]);
    }
    
    fputcsv($output, ['', '', '', '', 'TOTAL BIAYA:', $total_biaya, '', '']);
    
    fclose($output);
    exit;
}

// Jika tidak ada parameter yang sesuai
header('Location: kondisi.php');
exit;
?>