<?php
session_start();
include '../config/db.php';

// cek apakah pengguna sudah login dan memiliki hak akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$user_id = intval($_GET['id']);

// mencegah penghapusan akun sendiri
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['admin_error'] = "Anda tidak dapat menghapus akun sendiri.";
    header("Location: index.php");
    exit;
}

// menghapus items user terlebih dahulu
$delete_items_query = "DELETE FROM items WHERE user_id = $user_id";
mysqli_query($conn, $delete_items_query);

// menghapus user dari database
$delete_query = "DELETE FROM users WHERE id = $user_id";

if (mysqli_query($conn, $delete_query)) {
    $_SESSION['admin_success'] = "User berhasil dihapus!";
} else {
    $_SESSION['admin_error'] = "Terjadi kesalahan saat menghapus user.";
}

header("Location: index.php");
exit;
?>