<?php
// pages/booking_form.php
require_once __DIR__ . '/../config/database.php';
session_start();

// PROTEKSI: Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$success = '';
$error = '';

// 1. Ambil daftar ruangan yang statusnya 'available' untuk dimasukkan ke pilihan (select option)
$stmt_rooms = $pdo->query("SELECT * FROM rooms WHERE status = 'available' ORDER BY room_name ASC");
$rooms = $stmt_rooms->fetchAll();

// 2. Proses ketika Form di-submit oleh User
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id      = $_SESSION['user_id'];
    $room_id      = intval($_POST['room_id']);
    $booking_date = $_POST['booking_date'];
    $start_time   = $_POST['start_time'] . ':00'; // Menyelaraskan format TIME (HH:MM:SS)
    $end_time     = $_POST['end_time'] . ':00';
    $purpose      = trim($_POST['purpose']);

    if ($room_id > 0 && !empty($booking_date) && !empty($start_time) && !empty($end_time) && !empty($purpose)) {
        
        // JIKA JAM SELESAI LEBIH KECIL ATAU SAMA DENGAN JAM MULAI, TOLAK LANGSUNG
        if ($end_time <= $start_time) {
            $error = "Waktu selesai harus lebih lambat daripada waktu mulai!";
        } else {
            try {
                // LOGIKA EMAS: Cek bentrokan waktu dengan booking lain yang sudah 'approved'
                $sql_check = "SELECT COUNT(*) AS total FROM bookings 
                              WHERE room_id = :room_id 
                                AND booking_date = :booking_date 
                                AND status = 'approved'
                                AND (start_time < :end_time AND end_time > :start_time)";

                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->execute([
                    ':room_id'      => $room_id,
                    ':booking_date' => $booking_date,
                    ':start_time'   => $start_time,
                    ':end_time'     => $end_time
                ]);
                $check = $stmt_check->fetch();

                if ($check['total'] > 0) {
                    // Jika ditemukan bentrokan jadwal
                    $error = "Gagal! Ruangan tersebut sudah disetujui untuk digunakan oleh pengguna lain pada jam yang Anda pilih.";
                } else {
                    // Jika aman, masukkan data dengan status 'pending'
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

                    $success = "Permintaan booking berhasil dikirim! Silakan tunggu konfirmasi/approval dari Admin.";
                }
            } catch (\PDOException $e) {
                $error = "Terjadi kesalahan sistem: " . $e->getMessage();
            }
        }
    } else {
        $error = "Semua bidang form wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Booking Ruangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Form Reservasi Ruangan</h4>
                <a href="../dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
            </div>

            <?php if($success): ?>
                <div class="alert alert-success"><?= $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pilih Ruangan Kerja / Rapat</label>
                            <select name="room_id" class="form-select" required>
                                <option value="">-- Pilih Ruangan --</option>
                                <?php foreach($rooms as $room): ?>
                                    <option value="<?= $room['id']; ?>">
                                        <?= htmlspecialchars($room['room_name']); ?> (Kapasitas: <?= $room['capacity']; ?> Orang)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Booking</label>
                            <input type="date" name="booking_date" class="form-control" min="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keperluan / Agenda Rapat</label>
                            <textarea name="purpose" class="form-control" rows="3" placeholder="Contoh: Koordinasi internal tim backend dev" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Kirim Permohonan Reservasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>