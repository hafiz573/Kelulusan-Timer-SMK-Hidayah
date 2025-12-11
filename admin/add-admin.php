<?php
require_once '../config/database.php';
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitizeInput($_POST['nama']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($nama) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek apakah nama admin sudah ada
        $stmt = $pdo->prepare("SELECT id FROM admin WHERE nama = ?");
        $stmt->execute([$nama]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Nama admin sudah digunakan!';
        } else {
            // Hash password
            $hashed_password = hashPassword($password);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO admin (nama, password) VALUES (?, ?)");
                $stmt->execute([$nama, $hashed_password]);
                
                $success = 'Admin berhasil ditambahkan!';
                
                // Reset form
                $_POST = array();
            } catch(PDOException $e) {
                $error = 'Gagal menambahkan admin: ' . $e->getMessage();
            }
        }
    }
}
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Tambah Admin Baru</h5>
                </div>
                <div class="card-body">
                    <?php if($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="nama" class="form-label">
                                    <i class="fas fa-user me-2"></i>Nama Admin *
                                </label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?php echo $_POST['nama'] ?? ''; ?>" 
                                       placeholder="Masukkan nama admin" required>
                                <div class="form-text">
                                    Nama ini akan digunakan untuk login
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">
                                    <i class="fas fa-user-tag me-2"></i>Role
                                </label>
                                <input type="text" class="form-control" value="Admin" readonly>
                                <div class="form-text">
                                    Role default untuk admin sistem
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password *
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Masukkan password" required>
                                <div class="form-text">
                                    Minimal 6 karakter
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Konfirmasi Password *
                                </label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</h6>
                            <ul class="mb-0">
                                <li>Admin baru akan memiliki akses penuh ke sistem</li>
                                <li>Pastikan memberikan password yang aman</li>
                                <li>Simpan informasi login dengan baik</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Admin
                            </button>
                            <a href="admins.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password minimal 6 karakter!');
        return false;
    }
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password dan Konfirmasi Password tidak sama!');
        return false;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>