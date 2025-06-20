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

// Proses update HARUS di atas sebelum HTML output
if (isset($_POST['update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    
    $update_query = "UPDATE items SET title = '$title', category = '$category', status = '$status', notes = '$notes' WHERE id = $item_id AND user_id = '$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['user_success'] = "Item berhasil diperbarui!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['user_error'] = "Terjadi kesalahan saat memperbarui item.";
        header("Location: index.php");
        exit;
    }
}

// mengambil data item berdasarkan ID dan user_id
$item_query = "SELECT * FROM items WHERE id = $item_id AND user_id = '$user_id'";
$item_result = mysqli_query($conn, $item_query);
$item = mysqli_fetch_assoc($item_result);

if (!$item) {
    header("Location: index.php");
    exit;
}

$page_title = 'Edit Item';
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - ReadWatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container-fluid py-4">
        <h1 class="mb-4">Edit Item</h1>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul *</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori *</label>
                                <select name="category" class="form-select" required>
                                    <option value="Film" <?php echo $item['category'] == 'Film' ? 'selected' : ''; ?>>🎬 Film</option>
                                    <option value="Anime" <?php echo $item['category'] == 'Anime' ? 'selected' : ''; ?>>📺 Anime</option>
                                    <option value="Komik" <?php echo $item['category'] == 'Komik' ? 'selected' : ''; ?>>📚 Komik</option>
                                    <option value="Novel" <?php echo $item['category'] == 'Novel' ? 'selected' : ''; ?>>📖 Novel</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select name="status" class="form-select" required>
                                    <option value="Sedang Berjalan" <?php echo $item['status'] == 'Sedang Berjalan' ? 'selected' : ''; ?>>⏳ Sedang Berjalan</option>
                                    <option value="Sudah Tamat" <?php echo $item['status'] == 'Sudah Tamat' ? 'selected' : ''; ?>>✅ Sudah Tamat</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan pribadi..."><?php echo htmlspecialchars($item['notes']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="update" class="btn btn-warning">
                            <i class="bi bi-check"></i> Update
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php  
include '../includes/footer.php';
?>