<?php
// pages/my_bookings.php
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT b.*, r.room_name FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.user_id = :user_id ORDER BY b.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$my_bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Booking Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Status Pengajuan Reservasi Saya</h4>
        <a href="../dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Keperluan</th>
                        <th>Status Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($my_bookings)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Anda belum pernah melakukan booking ruangan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($my_bookings as $mb): ?>
                            <tr>
                                <td><?= htmlspecialchars($mb['room_name']); ?></td>
                                <td><?= $mb['booking_date']; ?></td>
                                <td><?= $mb['start_time']; ?> - <?= $mb['end_time']; ?></td>
                                <td><?= htmlspecialchars($mb['purpose']); ?></td>
                                <td>
                                    <span class="badge bg-<?= $mb['status'] === 'approved' ? 'success' : ($mb['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                        <?= ucfirst($mb['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>