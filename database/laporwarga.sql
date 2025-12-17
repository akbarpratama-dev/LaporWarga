-- Database: LaporWarga2
-- LaporWarga - Citizen Reporting System
-- Created: December 2025

CREATE DATABASE IF NOT EXISTS LaporWarga2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE LaporWarga2;

-- Table: admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: laporan
CREATE TABLE IF NOT EXISTS laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_laporan VARCHAR(20) NOT NULL UNIQUE,
    nama_pelapor VARCHAR(100) NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('Diterima', 'Diproses', 'Selesai') DEFAULT 'Diterima',
    tanggal_lapor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_selesai DATETIME DEFAULT NULL,
    biaya DECIMAL(15,2) DEFAULT NULL,
    durasi VARCHAR(50) DEFAULT NULL,
    foto_after VARCHAR(255) DEFAULT NULL,
    catatan_admin TEXT DEFAULT NULL,
    INDEX idx_kode (kode_laporan),
    INDEX idx_status (status),
    INDEX idx_tanggal (tanggal_lapor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: info_warga
CREATE TABLE IF NOT EXISTS info_warga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tanggal (tanggal_mulai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: admin123)
INSERT INTO admin (username, password, nama_lengkap) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert sample info_warga
INSERT INTO info_warga (judul, deskripsi, lokasi, tanggal_mulai, tanggal_selesai) VALUES
('Pemeliharaan Jalan RT 05', 'Pengaspalan ulang jalan utama RT 05 sepanjang 500 meter untuk meningkatkan kenyamanan warga', 'Jl. Mawar RT 05 RW 02', '2025-12-20', '2025-12-27'),
('Gotong Royong Bersama', 'Kegiatan bersih-bersih lingkungan dan penanaman pohon di area taman RW 02', 'Taman RW 02', '2025-12-22', '2025-12-22'),
('Perbaikan Saluran Air', 'Normalisasi dan pembersihan saluran air untuk mencegah banjir di musim hujan', 'Jl. Melati RT 03 RW 02', '2025-12-18', '2025-12-25');

-- Insert sample laporan
INSERT INTO laporan (kode_laporan, nama_pelapor, no_hp, kategori, deskripsi, lokasi, status, biaya, durasi, tanggal_selesai) VALUES
('LPR-20251201-001', 'Budi Santoso', '081234567890', 'Jalan Rusak', 'Jalan berlubang besar di depan rumah nomor 15', 'Jl. Kenanga No. 15 RT 01', 'Selesai', 2500000.00, '3 hari', '2025-12-10 15:30:00'),
('LPR-20251205-002', 'Siti Nurhaliza', '082345678901', 'Lampu Jalan', 'Lampu jalan mati sudah 1 minggu', 'Jl. Anggrek RT 02', 'Selesai', 350000.00, '1 hari', '2025-12-12 10:00:00'),
('LPR-20251210-003', 'Ahmad Hidayat', '083456789012', 'Saluran Air', 'Saluran air tersumbat sampah', 'Jl. Dahlia RT 03', 'Diproses', NULL, NULL, NULL);
