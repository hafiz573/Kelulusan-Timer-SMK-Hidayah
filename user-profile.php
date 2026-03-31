<?php
require_once 'config/database.php';

// Cek apakah user sudah login
requireUserLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$user_id_login = $_SESSION['user_id_login'];
$user_nama = $_SESSION['user_nama'];
// $user_no_absen = $_SESSION['user_no_absen'];
$user_kelas = $_SESSION['user_kelas'];
$user_status = $_SESSION['user_status'];

// Fetch NISN and NIS directly from database since they might not be in session initially
$stmt_nisn = $pdo->prepare("SELECT nisn, nis FROM users WHERE id = ?");
$stmt_nisn->execute([$user_id]);
$row_nisn = $stmt_nisn->fetch();
$user_nisn = $row_nisn['nisn'] ?? '-';
$user_nis = $row_nisn['nis'] ?? '-';
if (empty($user_nisn)) $user_nisn = '-';
if (empty($user_nis)) $user_nis = '-';

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
    <title>Status Kelulusan - <?php echo htmlspecialchars($user_nama); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #f4f7fe;
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(255, 255, 255, 0.5);
            --text-main: #2b3674;
            --text-muted: #a3aed1;
            --primary-color: #4318ff;
            --success-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --warning-gradient: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            --glass-shadow: 0 18px 40px rgba(112, 144, 176, 0.12);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--primary-bg);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,0.03) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.03) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.03) 0, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }



        /* Card Styling */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 22px 45px rgba(112, 144, 176, 0.18);
        }
        .card-header-glass {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
        }
        .card-header-glass h3 {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--text-main);
        }

        /* Table Styling */
        .table th {
            font-weight: 500;
            color: var(--text-muted);
            border: none;
        }
        .table td {
            color: var(--text-main);
            border: none;
            font-weight: 600;
        }

        /* Status Colors */
        .status-header {
            padding: 3rem 2rem;
            position: relative;
            z-index: 1;
        }
        .status-header::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIj48Zz48cGF0aCBkPSJNMTAwIDBoMjAwTDMwMCAxMDBIMDB6IiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9nPjwvc3ZnPg==') center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        .status-lulus .status-header {
            background: var(--success-gradient);
        }
        .status-tunda .status-header {
            background: var(--warning-gradient);
        }
        .status-header h1 {
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 8px;
            font-weight: 600;
        }
        
        /* Information boxes */
        .info-box {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        .info-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }
        .info-box h6 {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-box .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            background: var(--primary-bg);
            color: var(--primary-color);
        }

        /* Alerts */
        .custom-alert {
            border: none;
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .custom-alert.alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
        }
        .custom-alert.alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: #92400e;
        }
        .custom-alert h5 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .custom-alert ul li {
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        /* Buttons */
        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-modern:hover {
            transform: translateY(-2px);
        }
        .btn-modern-danger {
            background: #ef4444;
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            border: none;
        }
        .btn-modern-danger:hover {
            background: #dc2626;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            color: white;
        }
        .back-link {
            color: var(--text-muted);
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: var(--primary-color);
        }

    </style>
</head>
<body>
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
                    <ul class="dropdown-menu dropdown-menu-end">
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

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <!-- Status Kelulusan Card -->
                <div class="glass-card mb-5 <?php echo $status_class; ?>">
                    <div class="status-header text-white text-center">
                        <div class="mb-4">
                            <i class="<?php echo $status_icon; ?> fa-5x" style="filter: drop-shadow(0 4px 15px rgba(0,0,0,0.2));"></i>
                        </div>
                        <h1 class="display-4 fw-bold mb-3">
                            <?php echo $user_status; ?>
                        </h1>
                        <p class="lead mb-0 text-white" style="opacity: 0.9; font-weight: 500;"><?php echo $status_message; ?></p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5 bg-white">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="info-box h-100">
                                    <div class="icon-wrapper">
                                        <i class="fas fa-calendar-alt fa-lg"></i>
                                    </div>
                                    <div class="text-center">
                                        <h6>Tanggal Pengumuman</h6>
                                        <p class="fw-bold mb-0" style="font-size: 1.1rem; color: var(--text-main);"><?php echo date('d F Y'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box h-100">
                                    <div class="icon-wrapper" style="color: #10b981; background: #ecfdf5;">
                                        <i class="fas fa-school fa-lg"></i>
                                    </div>
                                    <div class="text-center">
                                        <h6>Sekolah</h6>
                                        <p class="fw-bold mb-0" style="font-size: 1.1rem; color: var(--text-main);">SMK Hidayah Semarang</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if($user_status == 'LULUS'): ?>
                        <div class="custom-alert alert-success mt-4">
                            <h5><i class="fas fa-info-circle me-2"></i>Informasi Kelulusan</h5>
                            <ul class="mb-0 ps-3">
                                <li>Pengambilan ijazah dapat dilakukan di TU sekolah</li>
                                <li>Bawa berkas lengkap (KTP dan bukti pembayaran)</li>
                                <li>Jadwal pengambilan: Senin - Jumat, 08:00 - 14:00</li>
                                <li>Hubungi sekolah untuk informasi lebih lanjut</li>
                            </ul>
                        </div>
                        <?php else: ?>
                        <div class="custom-alert alert-warning mt-4">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Langkah Selanjutnya</h5>
                            <ul class="mb-0 ps-3">
                                <li>Segera hubungi bagian administrasi sekolah</li>
                                <li>Periksa kelengkapan berkas dan administrasi</li>
                                <li>Lengkapi persyaratan yang belum terpenuhi</li>
                                <li>Status akan diperbarui setelah semua syarat terpenuhi</li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="glass-card mb-4">
                    <div class="card-header-glass d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                            <i class="fas fa-user-circle fa-lg"></i>
                        </div>
                        <h3 class="mb-0">Data Profil Siswa</h3>
                    </div>
                    <div class="card-body p-4 p-md-5 pt-md-4">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th width="180" class="text-muted pb-3"><i class="fas fa-user me-2 opacity-50"></i>Nama Lengkap</th>
                                        <td class="fw-bold fs-6 pb-3"><?php echo htmlspecialchars($user_nama); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pb-3"><i class="fas fa-id-badge me-2 opacity-50"></i>NISN</th>
                                        <td class="fw-bold fs-6 pb-3"><?php echo htmlspecialchars($user_nisn); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pb-3"><i class="fas fa-hashtag me-2 opacity-50"></i>NIS</th>
                                        <td class="fw-bold fs-6 pb-3"><?php echo htmlspecialchars($user_nis); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pb-3"><i class="fas fa-key me-2 opacity-50"></i>ID Login</th>
                                        <td class="pb-3">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                <i class="fas fa-fingerprint me-1"></i>
                                                <?php echo htmlspecialchars($user_id_login); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted pb-3"><i class="fas fa-users me-2 opacity-50"></i>Kelas</th>
                                        <td class="fw-bold fs-6 pb-3">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">
                                                <?php echo htmlspecialchars($user_kelas); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted"><i class="fas fa-shield-alt me-2 opacity-50"></i>Status Akun</th>
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i>Aktif
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-5">
                    <a href="logout.php" class="btn btn-modern btn-modern-danger mb-4">
                        <i class="fas fa-sign-out-alt"></i>Keluar dari Sistem
                    </a>
                    
                    <div>
                        <a href="index.php" class="text-decoration-none back-link">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Halaman Utama
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer Note -->
    <footer class="py-4 mt-auto">
        <div class="container text-center">
            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                <i class="fas fa-lock me-1"></i>
                Data ini diterbitkan secara otomatis dan bersifat rahasia. Jangan bagikan informasi Anda.
            </p>
        </div>
    </footer>

    <!-- Print Styles -->
    <style media="print">
        .navbar, .btn, .custom-alert, .glass-card:not(.status-lulus):not(.status-tunda), footer {
            display: none !important;
        }
        .status-header {
            background: #333 !important;
            color: white !important;
            padding: 20px !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
        .glass-card {
            border: 2px solid #000 !important;
            box-shadow: none !important;
            margin-top: 20px !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>