<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ReadWatch - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        
        /* Background dengan gradien sederhana */
        body {
            background: linear-gradient(135deg,rgb(100, 123, 224) 0%,rgb(24, 106, 164) 100%);
            background-attachment: fixed;
        }
        
        /* Wrapper untuk mengatur layout */
        .wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Content area flexible */
        .content {
            flex: 1;
        }
        
        /* Navbar dengan warna solid */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: #0d6efd !important;
        }
        
        /* Card dengan background putih solid */
        .card {
            background: rgba(255, 255, 255, 0.98);
            border: none;
        }
        
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- User Navbar - Sticky Top -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="index.php">
                    <i class="bi bi-book me-2"></i> ReadWatch
                </a>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="content">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <h2 class="text-primary"><i class="bi bi-book"></i> ReadWatch</h2>
                                    <p class="text-muted">Kelola Koleksi Bacaan & Tontonan</p>
                                </div>
                                <form method="POST" action="auth/login.php">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" id="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" id="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mb-3">
                                        <i class="bi bi-box-arrow-in-right"></i> Login
                                    </button>
                                </form>
                                
                                <div class="text-center">
                                    <p class="mb-0">Belum punya akun? <a href="auth/register.php" class="text-decoration-none">Daftar di sini</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

     
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>