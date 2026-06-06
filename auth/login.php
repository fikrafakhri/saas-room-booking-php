<?php
// auth/login.php
require_once __DIR__ . '/../config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Verifikasi kecocokan password yang di-hash
        if ($user && password_verify($password, $user['password'])) {
            // Simpan info user ke session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Lempar ke halaman utama dashboard
            header("Location: ../dashboard.php");
            exit;
        } else {
            $error = "Email atau Password salah!";
        }
    } else {
        $error = "Silakan isi semua bidang!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Room Booking SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Sign In</h3>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Masuk</button>
                    </form>
                    <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>