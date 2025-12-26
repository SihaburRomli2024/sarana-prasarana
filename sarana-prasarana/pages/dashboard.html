<?php
// FILE: pages/dashboard.php
// Dashboard dengan Bootstrap 5

session_start();
require_once '../config/koneksi.php';
require_once '../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

// Ambil statistik sarana
$total_sarana = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana");
$total_baik = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_kondisi = 1");
$total_rusak_ringan = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_kondisi = 2");
$total_rusak_berat = count_rows($koneksi, "SELECT id_sarana FROM sarana_prasarana WHERE id_kondisi = 3");

// Ambil statistik maintenance
$total_maintenance = count_rows($koneksi, "SELECT id_maintenance FROM maintenance");
$maintenance_bulan_ini = count_rows($koneksi, "SELECT id_maintenance FROM maintenance WHERE DATE_FORMAT(tanggal_maintenance, '%Y-%m') = '" . date('Y-m') . "'");

// Ambil sarana rusak berat
$sarana_rusak_berat = fetch_all($koneksi, "
    SELECT s.*, k.nama_kategori, l.nama_lokasi
    FROM sarana_prasarana s
    JOIN kategori_sarana k ON s.id_kategori = k.id_kategori
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    WHERE s.id_kondisi = 3
    LIMIT 5
");

// Ambil maintenance terbaru
$maintenance_terbaru = fetch_all($koneksi, "
    SELECT m.*, s.nama_sarana, l.nama_lokasi 
    FROM maintenance m
    JOIN sarana_prasarana s ON m.id_sarana = s.id_sarana
    JOIN lokasi l ON s.id_lokasi = l.id_lokasi
    ORDER BY m.tanggal_maintenance DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sarana Prasarana</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding-top: 80px;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: white !important;
        }

        .navbar-brand i {
            margin-right: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            margin: 0 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        /* Container */
        .container-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .page-header {
            margin-bottom: 3rem;
            animation: fadeIn 0.5s ease-in;
        }

        .page-header h1 {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #718096;
            font-size: 16px;
            margin: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .stat-card.card-green::before {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .stat-card.card-yellow::before {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }

        .stat-card.card-red::before {
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 15px;
        }

        .stat-card .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card.card-green .stat-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-card.card-yellow .stat-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stat-card.card-red .stat-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-percent {
            font-size: 13px;
            color: #a0aec0;
            font-weight: 500;
        }

        /* Alert Box */
        .alert-box {
            background: linear-gradient(135deg, #fef08a 0%, #fef3c7 100%);
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(250, 204, 21, 0.1);
        }

        .alert-box h4 {
            color: #92400e;
            margin-bottom: 10px;
        }

        .alert-box p {
            color: #b45309;
            margin: 0;
            font-size: 15px;
        }

        /* Table */
        .table-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        }

        .table-section h5 {
            font-size: 20px;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(90deg, #f7fafc 0%, #edf2f7 100%);
            border-bottom: 2px solid #e2e8f0;
        }

        .table thead th {
            color: #4a5568;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px;
            border: none;
        }

        .table tbody td {
            padding: 15px;
            color: #2d3748;
            border-color: #e2e8f0;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, #f7fafc 0%, #edf2f7 100%);
            box-shadow: inset 0 0 10px rgba(102, 126, 234, 0.05);
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        /* Badge */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-baik {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-ringan {
            background: #fed7aa;
            color: #92400e;
        }

        .badge-berat {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .badge-rencana {
            background: #dbeafe;
            color: #0c2d6b;
        }

        .badge-dikerjakan {
            background: #fed7aa;
            color: #92400e;
        }

        .badge-selesai {
            background: #d1fae5;
            color: #065f46;
        }

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 12px;
        }

        /* Grafik */
        .chart-container {
            position: relative;
            height: 250px;
            margin-bottom: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-main {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 24px;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .stat-number {
                font-size: 24px;
            }

            .table-section {
                padding: 1.5rem;
                overflow-x: auto;
            }

            .table {
                font-size: 13px;
            }

            .table thead th,
            .table tbody td {
                padding: 10px;
            }

            body {
                padding-top: 60px;
            }

            .navbar {
                padding: 0.75rem 1rem;
            }

            .navbar-brand {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid" style="max-width: 1400px; margin: 0 auto;">
            <a class="navbar-brand" href="#">
                <i class="fas fa-building"></i> Sarana & Prasarana
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sarana/index.php">
                            <i class="fas fa-box"></i> Data Sarana
                        </a>
                    <li>
                    <li class="nav-item">
                        <a class="nav-link"  href="../pages/kategori/index.php" class="menu-item">
                        <i class="fas fa-list"></i> Kategori
                        </a>
                </li>
                   <li>
                    <li class="nav-item">
                    <a class="nav-link" href="../pages/laporan/index.php" class="menu-item">
                        <i class="fas fa-file-alt"></i> Laporan
                    </a>
                </li>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="maintenance/index.php">
                            <i class="fas fa-tools"></i> Pemeliharaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <span style="color: rgba(255, 255, 255, 0.5); margin: 0 15px;">|</span>
                    </li>
                    <li class="nav-item">
                       <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle d-flex align-items-center"
       href="#"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false">

        <i class="fas fa-user-circle me-2"></i>
        <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
    </a>

    <ul class="dropdown-menu dropdown-menu-end shadow">
        <li class="dropdown-header text-muted">
            Login sebagai <strong><?php echo ucfirst($_SESSION['role']); ?></strong>
        </li>

        <li>
            <a class="dropdown-item" href="profile.php">
                <i class="fas fa-id-card me-2"></i> Profil Saya
            </a>
        </li>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li>
            <a class="dropdown-item" href="user/index.php">
                <i class="fas fa-users-cog me-2"></i> Kelola User
            </a>
        </li>
        <?php endif; ?>

        <li><hr class="dropdown-divider"></li>

        <li>
            <a class="dropdown-item text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</li>

                    </li>
                    <li class="nav-item">
                        <!-- <a class="btn btn-logout" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a> -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-main">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-chart-bar"></i> Dashboard</h1>
            <p>Selamat datang di Aplikasi Sarana & Prasarana SMP Bina Insan Mandiri</p>
        </div>

        <!-- Stat Cards -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-label">Total Sarana</div>
                    <div class="stat-number"><?php echo $total_sarana; ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card card-green">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-label">Kondisi Baik</div>
                    <div class="stat-number"><?php echo $total_baik; ?></div>
                    <div class="stat-percent"><?php echo $total_sarana > 0 ? round(($total_baik / $total_sarana) * 100, 1) : 0; ?>%</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card card-yellow">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-label">Rusak Ringan</div>
                    <div class="stat-number"><?php echo $total_rusak_ringan; ?></div>
                    <div class="stat-percent"><?php echo $total_sarana > 0 ? round(($total_rusak_ringan / $total_sarana) * 100, 1) : 0; ?>%</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card card-red">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-label">Rusak Berat</div>
                    <div class="stat-number"><?php echo $total_rusak_berat; ?></div>
                    <div class="stat-percent"><?php echo $total_sarana > 0 ? round(($total_rusak_berat / $total_sarana) * 100, 1) : 0; ?>%</div>
                </div>
            </div>
        </div>

        <!-- Alert -->
        <?php if ($total_rusak_berat > 0): ?>
        <div class="alert-box">
            <h4><i class="fas fa-warning"></i> Perhatian: Sarana Memerlukan Perbaikan</h4>
            <p>Ada <strong><?php echo $total_rusak_berat; ?></strong> sarana dengan kondisi rusak berat. Segera lakukan tindakan perbaikan.</p>
        </div>
        <?php endif; ?>

        <!-- Sarana Rusak Berat Table -->
        <div class="table-section">
            <h5><i class="fas fa-exclamation-circle"></i> Sarana Rusak Berat (Prioritas Tinggi)</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sarana</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($sarana_rusak_berat) > 0): ?>
                            <?php foreach ($sarana_rusak_berat as $idx => $sarana): ?>
                            <tr>
                                <td><?php echo $idx + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($sarana['nama_sarana']); ?></strong></td>
                                <td><?php echo htmlspecialchars($sarana['nama_kategori']); ?></td>
                                <td><?php echo htmlspecialchars($sarana['nama_lokasi']); ?></td>
                                <td><span class="badge badge-berat">Rusak Berat</span></td>
                                <td>
                                    <a href="sarana/detail.php?id=<?php echo $sarana['id_sarana']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #a0aec0;">
                                <i class="fas fa-check-circle" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                Tidak ada sarana dengan kondisi rusak berat
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Maintenance Terbaru Table -->
        <div class="table-section">
            <h5><i class="fas fa-tools"></i> Riwayat Pemeliharaan Terbaru</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Sarana</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($maintenance_terbaru) > 0): ?>
                            <?php foreach ($maintenance_terbaru as $idx => $maint): ?>
                            <tr>
                                <td><?php echo $idx + 1; ?></td>
                                <td><?php echo format_tanggal($maint['tanggal_maintenance']); ?></td>
                                <td><strong><?php echo htmlspecialchars($maint['nama_sarana']); ?></strong></td>
                                <td><?php echo ucfirst($maint['tipe_maintenance']); ?></td>
                                <td>
                                    <?php 
                                    $status_badge = match($maint['status']) {
                                        'rencana' => 'badge-rencana',
                                        'sedang dikerjakan' => 'badge-dikerjakan',
                                        'selesai' => 'badge-selesai',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?php echo $status_badge; ?>">
                                        <?php echo ucfirst($maint['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($maint['nama_lokasi']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #a0aec0;">
                                <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 10px; display: block;"></i>
                                Belum ada riwayat pemeliharaan
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

