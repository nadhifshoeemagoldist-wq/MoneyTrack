CREATE DATABASE IF NOT EXISTS moneytrack;
USE moneytrack;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tabel Kategori
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

-- Tabel Transaksi
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kategori_id INT NOT NULL,
    tipe ENUM('income', 'expense') NOT NULL,
    jumlah DECIMAL(15, 2) NOT NULL,
    tanggal DATE NOT NULL,
    deskripsi TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
);

-- Insert Kategori Default
INSERT IGNORE INTO kategori (id, nama_kategori) VALUES
(1, 'Makanan'),
(2, 'Transport'),
(3, 'Hiburan'),
(4, 'Gaji'),
(5, 'Lainnya');

-- Insert Dummy User (password: password123)
-- Hash generated using PHP password_hash('password123', PASSWORD_DEFAULT)
INSERT IGNORE INTO users (id, username, password) VALUES
(1, 'dummy_user', '$2y$10$wTInFItYd.yG/L5oN9yJ0Oe2oUqX9v7vC/qB2jH0l2GgT9F0R/G1q');

-- Insert Dummy Transaksi
INSERT IGNORE INTO transaksi (id, user_id, kategori_id, tipe, jumlah, tanggal, deskripsi) VALUES
(1, 1, 4, 'income', 15000000, '2026-04-01', 'Gaji Bulan April'),
(2, 1, 1, 'expense', 50000, '2026-04-02', 'Makan Siang Nasi Padang'),
(3, 1, 2, 'expense', 20000, '2026-04-03', 'Bensin Motor'),
(4, 1, 3, 'expense', 150000, '2026-04-05', 'Nonton Bioskop'),
(5, 1, 5, 'income', 500000, '2026-04-10', 'Bonus Proyek Lepas'),
(6, 1, 1, 'expense', 100000, '2026-04-15', 'Belanja Bahan Makanan');
