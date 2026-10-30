<?php
declare(strict_types=1);

// Gunakan satu zona waktu untuk PHP dan MySQL agar jam masuk/keluar konsisten.
date_default_timezone_set('Asia/Jakarta');

// Sesuaikan 4 nilai ini dengan AWeb Server/MySQL Anda.
$dbHost = '127.0.1.0';
$dbName = 'parkir_db';
$dbUser = 'root';
$dbPass = 'root';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        )
    );
    // Simpan dan baca DATETIME dengan zona waktu WIB (UTC+7).
    $pdo->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    die('Koneksi database gagal. Periksa config/database.php dan pastikan database parkir_db sudah dibuat. Error: ' . htmlspecialchars($e->getMessage()));
}
