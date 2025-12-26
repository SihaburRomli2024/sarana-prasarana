<?php
// FILE: pages/logout.php
// Proses Logout

session_start();

// Hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// Redirect ke halaman login
header('Location: ../public/index.php');
exit;
?>