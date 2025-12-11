<?php
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_login = sanitizeInput($_POST['id_login']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($id_login) || empty($password)) {
        $error = 'ID Login dan Password harus diisi!';
    } else {
        // Coba login user dengan ID Login
        if (loginUser($id_login, $password, $pdo)) {
            header('Location: user-profile.php');
            exit();
        } else {
            $error = 'ID Login atau Password salah!';
        }
    }
}

// Cek apakah timer sudah expired
if (!isTimerExpired($pdo)) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa - Aplikasi Kelulusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .id-login-example {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .id-login-example h6 {
            color: white;
            margin-bottom: 5px;
        }
        .id-login-badge {
            background: #ff6b6b;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card shadow-lg">
                    <div class="card-header text-center py-4">
                        <div class="login-icon mb-3">
                            <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                        </div>
                        <h2 class="mb-1 fw-bold">Login Siswa</h2>
                        <p class="text-muted mb-0">Masukkan ID Login dan Password Anda</p>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        
                        <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="loginForm">
                            <div class="mb-4">
                                <label for="id_login" class="form-label">
                                    <i class="fas fa-id-card me-2"></i>ID Login
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user-graduate text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control" 
                                           id="id_login" name="id_login" 
                                           placeholder="Masukkan ID Login Anda" 
                                           value="<?php echo $_POST['id_login'] ?? ''; ?>"
                                           required>
                                    <span class="input-group-text bg-light">
                                        <span class="id-login-badge">ID</span>
                                    </span>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ID Login terdiri dari huruf dan angka (contoh: K021GM)
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-key text-primary"></i>
                                    </span>
                                    <input type="password" class="form-control" 
                                           id="password" name="password" 
                                           placeholder="Masukkan password Anda" 
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    Password diberikan oleh sekolah
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg py-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-question-circle me-2"></i>Bantuan Login:</h6>
                                <ul class="mb-0 ps-3">
                                    <li>Pastikan mengetik dengan huruf besar/kecil sesuai</li>
                                    <li>Hubungi admin jika lupa ID Login atau password</li>
                                </ul>
                            </div>
                            
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Halaman Utama
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-center py-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Sistem login aman dengan ID unik
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Auto-uppercase untuk ID Login
        document.getElementById('id_login').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const idLogin = document.getElementById('id_login').value;
            const password = document.getElementById('password').value;
            
            if (idLogin.trim() === '' || password.trim() === '') {
                e.preventDefault();
                alert('Harap isi semua field!');
                return false;
            }
            
            // Validasi format ID Login
            const idLoginRegex = /^[A-Z0-9]{4,10}$/;
            if (!idLoginRegex.test(idLogin)) {
                e.preventDefault();
                alert('ID Login harus terdiri dari huruf dan angka (4-10 karakter)');
                return false;
            }
        });
        
        // Generate contoh ID Login random
        function generateExampleId() {
            const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            let id = 'K';
            
            // Tambah 2 angka
            for (let i = 0; i < 2; i++) {
                id += numbers.charAt(Math.floor(Math.random() * numbers.length));
            }
            
            // Tambah 2 huruf
            for (let i = 0; i < 2; i++) {
                id += letters.charAt(Math.floor(Math.random() * letters.length));
            }
            
            return id;
        }
        
        // Update contoh ID Login setiap 10 detik
        setInterval(() => {
            const exampleIds = document.querySelectorAll('.id-login-example span');
            exampleIds.forEach(span => {
                span.textContent = generateExampleId();
            });
        }, 10000);
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>