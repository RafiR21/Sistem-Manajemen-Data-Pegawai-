-- ============================================================
-- SISTEM MANAJEMEN DATA PEGAWAI
-- Skema Database MySQL
-- Dibuat untuk Uji Kompetensi Junior Web Programmer
-- ============================================================

-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS db_sistem_pegawai 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Menggunakan database yang baru dibuat
USE db_sistem_pegawai;

-- ============================================================
-- TABEL: pegawai
-- Deskripsi: Menyimpan data lengkap pegawai perusahaan
-- Relasi: Tabel utama (tidak memiliki foreign key ke tabel lain)
-- ============================================================
DROP TABLE IF EXISTS pegawai;

CREATE TABLE pegawai (
    -- Primary Key: ID unik untuk setiap pegawai
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID unik pegawai (Primary Key)',
    
    -- Data Identitas Pegawai
    nip VARCHAR(20) NOT NULL UNIQUE COMMENT 'Nomor Induk Pegawai (unik)',
    nama_lengkap VARCHAR(100) NOT NULL COMMENT 'Nama lengkap pegawai',
    tempat_lahir VARCHAR(50) DEFAULT NULL COMMENT 'Tempat lahir pegawai',
    tanggal_lahir DATE NOT NULL COMMENT 'Tanggal lahir pegawai (untuk kalkulasi usia)',
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL COMMENT 'Jenis kelamin pegawai',
    
    -- Data Kontak
    alamat TEXT DEFAULT NULL COMMENT 'Alamat lengkap pegawai',
    no_telepon VARCHAR(15) DEFAULT NULL COMMENT 'Nomor telepon/HP pegawai',
    email VARCHAR(100) DEFAULT NULL COMMENT 'Email pegawai (harus valid)',
    
    -- Data Pekerjaan & Pendidikan
    jabatan VARCHAR(100) DEFAULT NULL COMMENT 'Jabatan posisi pegawai',
    departemen VARCHAR(100) DEFAULT NULL COMMENT 'Departemen/bagian pegawai',
    pendidikan_terakhir ENUM('SMA/SMK', 'D3', 'S1', 'S2', 'S3') NOT NULL COMMENT 'Tingkat pendidikan terakhir',
    
    -- Status & Metadata
    status_kerja ENUM('Aktif', 'Cuti', 'Resign', 'Pensiun') DEFAULT 'Aktif' COMMENT 'Status kerja pegawai saat ini',
    tanggal_gabung DATE DEFAULT NULL COMMENT 'Tanggal pegawai bergabung',
    foto_profil VARCHAR(255) DEFAULT NULL COMMENT 'Path file foto profil pegawai',
    
    -- Timestamp (audit trail)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu data dibuat',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Waktu data terakhir diupdate'
) ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci
COMMENT='Tabel data pegawai perusahaan';

-- ============================================================
-- INDEX: Meningkatkan performa query pada kolom yang sering dicari
-- ============================================================

-- Index untuk pencarian berdasarkan nama
CREATE INDEX idx_nama ON pegawai(nama_lengkap);

-- Index untuk filter berdasarkan jenis kelamin (grafik dashboard)
CREATE INDEX idx_jenis_kelamin ON pegawai(jenis_kelamin);

-- Index untuk filter berdasarkan pendidikan terakhir (grafik dashboard)
CREATE INDEX idx_pendidikan ON pegawai(pendidikan_terakhir);

-- Index untuk filter berdasarkan status kerja
CREATE INDEX idx_status_kerja ON pegawai(status_kerja);

-- Index untuk sorting berdasarkan tanggal gabung
CREATE INDEX idx_tanggal_gabung ON pegawai(tanggal_gabung);

-- ============================================================
-- DATA SAMPLE: Contoh data pegawai untuk testing
-- ============================================================

INSERT INTO pegawai (nip, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_telepon, email, jabatan, departemen, pendidikan_terakhir, status_kerja, tanggal_gabung) VALUES
('PGW001', 'Ahmad Fauzi Rahman', 'Jakarta', '1990-05-15', 'Laki-laki', 'Jl. Merdeka No. 10, Jakarta Selatan', '08123456789', 'ahmad.fauzi@email.com', 'Senior Developer', 'IT', 'S1', 'Aktif', '2020-01-15'),
('PGW002', 'Siti Nurhaliza', 'Bandung', '1992-08-22', 'Perempuan', 'Jl. Asia Afrika No. 25, Bandung', '08234567890', 'siti.nurhaliza@email.com', 'UI/UX Designer', 'IT', 'S1', 'Aktif', '2020-03-01'),
('PGW003', 'Budi Santoso', 'Surabaya', '1985-12-10', 'Laki-laki', 'Jl. Pahlawan No. 5, Surabaya', '08345678901', 'budi.santoso@email.com', 'Project Manager', 'Operations', 'S2', 'Aktif', '2018-07-15'),
('PGW004', 'Dewi Lestari', 'Yogyakarta', '1995-03-18', 'Perempuan', 'Jl. Malioboro No. 8, Yogyakarta', '08456789012', 'dewi.lestari@email.com', 'Marketing Specialist', 'Marketing', 'S1', 'Aktif', '2021-06-01'),
('PGW005', 'Rudi Hartono', 'Semarang', '1988-07-25', 'Laki-laki', 'Jl. Pandanaran No. 12, Semarang', '08567890123', 'rudi.hartono@email.com', 'Database Admin', 'IT', 'D3', 'Aktif', '2019-09-15'),
('PGW006', 'Anisa Putri Rahayu', 'Malang', '1997-11-03', 'Perempuan', 'Jl. Ijen No. 3, Malang', '08678901234', 'anisa.putri@email.com', 'HR Staff', 'Human Resources', 'S1', 'Aktif', '2022-01-10'),
('PGW007', 'Eko Prasetyo', 'Solo', '1982-04-30', 'Laki-laki', 'Jl. Slamet Riyadi No. 7, Solo', '08789012345', 'eko.prasetyo@email.com', 'Finance Manager', 'Finance', 'S2', 'Aktif', '2016-04-01'),
('PGW008', 'Fitri Handayani', 'Denpasar', '1993-09-14', 'Perempuan', 'Jl. Raya Kuta No. 20, Denpasar', '08890123456', 'fitri.handayani@email.com', 'Account Executive', 'Sales', 'S1', 'Cuti', '2020-08-15'),
('PGW009', 'Hendra Gunawan', 'Medan', '1991-06-08', 'Laki-laki', 'Jl. Gatot Subroto No. 15, Medan', '08901234567', 'hendra.gunawan@email.com', 'Backend Developer', 'IT', 'S1', 'Aktif', '2021-02-28'),
('PGW010', 'Maya Sari', 'Palembang', '1996-01-20', 'Perempuan', 'Jl. Sudirman No. 9, Palembang', '08012345678', 'maya.sari@email.com', 'Quality Assurance', 'IT', 'S1', 'Aktif', '2022-05-16'),
('PGW011', 'Fajar Nugroho', 'Makassar', '1987-02-14', 'Laki-laki', 'Jl. AP Pettarani No. 4, Makassar', '08111223344', 'fajar.nugroho@email.com', 'System Analyst', 'IT', 'S2', 'Aktif', '2017-11-01'),
('PGW012', 'Rina Wulandari', 'Bogor', '1994-07-07', 'Perempuan', 'Jalak Harupat No. 11, Bogor', '08223344555', 'rina.wulandari@email.com', 'Frontend Developer', 'IT', 'S1', 'Aktif', '2021-09-01');

-- ============================================================
-- VIEW: Untuk memudahkan query data dengan usia terhitung
-- ============================================================

CREATE OR REPLACE VIEW v_pegawai_dengan_usia AS
SELECT 
    id,
    nip,
    nama_lengkap,
    tempat_lahir,
    tanggal_lahir,
    jenis_kelamin,
    alamat,
    no_telepon,
    email,
    jabatan,
    departemen,
    pendidikan_terakhir,
    status_kerja,
    tanggal_gabung,
    foto_profil,
    created_at,
    updated_at,
    -- Menghitung usia dari tanggal lahir hingga saat ini
    TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) AS usia
FROM pegawai;

-- ============================================================
-- QUERY VALIDASI: Cek apakah data sudah terinsert dengan benar
-- ============================================================

-- SELECT * FROM pegawai;
-- SELECT * FROM v_pegawai_dengan_usia;
-- SELECT COUNT(*) as total_pegawai FROM pegawai;
