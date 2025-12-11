<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitizeInput($_POST['nama']);
    $password = $_POST['password'];
    
    if (empty($nama) || empty($password)) {
        $error = "Nama dan password harus diisi!";
    } else {
        $hashed_password = hashPassword($password);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO admin (nama, password) VALUES (?, ?)");
            $stmt->execute([$nama, $hashed_password]);
            
            $success = "Admin berhasil dibuat!";
            $password_display = $password; // Simpan untuk ditampilkan
            $hash_display = $hashed_password;
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Create Admin Account</h3>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?><br>
                        <strong>Nama:</strong> <?php echo $nama; ?><br>
                        <strong>Password:</strong> <?php echo $password_display; ?><br>
                        <strong>Hash:</strong> <?php echo $hash_display; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label>Nama Admin:</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Admin</button>
                        <a href="index.php" class="btn btn-secondary">Back to Home</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>