<?php
require_once '../config/database.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? 0;
$success = '';
$error = '';

// Ambil data admin
$stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: admins.php?error=Admin tidak ditemukan');
    exit();
}

// Cegah edit admin sendiri
if ($admin['id'] == $admin_id) {
    header('Location: admins.php?error=Tidak dapat mengedit akun sendiri');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitizeInput($_POST['nama']);
    $change_password = isset($_POST['change_password']) && $_POST['change_password'] == '1';
    
    // Validasi nama
    if (empty($nama)) {
        $error = 'Nama tidak boleh kosong!';
    } else {
        // Cek apakah nama sudah digunakan oleh admin lain
        $stmt = $pdo->prepare("SELECT id FROM admin WHERE nama = ? AND id != ?");
        $stmt->execute([$nama, $id]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Nama admin sudah digunakan!';
        } else {
            $sql = "UPDATE admin SET nama = ?";
            $params = [$nama];
            
            // Jika password diubah
            if ($change_password) {
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                
                if (empty($password)) {
                    $error = 'Password tidak boleh kosong!';
                } elseif (strlen($password) < 6) {
                    $error = 'Password minimal 6 karakter!';
                } elseif ($password !== $confirm_password) {
                    $error = 'Password dan konfirmasi password tidak sama!';
                } else {
                    $hashed_password = hashPassword($password);
                    $sql .= ", password = ?";
                    $params[] = $hashed_password;
                }
            }
            
            if (!$error) {
                $sql .= " WHERE id = ?";
                $params[] = $id;
                
                try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    
                    $success = 'Admin berhasil diupdate!';
                    
                    // Update data admin
                    $admin['nama'] = $nama;
                } catch(PDOException $e) {
                    $error = 'Gagal mengupdate admin: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Admin</h5>
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
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="nama" class="form-label">
                                        <i class="fas fa-user me-2"></i>Nama Admin *
                                    </label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?php echo htmlspecialchars($admin['nama']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-user-tag me-2"></i>Role
                                    </label>
                                    <input type="text" class="form-control" value="<?php echo $admin['role']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="change_password" 
                                               name="change_password" value="1">
                                        <label class="form-check-label" for="change_password">
                                            <i class="fas fa-key me-2"></i>Ubah Password
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Tanggal Dibuat
                                    </label>
                                    <input type="text" class="form-control" 
                                           value="<?php echo formatDateTimeIndonesia($admin['created_at']); ?>" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Password fields (hidden by default) -->
                        <div id="password_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Password Baru *
                                        </label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <div class="form-text">Minimal 6 karakter</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Konfirmasi Password *
                                        </label>
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi:</h6>
                            <ul class="mb-0">
                                <li>Kosongkan checkbox password jika tidak ingin mengubah password</li>
                                <li>ID Admin tidak dapat diubah</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Admin
                            </button>
                            <a href="admins.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

<script>
// Toggle password fields
document.getElementById('change_password').addEventListener('change', function() {
    const passwordFields = document.getElementById('password_fields');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (this.checked) {
        passwordFields.style.display = 'block';
        passwordInput.required = true;
        confirmPasswordInput.required = true;
    } else {
        passwordFields.style.display = 'none';
        passwordInput.required = false;
        confirmPasswordInput.required = false;
        passwordInput.value = '';
        confirmPasswordInput.value = '';
    }
});

// Password validation
document.querySelector('form').addEventListener('submit', function(e) {
    const changePassword = document.getElementById('change_password').checked;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (changePassword) {
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
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>