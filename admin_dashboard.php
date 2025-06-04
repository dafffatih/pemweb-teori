<?php
session_start();
include 'db.php';

// Cek apakah user sudah login dan merupakan admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Handle form submission untuk tambah user
if (isset($_POST['tambah_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password_hash', '$role')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan user!');</script>";
    }
}

// Handle delete user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Jangan biarkan admin menghapus dirinya sendiri
    if ($delete_id == $_SESSION['user_id']) {
        echo "<script>alert('Tidak dapat menghapus akun sendiri!'); window.location.href='admin_dashboard.php';</script>";
        exit;
    }
    
    $query = "DELETE FROM users WHERE id = $delete_id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('User berhasil dihapus!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus user!');</script>";
    }
}

// Ambil statistik users
$total_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));
$total_admin = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role = 'admin'"));
$total_user_biasa = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role = 'user'"));

// Ambil semua users
$users_query = "SELECT * FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ReadWatch</title>
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
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">📚 ReadWatch</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <?php if (isset($_SESSION['username'])): ?>
                        Selamat datang, <?= $_SESSION['username'] ?>!
                    <?php else: ?>
                        Selamat datang, Pengguna!
                    <?php endif; ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Admin Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 text-primary">👑 Admin Dashboard</h1>
                        <p class="text-muted mb-0">Selamat datang, <?= isset($_SESSION['username']) ? $_SESSION['username'] : 'Administrator' ?>! Kelola sistem ReadWatch</p>
                    </div>
                    <span class="badge bg-danger fs-6 px-3 py-2">🔐 Administrator</span>
                </div>

                <!-- Admin Welcome Card -->
                <div class="card bg-gradient-primary text-white mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title mb-2">🎯 Panel Administrasi</h4>
                                <p class="card-text mb-0">
                                    Sebagai administrator, Anda memiliki akses penuh untuk mengelola pengguna sistem ReadWatch. Anda
                                    dapat menambah, melihat, dan menghapus akun pengguna.
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="fs-1">👨‍💼</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-0">Total User</h5>
                                    <h2 class="mb-0"><?= $total_users ?></h2>
                                </div>
                                <div class="fs-1 opacity-50">👥</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-0">Admin</h5>
                                    <h2 class="mb-0"><?= $total_admin ?></h2>
                                </div>
                                <div class="fs-1 opacity-50">👑</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-0">User Biasa</h5>
                                    <h2 class="mb-0"><?= $total_user_biasa ?></h2>
                                </div>
                                <div class="fs-1 opacity-50">👤</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Header Management -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1">👥 Manajemen Pengguna</h4>
                        <p class="text-muted mb-0">Kelola akun pengguna sistem ReadWatch</p>
                    </div>
                    <button class="btn btn-danger btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <span class="me-2">➕</span>
                        Daftarkan User Baru
                    </button>
                </div>

                <!-- Users Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">👥 Daftar Pengguna (<?= $total_users ?>)</h6>
                        <small class="text-muted">
                            <?= $total_admin ?> Admin, <?= $total_user_biasa ?> User Biasa
                        </small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">User</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0">Role</th>
                                        <th class="border-0">Tanggal Daftar</th>
                                        <th class="border-0 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 text-white fw-bold <?= $user['role'] == 'admin' ? 'bg-danger' : 'bg-primary' ?>" 
                                                     style="width: 40px; height: 40px;">
                                                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold"><?= htmlspecialchars($user['username']) ?></div>
                                                    <small class="text-muted">
                                                        <?= $user['role'] == 'admin' ? '👑 Administrator' : '👤 User Biasa' ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-muted"><?= htmlspecialchars($user['email']) ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge <?= $user['role'] == 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                                                <?= $user['role'] == 'admin' ? '👑 Admin' : '👤 User' ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($user['created_at'])) ?></small>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php if ($user['id'] == 1 || $user['id'] == $_SESSION['user_id']): ?>
                                                <span class="badge bg-warning text-dark">🔒 Protected</span>
                                            <?php else: ?>
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                                        title="Hapus user">
                                                    🗑️ Hapus
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="addUserModalLabel">➕ Daftarkan User Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">👤 Username</label>
                            <input type="text" name="username" class="form-control form-control-lg" 
                                   placeholder="Masukkan username..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📧 Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" 
                                   placeholder="Masukkan email..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🔒 Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" 
                                   placeholder="Masukkan password..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🎭 Role</label>
                            <select name="role" class="form-select form-select-lg">
                                <option value="user">👤 User Biasa</option>
                                <option value="admin">👑 Administrator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_user" class="btn btn-danger">💾 Daftarkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function confirmDelete(userId, username) {
            if (confirm(`Yakin ingin menghapus user "${username}"?`)) {
                window.location.href = `admin_dashboard.php?delete_id=${userId}`;
            }
        }
    </script>
</body>
</html>
