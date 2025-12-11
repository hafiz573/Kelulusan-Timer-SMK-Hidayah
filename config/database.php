<?php
session_start();

// Set timezone ke Jakarta (GMT+7)
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_kelulusan');
define('DB_USER', 'root');
define('DB_PASS', '');

// Koneksi Database
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Set timezone untuk MySQL juga
    $pdo->exec("SET time_zone = '+07:00'");
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Fungsi Helper dengan waktu Jakarta
function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

function formatDateTimeIndonesia($datetime) {
    $bulan = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    );
    
    $timestamp = strtotime($datetime);
    $hari = date('N', $timestamp);
    $nama_hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$hari - 1];
    
    $tanggal = date('j', $timestamp);
    $bulan_nama = $bulan[date('n', $timestamp)];
    $tahun = date('Y', $timestamp);
    $jam = date('H:i:s', $timestamp);
    
    return "$nama_hari, $tanggal $bulan_nama $tahun $jam WIB";
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatDateOnly($datetime) {
    return date('d/m/Y', strtotime($datetime));
}

// Fungsi Autentikasi Admin
function loginAdmin($nama, $password, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE nama = ?");
    $stmt->execute([$nama]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ../admin-login.php');
        exit();
    }
}

// Fungsi Autentikasi User/Siswa DENGAN ID_LOGIN
function loginUser($id_login, $password, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_login = ?");
    $stmt->execute([$id_login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_id_login'] = $user['id_login'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_no_absen'] = $user['no_absen'];
        $_SESSION['user_kelas'] = $user['kelas'];
        $_SESSION['user_status'] = $user['status_lulus'];
        $_SESSION['user_logged_in'] = true;
        return true;
    }
    return false;
}

function isUserLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

function requireUserLogin() {
    if (!isUserLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Fungsi Timer dengan waktu Jakarta
function getTimerDeadline($pdo) {
    $stmt = $pdo->query("SELECT deadline FROM timer_setting ORDER BY id DESC LIMIT 1");
    $timer = $stmt->fetch();
    return $timer ? $timer['deadline'] : date('Y-m-d H:i:s', strtotime('+7 days'));
}

function isTimerExpired($pdo) {
    $deadline = getTimerDeadline($pdo);
    $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $future_date = new DateTime($deadline, new DateTimeZone('Asia/Jakarta'));
    return $now > $future_date;
}

// Fungsi untuk menghitung sisa waktu
function getRemainingTime($pdo) {
    $deadline = getTimerDeadline($pdo);
    $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $future_date = new DateTime($deadline, new DateTimeZone('Asia/Jakarta'));
    
    if ($now > $future_date) {
        return [
            'expired' => true,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0
        ];
    }
    
    $interval = $future_date->diff($now);
    
    return [
        'expired' => false,
        'days' => $interval->format('%a'),
        'hours' => $interval->format('%h'),
        'minutes' => $interval->format('%i'),
        'seconds' => $interval->format('%s')
    ];
}

// Fungsi Statistik
function getStatistics($pdo) {
    $stats = [];
    
    // Total siswa
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['total_siswa'] = $stmt->fetch()['total'];
    
    // Lulus
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status_lulus = 'LULUS'");
    $stats['total_lulus'] = $stmt->fetch()['total'];
    
    // Ditangguhkan
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status_lulus = 'KELULUSAN DITANGGUHKAN'");
    $stats['total_tunda'] = $stmt->fetch()['total'];
    
    // Total admin
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM admin");
    $stats['total_admin'] = $stmt->fetch()['total'];
    
    return $stats;
}
?>