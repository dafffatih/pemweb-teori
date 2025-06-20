<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$user_id = intval($_GET['id']);

// Proses update HARUS di atas sebelum HTML output
if (isset($_POST['update'])) {
    $role = $_POST['role'];
    $update_query = "UPDATE users SET role = '$role' WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['admin_success'] = "Role user berhasil diperbarui!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['admin_error'] = "Gagal memperbarui role user.";
        header("Location: index.php");
        exit;
    }
}

// mengambil data user berdasarkan ID
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if (!$user) {
    header("Location: index.php");
    exit;
}
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - ReadWatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light"></body>
    <div class="container-fluid py-4">
        <h1 class="mb-4">Edit User Role</h1>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role *</label>
                                <select name="role" class="form-select" required>
                                    <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User Biasa</option>
                                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" name="update" class="btn btn-warning">
                                    <i class="bi bi-check"></i> Update Role
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">User Info</h5>
                    </div>
                    <div class="card-body text-center">
                        <h6><?php echo htmlspecialchars($user['full_name']); ?></h6>
                        <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                        <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                        <hr>
                        <small class="text-muted">
                            Bergabung: <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php  
include '../includes/footer.php';
?>