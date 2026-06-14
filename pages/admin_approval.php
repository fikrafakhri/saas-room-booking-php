<?php
// pages/admin_approval.php
require_once __DIR__ . '/../config/database.php';
session_start();

// PROTEKSI: Hanya Admin Ruangan yang bisa masuk
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_ruangan') {
    header("Location: ../dashboard.php");
    exit;
}
// Tambahkan baris ini di paling atas setelah require database:
require_once __DIR__ . '/../helpers/log_helper.php';

// Proses Update Status (Approve / Reject)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $action = $_GET['action'];
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    $admin_id = $_SESSION['user_id'];

    try {
        // Ambil data detail booking dulu buat bahan deskripsi log
        $stmt_detail = $pdo->prepare("SELECT b.*, r.room_name FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.id = :id");
        $stmt_detail->execute([':id' => $booking_id]);
        $booking_data = $stmt_detail->fetch();

        // Update status di database
        $sql = "UPDATE bookings SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':status' => $status, ':id' => $booking_id]);

        // EKSKUSI AUDIT TRAIL: Catat aksi admin ke log!
        $log_action = "MANAJEMEN RESERVASI";
        $log_desc = "Admin " . $_SESSION['username'] . " mengubah status Booking ID #" . $booking_id . " (Ruangan: " . $booking_data['room_name'] . ") menjadi " . strtoupper($status);
        
        writeActivityLog($pdo, $admin_id, $log_action, $log_desc);

        header("Location: admin_approval.php?msg=success");
        exit;
    } catch (\PDOException $e) {
        echo "Gagal memperbarui status: " . $e->getMessage();
    }
}

// Ambil semua data booking beserta nama user dan nama ruangan (Menggunakan JOIN)
$sql_get = "SELECT b.*, u.username, r.room_name 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            ORDER BY b.id DESC";
$bookings = $pdo->query($sql_get)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Persetujuan Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Panel Verifikasi Booking (Admin)</h4>
        <a href="../dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
        <div class="alert alert-success">Status reservasi berhasil diperbarui!</div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Pemohon</th>
                        <th>Ruangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada pengajuan booking masuk.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($bookings as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['username']); ?></td>
                                <td><?= htmlspecialchars($b['room_name']); ?></td>
                                <td><?= $b['booking_date']; ?></td>
                                <td><?= $b['start_time']; ?> - <?= $b['end_time']; ?></td>
                                <td><?= htmlspecialchars($b['purpose']); ?></td>
                                <td>
                                    <span class="badge bg-<?= $b['status'] === 'approved' ? 'success' : ($b['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                        <?= ucfirst($b['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($b['status'] === 'pending'): ?>
                                        <a href="admin_approval.php?action=approve&id=<?= $b['id']; ?>" class="btn btn-success btn-sm">Setujui</a>
                                        <a href="admin_approval.php?action=reject&id=<?= $b['id']; ?>" class="btn btn-danger btn-sm">Tolak</a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
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