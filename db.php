<?php
// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$password = "";
$db = "readwatch"; 

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $password, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset UTF-8 untuk mendukung karakter Indonesia
mysqli_set_charset($conn, "utf8");
?>