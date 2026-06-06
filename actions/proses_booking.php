<?php
// actions/proses_booking.php
require_once __DIR__ . '/../config/database.php';

// Simulasi data input dari form (Nanti ini diambil dari $_POST)
$room_id      = 1; 
$booking_date = '2026-06-10';
$start_time   = '10:00:00';
$end_time     = '12:00:00';
$user_id      = 1; // ID user yang login
$purpose      = 'Rapat Proyek Backend';

// 1. QUERY VALIDASI: Cek apakah ada jadwal approved yang bertabrakan
$sql_check = "SELECT COUNT(*) AS total FROM bookings 
              WHERE room_id = :room_id 
                AND booking_date = :booking_date 
                AND status = 'approved'
                AND (start_time < :end_time AND end_time > :start_time)";

$stmt = $pdo->prepare($sql_check);
$stmt->execute([
    ':room_id'      => $room_id,
    ':booking_date' => $booking_date,
    ':start_time'   => $start_time,
    ':end_time'     => $end_time
]);

$result = $stmt->fetch();

// 2. LOGIKA KEPUTUSAN
if ($result['total'] > 0) {
    // Jika ditemukan bentrokan
    echo "Gagal: Ruangan sudah di-booking pada jam tersebut. Silakan pilih waktu lain.";
} else {
    // Jika aman, masukkan data booking baru dengan status 'pending' (butuh approval admin)
    $sql_insert = "INSERT INTO bookings (user_id, room_id, booking_date, start_time, end_time, purpose, status) 
                   VALUES (:user_id, :room_id, :booking_date, :start_time, :end_time, :purpose, 'pending')";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([
        ':user_id'      => $user_id,
        ':room_id'      => $room_id,
        ':booking_date' => $booking_date,
        ':start_time'   => $start_time,
        ':end_time'     => $end_time,
        ':purpose'      => $purpose
    ]);

    echo "Sukses: Permintaan booking berhasil dikirim! Menunggu persetujuan admin.";
}