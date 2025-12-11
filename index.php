<?php
require_once 'config/database.php';

$deadline = getTimerDeadline($pdo);
$is_expired = isTimerExpired($pdo);

// Hitung waktu tersisa
$now = new DateTime();
$future_date = new DateTime($deadline);
$interval = $future_date->diff($now);

$days = $interval->format('%a');
$hours = $interval->format('%h');
$minutes = $interval->format('%i');
$seconds = $interval->format('%s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kelulusan - SMK Negeri 1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>Kelulusan SMK
            </a>
            <div class="d-flex">
                <a href="admin-login.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-user-shield me-1"></i>Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container py-5 mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Header Section -->
                    <div class="text-center mb-5">
                        <div class="school-logo mb-4">
                            <i class="fas fa-school fa-4x text-primary"></i>
                        </div>
                        <h1 class="display-4 fw-bold text-primary">PENGUMUMAN KELULUSAN</h1>
                        <p class="lead text-muted">Tahun Pelajaran 2025/2026</p>
                        <p class="text-muted">SMK Hidayah Semarang</p>
                    </div>

                    <?php if(!$is_expired): ?>
                    <!-- Countdown Timer Section -->
                    <div class="card shadow-lg border-0 mb-5 animate__animated animate__fadeInUp">
                        <div class="card-header bg-gradient-primary text-white py-4">
                            <h2 class="mb-0 text-center">
                                <i class="fas fa-clock me-2"></i>Countdown Menuju Pengumuman
                            </h2>
                        </div>
                        <div class="card-body p-4 p-lg-5">
                            <div class="row text-center" id="countdown-timer">
                                <div class="col-6 col-md-3 mb-4">
                                    <div class="countdown-box bg-primary text-white rounded-3 p-4 shadow">
                                        <div class="countdown-number display-4 fw-bold" id="days"><?php echo $days; ?></div>
                                        <div class="countdown-label h6 mt-2">Hari</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-4">
                                    <div class="countdown-box bg-success text-white rounded-3 p-4 shadow">
                                        <div class="countdown-number display-4 fw-bold" id="hours"><?php echo $hours; ?></div>
                                        <div class="countdown-label h6 mt-2">Jam</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-4">
                                    <div class="countdown-box bg-warning text-white rounded-3 p-4 shadow">
                                        <div class="countdown-number display-4 fw-bold" id="minutes"><?php echo $minutes; ?></div>
                                        <div class="countdown-label h6 mt-2">Menit</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-4">
                                    <div class="countdown-box bg-danger text-white rounded-3 p-4 shadow">
                                        <div class="countdown-number display-4 fw-bold" id="seconds"><?php echo $seconds; ?></div>
                                        <div class="countdown-label h6 mt-2">Detik</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Pengumuman akan dibuka pada: 
                                    <strong><?php echo formatDateTimeIndonesia($deadline); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Information Section -->
                    <div class="row mb-5">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <i class="fas fa-info-circle text-primary me-2"></i>Informasi Penting
                                    </h4>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Pastikan ID login Anda benar
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Siapkan password yang telah diberikan
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Hubungi admin jika mengalami kendala
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h4 class="card-title">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Persyaratan
                                    </h4>
                                    <p class="card-text">
                                        Status kelulusan dapat dilihat setelah countdown selesai. 
                                        Pastikan semua administrasi telah lengkap sebelum melihat pengumuman.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php else: ?>
                    <!-- Login Section (After Timer Expires) -->
                    <div class="card shadow-lg border-0 mb-5 animate__animated animate__fadeIn">
                        <div class="card-header bg-gradient-success text-white py-4">
                            <h2 class="mb-0 text-center">
                                <i class="fas fa-check-circle me-2"></i>Waktu Pengumuman Telah Tiba!
                            </h2>
                        </div>
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <div class="success-icon mb-4">
                                    <i class="fas fa-graduation-cap fa-5x text-success"></i>
                                </div>
                                <h3 class="mb-3">Pengumuman Kelulusan Telah Dibuka</h3>
                                <p class="lead text-muted">Silahkan login untuk melihat status kelulusan Anda</p>
                            </div>
                            
                            <div class="d-grid gap-2 col-md-6 mx-auto">
                                <a href="login.php" class="btn btn-success btn-lg py-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>LOGIN SISWA
                                </a>
                            </div>
                            
                            <div class="mt-4">
                                <p class="text-muted">
                                    <small>
                                        <i class="fas fa-user-shield me-1"></i>
                                        <a href="admin-login.php" class="text-decoration-none">Login sebagai Admin</a>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="alert alert-primary">
                        <h5><i class="fas fa-lightbulb me-2"></i>Cara Login:</h5>
                        <ol class="mb-0">
                            <li>Klik tombol "LOGIN SISWA" di atas</li>
                            <li>Masukkan No Login dan Password</li>
                            <li>Tekan tombol Login untuk melihat status kelulusan</li>
                        </ol>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sistem Informasi Kelulusan</h5>
                    <p class="mb-0">SMK Hidayah Semarang</p>
                    <p class="mb-0">Tahun Pelajaran 2025/2026</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="fas fa-copyright me-1"></i>
                        2025 Aplikasi Kelulusan. All rights reserved.
                    </p>
                    <p class="mb-0">
                        <small>Version 1.0.0</small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        const deadline = '<?php echo $deadline; ?>';
        const isExpired = <?php echo $is_expired ? 'true' : 'false'; ?>;
        
        function updateCountdown() {
            if (isExpired) return;
            
            const now = new Date().getTime();
            const targetDate = new Date(deadline).getTime();
            const timeLeft = targetDate - now;
            
            if (timeLeft <= 0) {
                location.reload();
                return;
            }
            
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }
        
        // Update countdown setiap detik
        if (!isExpired) {
            setInterval(updateCountdown, 1000);
        }
        
        // Animasi countdown numbers
        document.addEventListener('DOMContentLoaded', function() {
            const numbers = document.querySelectorAll('.countdown-number');
            numbers.forEach(number => {
                number.classList.add('animate__animated', 'animate__pulse');
            });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
</body>
</html>