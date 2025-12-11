<?php
require_once '../config/database.php';
requireAdminLogin();

// Query untuk mengambil data user
$stmt = $pdo->query("SELECT 
    id_login,
    nama, 
    no_absen, 
    kelas, 
    status_lulus,
    DATE(created_at) as tanggal_dibuat
    FROM users 
    ORDER BY kelas, no_absen");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set header untuk file CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=daftar_siswa_' . date('Y-m-d') . '.csv');

// Buat output stream
$output = fopen('php://output', 'w');

// Tambah BOM untuk UTF-8
fwrite($output, "\xEF\xBB\xBF");

// Header CSV
fputcsv($output, ['NO', 'ID LOGIN', 'NAMA SISWA', 'NO ABSEN', 'KELAS', 'STATUS KELULUSAN', 'TANGGAL DIBUAT']);

// Data user
$no = 1;
foreach ($users as $user) {
    fputcsv($output, [
        $no++,
        $user['id_login'],
        $user['nama'],
        $user['no_absen'],
        $user['kelas'],
        $user['status_lulus'],
        $user["tanggal_dibuat"]
    ], ';');
}

fclose($output);
exit();
?>