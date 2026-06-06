<?php
// pages/manage_rooms.php
require_once __DIR__ . '/../config/database.php';
session_start();

// PROTEKSI: Hanya Admin Ruangan yang boleh masuk ke halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin_ruangan') {
    header("Location: ../dashboard.php");
    exit;
}

$success = '';
$error = '';

// Proses Tambah Ruangan Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = trim($_POST['room_name']);
    $capacity  = intval($_POST['capacity']);
    $location  = trim($_POST['location']);

    if (!empty($room_name) && $capacity > 0 && !empty($location)) {
        try {
            $sql = "INSERT INTO rooms (room_name, capacity, location, status) VALUES (:room_name, :capacity, :location, 'available')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':room_name' => $room_name,
                ':capacity'  => $capacity,
                ':location'  => $location
            ]);
            $success = "Ruangan baru berhasil ditambahkan!";
        } catch (\PDOException $e) {
            $error = "Gagal menambah ruangan: " . $e->getMessage();
        }
    } else {
        $error = "Semua data form wajib diisi dengan benar!";
    }
}

// Ambil semua data ruangan dari database untuk ditampilkan di tabel
$stmt_rooms = $pdo->query("SELECT * FROM rooms ORDER BY id DESC");
$rooms = $stmt_rooms->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Ruangan - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola Ruangan Kerja / Rapat</h2>
        <a href="../dashboard.php" class="btn btn-secondary">← Kembali ke Dashboard</a>
    </div>

    <?php if($success): ?>
        <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Tambah Ruangan</h5>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Ruangan</label>
                            <input type="text" name="room_name" class="form-control" placeholder="Contoh: Ruang Meeting Meja Bundar" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kapasitas (Orang)</label>
                            <input type="number" name="capacity" class="form-control" min="1" placeholder="Contoh: 12" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi / Lantai</label>
                            <textarea name="location" class="form-control" rows="2" placeholder="Contoh: Gedung A, Lantai 2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Simpan Ruangan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Daftar Ruangan Saat Ini</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Ruangan</th>
                                    <th>Kapasitas</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($rooms)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data ruangan. Silakan tambah terlebih dahulu.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($rooms as $room): ?>
                                        <tr>
                                            <td><?= $room['id']; ?></td>
                                            <td><strong><?= htmlspecialchars($room['room_name']); ?></strong></td>
                                            <td><?= $room['capacity']; ?> Orang</td>
                                            <td><?= htmlspecialchars($room['location']); ?></td>
                                            <td>
                                                <span class="badge bg-<?= $room['status'] === 'available' ? 'success' : 'danger'; ?>">
                                                    <?= ucfirst($room['status']); ?>
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
        </div>
    </div>
</div>

</body>
</html>