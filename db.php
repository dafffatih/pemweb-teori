<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "readwatch"; 

$conn = mysqli_connect($host, $user, $password, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>