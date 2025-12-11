<?php
require_once '../config/database.php';
require_once 'includes/header.php';

$success = '';
$error = '';

// Ambil data timer saat ini
$stmt = $pdo->query("SELECT * FROM timer_setting ORDER BY id DESC LIMIT 1");
$timer = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deadline = $_POST['deadline'];
    
    if (empty($deadline)) {
        $error = 'Tanggal deadline harus diisi!';
    } else {
        try {
            if ($timer) {
                // Update existing timer
                $stmt = $pdo->prepare("UPDATE timer_setting SET deadline = ? WHERE id = ?");
                $stmt->execute([$deadline, $timer['id']]);
            } else {
                // Insert new timer
                $stmt = $pdo->prepare("INSERT INTO timer_setting (deadline) VALUES (?)");
                $stmt->execute([$deadline]);
            }
            
            $success = 'Timer berhasil diupdate!';
            
            // Update $timer variable
            $timer['deadline'] = $deadline;
        } catch(PDOException $e) {
            $error = 'Gagal mengupdate timer: ' . $e->getMessage();
        }
    }
}

// Format tanggal untuk input datetime-local
$current_deadline = $timer ? date('Y-m-d\TH:i', strtotime($timer['deadline'])) : date('Y-m-d\TH:i');
$is_expired = isTimerExpired($pdo);
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Setting Timer Kelulusan</h5>
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
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <label for="deadline" class="form-label">Tanggal & Waktu Deadline *</label>
                                    <input type="datetime-local" class="form-control" id="deadline" 
                                           name="deadline" value="<?php echo $current_deadline; ?>" required>
                                    <div class="form-text">
                                        Atur kapan countdown berakhir dan pengumuman dibuka
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Informasi:</h6>
                                    <ul class="mb-0">
                                        <li>Timer akan berhenti saat mencapai waktu ini</li>
                                        <li>Tombol login akan muncul setelah waktu habis</li>
                                        <li>Pastikan waktu sudah sesuai sebelum disimpan</li>
                                    </ul>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Preview Countdown</h6>
                                </div>
                                <div class="card-body text-center">
                                    <?php if($timer): ?>
                                    <div class="mb-4">
                                        <p class="text-muted mb-1">Deadline saat ini:</p>
                                        <h4 class="text-primary mb-3">
                                            <?php echo formatDateTimeIndonesia($timer['deadline']); ?>
                                        </h4>
                                        
                                        <div class="alert <?php echo $is_expired ? 'alert-success' : 'alert-warning'; ?>">
                                            <h6 class="mb-2">
                                                <i class="fas fa-<?php echo $is_expired ? 'check-circle' : 'hourglass-half'; ?> me-2"></i>
                                                Status: <?php echo $is_expired ? 'SELESAI' : 'BERJALAN'; ?>
                                            </h6>
                                            <?php if(!$is_expired): ?>
                                            <?php
                                            $now = new DateTime();
                                            $future_date = new DateTime($timer['deadline']);
                                            $interval = $future_date->diff($now);
                                            ?>
                                            <p class="mb-0">
                                                Sisa: <?php echo $interval->format('%a'); ?> hari
                                                <?php echo $interval->format('%h'); ?> jam
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-muted py-4">
                                        <i class="fas fa-clock fa-3x mb-3"></i>
                                        <p>Belum ada pengaturan timer</p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                                            <i class="fas fa-sync me-1"></i>Refresh Preview
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timer Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-question-circle me-2"></i>Panduan Timer
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <i class="fas fa-play-circle fa-2x text-primary mb-2"></i>
                                                <h6>Timer Berjalan</h6>
                                                <p class="mb-0 small">Siswa belum bisa login</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <i class="fas fa-stop-circle fa-2x text-success mb-2"></i>
                                                <h6>Timer Selesai</h6>
                                                <p class="mb-0 small">Siswa bisa login</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-center p-3 border rounded">
                                                <i class="fas fa-redo fa-2x text-warning mb-2"></i>
                                                <h6>Reset Timer</h6>
                                                <p class="mb-0 small">Atur ulang deadline</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php require_once 'includes/footer.php'; ?>