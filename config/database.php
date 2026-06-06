<?php
// config/database.php

// Fungsi sederhana untuk membaca file .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Panggil fungsi env loader
loadEnv(__DIR__ . '/../.env');

$host = $_ENV['DB_HOST'] ?? 'localhost';
$db   = $_ENV['DB_NAME'] ?? 'db_room_booking';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Menampilkan error sebagai pengecualian
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Hasil fetch berupa array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Mematikan emulasi agar query asli aman
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     // Tetap biarkan kosong jika sukses, atau hilangkan comment di bawah untuk tes awal:
     // echo "Koneksi Database Sukses!";
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}