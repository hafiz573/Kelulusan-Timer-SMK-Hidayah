<?php
require_once '../config/database.php';
require_once 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_login = strtoupper(sanitizeInput($_POST['id_login'])); // Auto uppercase
    $nama = sanitizeInput($_POST['nama']);
    $no_absen = sanitizeInput($_POST['no_absen']);
    $kelas = sanitizeInput($_POST['kelas']);
    $status_lulus = $_POST['status_lulus'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($id_login) || empty($nama) || empty($no_absen) || empty($kelas) || empty($password)) {
        $error = 'Semua field wajib diisi!';
    } elseif (!preg_match('/^[A-Z0-9]{4,10}$/', $id_login)) {
        $error = 'ID Login harus 4-10 karakter, hanya huruf dan angka!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek apakah id_login sudah ada
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id_login = ?");
        $stmt->execute([$id_login]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'ID Login sudah digunakan!';
        } else {
            // Hash password
            $hashed_password = hashPassword($password);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO users (id_login, nama, no_absen, kelas, status_lulus, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$id_login, $nama, $no_absen, $kelas, $status_lulus, $hashed_password]);
                
                $success = 'Siswa berhasil ditambahkan dengan ID Login: ' . $id_login;
                
                // Reset form
                $_POST = [];
            } catch(PDOException $e) {
                $error = 'Gagal menambahkan siswa: ' . $e->getMessage();
            }
        }
    }
}
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Tambah Siswa Baru</h5>
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
                    
                    <form method="POST" action="" id="addUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_login" class="form-label">
                                    <i class="fas fa-id-card me-2"></i>ID Login *
                                </label>
                                <input type="text" class="form-control" id="id_login" name="id_login" 
                                       value="<?php echo $_POST['id_login'] ?? ''; ?>" 
                                       placeholder="Contoh: K021GM" required
                                       pattern="[A-Z0-9]{4,10}"
                                       title="4-10 karakter huruf/angka (contoh: K021GM)">
                                <div class="form-text">ID Login unik untuk login siswa</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?php echo $_POST['nama'] ?? ''; ?>" 
                                       placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_absen" class="form-label">No Absen *</label>
                                <input type="text" class="form-control" id="no_absen" name="no_absen" 
                                       value="<?php echo $_POST['no_absen'] ?? ''; ?>" 
                                       placeholder="Contoh: 11, 12, 13" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kelas" class="form-label">Kelas *</label>
                                <input type="text" class="form-control" id="kelas" name="kelas" 
                                       value="<?php echo $_POST['kelas'] ?? ''; ?>" 
                                       placeholder="Contoh: XII RPL, XII TKJ" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status_lulus" class="form-label">Status Kelulusan *</label>
                                <select class="form-select" id="status_lulus" name="status_lulus" required>
                                    <option value="KELULUSAN DITANGGUHKAN" 
                                        <?php echo ($_POST['status_lulus'] ?? '') == 'KELULUSAN DITANGGUHKAN' ? 'selected' : ''; ?>>
                                        KELULUSAN DITANGGUHKAN
                                    </option>
                                    <option value="LULUS" 
                                        <?php echo ($_POST['status_lulus'] ?? '') == 'LULUS' ? 'selected' : ''; ?>>
                                        LULUS
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Masukkan password" required>
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" placeholder="Ulangi password" required>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi ID Login:</h6>
                            <ul class="mb-0">
                                <li>ID Login akan digunakan siswa untuk login</li>
                                <li>ID Login harus unik (tidak boleh sama)</li>
                                <li>Contoh format: K021GM, XII01A, 2023S01</li>
                                <li>Simpan informasi ID Login dengan baik</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Siswa
                            </button>
                            <a href="users.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

<script>
// Auto-uppercase untuk ID Login
document.getElementById('id_login').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Validasi form
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    const idLogin = document.getElementById('id_login').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Validasi ID Login
    const idLoginRegex = /^[A-Z0-9]{4,10}$/;
    if (!idLoginRegex.test(idLogin)) {
        e.preventDefault();
        alert('ID Login harus 4-10 karakter, hanya huruf dan angka!');
        return false;
    }
    
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