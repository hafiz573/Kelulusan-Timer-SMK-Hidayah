<?php
require_once '../config/database.php';

$id = $_GET['id'] ?? 0;

// Cek apakah user ada
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php?error=Siswa tidak ditemukan');
    exit();
}

// Hapus user
try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: users.php?success=Siswa berhasil dihapus');
    exit();
} catch(PDOException $e) {
    header('Location: users.php?error=Gagal menghapus siswa: ' . $e->getMessage());
    exit();
}
?>