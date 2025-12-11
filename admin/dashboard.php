<?php
require_once '../config/database.php';
require_once 'includes/header.php';

// Ambil statistik
$stats = getStatistics($pdo);
$deadline = getTimerDeadline($pdo);
$is_timer_expired = isTimerExpired($pdo);

// Hitung persentase
$persentase_lulus = $stats['total_siswa'] > 0 ? 
    round(($stats['total_lulus'] / $stats['total_siswa']) * 100, 2) : 0;
$persentase_tunda = $stats['total_siswa'] > 0 ? 
    round(($stats['total_tunda'] / $stats['total_siswa']) * 100, 2) : 0;

// Ambil 5 siswa terbaru
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$recent_users = $stmt->fetchAll();
?>
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Siswa
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_siswa']; ?>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-primary">Semua Kelas</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Lulus
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_lulus']; ?>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-success"><?php echo $persentase_lulus; ?>%</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Ditangguhkan
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_tunda']; ?>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-warning"><?php echo $persentase_tunda; ?>%</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Admin Sistem
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_admin']; ?>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-info">Aktif</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Timer Status & Recent Activity -->
            <div class="row">
                <!-- Timer Status -->
                <div class="col-xl-4 col-lg-5 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-clock me-2"></i>Status Timer
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-<?php echo $is_timer_expired ? 'check-circle text-success' : 'hourglass-half text-warning'; ?> fa-3x"></i>
                                </div>
                                <h5 class="mb-3">
                                    <?php echo $is_timer_expired ? 'SELESAI' : 'BERJALAN'; ?>
                                </h5>
                                <p class="text-muted mb-1">Deadline Pengumuman:</p>
                                <h6 class="text-primary mb-3"><?php echo formatDateTimeIndonesia($deadline); ?></h6>
                                
                                <?php if(!$is_timer_expired): ?>
                                <?php
                                $now = new DateTime();
                                $future_date = new DateTime($deadline);
                                $interval = $future_date->diff($now);
                                ?>
                                <div class="alert alert-warning">
                                    <h6>Sisa Waktu:</h6>
                                    <p class="mb-1">
                                        <?php echo $interval->format('%a'); ?> hari, 
                                        <?php echo $interval->format('%h'); ?> jam, 
                                        <?php echo $interval->format('%i'); ?> menit
                                    </p>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-success">
                                    <h6>Status:</h6>
                                    <p class="mb-0">Siswa sudah bisa login</p>
                                </div>
                                <?php endif; ?>
                                
                                <a href="timer-setting.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cog me-1"></i>Atur Timer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-history me-2"></i>Aktivitas Terbaru
                            </h6>
                            <a href="users.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>No Absen</th>
                                            <th>Kelas</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($recent_users) > 0): ?>
                                        <?php foreach($recent_users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($user['no_absen']); ?></td>
                                            <td><?php echo htmlspecialchars($user['kelas']); ?></td>
                                            <td>
                                                <?php if($user['status_lulus'] == 'LULUS'): ?>
                                                <span class="badge bg-success">LULUS</span>
                                                <?php else: ?>
                                                <span class="badge bg-warning">DITANGGUHKAN</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatDateOnly($user['created_at']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                Belum ada data siswa
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bolt me-2"></i>Aksi Cepat
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="add-user.php" class="btn btn-primary w-100 py-3">
                                        <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                        Tambah Siswa
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="users.php" class="btn btn-success w-100 py-3">
                                        <i class="fas fa-users fa-2x mb-2"></i><br>
                                        Kelola Siswa
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="export-csv.php" class="btn btn-info w-100 py-3">
                                        <i class="fas fa-file-export fa-2x mb-2"></i><br>
                                        Export Data
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="timer-setting.php" class="btn btn-warning w-100 py-3">
                                        <i class="fas fa-clock fa-2x mb-2"></i><br>
                                        Atur Timer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php require_once 'includes/footer.php'; ?>