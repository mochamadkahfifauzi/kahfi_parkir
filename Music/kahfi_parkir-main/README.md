APLIKASI PARKIR

Akun demo:
- Admin: admin / admin123
- Petugas: petugas / petugas123
- Owner: owner / owner123

INSTALASI BARU
1. Buat database dengan menjalankan database.sql.
2. Sesuaikan host, nama database, username, dan password di config/database.php.
3. Buka index.php melalui server PHP.

DATABASE YANG SUDAH ADA
- Backup database terlebih dahulu.
- Jalankan migration_update.sql satu kali untuk memindahkan pilihan area ke transaksi dan menambahkan durasi aktual dalam menit.
- Jangan jalankan database.sql pada database berisi data penting karena file tersebut menghapus lalu membuat ulang tabel.

PERUBAHAN
- Petugas memilih kendaraan dari daftar kendaraan yang sudah terdaftar, bukan mengetik nomor polisi.
- Area dipilih saat kendaraan masuk. Area yang kapasitasnya penuh tidak dapat dipilih. Kendaraan tidak lagi terikat permanen pada satu area.
- Waktu PHP dan sesi MySQL menggunakan zona waktu Asia/Jakarta (UTC+7).
- Sistem menyimpan durasi aktual dalam menit dan memisahkannya dari jam yang ditagihkan. Tagihan minimum tetap satu jam dan pembulatan tarif dilakukan per jam.
