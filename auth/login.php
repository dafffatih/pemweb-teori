<?php 
session_start(); 
include '../config/db.php'; 

if (isset($_SESSION['user_id'])) {
    // Redirect langsung ke halaman index sesuai role
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../user/index.php");
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']); 
    $password = $_POST['password']; 
    
    $query = "SELECT * FROM users WHERE username='$username'"; 
    $result = mysqli_query($conn, $query); 
    $user = mysqli_fetch_assoc($result); 
    
    if ($user && password_verify($password, $user['password'])) { 
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username']; 
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        // Redirect langsung ke halaman index sesuai role
        if ($user['role'] == 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../user/index.php");
        }
        exit; 
    } else { 
        $_SESSION['login_error'] = "Login gagal. Username atau password salah.";
        header("Location: ../index.php"); 
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>