<?php
// auth/register.php
require_once __DIR__ . '/../config/database.php';
session_start();

// Jika sudah login, lempar ke dashboard masing-masing
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
    $role     = $_POST['role']; // Kita izinkan pilih role dulu untuk mempermudah testing

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Hash password demi keamanan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email'    => $email,
                ':password' => $hashed_password,
                ':role'     => $role
            ]);
            
            $success = "Registrasi berhasil! Silakan <a href='login.php'>Login disini</a>.";
        } catch (\PDOException $e) {
            // Cek jika username atau email duplikat
            if ($e->getCode() == 23000) {
                $error = "Username atau Email sudah terdaftar!";
            } else {
                $error = "Terjadi kesalahan: " . $e->getMessage();
            }
        }
    } else {
        $error = "Semua data wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Room Booking SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Buat Akun</h3>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?= $success; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Daftar Sebagai (Role)</label>
                            <select name="role" class="form-select">
                                <option value="user_biasa">User Biasa (Karyawan/Mahasiswa)</option>
                                <option value="admin_ruangan">Admin Ruangan (Verifikator)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar</button>
                    </form>
                    <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>