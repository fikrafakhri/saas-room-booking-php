<?php
// dashboard.php
session_start();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Room Booking SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link href="/room-booking-saas/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="fa-solid fa-cubes me-2"></i>SaaS Room Booking</a>
        <div class="navbar-nav ms-auto align-items-center">
            <span class="nav-link text-white me-3">
                <i class="fa-regular fa-user-circle me-1"></i> Halo, <strong><?= htmlspecialchars($username); ?></strong> 
                <span class="badge bg-light text-dark ms-1" style="font-size: 0.75rem;"><?= ucfirst(str_replace('_', ' ', $role)); ?></span>
            </span>
            <a class="btn btn-danger btn-modern btn-sm px-3" href="auth/logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="welcome-banner">
                <h2 class="fw-bold mb-2">Selamat Datang Kembali!</h2>
                <p class="lead mb-0 opacity-75">Sistem reservasi ruangan pintar untuk efisiensi kerja tim Anda secara real-time.</p>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if ($role === 'user_biasa'): ?>
            <div class="col-md-6 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-header-custom bg-primary text-white">
                        <i class="fa-solid fa-calendar-plus me-2"></i> Menu Reservasi
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div>
                            <h5 class="fw-bold">Buat Reservasi Baru</h5>
                            <p class="text-muted">Cari ruangan rapat yang kosong, atur jam pemakaian, dan kirim pengajuan langsung ke pihak admin.</p>
                        </div>
                        <a href="pages/booking_form.php" class="btn btn-primary btn-modern w-100 mt-3">
                            <i class="fa-solid fa-file-pen me-2"></i> Buka Form Booking
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-header-custom bg-secondary text-white">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i> Status Pengajuan
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div>
                            <h5 class="fw-bold">Riwayat & Status</h5>
                            <p class="text-muted">Pantau daftar permohonan pemakaian ruangan Anda, apakah berstatus disetujui atau ditolak.</p>
                        </div>
                        <a href="pages/my_bookings.php" class="btn btn-secondary btn-modern w-100 mt-3">
                            <i class="fa-solid fa-eye me-2"></i> Lihat Riwayat Status
                        </a>
                    </div>
                </div>
            </div>

        <?php elseif ($role === 'admin_ruangan'): ?>
            <div class="col-md-6 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-header-custom bg-warning text-dark">
                        <i class="fa-solid fa-shield-check me-2"></i> Panel Verifikasi
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div>
                            <h5 class="fw-bold">Butuh Persetujuan Admin</h5>
                            <p class="text-muted">Periksa pengajuan masuk dari user. Sistem otomatis memblokir jadwal jika terdeteksi bentrok.</p>
                        </div>
                        <a href="pages/admin_approval.php" class="btn btn-warning text-dark btn-modern w-100 mt-3">
                            <i class="fa-solid fa-list-check me-2"></i> Kelola Persetujuan
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card card-modern h-100">
                    <div class="card-header-custom bg-success text-white">
                        <i class="fa-solid fa-door-open me-2"></i> Fasilitas Kantor
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div>
                            <h5 class="fw-bold">Manajemen Ruang Rapat</h5>
                            <p class="text-muted">Tambah ruang kerja baru, kelola kapasitas meja/kursi, atau setel status pemeliharaan (maintenance).</p>
                        </div>
                        <a href="pages/manage_rooms.php" class="btn btn-success btn-modern w-100 mt-3">
                            <i class="fa-solid fa-sliders me-2"></i> Konfigurasi Ruangan
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>