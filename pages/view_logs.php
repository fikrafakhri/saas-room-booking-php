<?php
// pages/view_logs.php
require_once __DIR__ . '/../config/database.php';
session_start();

// PROTEKSI: Hanya Admin Ruangan yang bisa melihat log audit
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_ruangan') {
    header("Location: ../dashboard.php");
    exit;
}

// Ambil data log aktivitas dan join dengan tabel users
$sql = "SELECT l.*, u.username, u.role FROM activity_logs l 
        LEFT JOIN users u ON l.user_id = u.id 
        ORDER BY l.id DESC LIMIT 100"; // Batasi 100 log terbaru
$logs = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Audit Trail - Sistem Log Keamanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fa-solid fa-user-shield text-danger me-2"></i>Sistem Log Aktivitas (Audit Trail)</h4>
        <a href="../dashboard.php" class="btn btn-secondary btn-sm">← Dashboard</a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Aktor (User)</th>
                            <th>Kategori Aksi</th>
                            <th>Deskripsi Aktivitas</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($logs)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas yang tercatat sistem.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($logs as $log): ?>
                                <tr>
                                    <td class="small text-muted"><?= $log['created_at']; ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($log['username'] ?? 'Sistem'); ?></strong>
                                        <br><span class="badge bg-light text-dark" style="font-size: 0.7rem;"><?= $log['role'] ?? 'System'; ?></span>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?= $log['action']; ?></span></td>
                                    <td class="text-secondary" style="font-size: 0.95rem;"><?= htmlspecialchars($log['description']); ?></td>
                                    <td><code class="text-danger"><?= $log['ip_address']; ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>