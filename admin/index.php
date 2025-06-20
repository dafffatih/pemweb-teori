<?php
session_start();
include '../config/db.php';

// cek apakah pengguna sudah login dan memiliki hak akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$page_title = 'Admin Panel';
$base_path = '../';
include '../includes/header.php';

// mengambil statistik pengguna
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_admins = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin'"))['count'];
$total_regular_users = $total_users - $total_admins;
$recent_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['count'];
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-shield-check"></i> Admin Panel</h1>
        <div class="badge bg-success fs-6"><?php echo $total_admins; ?> Admin, <?php echo $total_regular_users; ?> User Biasa</div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $total_users; ?></h4>
                            <p class="mb-0">Total Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $total_admins; ?></h4>
                            <p class="mb-0">Administrators</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $total_regular_users; ?></h4>
                            <p class="mb-0">Regular Users</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?php echo $recent_users; ?></h4>
                            <p class="mb-0">New This Week</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-plus fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Management -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Pengguna (<?php echo $total_users; ?>)</h5>
            <a href="create.php" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Tambah User
            </a>
        </div>
        <div class="card-body">
            <?php
            if (isset($_SESSION['admin_success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['admin_success'] . '</div>';
                unset($_SESSION['admin_success']);
            }
            if (isset($_SESSION['admin_error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['admin_error'] . '</div>';
                unset($_SESSION['admin_error']);
            }
            ?>
            
            <?php
            $users_query = "SELECT * FROM users ORDER BY created_at DESC";
            $users_result = mysqli_query($conn, $users_query);
            
            if (mysqli_num_rows($users_result) > 0):
            ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="rounded-circle bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?> text-white d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                        <br><small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                        <?php if ($user['role'] == 'admin'): ?>
                                            <br><small class="text-warning"><i class="bi bi-star-fill"></i> Administrator</small>
                                        <?php else: ?>
                                            <br><small class="text-info"><i class="bi bi-person"></i> User Biasa</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                                    <?php echo $user['role'] == 'admin' ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="delete.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Hapus user ini? Semua data terkait akan ikut terhapus.')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                                <?php endif; ?> 
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="text-muted">No users found</h4>
                <p class="text-muted">Start by creating the first user!</p>
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Create User
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php  
include '../includes/footer.php';
?>