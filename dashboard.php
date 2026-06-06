<?php
// dashboard.php
session_start();

// Proteksi Halaman: Jika belum login, tendang kembali ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$username = $_SESSION['username'];
$role     = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Room Booking SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">SaaS Room Booking</a>
        <div class="navbar-nav ms-auto">
            <span class="nav-link text-white me-3">Halo, <strong><?= htmlspecialchars($username); ?></strong> (<?= ucfirst(str_replace('_', ' ', $role)); ?>)</span>
            <a class="btn btn-danger btn-sm" href="auth/logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2>Selamat Datang di Sistem Reservasi Ruangan</h2>
                    <p class="text-muted">Gunakan hak akses Anda untuk mengelola atau memesan ruangan rapat secara real-time.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if ($role === 'user_biasa'): ?>
            <div class="col-md-6">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white">Menu Karyawan / Mahasiswa</div>
                    <div class="card-body">
                        <h5 class="card-title">Buat Reservasi Baru</h5>
                        <p class="card-text">Pilih ruangan yang tersedia, tentukan jam, dan kirim permohonan ke admin.</p>
                        <a href="pages/booking_form.php" class="btn btn-primary">Form Booking Ruangan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-secondary mb-3">
                    <div class="card-header bg-secondary text-white">Status Booking Anda</div>
                    <div class="card-body">
                        <h5 class="card-title">Riwayat Permohonan</h5>
                        <p class="card-text">Pantau apakah status booking Anda disetujui atau ditolak oleh admin.</p>
                        <a href="pages/my_bookings.php" class="btn btn-secondary">Lihat Status</a>
                    </div>
                </div>
            </div>

        <?php elseif ($role === 'admin_ruangan'): ?>
            <div class="col-md-6">
                <div class="card border-warning mb-3">
                    <div class="card-header bg-warning text-dark">Panel Verifikasi Admin</div>
                    <div class="card-body">
                        <h5 class="card-title">Butuh Persetujuan</h5>
                        <p class="card-text">Ada permohonan booking masuk yang perlu Anda periksa agar tidak bentrok.</p>
                        <a href="pages/admin_approval.php" class="btn btn-warning text-dark">Kelola Persetujuan</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white">Manajemen Ruangan</div>
                    <div class="card-body">
                        <h5 class="card-title">Daftar Kamar & Ruangan</h5>
                        <p class="card-text">Tambah ruangan baru, ubah kapasitas, atau kunci ruangan yang sedang maintenance.</p>
                        <a href="pages/manage_rooms.php" class="btn btn-success">Kelola Ruangan</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>