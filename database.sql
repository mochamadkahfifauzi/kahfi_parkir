CREATE DATABASE IF NOT EXISTS parkir_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE parkir_db;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS transaksi_parkir;
DROP TABLE IF EXISTS kendaraan;
DROP TABLE IF EXISTS tarif_parkir;
DROP TABLE IF EXISTS area_parkir;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE area_parkir (
  id_area INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_area VARCHAR(100) NOT NULL,
  kapasitas INT UNSIGNED NOT NULL,
  lokasi VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tarif_parkir (
  id_tarif INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  jenis_kendaraan VARCHAR(20) NOT NULL UNIQUE,
  tarif_per_jam INT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kendaraan (
  id_kendaraan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  no_polisi VARCHAR(20) NOT NULL UNIQUE,
  jenis_kendaraan VARCHAR(20) NOT NULL,
  id_area INT UNSIGNED NOT NULL,
  CONSTRAINT fk_kendaraan_area FOREIGN KEY (id_area) REFERENCES area_parkir(id_area) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transaksi_parkir (
  id_transaksi INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_kendaraan INT UNSIGNED NOT NULL,
  id_tarif INT UNSIGNED NOT NULL,
  id_user INT UNSIGNED NOT NULL,
  waktu_masuk DATETIME NOT NULL,
  waktu_keluar DATETIME NULL,
  durasi_jam INT UNSIGNED NULL,
  total_bayar INT UNSIGNED NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Parkir',
  CONSTRAINT fk_transaksi_kendaraan FOREIGN KEY (id_kendaraan) REFERENCES kendaraan(id_kendaraan) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_transaksi_tarif FOREIGN KEY (id_tarif) REFERENCES tarif_parkir(id_tarif) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_transaksi_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_transaksi_status (status),
  INDEX idx_transaksi_masuk (waktu_masuk),
  INDEX idx_transaksi_keluar (waktu_keluar)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (nama, username, password, role) VALUES
('Administrator','admin','$2y$12$oFSc5EIluB.tCGC/Imiw7.fzLTMtl6B/TUd6JSaRaXE/rt6wOKSF6','admin'),
('Petugas Parkir','petugas','$2y$12$S2gIE8OVUKfO5ADcHBIceukBNi8PztuEg.axRoy99OXPwy2.SL9s.','petugas'),
('Owner','owner','$2y$12$n0nOeY1iPnMZoywIE/8Jve3aCZCWpc0ZK6qqSi7AKAkUpwc9z2Xl.','owner');

INSERT INTO area_parkir (nama_area, kapasitas, lokasi) VALUES
('Area A',50,'Depan Gedung'),
('Area B',75,'Samping Gedung');

INSERT INTO tarif_parkir (jenis_kendaraan, tarif_per_jam) VALUES
('Motor',2000),
('Mobil',5000),
('Bus',10000),
('Truk',10000);

INSERT INTO kendaraan (no_polisi, jenis_kendaraan, id_area) VALUES
('B 1234 ABC','Motor',1),
('B 5678 XYZ','Mobil',1);
