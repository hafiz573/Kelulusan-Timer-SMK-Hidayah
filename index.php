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
    <title>Aplikasi Kelulusan - SMK Hidayah Semarang</title>
    <link rel="icon" href="img/hidayah.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: transparent !important; }
        body.ui-hidden { overflow: hidden !important; width: 100%; height: 100dvh; }
        .bg-wrap { position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; z-index: -10; overflow: hidden; background: #f5f7fa; }
        .bg-wrap::before { content: ''; position: absolute; top: -5%; left: -5%; width: 110%; height: 110%; background-image: var(--bg-url); background-size: cover; background-position: center; filter: blur(40px) brightness(0.5); opacity: 0; transition: opacity 0.8s ease; z-index: 0; }
        .bg-slider { position: absolute; z-index: 1; width: 100%; height: 100%; background-size: cover; background-position: center; background-repeat: no-repeat; transition: opacity 1s ease-in-out; opacity: 0; }
        .bg-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.82); z-index: 2; }
        .intro-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f5f7fa; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 1s ease-in-out, background 0.5s; z-index: 5; }
        .intro-layer.active { opacity: 1; }
        .intro-text-animate { color: #2c3e50; font-size: 8rem; font-weight: 800; letter-spacing: 20px; text-transform: uppercase; text-shadow: 0 4px 15px rgba(0,0,0,0.1); animation: zoomFade 2.5s forwards; }
        @keyframes zoomFade { 0% { transform: scale(0.6); opacity: 0; } 20% { transform: scale(1); opacity: 1; } 80% { transform: scale(1.05); opacity: 1; } 100% { transform: scale(1.2); opacity: 0; } }
        @media (max-width: 768px) { 
            .intro-text-animate { font-size: 3.5rem; letter-spacing: 10px; } 
            .bg-wrap::before { filter: blur(15px) brightness(0.5); }
        }
        
        /* UI Toggle Animation Styles (Fly into Button) */
        nav, main, footer, .bg-overlay { 
            transition: all 0.7s cubic-bezier(0.68, -0.1, 0.265, 1.15); 
        }
        
        nav { transform-origin: calc(100vw - 60px) calc(100vh - 60px); }
        main { transform-origin: right bottom; }
        footer { transform-origin: right center; }

        body.ui-hidden nav, body.ui-hidden main, body.ui-hidden footer, body.ui-hidden .bg-overlay { 
            opacity: 0 !important; 
            pointer-events: none !important; 
        }
        
        body.ui-hidden nav { transform: scale(0); filter: blur(5px); }
        body.ui-hidden main { transform: translate(40vw, 40vh) scale(0) rotate(5deg); filter: blur(10px); }
        body.ui-hidden footer { transform: translate(30vw, 10vh) scale(0); filter: blur(5px); }

        /* Responsive Photo Viewer Mode */
        body.ui-hidden .bg-wrap { background: #fff; transition: background 0.5s; }
        body.ui-hidden .bg-wrap::before { opacity: 1; }
        body.ui-hidden .bg-slider { background-size: contain; }
        
        #toggle-ui-btn { transition: all 0.3s ease; opacity: 0.6; }
        #toggle-ui-btn:hover { transform: scale(1.1); opacity: 1; }

        @media (max-width: 768px) { 
            #toggle-ui-btn { bottom: 20px !important; right: 20px !important; width: 50px !important; height: 50px !important; }
            #toggle-ui-btn i { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <!-- Background Slider Elements -->
    <div class="bg-wrap">
        <div id="bg-slider" class="bg-slider"></div>
        <div class="bg-overlay"></div>
        <div id="intro-layer" class="intro-layer">
            <h1 id="intro-text"></h1>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>Kelulusan SMK
            </a>
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
                        <small>Version 1.0.9</small>
                    </p>
                    <p class="mb-0">
                        <small>
                            Creator: <a href="https://github.com/hafiz573" target="_blank" rel="noopener noreferrer">Hafiz Muhammad Fiqar (RPL)</a>
                        </small>
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
    
    <!-- Background Slider Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = [
                { file: 'AK.JPG', title: 'AK' },
                { file: 'MP.JPG', title: 'MP' },
                { file: 'RPL.JPG', title: 'RPL' },
                { file: 'TKJ.JPG', title: 'TKJ' }
            ];
            
            const bgSlider = document.getElementById('bg-slider');
            const introLayer = document.getElementById('intro-layer');
            const introText = document.getElementById('intro-text');
            
            let currentSlide = 0;
            
            function nextSlide() {
                const slide = slides[currentSlide];
                
                // 1. Reset Text Animation and Setup Intro
                introText.className = ''; 
                void introText.offsetWidth; // Trigger reflow
                
                introText.textContent = slide.title;
                introText.className = 'intro-text-animate';
                introLayer.classList.add('active');
                
                // 2. Change Image Behind Intro
                setTimeout(() => {
                    const imgUrl = `url('img/${slide.file}')`;
                    bgSlider.style.backgroundImage = imgUrl;
                    document.querySelector('.bg-wrap').style.setProperty('--bg-url', imgUrl);
                    bgSlider.style.opacity = 1;
                }, 1000);
                
                // 3. Fade Out Intro via transition, revealing the new background
                setTimeout(() => {
                    introLayer.classList.remove('active');
                }, 2500);
                
                // 4. Set Next Slide Timing
                setTimeout(() => {
                    currentSlide = (currentSlide + 1) % slides.length;
                    nextSlide();
                }, 7500);
            }
            
            // Start animation
            nextSlide();

            // Toggle UI Logic
            const toggleUiBtn = document.getElementById('toggle-ui-btn');
            const toggleIcon = toggleUiBtn.querySelector('i');
            
            toggleUiBtn.addEventListener('click', function() {
                document.body.classList.toggle('ui-hidden');
                
                if (document.body.classList.contains('ui-hidden')) {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                    toggleUiBtn.classList.remove('btn-dark');
                    toggleUiBtn.classList.add('btn-light');
                } else {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                    toggleUiBtn.classList.remove('btn-light');
                    toggleUiBtn.classList.add('btn-dark');
                }
            });
        });
    </script>
    
    <!-- Toggle UI Button -->
    <button id="toggle-ui-btn" class="btn btn-dark rounded-circle shadow-lg" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 9999; display: flex; align-items: center; justify-content: center;" title="Toggle UI">
        <i class="fas fa-eye-slash fa-lg"></i>
    </button>
</body>
</html>