<?php
// FILE: includes/session.php
// Manajemen Session & Cek Login

// Mulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include koneksi dan fungsi
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/functions.php';

// ============================================
// FUNGSI CEK LOGIN
// ============================================

/**
 * Cek apakah user sudah login
 */
function cek_login() {
    if (!isset($_SESSION['id_user']) || !isset($_SESSION['username'])) {
        header('Location: ../pages/login.php');
        exit;
    }
}

/**
 * Cek apakah user sudah login (redirect ke dashboard jika sudah)
 */
function cek_login_redirect() {
    if (isset($_SESSION['id_user']) && isset($_SESSION['username'])) {
        header('Location: ../pages/dashboard.php');
        exit;
    }
}

/**
 * Cek role user (admin, guru, staff)
 */
function cek_role($role_yang_diizinkan = []) {
    if (!in_array($_SESSION['role'], $role_yang_diizinkan)) {
        header('Location: ../pages/dashboard.php');
        exit;
    }
}

/**
 * Ambil informasi user dari session
 */
function get_user_info() {
    return [
        'id_user' => $_SESSION['id_user'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

/**
 * Logout user
 */
function logout_user() {
    $_SESSION = [];
    session_unset();
    session_destroy();
    header('Location: ../pages/login.php');
    exit;
}

?>