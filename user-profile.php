<?php
require_once 'config/database.php';

// Cek apakah user sudah login
if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$user_id_login = $_SESSION['user_id_login'];
$user_nama = $_SESSION['user_nama'];
// $user_no_absen = $_SESSION['user_no_absen'];
$user_kelas = $_SESSION['user_kelas'];
$user_status = $_SESSION['user_status'];

// Tampilkan pesan berdasarkan status
if ($user_status == 'LULUS') {
    $status_message = "Selamat! Anda dinyatakan LULUS dari SMK Hidayah Semarang";
    $status_icon = "fas fa-trophy";
    $status_class = "status-lulus";
    $status_color = "success";
} else {
    $status_message = "Kelulusan Anda ditangguhkan. Silahkan hubungi bagian administrasi sekolah.";
    $status_icon = "fas fa-clock";
    $status_class = "status-tunda";
    $status_color = "warning";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Kelulusan - <?php echo $user_nama; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="profile-page">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>Kelulusan SMK
            </a>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>
                        <?php echo htmlspecialchars($user_nama); ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-5 mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Profile Card -->
                <div class="card shadow-lg mb-4">
                    <div class="card-header bg-white py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user-circle me-2 text-primary"></i>Profil Siswa
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <!-- <div class="col-md-3 text-center mb-4 mb-md-0">
                                <div class="avatar-circle bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                     style="width: 120px; height: 120px;">
                                    <i class="fas fa-user-graduate fa-4x text-white"></i>
                                </div>
                            </div> -->
                            <div class="col-md-9">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="150" class="text-muted">Nama Lengkap</th>
                                            <td class="fw-bold"><?php echo htmlspecialchars($user_nama); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">ID Login</th>
                                            <td>
                                                <span class="badge bg-info text-black">
                                                    <i class="fas fa-id-card me-1"></i>
                                                    <?php echo htmlspecialchars($user_id_login); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Kelas</th>
                                            <td class="fw-bold"><?php echo htmlspecialchars($user_kelas); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Status Login</th>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Kelulusan Card -->
                <div class="card shadow-lg mb-4 <?php echo $status_class; ?>">
                    <div class="card-body p-0">
                        <div class="status-header bg-<?php echo $status_color; ?> text-white text-center py-5">
                            <div class="status-icon mb-4">
                                <i class="<?php echo $status_icon; ?> fa-4x"></i>
                            </div>
                            <h1 class="display-4 fw-bold mb-3">
                                <?php echo $user_status; ?>
                            </h1>
                            <p class="lead mb-0"><?php echo $status_message; ?></p>
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-calendar-alt fa-2x text-primary mb-3"></i>
                                            <h6>Tanggal Pengumuman</h6>
                                            <p class="fw-bold mb-0"><?php echo date('d F Y'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <i class="fas fa-school fa-2x text-primary mb-3"></i>
                                            <h6>Sekolah</h6>
                                            <p class="fw-bold mb-0">SMK Hidayah Semarang</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if($user_status == 'LULUS'): ?>
                            <div class="alert alert-success mt-3">
                                <h5><i class="fas fa-info-circle me-2"></i>Informasi Kelulusan:</h5>
                                <ul class="mb-0">
                                    <li>Pengambilan ijazah dapat dilakukan di TU sekolah</li>
                                    <li>Bawa berkas lengkap (KTP dan bukti pembayaran)</li>
                                    <li>Jadwal pengambilan: Senin - Jumat, 08:00 - 14:00</li>
                                    <li>Hubungi sekolah untuk informasi lebih lanjut</li>
                                </ul>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning mt-3">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Langkah Selanjutnya:</h5>
                                <ul class="mb-0">
                                    <li>Segera hubungi bagian administrasi sekolah</li>
                                    <li>Periksa kelengkapan berkas dan administrasi</li>
                                    <li>Lengkapi persyaratan yang belum terpenuhi</li>
                                    <li>Status akan diperbarui setelah semua syarat terpenuhi</li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <a href="index.php" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="mt-5 pt-4 border-top">
                    <p class="text-muted text-center">
                        <small>
                            <i class="fas fa-shield-alt me-1"></i>
                            Data ini bersifat rahasia. Jangan bagikan informasi login Anda kepada siapapun.
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Print Styles -->
    <style media="print">
        .navbar, .btn, .alert, .card:not(.status-lulus):not(.status-tunda) {
            display: none !important;
        }
        .status-header {
            background: #333 !important;
            color: white !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>