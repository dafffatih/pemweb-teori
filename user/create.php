<?php
session_start();
include '../config/db.php';

// cek apakah pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

$page_title = 'Catat Baru';
$base_path = '../';
include '../includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">Catat Item Baru</h1>
    
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori *</label>
                            <select name="category" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Film">🎬 Film</option>
                                <option value="Anime">📺 Anime</option>
                                <option value="Komik">📚 Komik</option>
                                <option value="Novel">📖 Novel</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select name="status" class="form-select" required>
                                <option value="">Pilih Status</option>
                                <option value="Sedang Berjalan">⏳ Sedang Berjalan</option>
                                <option value="Sudah Tamat">✅ Sudah Tamat</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan pribadi..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="bi bi-check"></i> Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if (isset($_POST['simpan'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    
    $insert_query = "INSERT INTO items (user_id, title, category, status, notes) 
                     VALUES ('$user_id', '$title', '$category', '$status', '$notes')";
    
    if (mysqli_query($conn, $insert_query)) {
        $_SESSION['user_success'] = "Item berhasil ditambahkan ke koleksi!";
        echo "<script>
            alert('Item Berhasil Ditambah');
            window.location.href = 'index.php';
        </script>";
    } else {
        $_SESSION['user_error'] = "Terjadi kesalahan saat menambah item.";
        header("Location: index.php");
        exit;
    }
}


?>

<?php  
include '../includes/footer.php';
?>