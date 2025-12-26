<?php
// FILE: includes/header.php
// Template Header (Support hide sidebar & topbar)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/functions.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$user_info = [
    'nama_lengkap' => $_SESSION['nama_lengkap'] ?? 'User',
    'role' => $_SESSION['role'] ?? 'user'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Sarana & Prasarana - SMP Bina Insan Mandiri</title>

    <link rel="stylesheet" href="<?php echo dirname(__DIR__); ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <?php if (!isset($hideSidebar)): ?>
    <aside class="sidebar no-print">
        <div class="sidebar-header text-center">
            <h4 class="mb-0">SMP Bina Insan</h4>
            <small>Sarana & Prasarana</small>
        </div>

        <nav class="sidebar-menu mt-3">
            <ul>
                <li>
                    <a href="../pages/dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <li>
                    <a href="../pages/sarana/index.php">
                        <i class="fas fa-box"></i> Data Sarana
                    </a>
                </li>

                <li>
                    <a href="../pages/maintenance/index.php">
                        <i class="fas fa-tools"></i> Pemeliharaan
                    </a>
                </li>

                <li>
                    <a href="../pages/laporan/kondisi.php">
                        <i class="fas fa-file-alt"></i> Laporan
                    </a>
                </li>

                <?php if ($user_info['role'] === 'admin'): ?>
                <li>
                    <a href="../pages/user/index.php">
                        <i class="fas fa-users"></i> Kelola User
                    </a>
                </li>

                <li>
                    <a href="../pages/kategori/index.php">
                        <i class="fas fa-list"></i> Kategori
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>
    <?php endif; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <?php if (!isset($hideTopbar)): ?>
        <div class="topbar no-print d-flex justify-content-between align-items-center">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="user-menu dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="me-2 text-end">
                        <strong><?= htmlspecialchars($user_info['nama_lengkap']); ?></strong><br>
                        <small><?= ucfirst($user_info['role']); ?></small>
                    </span>
                    <img src="https://via.placeholder.com/40" class="rounded-circle" alt="Avatar">
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="../pages/profile.php">
                            <i class="fas fa-user"></i> Profil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="../pages/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- FULL WIDTH JIKA SIDEBAR DISEMBUNYIKAN -->
        <?php if (isset($hideSidebar)): ?>
        <style>
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        </style>
        <?php endif; ?>

        <!-- PAGE CONTENT -->
        <div class="page-content">
