<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SI-SARPRAS | Sistem Informasi Sarana & Prasarana</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero { background: linear-gradient(135deg, #0d6efd, #084298); color: #fff; }
        .stat-card { border-left: 5px solid #0d6efd; }
        footer { background: #f8f9fa; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="#">SI-SARPRAS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#profil">Profil Sistem</a></li>
                <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                <li class="nav-item"><a class="nav-link" href="#statistik">Statistik</a></li>
                <li class="nav-item"><a class="btn btn-light btn-sm ms-2" href="../pages/login.php">Masuk</a></li>

            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<section id="beranda" class="hero pt-5 mt-5">
    <div class="container py-5 text-center">
        <h1 class="fw-bold">Sistem Informasi Sarana dan Prasarana Sekolah</h1>
        <p class="lead">Mendukung pengelolaan aset sekolah secara efektif, efisien, dan terdokumentasi</p>
        <a href="../pages/login.php" class="btn btn-light btn-lg mt-3">Masuk Sistem</a>
    </div>
</section>

<!-- PROFIL -->
<section id="profil" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="fw-semibold">Profil Sistem</h3>
                <p>Sistem Informasi Sarana dan Prasarana Sekolah merupakan aplikasi berbasis web yang dikembangkan untuk membantu sekolah dalam mengelola data aset secara terintegrasi.</p>
                <ul>
                    <li>Meningkatkan akurasi data aset</li>
                    <li>Mempermudah monitoring sarana</li>
                    <li>Mendukung transparansi pelaporan</li>
                </ul>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/images/illustration.png" class="img-fluid" alt="Ilustrasi Sistem">
            </div>
        </div>
    </div>
</section>

<!-- FITUR -->
<section id="fitur" class="bg-light py-5">
    <div class="container">
        <h3 class="text-center fw-semibold mb-4">Fitur Utama Aplikasi</h3>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h6 class="fw-semibold">Manajemen Sarana</h6>
                        <p>Pendataan inventaris sekolah terpusat</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h6 class="fw-semibold">Pemeliharaan</h6>
                        <p>Monitoring kondisi sarana</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h6 class="fw-semibold">Laporan</h6>
                        <p>Rekap dan cetak laporan PDF</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h6 class="fw-semibold">Manajemen User</h6>
                        <p>Pengaturan hak akses pengguna</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATISTIK -->
<section id="statistik" class="py-5">
    <div class="container">
        <h3 class="text-center fw-semibold mb-4">Statistik Sarana (Real-Time)</h3>
        <div class="row g-4">
            <div class="col-md-3"><div class="card stat-card"><div class="card-body"><h6>Total Sarana</h6><h4>120</h4></div></div></div>
            <div class="col-md-3"><div class="card stat-card"><div class="card-body"><h6>Layak Pakai</h6><h4>95</h4></div></div></div>
            <div class="col-md-3"><div class="card stat-card"><div class="card-body"><h6>Rusak Ringan</h6><h4>15</h4></div></div></div>
            <div class="col-md-3"><div class="card stat-card"><div class="card-body"><h6>Rusak Berat</h6><h4>10</h4></div></div></div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="py-3 text-center">
    <small>Sistem Informasi Sarana dan Prasarana Sekolah &copy; 2025</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
