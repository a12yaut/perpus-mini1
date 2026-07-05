-- database/schema.sql
-- Buat database
-- ============================================
-- Tabel users (pengguna)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'anggota') DEFAULT 'anggota',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Tabel books (buku)
-- ============================================
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    tahun_terbit INT NOT NULL,
    sinopsis TEXT,
    stok INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Tabel loans (peminjaman)
-- ============================================
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_jatuh_tempo DATE NOT NULL,
    tanggal_kembali DATE NULL,
    denda INT DEFAULT 0,
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status)
);

-- ============================================
-- Data Awal
-- ============================================

-- 1. Insert Admin Default
-- Password: admin123 (sudah di-hash)
INSERT INTO users (nama, email, password, role) VALUES 
('Administrator', 'admin@perpus.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- 2. Insert Sample Books (5 buku contoh)
INSERT INTO books (judul, penulis, kategori, tahun_terbit, sinopsis, stok) VALUES
('Laskar Pelangi', 'Andrea Hirata', 'Fiksi', 2005, 'Kisah inspiratif tentang perjuangan anak-anak di Belitung untuk meraih mimpi.', 5),
('Bumi Manusia', 'Pramoedya Ananta Toer', 'Sejarah', 1980, 'Novel tentang perjuangan pribumi di era kolonial Belanda.', 3),
('Atomic Habits', 'James Clear', 'Non-Fiksi', 2018, 'Panduan praktis membangun kebiasaan baik dan menghentikan kebiasaan buruk.', 4),
('Sapiens', 'Yuval Noah Harari', 'Sejarah', 2014, 'Sejarah singkat umat manusia dari zaman purba hingga sekarang.', 2),
('Pulang', 'Tere Liye', 'Fiksi', 2015, 'Kisah tentang keluarga dan perjalanan pulang ke rumah.', 3);

-- ============================================
-- Selesai
-- ============================================