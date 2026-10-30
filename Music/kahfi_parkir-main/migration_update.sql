-- Jalankan SATU KALI pada database parkir_db yang sudah lama dipakai.
-- Cadangkan database terlebih dahulu sebelum menjalankan migrasi ini.
-- Migrasi memindahkan relasi area dari master kendaraan ke setiap transaksi.

ALTER TABLE transaksi_parkir
  ADD COLUMN id_area INT UNSIGNED NULL AFTER id_kendaraan,
  ADD COLUMN durasi_menit INT UNSIGNED NULL AFTER waktu_keluar;

-- Transaksi lama mengikuti area kendaraan saat transaksi tersebut dibuat.
UPDATE transaksi_parkir t
JOIN kendaraan k ON k.id_kendaraan = t.id_kendaraan
SET t.id_area = k.id_area
WHERE t.id_area IS NULL;

ALTER TABLE transaksi_parkir
  MODIFY id_area INT UNSIGNED NOT NULL,
  ADD CONSTRAINT fk_transaksi_area
    FOREIGN KEY (id_area) REFERENCES area_parkir(id_area)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  ADD INDEX idx_transaksi_area_status (id_area, status);

ALTER TABLE kendaraan
  DROP FOREIGN KEY fk_kendaraan_area,
  DROP COLUMN id_area;

-- Durasi historis lama tidak dapat dipulihkan hingga menit secara akurat.
-- Catatan lama tetap mempertahankan durasi_jam dan total_bayar yang tersimpan.
