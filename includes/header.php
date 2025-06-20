<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ReadWatch' : 'ReadWatch'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
        }
    </style>
</head>
<body class="bg-light">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark <?php echo $_SESSION['role'] == 'admin' ? 'bg-success' : 'bg-primary'; ?>">
            <div class="container-fluid">
                <?php
                // Tentukan root path berdasarkan lokasi file
                $current_dir = basename(dirname($_SERVER['PHP_SELF']));
                if ($current_dir == 'admin' || $current_dir == 'user') {
                    $root_path = '../';
                } else {
                    $root_path = '';
                }
                ?>
                
                <a class="navbar-brand" href="<?php echo $root_path; ?><?php echo $_SESSION['role'] == 'admin' ? 'admin/' : 'user/'; ?>index.php">
                    <i class="bi bi-collection"></i> ReadWatch
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $root_path; ?>admin/index.php">
                                <i class="bi bi-people"></i> Kelola Pengguna
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $root_path; ?>user/index.php">
                                <i class="bi bi-collection"></i> Koleksi Saya
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <button class="btn nav-link dropdown-toggle text-white border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="d-inline-block rounded-circle bg-<?php echo $_SESSION['role'] == 'admin' ? 'danger' : 'warning'; ?> text-white text-center me-2" style="width: 30px; height: 30px; line-height: 30px;">
                                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                                </div>
                                <?php echo $_SESSION['full_name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header"><?php echo $_SESSION['role'] == 'admin' ? 'Administrator' : 'User'; ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $root_path; ?>auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Content wrapper -->
        <div class="content">