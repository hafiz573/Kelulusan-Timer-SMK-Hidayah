<?php
require_once 'config/database.php';

// Redirect jika sudah login
if (isAdminLoggedIn()) {
    header('Location: admin/dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitizeInput($_POST['nama']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($nama) || empty($password)) {
        $error = 'Nama dan Password harus diisi!';
    } else {
        // Coba login admin
        if (loginAdmin($nama, $password, $pdo)) {
            header('Location: admin/dashboard.php');
            exit();
        } else {
            $error = 'Nama atau Password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Aplikasi Kelulusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="admin-login-page">
    <div class="admin-login-container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5 col-lg-4">
                <div class="card admin-login-card shadow-lg">
                    <div class="card-header text-center py-4 bg-dark text-white">
                        <div class="admin-icon mb-3">
                            <div class="icon-wrapper">
                                <i class="fas fa-user-shield fa-3x"></i>
                            </div>
                        </div>
                        <h2 class="mb-1">Admin Login</h2>
                        <p class="mb-0 opacity-75">Panel Administrator Sistem</p>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="adminLoginForm">
                            <div class="mb-4">
                                <label for="nama" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nama Admin
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user-tie text-dark"></i>
                                    </span>
                                    <input type="text" class="form-control" 
                                           id="nama" name="nama" 
                                           placeholder="Masukkan nama admin" 
                                           value="<?php echo $_POST['nama'] ?? ''; ?>"
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-lock text-dark"></i>
                                    </span>
                                    <input type="password" class="form-control" 
                                           id="password" name="password" 
                                           placeholder="Masukkan password" 
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            id="toggleAdminPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    Hanya untuk admin terotorisasi
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-dark btn-lg py-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login sebagai Admin
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Halaman Utama
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-center py-3 bg-light">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Sistem Administrator Kelulusan &copy; 2024
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('toggleAdminPassword').addEventListener('click', function() {
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
        
        // Form validation
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            const nama = document.getElementById('nama').value;
            const password = document.getElementById('password').value;
            
            if (nama.trim() === '' || password.trim() === '') {
                e.preventDefault();
                alert('Harap isi semua field!');
                return false;
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>