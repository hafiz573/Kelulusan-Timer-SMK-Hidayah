<?php
require_once '../config/database.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_ids']) && is_array($_POST['user_ids'])) {
        $ids = $_POST['user_ids'];
        $deletedCount = 0;
        
        if (count($ids) > 0) {
            try {
                // Prepare a dynamic query with ? placeholders
                $inQuery = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($inQuery)");
                $stmt->execute($ids);
                
                $deletedCount = $stmt->rowCount();
                
                $_SESSION['success'] = "$deletedCount data siswa berhasil dihapus secara massal.";
            } catch(PDOException $e) {
                $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Tidak ada siswa yang dipilih untuk dihapus.";
        }
    } else {
        $_SESSION['error'] = "Tidak ada siswa yang dipilih.";
    }
} else {
    $_SESSION['error'] = "Akses tidak valid.";
}

header('Location: users.php');
exit();
?>
