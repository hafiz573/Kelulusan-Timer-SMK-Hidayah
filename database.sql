-- Buat database
CREATE DATABASE IF NOT EXISTS db_kelulusan 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE db_kelulusan;

-- Set timezone default untuk sesi
SET time_zone = '+07:00';

-- Tabel users (siswa) DENGAN ID_LOGIN
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_login VARCHAR(50) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    no_absen VARCHAR(10) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    status_lulus ENUM('LULUS', 'KELULUSAN DITANGGUHKAN') NOT NULL DEFAULT 'KELULUSAN DITANGGUHKAN',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel admin
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('Admin') NOT NULL DEFAULT 'Admin',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel timer_setting
CREATE TABLE timer_setting (
    id INT PRIMARY KEY AUTO_INCREMENT,
    deadline DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Buat index untuk performa query
CREATE INDEX idx_id_login ON users(id_login);
CREATE INDEX idx_no_absen ON users(no_absen);
CREATE INDEX idx_status_lulus ON users(status_lulus);
CREATE INDEX idx_kelas ON users(kelas);
CREATE INDEX idx_admin_nama ON admin(nama);

-- View untuk monitoring waktu
CREATE VIEW timer_view AS
SELECT 
    deadline,
    DATE_FORMAT(deadline, '%W, %d %M %Y %H:%i:%s') as deadline_formatted,
    TIMESTAMPDIFF(SECOND, NOW(), deadline) as seconds_remaining,
    TIMESTAMPDIFF(DAY, NOW(), deadline) as days_remaining,
    TIMESTAMPDIFF(HOUR, NOW(), deadline) as hours_remaining,
    CASE 
        WHEN NOW() > deadline THEN 'EXPIRED'
        ELSE 'ACTIVE'
    END as status
FROM timer_setting
ORDER BY id DESC
LIMIT 1;