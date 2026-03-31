<?php
require_once '../config/database.php';
require_once 'includes/header.php';

// Ambil semua siswa
$stmt = $pdo->query("SELECT * FROM users ORDER BY kelas, id_login");
$users = $stmt->fetchAll();
?>
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Siswa</h5>
                    <button type="button" class="btn btn-danger btn-sm" id="btnHapusTerpilih" style="display:none;" onclick="hapusTerpilih()">
                        <i class="fas fa-trash me-2"></i>Hapus Terpilih (<span id="countTerpilih">0</span>)
                    </button>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form id="formBulkDelete" action="delete-multiple-users.php" method="POST">
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead class="table-light">
                                <tr>
                                    <th width="30">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th width="50">No</th>
                                    <th>ID Login</th>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($users) > 0): ?>
                                <?php foreach($users as $index => $user): ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="<?php echo $user['id']; ?>">
                                        </div>
                                    </td>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="badge bg-info text-black">
                                            <i class="fas fa-id-card me-1"></i>
                                            <?php echo htmlspecialchars($user['id_login']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($user['nisn'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($user['nis'] ?? '-'); ?></td>
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
                    </form>
                </div>
            </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const btnHapusTerpilih = document.getElementById('btnHapusTerpilih');
    const countTerpilih = document.getElementById('countTerpilih');
    
    function toggleDeleteBtn() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        if (checkedCount > 0) {
            btnHapusTerpilih.style.display = 'inline-block';
            countTerpilih.innerText = checkedCount;
        } else {
            btnHapusTerpilih.style.display = 'none';
        }
    }
    
    if(selectAll) {
        selectAll.addEventListener('change', function() {
            userCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            toggleDeleteBtn();
        });
    }
    
    userCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.user-checkbox:checked').length === userCheckboxes.length;
            selectAll.checked = allChecked;
            toggleDeleteBtn();
        });
    });
});

function hapusTerpilih() {
    if(confirm('Yakin ingin menghapus semua siswa yang dipilih? Aksi ini tidak dapat dibatalkan!')) {
        document.getElementById('formBulkDelete').submit();
    }
}

function viewUser(userId) {
    // Implement view user details modal
    alert('Fitur detail siswa akan ditampilkan di sini. ID: ' + userId);
}
</script>

<?php require_once 'includes/footer.php'; ?>