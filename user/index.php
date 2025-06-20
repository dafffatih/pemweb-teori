<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Filter PHP
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$where_conditions = ["user_id = '$user_id'"];

if (!empty($search)) {
    $where_conditions[] = "title LIKE '%$search%'";
}
if (!empty($category_filter)) {
    $where_conditions[] = "category = '$category_filter'";
}
if (!empty($status_filter)) {
    $where_conditions[] = "status = '$status_filter'";
}

$where_clause = implode(' AND ', $where_conditions);

// Statistik
$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM items WHERE user_id = '$user_id'"))['count'];
$sedang_berjalan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM items WHERE user_id = '$user_id' AND status = 'Sedang Berjalan'"))['count'];
$sudah_tamat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM items WHERE user_id = '$user_id' AND status = 'Sudah Tamat'"))['count'];

// Ambil data items
$items_query = "SELECT * FROM items WHERE $where_clause ORDER BY created_at DESC";
$items_result = mysqli_query($conn, $items_query);

$page_title = 'Koleksi Saya';
$base_path = '../';
include '../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-collection"></i> Koleksi Saya</h1>
        <div class="badge bg-primary fs-6"><?php echo $total_items; ?> Total Items</div>
    </div>
    <!-- kartu statistik -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white" >
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $total_items; ?></h4>
                            <p class="mb-0">Total Koleksi</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-collection fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $sedang_berjalan; ?></h4>
                            <p class="mb-0">Sedang Berjalan</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-play-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $sudah_tamat; ?></h4>
                            <p class="mb-0">Sudah Tamat</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Koleksi -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Koleksi</h5>
            <a href="create.php" class="btn btn-primary">
                <i class="bi bi-plus"></i> Catat Baru
            </a>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['user_success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['user_success'] . '</div>';
                unset($_SESSION['user_success']);
            }
            if (isset($_SESSION['user_error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['user_error'] . '</div>';
                unset($_SESSION['user_error']);
            }
            ?>
            
            
            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari judul..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="Film" <?php echo $category_filter == 'Film' ? 'selected' : ''; ?>>🎬 Film</option>
                        <option value="Anime" <?php echo $category_filter == 'Anime' ? 'selected' : ''; ?>>📺 Anime</option>
                        <option value="Komik" <?php echo $category_filter == 'Komik' ? 'selected' : ''; ?>>📚 Komik</option>
                        <option value="Novel" <?php echo $category_filter == 'Novel' ? 'selected' : ''; ?>>📖 Novel</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Sedang Berjalan" <?php echo $status_filter == 'Sedang Berjalan' ? 'selected' : ''; ?>>⏳ Sedang Berjalan</option>
                        <option value="Sudah Tamat" <?php echo $status_filter == 'Sudah Tamat' ? 'selected' : ''; ?>>✅ Sudah Tamat</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
            
            <?php if (mysqli_num_rows($items_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table table-primary">
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Tanggal Ditambah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php 
                                    $icons = ['Film' => '🎬', 'Anime' => '📺', 'Komik' => '📚', 'Novel' => '📖'];
                                    echo $icons[$item['category']] . ' ' . $item['category']; 
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $item['status'] == 'Sudah Tamat' ? 'success' : 'warning'; ?>">
                                    <?php echo $item['status'] == 'Sudah Tamat' ? '✅' : '⏳'; ?> <?php echo $item['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($item['notes'])): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars(substr($item['notes'], 0, 50)); ?><?php echo strlen($item['notes']) > 50 ? '...' : ''; ?></small>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($item['created_at'])); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Hapus item ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-collection display-1 text-muted"></i>
                <h4 class="text-muted">Belum ada koleksi</h4>
                <p class="text-muted">Mulai catat film, anime, komik, atau novel favorit Anda!</p>
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Catat Baru
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php  
include '../includes/footer.php';
?>
