<?php
require_once '../config/database.php';
require_once 'includes/header.php';

// Ambil semua admin (kecuali yang sedang login)
$stmt = $pdo->prepare("SELECT * FROM admin WHERE id != ? ORDER BY created_at DESC");
$stmt->execute([$admin_id]);
$admins = $stmt->fetchAll();
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Daftar Admin</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Admin</th>
                                    <th>Role</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Terakhir Update</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($admins) > 0): ?>
                                <?php foreach($admins as $index => $admin): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle-sm bg-dark rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-user-shield text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($admin['nama']); ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $admin['role']; ?></span>
                                    </td>
                                    <td><?php echo formatDateOnly($admin['created_at']); ?></td>
                                    <td><?php echo formatDateOnly($admin['updated_at']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="edit-admin.php?id=<?php echo $admin['id']; ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete-admin.php?id=<?php echo $admin['id']; ?>" 
                                               class="btn btn-danger" title="Hapus"
                                               onclick="return confirm('Yakin ingin menghapus admin ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                                            <h5>Tidak ada admin lain</h5>
                                            <p>Hanya Anda yang terdaftar sebagai admin</p>
                                            <a href="add-admin.php" class="btn btn-primary">
                                                <i class="fas fa-user-plus me-2"></i>Tambah Admin
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<?php require_once 'includes/footer.php'; ?>