<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "readwatch2";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fungsi untuk membuat data default bagi pengguna baru
// Fungsi ini akan membuat beberapa item contoh untuk pengguna baru
function createDefaultData($user_id, $conn) {
    // Membuat beberapa item contoh
    $sample_items = [
        ['title' => 'One Piece', 'category' => 'Anime', 'status' => 'Sedang Berjalan'],
        ['title' => 'Naruto', 'category' => 'Komik', 'status' => 'Sudah Tamat'],
        ['title' => 'Avengers Endgame', 'category' => 'Film', 'status' => 'Sudah Tamat']
    ];
    
    foreach ($sample_items as $item) {
        $title = mysqli_real_escape_string($conn, $item['title']);
        $category = mysqli_real_escape_string($conn, $item['category']);
        $status = mysqli_real_escape_string($conn, $item['status']);
        
        $query = "INSERT INTO items (user_id, title, category, status) VALUES ('$user_id', '$title', '$category', '$status')";
        mysqli_query($conn, $query);
    }
}
?>