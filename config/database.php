<?php
declare(strict_types=1);

// Sesuaikan 4 nilai ini dengan AWeb Server/MySQL Anda.
$dbHost = 'localhost';
$dbName = 'parkir_db';
$dbUser = 'root';
$dbPass = '';

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
} catch (PDOException $e) {
    die('Koneksi database gagal. Periksa config/database.php dan pastikan database parkir_db sudah dibuat. Error: ' . htmlspecialchars($e->getMessage()));
}
