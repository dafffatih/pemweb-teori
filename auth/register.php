<?php
session_start();
include '../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ReadWatch - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Pastikan HTML dan body menggunakan full height */
        html, body {
            height: 100%;
        }

         /* Background dengan gradien sederhana */
        body {
            background: linear-gradient(135deg,rgb(102, 122, 210) 0%,rgb(28, 126, 197) 100%);
            background-attachment: fixed;
        }
        
        /* Wrapper untuk mengatur layout */
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Content area flexible */
        .content {
            flex: 1;
        }
        
        
        /* Navbar tetap di atas */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1020;
        }
    </style>
</head>
<body class="bg-light">
    <div class="wrapper">
        <!-- User Navbar - Sticky Top -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="index.php">
                    <i class="bi bi-book me-2"></i> ReadWatch
                </a>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="content">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h2 class="text-primary"><i class="bi bi-book"></i> ReadWatch</h2>
                                    <p class="text-muted">Daftar Akun Baru</p>
                                </div>

                                <?php
                                if (isset($_SESSION['register_error'])) {
                                    echo '<div class="alert alert-danger">' . $_SESSION['register_error'] . '</div>';
                                    unset($_SESSION['register_error']);
                                }
                                ?>

                                <?php
                                if (isset($_SESSION['register_success'])) {
                                    echo '<div class="alert alert-success">' . $_SESSION['register_success'] . '</div>';
                                    unset($_SESSION['register_success']);
                                }
                                ?>

                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Nama Lengkap</label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <button type="submit" name="register" class="btn btn-primary w-100 mb-3">
                                        <i class="bi bi-person-plus"></i> Daftar
                                    </button>
                                </form>
                                
                                <div class="text-center">
                                    <p class="mb-0">Sudah punya akun? <a href="../index.php" class="text-decoration-none">Login di sini</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['register'])) {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Cek apakah username atau email sudah ada
        $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $_SESSION['register_error'] = "Username atau email sudah digunakan.";
        } else {
            $insert_query = "INSERT INTO users (full_name, username, email, password, role) VALUES ('$full_name', '$username', '$email', '$password', 'user')";
            
            if (mysqli_query($conn, $insert_query)) {
                $user_id = mysqli_insert_id($conn);
                createDefaultData($user_id, $conn);
                
                $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
                header("Location: ../index.php");
                exit;
            } else {
                $_SESSION['register_error'] = "Terjadi kesalahan saat mendaftar.";
            }
        }
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>