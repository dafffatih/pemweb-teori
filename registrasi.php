<?php
session_start();
include 'db.php';

if (isset($_SESSION['user'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak sama!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        $check_username = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_username);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $check_email = "SELECT id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $check_email);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $error = "Email sudah digunakan!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
                $stmt = mysqli_prepare($conn, $insert_query);
                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);

                if (mysqli_stmt_execute($stmt)) {
                    $success = "Registrasi berhasil! Silakan login dengan akun baru Anda.";
                } else {
                    $error = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - ReadWatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg login-card">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h2 class="card-title text-primary login-title">📚 ReadWatch</h2>
                                <p class="text-muted login-subtitle">Daftar akun baru</p>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-custom" role="alert">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="alert alert-success alert-custom" role="alert">
                                    <?= htmlspecialchars($success) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-semibold">👤 Username</label>
                                    <input
                                        type="text"
                                        class="form-control form-control-lg"
                                        id="username"
                                        name="username"
                                        placeholder="Masukkan username"
                                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                                        required
                                    >
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">📧 Email</label>
                                    <input
                                        type="email"
                                        class="form-control form-control-lg"
                                        id="email"
                                        name="email"
                                        placeholder="Masukkan email"
                                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                                        required
                                    >
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">🔒 Password</label>
                                    <input
                                        type="password"
                                        class="form-control form-control-lg"
                                        id="password"
                                        name="password"
                                        placeholder="Masukkan password"
                                        required
                                    >
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label fw-semibold">🔒 Konfirmasi Password</label>
                                    <input
                                        type="password"
                                        class="form-control form-control-lg"
                                        id="confirm_password"
                                        name="confirm_password"
                                        placeholder="Konfirmasi password"
                                        required
                                    >
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">📝 Daftar</button>
                            </form>

                            <div class="text-center">
                                <p class="mb-0">Sudah punya akun? <a href="index.php" class="btn-link text-decoration-none"><strong>Masuk di sini</strong></a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
