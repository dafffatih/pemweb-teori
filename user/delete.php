<?php
session_start();
include '../config/db.php';

// cek apakah pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

$item_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// menghapus item dari database (hanya milik user yang login)
$delete_query = "DELETE FROM items WHERE id = $item_id AND user_id = '$user_id'";

if (mysqli_query($conn, $delete_query)) {
    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['user_success'] = "Item berhasil dihapus dari koleksi!";
    } else {
        $_SESSION['user_error'] = "Item tidak ditemukan atau bukan milik Anda.";
    }
} else {
    $_SESSION['user_error'] = "Terjadi kesalahan saat menghapus item.";
}

header("Location: index.php");
exit;
?>