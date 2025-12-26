<?php
// FILE: pages/login.php
// Halaman Login dengan Bootstrap 5

session_start();
require_once '../config/koneksi.php';
require_once '../config/functions.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['id_user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitasi($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validasi input
    if (!validasi_tidak_kosong($username) || !validasi_tidak_kosong($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        // Query cari user
        $query = "SELECT * FROM users WHERE username = '" . escape($koneksi, $username) . "'";
        $result = mysqli_query($koneksi, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (verifikasi_password($password, $user['password'])) {
                // Cek status user
                if ($user['status'] == 'nonaktif') {
                    $error = 'Akun Anda telah dinonaktifkan!';
                } else {
                    // Login berhasil, set session
                    $_SESSION['id_user'] = $user['id_user'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['avatar'] = $user['avatar'] ?? 'default.png';
                    
                    // Redirect ke dashboard
                    header('Location: dashboard.php');
                    exit;
                }
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Sarana & Prasarana</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .login-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #999;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: 8px;
            margin-bottom: 25px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-danger {
            background-color: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .demo-info {
            background-color: #f0f7ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .demo-info strong {
            display: block;
            color: #667eea;
            margin-bottom: 8px;
        }

        .demo-info p {
            margin: 3px 0;
            color: #555;
        }

        .login-footer {
            text-align: center;
            padding: 20px 40px;
            background: #f9f9f9;
            color: #666;
            font-size: 12px;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            pointer-events: none;
        }

        .form-control.with-icon {
            padding-right: 40px;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #999;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
        }

        .divider span {
            padding: 0 10px;
        }

        @media (max-width: 576px) {
            .login-header {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .login-body {
                padding: 30px 20px;
            }

            .login-footer {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <h1>
                    <i class="fas fa-building"></i> SMP Bina Insan
                </h1>
                <p>Sistem Sarana & Prasarana</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <!-- Error Alert -->
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- <!-- Demo Info -->
               <!--  <div class="demo-info">
                    <strong><i class="fas fa-info-circle"></i> Demo Login</strong>
                    <p><strong>Username:</strong> admin</p>
                    <p><strong>Password:</strong> admin123</p>
                </div> --> 

                <!-- Login Form -->
                <form method="POST" action="">
                    <!-- Username -->
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-group-icon">
                            <input type="text" id="username" name="username" class="form-control with-icon"
                                   value="<?php echo htmlspecialchars($username); ?>" 
                                   placeholder="Masukkan username" required autofocus>
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group-icon">
                            <input type="password" id="password" name="password" class="form-control with-icon"
                                   placeholder="Masukkan password" required>
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; 2024 SMP Bina Insan Mandiri. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>