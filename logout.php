<?php
session_start();

// Cek apakah logout dari admin
if (isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_nama']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_logged_in']);
    header('Location: index.php');
    exit();
}

// Logout user biasa
if (isset($_SESSION['user_logged_in'])) {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_nama']);
    unset($_SESSION['user_no_absen']);
    unset($_SESSION['user_kelas']);
    unset($_SESSION['user_status']);
    unset($_SESSION['user_logged_in']);
    header('Location: index.php');
    exit();
}

// Default redirect ke index
header('Location: index.php');
exit();
?>