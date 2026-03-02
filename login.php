<?php
session_start();
require_once __DIR__ . '/config/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = clean_input($_POST['username'] ?? '');
    $npk      = clean_input($_POST['npk'] ?? '');

    if (empty($username) || empty($npk)) {
        $error = 'Username dan NPK harus diisi!';
    } else {

        $user = get_user_by_username_npk($username, $npk);

        if ($user) {

    $_SESSION['employee_id']   = $user['id'];
    $_SESSION['employee_user'] = $user['username'];
    $_SESSION['employee_name'] = $user['nama_lengkap'];

    header("Location: index.php");
    exit;
} else {
            $error = 'Username atau NPK salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kabupaten Kupang</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <img src="Astra-Agro.gif" alt="Logo Kabupaten Kupang" class="logo-img">
                <h4 class="app-title">Sistem Cuti Karyawan</h4>
                <p class="app-subtitle">Portal Internal Perusahaan</p>
            </div>
            <div class="card-body">
                
                <?php if ($error): ?>
                <div class="alert alert-custom mb-4" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?= $error ?></div>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-floating position-relative">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autocomplete="off">
                        <label for="username">Username</label>
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    
                    <div class="form-floating position-relative">
                       <input type="text" class="form-control" id="npk" name="npk" placeholder="NPK" required>
                        <label for="npk">NPK</label>
                        <span class="input-group-text password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label text-muted small" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                        <a href="lupa_password.php" class="text-decoration-none small" style="color: var(--primary-color);">Lupa Password?</a>
                    </div>

                    <button type="submit" class="btn btn-login">
                        MASUK <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="footer-links">
            <p>&copy; <?= date('Y') ?> PT Makmur</p>
        </div>
    </div>

    <script>
        function togglePassword() {
    const npkInput = document.getElementById('npk');
    const toggleIcon = document.getElementById('toggleIcon');

    if (npkInput.type === 'password') {
        npkInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        npkInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
    </script>
</body>
</html>