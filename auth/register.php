<?php
// auth/register.php
require_once __DIR__ . '/../config/database.php';
session_start();

// Jika sudah login, tendang ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    
    // KEAMANAN KRUSIAL: Sifat role langsung dikunci dari backend menjadi 'user_biasa'
    $role     = 'user_biasa'; 

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Cek apakah email sudah terdaftar
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt_check->execute([':email' => $email]);
        
        if ($stmt_check->fetchColumn() > 0) {
            $error = "Email sudah terdaftar! Gunakan email lain.";
        } else {
            // Hash password demi keamanan kredensial
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            try {
                $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':username' => $username,
                    ':email'    => $email,
                    ':password' => $hashed_password,
                    ':role'     => $role // Selalu masuk sebagai user_biasa
                ]);

                $success = "Akun berhasil dibuat! Silakan <a href='login.php' class='alert-link'>Login di sini</a>.";
            } catch (\PDOException $e) {
                $error = "Gagal mendaftarkan akun: " . $e->getMessage();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Room Booking SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .register-container { max-width: 450px; margin-top: 80px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="register-container w-100">
        <div class="card p-4">
            <h3 class="text-center fw-bold text-primary mb-3">Buat Akun</h3>
            <p class="text-center text-muted small mb-4">Daftarkan diri Anda untuk mulai menggunakan sistem reservasi ruangan.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small" role="alert"><?= $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success py-2 small" role="alert"><?= $success; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label small fw-semibold">Nama Lengkap</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan nama Anda" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="nama@perusahaan.com" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2" style="border-radius: 8px;">Daftar Sekarang</button>
            </form>

            <div class="text-center mt-4 small text-muted">
                Sudah punya akun? <a href="login.php" class="text-decoration-none fw-semibold">Masuk</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>