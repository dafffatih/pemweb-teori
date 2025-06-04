<?php
session_start();
include 'db.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

// Ambil pesan error dari session jika ada
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$success = isset($_SESSION['register_success']) ? $_SESSION['register_success'] : '';

// Hapus pesan dari session setelah ditampilkan
unset($_SESSION['login_error']);
unset($_SESSION['register_success']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadWatch - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="style.css" rel="stylesheet">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg login-card">
                        <div class="card-body p-4">
                            <!-- Header -->
                            <div class="text-center mb-4">
                                <h2 class="card-title text-primary login-title">📚 ReadWatch</h2>
                                <p class="text-muted login-subtitle" id="loginSubtitle">Masuk ke akun Anda</p>
                            </div>

                            <!-- Alert Error -->
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-custom" role="alert">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Alert Success -->
                            <?php if ($success): ?>
                                <div class="alert alert-success alert-custom" role="alert">
                                    <?= htmlspecialchars($success) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Form Login -->
                            <form id="loginForm" action="login.php" method="POST" style="display: block;">
                                <div class="mb-3">
                                    <label for="loginUsername" class="form-label fw-semibold">
                                        👤 Username
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control form-control-lg"
                                        id="loginUsername"
                                        name="username"
                                        placeholder="Masukkan username"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label fw-semibold">
                                        🔒 Password
                                    </label>
                                    <input
                                        type="password"
                                        class="form-control form-control-lg"
                                        id="loginPassword"
                                        name="password"
                                        placeholder="Masukkan password"
                                        required
                                    />
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" name="login">
                                    🚀 Masuk
                                </button>
                            </form>

                            <!-- Form Register -->
                            <form id="registerForm" action="registrasi.php" method="POST" style="display: none;">
                                <div class="mb-3">
                                    <label for="registerUsername" class="form-label fw-semibold">
                                        👤 Username
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control form-control-lg"
                                        id="registerUsername"
                                        name="username"
                                        placeholder="Masukkan username"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <label for="registerEmail" class="form-label fw-semibold">
                                        📧 Email
                                    </label>
                                    <input
                                        type="email"
                                        class="form-control form-control-lg"
                                        id="registerEmail"
                                        name="email"
                                        placeholder="Masukkan email"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <label for="registerPassword" class="form-label fw-semibold">
                                        🔒 Password
                                    </label>
                                    <input
                                        type="password"
                                        class="form-control form-control-lg"
                                        id="registerPassword"
                                        name="password"
                                        placeholder="Masukkan password"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label fw-semibold">
                                        🔒 Konfirmasi Password
                                    </label>
                                    <input
                                        type="password"
                                        class="form-control form-control-lg"
                                        id="confirmPassword"
                                        name="confirm_password"
                                        placeholder="Konfirmasi password"
                                        required
                                    />
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" name="register">
                                    📝 Daftar
                                </button>
                            </form>

                            <!-- Switch Login/Register -->
                            <div class="text-center">
                                <button type="button" id="switchBtn" class="btn btn-link text-decoration-none">
                                    Belum punya akun? <strong>Daftar di sini</strong>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript untuk Switch Login/Register -->
    <script>
        let isLogin = true;
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const loginSubtitle = document.getElementById('loginSubtitle');
        const switchBtn = document.getElementById('switchBtn');


        // Fungsi untuk switch antara login dan register
        function switchMode() {
            isLogin = !isLogin;
            
            if (isLogin) {
                // Mode Login
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                loginSubtitle.textContent = 'Masuk ke akun Anda';
                switchBtn.innerHTML = 'Belum punya akun? <strong>Daftar di sini</strong>';
            } else {
                // Mode Register
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                loginSubtitle.textContent = 'Daftar akun baru';
                switchBtn.innerHTML = 'Sudah punya akun? <strong>Masuk di sini</strong>';

            }
            
            // Reset form
            document.querySelectorAll('input').forEach(input => {
                input.value = '';
            });
        }

        // Event listener untuk tombol switch
        switchBtn.addEventListener('click', switchMode);

        // Validasi form register
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
        });
    </script>
</body>
</html>