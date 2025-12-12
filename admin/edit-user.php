<?php
require_once '../config/database.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? 0;
$success = '';
$error = '';

// Ambil data siswa
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php?error=Siswa tidak ditemukan');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_login = strtoupper(sanitizeInput($_POST['id_login']));
    $nama = sanitizeInput($_POST['nama']);
    // $no_absen = sanitizeInput($_POST['no_absen']);
    $kelas = sanitizeInput($_POST['kelas']);
    $status_lulus = $_POST['status_lulus'];
    $change_password = isset($_POST['change_password']) && $_POST['change_password'] == '1';
    
    // Validasi
    if (empty($id_login) || empty($nama) || empty($kelas)) {
        $error = 'Semua field wajib tidak boleh kosong!';
    } elseif (!preg_match('/^[A-Z0-9]{4,10}$/', $id_login)) {
        $error = 'ID Login harus 4-10 karakter, hanya huruf dan angka!';
    } else {
        // Cek apakah id_login sudah digunakan oleh user lain
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id_login = ? AND id != ?");
        $stmt->execute([$id_login, $id]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'ID Login sudah digunakan oleh siswa lain!';
            } else {
                $sql = "UPDATE users SET id_login = ?, nama = ?, kelas = ?, status_lulus = ?";
                $params = [$id_login, $nama, $kelas, $status_lulus];
                
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
                        
                        $success = 'Data siswa berhasil diupdate!';
                        
                        // Update data user untuk ditampilkan
                        $user['id_login'] = $id_login;
                        $user['nama'] = $nama;
                        // $user['no_absen'] = $no_absen;
                        $user['kelas'] = $kelas;
                        $user['status_lulus'] = $status_lulus;
                    } catch(PDOException $e) {
                        $error = 'Gagal mengupdate siswa: ' . $e->getMessage();
                    }
                }
            }
        }
    }
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Data Siswa</h5>
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
                    
                    <form method="POST" action="" id="editUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_login" class="form-label">
                                    <i class="fas fa-id-card me-2"></i>ID Login *
                                </label>
                                <input type="text" class="form-control" id="id_login" name="id_login" 
                                       value="<?php echo htmlspecialchars($user['id_login']); ?>" 
                                       placeholder="Contoh: K021GM" required
                                       pattern="[A-Z0-9]{4,10}"
                                       title="4-10 karakter huruf/angka (contoh: K021GM)">
                                <div class="form-text">ID Login unik untuk login siswa</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- <div class="col-md-6 mb-3">
                                <label for="no_absen" class="form-label">No Absen *</label>
                                <input type="text" class="form-control" id="no_absen" name="no_absen" 
                                       value="<?php echo htmlspecialchars($user['no_absen']); ?>" required>
                            </div> -->
                            <div class="col-md-6 mb-3">
                                <label for="kelas" class="form-label">Kelas *</label>
                                <input type="text" class="form-control" id="kelas" name="kelas" 
                                       value="<?php echo htmlspecialchars($user['kelas']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status_lulus" class="form-label">Status Kelulusan *</label>
                                <select class="form-select" id="status_lulus" name="status_lulus" required>
                                    <option value="KELULUSAN DITANGGUHKAN" 
                                        <?php echo $user['status_lulus'] == 'KELULUSAN DITANGGUHKAN' ? 'selected' : ''; ?>>
                                        KELULUSAN DITANGGUHKAN
                                    </option>
                                    <option value="LULUS" 
                                        <?php echo $user['status_lulus'] == 'LULUS' ? 'selected' : ''; ?>>
                                        LULUS
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="change_password" 
                                           name="change_password" value="1">
                                    <label class="form-check-label" for="change_password">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Password fields (hidden by default) -->
                        <div id="password_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password Baru *</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <div class="form-text">Minimal 6 karakter</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Dibuat</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo formatDateTimeIndonesia($user['created_at']); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Terakhir Update</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo formatDateTimeIndonesia($user['updated_at']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi Penting:</h6>
                            <ul class="mb-0">
                                <li>ID Login digunakan siswa untuk login ke sistem</li>
                                <li>Pastikan ID Login unik dan mudah diingat siswa</li>
                                <li>Centang "Ubah Password" hanya jika ingin mengganti password</li>
                                <li>Password default untuk siswa baru: <strong>siswa123</strong></li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Data Siswa
                            </button>
                            <a href="users.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                            <button type="button" class="btn btn-info" onclick="copyLoginInfo()">
                                <i class="fas fa-copy me-2"></i>Salin Info Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>

<script>
// Auto-uppercase untuk ID Login
document.getElementById('id_login').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

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

// Form validation
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    const idLogin = document.getElementById('id_login').value;
    const changePassword = document.getElementById('change_password').checked;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Validasi ID Login
    const idLoginRegex = /^[A-Z0-9]{4,10}$/;
    if (!idLoginRegex.test(idLogin)) {
        e.preventDefault();
        alert('ID Login harus 4-10 karakter, hanya huruf dan angka!');
        return false;
    }
    
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

// Copy login info to clipboard
function copyLoginInfo() {
    const idLogin = document.getElementById('id_login').value;
    const nama = document.getElementById('nama').value;
    
    if (!idLogin || !nama) {
        alert('Harap isi ID Login dan Nama terlebih dahulu');
        return;
    }
    
    const infoText = `Informasi Login Siswa:\n\nNama: ${nama}\nID Login: ${idLogin}\n\nGunakan ID Login ini untuk masuk ke sistem kelulusan.`;
    
    navigator.clipboard.writeText(infoText).then(function() {
        alert('Informasi login berhasil disalin ke clipboard!');
    }).catch(function(err) {
        console.error('Gagal menyalin: ', err);
        alert('Gagal menyalin informasi. Silahkan salin manual:\n\n' + infoText);
    });
}

// Real-time validation untuk ID Login
document.getElementById('id_login').addEventListener('blur', function() {
    const idLogin = this.value;
    const idLoginRegex = /^[A-Z0-9]{4,10}$/;
    
    if (idLogin && !idLoginRegex.test(idLogin)) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
        
        const errorDiv = document.getElementById('id_login_error') || document.createElement('div');
        errorDiv.id = 'id_login_error';
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = 'ID Login harus 4-10 karakter huruf/angka (contoh: K021GM)';
        
        if (!this.nextElementSibling || this.nextElementSibling.id !== 'id_login_error') {
            this.parentNode.insertBefore(errorDiv, this.nextSibling);
        }
    } else if (idLogin) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        
        const errorDiv = document.getElementById('id_login_error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
});

// Real-time validation untuk password match
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
        
        const errorDiv = document.getElementById('password_match_error') || document.createElement('div');
        errorDiv.id = 'password_match_error';
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = 'Password tidak sama';
        
        if (!this.nextElementSibling || this.nextElementSibling.id !== 'password_match_error') {
            this.parentNode.insertBefore(errorDiv, this.nextSibling);
        }
    } else if (confirmPassword) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        
        const errorDiv = document.getElementById('password_match_error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
});

// Generate contoh ID Login
function generateExampleId() {
    const kelas = document.getElementById('kelas').value;
    
    if (!kelas) {
        alert('Harap isi Kelas terlebih dahulu');
        return;
    }
    
    // Format: [3 huruf kelas][2 digit absen][2 huruf random]
    const kelasCode = kelas.replace(/[^A-Z]/gi, '').substring(0, 3).toUpperCase();
    const absenCode = noAbsen.padStart(2, '0');
    
    // Random 2 huruf
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const randomLetters = letters[Math.floor(Math.random() * 26)] + letters[Math.floor(Math.random() * 26)];
    
    const generatedId = kelasCode + absenCode + randomLetters;
    document.getElementById('id_login').value = generatedId;
    
    // Trigger validation
    document.getElementById('id_login').dispatchEvent(new Event('blur'));
}

// // Add generate button
// document.addEventListener('DOMContentLoaded', function() {
//     const idLoginGroup = document.getElementById('id_login').parentNode;
//     const generateButton = document.createElement('button');
//     generateButton.type = 'button';
//     generateButton.className = 'btn btn-sm btn-outline-secondary mt-2';
//     generateButton.innerHTML = '<i class="fas fa-magic me-1"></i> Generate ID Login';
//     generateButton.onclick = generateExampleId;
    
//     idLoginGroup.appendChild(generateButton);
// });

// Auto-generate ID Login jika kosong berdasarkan kelas dan absen
document.getElementById('kelas').addEventListener('blur', function() {
    const idLogin = document.getElementById('id_login').value;
    const kelas = this.value;
    
    if (!idLogin && kelas) {
        generateExampleId();
    }
});

document.getElementById('no_absen').addEventListener('blur', function() {
    const idLogin = document.getElementById('id_login').value;
    const kelas = document.getElementById('kelas').value;
    const noAbsen = this.value;
    
    if (!idLogin && kelas) {
        generateExampleId();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>