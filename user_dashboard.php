<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit;
}

// Get user data from session
$username = $_SESSION['user']; // This is the username string
$user_id = $_SESSION['user_id']; // Use the separate user_id session variable
$user_email = $_SESSION['email']; // Use the separate email session variable

// Handle form submission untuk menambah/edit koleksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        if ($_POST['action'] === 'add') {
            $sql = "INSERT INTO todolist (title, category, status, user_id, created_at, updated_at) VALUES ('$title', '$category', '$status', '$user_id', NOW(), NOW())";
            if (mysqli_query($conn, $sql)) {
                $success = "Koleksi berhasil ditambahkan!";
            } else {
                $error = "Gagal menambahkan koleksi!";
            }
        } elseif ($_POST['action'] === 'edit') {
            $id = (int)$_POST['id'];
            $sql = "UPDATE todolist SET title='$title', category='$category', status='$status', updated_at=NOW() WHERE id=$id AND user_id=$user_id";
            if (mysqli_query($conn, $sql)) {
                $success = "Koleksi berhasil diupdate!";
            } else {
                $error = "Gagal mengupdate koleksi!";
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM todolist WHERE id=$id AND user_id=$user_id";
    if (mysqli_query($conn, $sql)) {
        $success = "Koleksi berhasil dihapus!";
    } else {
        $error = "Gagal menghapus koleksi!";
    }
}

// Get filter parameters
$filterCategory = isset($_GET['category']) ? $_GET['category'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with filters
$whereClause = "WHERE user_id = " . $user_id;
if (!empty($filterCategory)) {
    $whereClause .= " AND category = '" . mysqli_real_escape_string($conn, $filterCategory) . "'";
}
if (!empty($filterStatus)) {
    $whereClause .= " AND status = '" . mysqli_real_escape_string($conn, $filterStatus) . "'";
}
if (!empty($searchTerm)) {
    $whereClause .= " AND title LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'";
}

// Get user's collections
$sql = "SELECT * FROM todolist $whereClause ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$collections = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get statistics
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'akan' THEN 1 ELSE 0 END) as akan,
    SUM(CASE WHEN status = 'sedang' THEN 1 ELSE 0 END) as sedang,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN category = 'Film' THEN 1 ELSE 0 END) as film,
    SUM(CASE WHEN category = 'Anime' THEN 1 ELSE 0 END) as anime,
    SUM(CASE WHEN category = 'Komik' THEN 1 ELSE 0 END) as komik,
    SUM(CASE WHEN category = 'Novel' THEN 1 ELSE 0 END) as novel
    FROM todolist WHERE user_id = " . $user_id;
$statsResult = mysqli_query($conn, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);

$progress = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadWatch - Dashboard User</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">📚 ReadWatch</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    
                </ul>
                <div class="d-flex align-items-center text-white">
                    <span class="me-3">Halo, <?= htmlspecialchars($username) ?>!</span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-4">
        <!-- Alert Messages -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- User Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 text-primary">📚 My ReadWatch</h1>
                <p class="text-muted mb-0">Selamat datang, <?= htmlspecialchars($username) ?>! Kelola koleksi bacaan & tontonan Anda</p>
            </div>
            <span class="badge bg-success fs-6 px-3 py-2">👤 User</span>
        </div>

        <!-- Welcome Card -->
        <div class="card bg-gradient-success text-white mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title mb-2">🎬 Koleksi Pribadi Anda</h4>
                        <p class="card-text mb-0">
                            Catat dan pantau progress film, anime, komik, dan novel yang akan, sedang atau sudah Anda nikmati. Jangan
                            sampai lupa apa yang sudah ditonton atau dibaca!
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="fs-1">📖</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col mb-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Total Koleksi</h5>
                            <h2 class="mb-0"><?= $stats['total'] ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">📊</div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Mendatang</h5>
                            <h2 class="mb-0"><?= $stats['akan'] ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">⏳</div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Sedang Berjalan</h5>
                            <h2 class="mb-0"><?= $stats['sedang'] ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">🔄</div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Sudah Tamat</h5>
                            <h2 class="mb-0"><?= $stats['selesai'] ?></h2>
                        </div>
                        <div class="fs-1 opacity-50">✅</div>
                    </div>
                </div>
            </div>
            <div class="col mb-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Progress</h5>
                            <h2 class="mb-0"><?= $progress ?>%</h2>
                        </div>
                        <div class="fs-1 opacity-50">📈</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">🎯 Koleksi Saya</h4>
                <p class="text-muted mb-0">Pantau progress bacaan dan tontonan favorit Anda</p>
            </div>
            <button class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                <span class="me-2">📝</span>
                Catat Baru
            </button>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">🔍 Cari & Filter Koleksi</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cari Judul</label>
                            <div class="input-group">
                                <span class="input-group-text">🔍</span>
                                <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan judul..." value="<?= htmlspecialchars($searchTerm) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Filter Kategori</label>
                            <select class="form-select" name="category">
                                <option value="">🎯 Semua Kategori</option>
                                <option value="Film" <?= $filterCategory === 'Film' ? 'selected' : '' ?>>🎬 Film</option>
                                <option value="Anime" <?= $filterCategory === 'Anime' ? 'selected' : '' ?>>🎌 Anime</option>
                                <option value="Komik" <?= $filterCategory === 'Komik' ? 'selected' : '' ?>>📚 Komik</option>
                                <option value="Novel" <?= $filterCategory === 'Novel' ? 'selected' : '' ?>>📖 Novel</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Filter Status</label>
                            <select class="form-select" name="status">
                                <option value="">📋 Semua Status</option>
                                <option value="akan" <?= $filterStatus === 'akan' ? 'selected' : '' ?>>⏳ Mendatang</option>
                                <option value="sedang" <?= $filterStatus === 'sedang' ? 'selected' : '' ?>>🔄 Sedang Berjalan</option>
                                <option value="selesai" <?= $filterStatus === 'selesai' ? 'selected' : '' ?>>✅ Sudah Tamat</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">🔍 Filter</button>
                            <a href="user_dashboard.php" class="btn btn-outline-secondary">🔄 Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Collections Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">📋 Daftar Koleksi (<?= count($collections) ?>)</h6>
                <?php if (count($collections) > 0): ?>
                    <small class="text-muted">Menampilkan <?= count($collections) ?> koleksi pribadi</small>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (count($collections) === 0): ?>
                    <div class="text-center py-5">
                        <div class="fs-1 mb-3">📚</div>
                        <h5 class="text-muted">Belum ada koleksi yang dicatat</h5>
                        <p class="text-muted">Klik tombol "Catat Baru" untuk memulai mencatat koleksi Anda</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Judul</th>
                                    <th class="border-0">Kategori</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Tanggal Catat</th>
                                    <th class="border-0 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($collections as $item): ?>
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2 fs-5">
                                                    <?php
                                                    switch ($item['category']) {
                                                        case 'Film': echo '🎬'; break;
                                                        case 'Anime': echo '🎌'; break;
                                                        case 'Komik': echo '📚'; break;
                                                        case 'Novel': echo '📖'; break;
                                                        default: echo '📄';
                                                    }
                                                    ?>
                                                </span>
                                                <div>
                                                    <div class="fw-semibold"><?= htmlspecialchars($item['title']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge category-badge <?php
                                                switch ($item['category']) {
                                                    case 'Film': echo 'bg-primary'; break;
                                                    case 'Anime': echo 'bg-danger'; break;
                                                    case 'Komik': echo 'bg-success'; break;
                                                    case 'Novel': echo 'bg-warning'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                            ?>">
                                                <?= htmlspecialchars($item['category']) ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge status-badge 
                                            <?php
                                            if ($item['status'] === 'selesai') {
                                                echo 'bg-info';
                                            } elseif ($item['status'] === 'sedang') {
                                                echo 'bg-warning text-dark';
                                            } elseif ($item['status'] === 'akan') {
                                                echo 'bg-danger';
                                            }
                                            $item['status'] === 'selesai' ? 'bg-info' : 'bg-warning text-dark'
                                            ?>
                                            ">
                                                <?php
                                                if ($item['status'] === 'selesai') {
                                                    echo '✅ Tamat';
                                                } elseif ($item['status'] === 'sedang') {
                                                    echo '🔄 Berjalan';
                                                } elseif ($item['status'] === 'akan') {
                                                    echo '⏳ Mendatang';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($item['created_at'])) ?></small>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-success edit-btn" 
                                                        data-id="<?= $item['id'] ?>"
                                                        data-title="<?= htmlspecialchars($item['title']) ?>"
                                                        data-category="<?= $item['category'] ?>"
                                                        data-status="<?= $item['status'] ?>"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        title="Edit koleksi">
                                                    ✏️
                                                </button>
                                                <a href="?delete=<?= $item['id'] ?>" 
                                                   class="btn btn-outline-danger"
                                                   onclick="return confirm('Yakin ingin menghapus dari koleksi?')"
                                                   title="Hapus dari koleksi">
                                                    🗑️
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">📝 Catat Koleksi Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📝 Judul</label>
                            <input type="text" class="form-control form-control-lg" name="title" placeholder="Masukkan judul..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🎯 Kategori</label>
                            <select class="form-select form-select-lg" name="category" required>
                                <option value="Film">🎬 Film</option>
                                <option value="Anime">🎌 Anime</option>
                                <option value="Komik">📚 Komik</option>
                                <option value="Novel">📖 Novel</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📊 Status</label>
                            <select class="form-select form-select-lg" name="status" required>
                                <option value="akan">⏳ Mendatang</option>
                                <option value="sedang">🔄 Sedang Berjalan</option>
                                <option value="selesai">✅ Sudah Tamat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">💾 Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">✏️ Edit Koleksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📝 Judul</label>
                            <input type="text" class="form-control form-control-lg" name="title" id="editTitle" placeholder="Masukkan judul..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🎯 Kategori</label>
                            <select class="form-select form-select-lg" name="category" id="editCategory" required>
                                <option value="Film">🎬 Film</option>
                                <option value="Anime">🎌 Anime</option>
                                <option value="Komik">📚 Komik</option>
                                <option value="Novel">📖 Novel</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📊 Status</label>
                            <select class="form-select form-select-lg" name="status" id="editStatus" required>
                                <option value="akan">⏳ Mendatang</option>
                                <option value="sedang">🔄 Sedang Berjalan</option>
                                <option value="selesai">✅ Sudah Tamat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">💾 Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Handle edit button click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('editId').value = this.dataset.id;
                document.getElementById('editTitle').value = this.dataset.title;
                document.getElementById('editCategory').value = this.dataset.category;
                document.getElementById('editStatus').value = this.dataset.status;
            });
        });
    </script>
</body>
</html>