<?php
// ==============================================
// HARUS DI AWAL - Output buffering
// ==============================================
ob_start();

require_once '../config/database.php';
require_once 'includes/header.php';

$success = '';
$error = '';
$imported_count = 0;
$failed_rows = [];

// ==============================================
// DOWNLOAD TEMPLATE - SESUAI GAMBAR
// ==============================================
if (isset($_GET['download_template'])) {
    // Clear semua buffer output sebelumnya agar file tidak corrupt
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set headers untuk download file CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=template_import_siswa_' . date('Y-m-d') . '.csv');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Buka output stream
    $output = fopen('php://output', 'w');
    
    // Tambah BOM (Byte Order Mark) agar Excel bisa membaca karakter UTF-8 dengan benar
    fwrite($output, "\xEF\xBB\xBF");
    
    // 1. Header CSV (Sesuai Gambar)
    fputcsv($output, [
        'ID_LOGIN', 
        'NAMA', 
        // 'NO_ABSEN', 
        'KELAS', 
        'STATUS_LULUS', 
        'PASSWORD'
    ], ';'); // Menggunakan delimiter titik koma (;) sesuai standar Excel Indonesia, atau ganti ',' jika perlu
    
    // 2. Data Baris 1 (Hafiz)
    fputcsv($output, [
        'B021G', 
        'Hafiz', 
        'XII RPL', 
        'KELULUSAN DITANGGUHKAN', 
        '12345678'
    ], ';');

    // 3. Data Baris 2 (Faiq)
    fputcsv($output, [
        'J03167', 
        'Faiq', 
        'XII RPL', 
        'LULUS', 
        '12345678'
    ], ';');

    // 4. Data Baris 3 (KIVA)
    fputcsv($output, [
        'K73131', 
        'KIVA', 
        'XII RPL', 
        'KELULUSAN DITANGGUHKAN', 
        '12345678'
    ], ';');
    
    // Tutup stream dan hentikan script
    fclose($output);
    exit();
}

// ==============================================
// IMPORT LOGIC
// ==============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];
    
    // Validasi file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Error upload file: ' . $file['error'];
    } elseif ($file['type'] !== 'text/csv' && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
        $error = 'File harus berformat CSV (.csv)';
    } elseif ($file['size'] > 5242880) { // 5MB max
        $error = 'Ukuran file maksimal 5MB';
    } else {
        // Buka file CSV
        if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
            $pdo->beginTransaction();
            
            try {
                $row = 0;
                $headers = [];
                
                while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                    $row++;
                    
                    // Baris pertama sebagai header
                    if ($row === 1) {
                        $headers = array_map('trim', $data);
                        continue;
                    }
                    
                    // Validasi jumlah kolom
                    if (count($data) < 5) {
                        $failed_rows[] = [
                            'row' => $row,
                            'data' => $data,
                            'error' => 'Jumlah kolom tidak sesuai'
                        ];
                        continue;
                    }
                    
                    // Mapping data
                    $data = array_map('trim', $data);
                    
                    // Ambil data dari CSV
                    $id_login = strtoupper($data[0] ?? '');
                    $nama = $data[1] ?? '';
                    // $no_absen = $data[2] ?? '';
                    $kelas = $data[2] ?? '';
                    $status_lulus = strtoupper($data[3] ?? '');
                    $password = $data[4] ?? '';
                    
                    // Validasi data
                    $validation_errors = [];
                    
                    if (empty($id_login)) {
                        $validation_errors[] = 'ID Login kosong';
                    } elseif (!preg_match('/^[A-Z0-9]{4,10}$/', $id_login)) {
                        $validation_errors[] = 'Format ID Login salah';
                    }
                    
                    if (empty($nama)) {
                        $validation_errors[] = 'Nama kosong';
                    }
                    
                    // if (empty($no_absen)) {
                    //     $validation_errors[] = 'No Absen kosong';
                    // }
                    
                    if (empty($kelas)) {
                        $validation_errors[] = 'Kelas kosong';
                    }
                    
                    if (!in_array($status_lulus, ['LULUS', 'KELULUSAN DITANGGUHKAN'])) {
                        $validation_errors[] = 'Status kelulusan tidak valid';
                    }
                    
                    if (empty($password)) {
                        $validation_errors[] = 'Password kosong';
                    } elseif (strlen($password) < 6) {
                        $validation_errors[] = 'Password minimal 6 karakter';
                    }
                    
                    // Jika ada error validasi
                    if (!empty($validation_errors)) {
                        $failed_rows[] = [
                            'row' => $row,
                            'data' => $data,
                            'error' => implode(', ', $validation_errors)
                        ];
                        continue;
                    }
                    
                    // Cek apakah ID Login sudah ada
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE id_login = ?");
                    $stmt->execute([$id_login]);
                    
                    if ($stmt->rowCount() > 0) {
                        $failed_rows[] = [
                            'row' => $row,
                            'data' => $data,
                            'error' => 'ID Login sudah digunakan'
                        ];
                        continue;
                    }
                    
                    // Cek apakah No Absen sudah ada
                    // $stmt = $pdo->prepare("SELECT id FROM users WHERE no_absen = ?");
                    // $stmt->execute([$no_absen]);
                    
                    // Hash password
                    $hashed_password = hashPassword($password);
                    
                    // Insert data ke database
                    $stmt = $pdo->prepare("
                        INSERT INTO users (id_login, nama, kelas, status_lulus, password) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    if ($stmt->execute([$id_login, $nama, $kelas, $status_lulus, $hashed_password])) {
                        $imported_count++;
                    } else {
                        $failed_rows[] = [
                            'row' => $row,
                            'data' => $data,
                            'error' => 'Gagal insert ke database'
                        ];
                    }
                }
                
                fclose($handle);
                
                // Commit transaksi
                $pdo->commit();
                
                if ($imported_count > 0) {
                    $success = "Berhasil mengimpor $imported_count data siswa.";
                    
                    if (!empty($failed_rows)) {
                        $success .= " " . count($failed_rows) . " data gagal diimpor.";
                    }
                } else {
                    $error = "Tidak ada data yang berhasil diimpor.";
                }
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Error saat mengimpor data: " . $e->getMessage();
            }
        } else {
            $error = "Gagal membuka file CSV";
        }
    }
}

// ==============================================
// HTML OUTPUT - SETELAH SEMUA LOGIC
// ==============================================
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-import me-2"></i>Import Data Siswa dari CSV</h5>
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Upload Form -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Upload File CSV</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" enctype="multipart/form-data" id="importForm">
                                        <div class="mb-4">
                                            <label for="csv_file" class="form-label">Pilih File CSV</label>
                                            <input type="file" class="form-control" id="csv_file" name="csv_file" 
                                                   accept=".csv" required>
                                            <div class="form-text">
                                                Format file: CSV (Comma Separated Values)
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-info-circle me-2"></i>Panduan:</h6>
                                            <ul class="mb-0">
                                                <li>File harus berformat CSV dengan encoding UTF-8</li>
                                                <li>Maksimal ukuran file: 5MB</li>
                                                <li>Pastikan format kolom sesuai template</li>
                                                <li>Data yang sudah ada (ID Login) akan dilewati</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-upload me-2"></i>Import Data
                                            </button>
                                            <a href="import-csv.php?download_template=1" class="btn btn-success">
                                                <i class="fas fa-download me-2"></i>Download Template
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Format Template -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-table me-2"></i>Format CSV yang Benar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Kolom</th>
                                                    <th>Contoh</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>ID_LOGIN</strong></td>
                                                    <td>K021GM</td>
                                                    <td>4-10 karakter huruf/angka, UNIQUE</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>NAMA</strong></td>
                                                    <td>Hafiz Muhammad Fiqar</td>
                                                    <td>Nama lengkap siswa</td>
                                                </tr>
                                                <!-- <tr>
                                                    <td><strong>NO_ABSEN</strong></td>
                                                    <td>11</td>
                                                    <td>Nomor absen, UNIQUE</td>
                                                </tr> -->
                                                <tr>
                                                    <td><strong>KELAS</strong></td>
                                                    <td>XII RPL</td>
                                                    <td>Kelas siswa</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>STATUS_LULUS</strong></td>
                                                    <td>LULUS</td>
                                                    <td>LULUS / KELULUSAN DITANGGUHKAN</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>PASSWORD</strong></td>
                                                    <td>siswa123</td>
                                                    <td>Minimal 6 karakter</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</h6>
                                        <ul class="mb-0">
                                            <li>Pastikan header sesuai (baris pertama)</li>
                                            <li>Gunakan colum sebagai pemisah</li>
                                            <li>Password akan di-hash secara otomatis</li>
                                            <li>Data duplikat akan dilewati</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Failed Rows Display -->
                    <?php if(!empty($failed_rows)): ?>
                    <div class="card mt-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Data yang Gagal Diimport (<?php echo count($failed_rows); ?> baris)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="50">Baris</th>
                                            <th>ID Login</th>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>Status</th>
                                            <th>Password</th>
                                            <th>Error</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($failed_rows as $failed): ?>
                                        <tr class="table-danger">
                                            <td><?php echo $failed['row']; ?></td>
                                            <td><?php echo htmlspecialchars($failed['data'][0] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($failed['data'][1] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($failed['data'][2] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($failed['data'][3] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($failed['data'][4] ?? '-'); ?></td>
                                            <td class="text-danger">
                                                <i class="fas fa-times-circle me-1"></i>
                                                <?php echo htmlspecialchars($failed['error']); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-danger" id="exportFailedBtn">
                                    <i class="fas fa-file-export me-1"></i>Export Error Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Import Statistics -->
                    <?php if($imported_count > 0): ?>
                    <div class="card mt-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Statistik Import
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="stat-box bg-primary text-white p-3 rounded">
                                        <h3 class="mb-0"><?php echo $imported_count; ?></h3>
                                        <p class="mb-0">Berhasil Diimport</p>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="stat-box bg-danger text-white p-3 rounded">
                                        <h3 class="mb-0"><?php echo count($failed_rows); ?></h3>
                                        <p class="mb-0">Gagal Diimport</p>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="stat-box bg-info text-white p-3 rounded">
                                        <h3 class="mb-0"><?php echo $imported_count + count($failed_rows); ?></h3>
                                        <p class="mb-0">Total Baris</p>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="stat-box bg-warning text-white p-3 rounded">
                                        <?php 
                                        $success_rate = ($imported_count + count($failed_rows)) > 0 
                                            ? round(($imported_count / ($imported_count + count($failed_rows))) * 100, 2) 
                                            : 0;
                                        ?>
                                        <h3 class="mb-0"><?php echo $success_rate; ?>%</h3>
                                        <p class="mb-0">Success Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Navigation -->
                    <div class="mt-4">
                        <a href="users.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Siswa
                        </a>
                        <a href="add-user.php" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-user-plus me-2"></i>Tambah Manual
                        </a>
                    </div>
                </div>
            </div>

<?php require_once 'includes/footer.php'; ?>