<?php
session_start();
include 'db.php';

// Cek apakah sudah login
if (isset($_SESSION['user'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

// Proses login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Cek apakah username dan password tidak kosong
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Username dan password harus diisi!";
        header("Location: index.php");
        exit;
    }

    // Query untuk mencari user berdasarkan username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil, simpan data ke session
            $_SESSION['user'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            // Password salah
            $_SESSION['login_error'] = "Username atau password salah!";
            header("Location: index.php");
            exit;
        }
    } else {
        // Username tidak ditemukan
        $_SESSION['login_error'] = "Username atau password salah!";
        header("Location: index.php");
        exit;
    }
} else {
    // Jika tidak ada POST request, redirect ke halaman login
    header("Location: index.php");
    exit;
}
?>