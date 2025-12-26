<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit;
}


$hideSidebar = true;
$hideTopbar  = true; 
include '../../includes/header.php';
?>
 </button>
    <a href="../dashboard.php" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
<div class="container">

    <div class="page-header mb-4">
        <h4 class="fw-bold">
            <i class="fas fa-file-alt"></i> Menu Laporan
        </h4>
        <p>Pilih jenis laporan yang ingin ditampilkan atau dicetak</p>
    </div>

    <div class="row">

        <!-- LAPORAN KONDISI -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                    <h6 class="fw-bold">Kondisi Sarana</h6>
                    <p class="small text-muted">Baik, Rusak Ringan, Rusak Berat</p>
                    <a href="kondisi.php" class="btn btn-sm btn-success w-100">
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <!-- LAPORAN STATISTIK -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                    <h6 class="fw-bold">Statistik</h6>
                    <p class="small text-muted">Grafik sarana & kondisi</p>
                    <a href="statistik.php" class="btn btn-sm btn-primary w-100">
                        Lihat Statistik
                    </a>
                </div>
            </div>
        </div>

        <!-- LAPORAN PERBAIKAN -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-tools fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold">Perbaikan</h6>
                    <p class="small text-muted">Riwayat maintenance</p>
                    <a href="perbaikan.php" class="btn btn-sm btn-warning w-100">
                        Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
        
        <!-- EXPORT -->
        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="fas fa-file-export fa-3x text-danger mb-3"></i>
                    <h6 class="fw-bold">Export</h6>
                    <p class="small text-muted">PDF / Excel</p>
                    <a href="export.php" class="btn btn-sm btn-danger w-100">
                        Export Data
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
<?php
$hideSidebar = true;
$hideTopbar  = true;
?>
<?php include '../../includes/footer.php'; ?>
