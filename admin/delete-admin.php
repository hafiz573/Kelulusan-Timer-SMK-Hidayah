<?php
require_once '../config/database.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? 0;

// Cegah penghapusan admin sendiri
if ($id == $admin_id) {
    header('Location: admins.php?error=Tidak dapat menghapus akun sendiri');
    exit();
}

// Cek apakah admin ada
$stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    header('Location: admins.php?error=Admin tidak ditemukan');
    exit();
}

// Hapus admin
try {
    $stmt = $pdo->prepare("DELETE FROM admin WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: admins.php?success=Admin berhasil dihapus');
    exit();
} catch(PDOException $e) {
    header('Location: admins.php?error=Gagal menghapus admin: ' . $e->getMessage());
    exit();
}
?>