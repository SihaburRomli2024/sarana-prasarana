
<?php
// FILE: index.php
session_start();

if (isset($_SESSION['id_user'])) {
    header('Location: pages/dashboard.php');
} else {
    header('Location: pages/login.php');
}
exit;
?>