<?php
require_once '../config/database.php';
require_once 'includes/header.php';

// Ambil semua siswa
$stmt = $pdo->query("SELECT * FROM users ORDER BY kelas, id_login");
$users = $stmt->fetchAll();
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>ID Login</th>
                                    <th>Nama Siswa</th>
                                    <th>No Absen</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($users) > 0): ?>
                                <?php foreach($users as $index => $user): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="badge bg-info text-black">
                                            <i class="fas fa-id-card me-1"></i>
                                            <?php echo htmlspecialchars($user['id_login']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                    <td><?php // echo htmlspecialchars($user['no_absen']); ?></td>
                                    <td><?php echo htmlspecialchars($user['kelas']); ?></td>
                                    <td>
                                        <?php if($user['status_lulus'] == 'LULUS'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>LULUS
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>DITANGGUHKAN
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit-user.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete-user.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-danger" title="Hapus"
                                               onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<script>
function viewUser(userId) {
    // Implement view user details modal
    alert('Fitur detail siswa akan ditampilkan di sini. ID: ' + userId);
}
</script>

<?php require_once 'includes/footer.php'; ?>