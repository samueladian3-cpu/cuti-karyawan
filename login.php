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
    <style>
        :root {
            --primary-color: #1a3a8f;
            --primary-dark: #0d2561;
            --secondary-color: #f9a825;
            --text-color: #333;
            --text-light: #6c757d;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('../img/Kantor.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(13, 37, 97, 0.85); /* Overlay gelap dengan tint biru */
            backdrop-filter: blur(5px);
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.8s ease;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
        }

        .card-header {
            background: transparent;
            border-bottom: none;
            padding: 40px 30px 20px;
            text-align: center;
        }

        .logo-img {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .app-title {
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
            font-size: 1.5rem;
        }

        .app-subtitle {
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 400;
        }

        .card-body {
            padding: 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating .form-control {
            border-radius: 12px;
            border: 2px solid #eee;
            padding-left: 20px;
            height: 55px;
        }

        .form-floating .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.25rem rgba(26, 58, 143, 0.1);
        }

        .form-floating label {
            padding-left: 20px;
            color: #999;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(26, 58, 143, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 58, 143, 0.4);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
        }

        .alert-custom {
            border-radius: 12px;
            font-size: 0.9rem;
            border: none;
            background-color: #fee2e2;
            color: #dc2626;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-links {
            text-align: center;
            margin-top: 25px;
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Input Icon styling trick */
        .input-group-text {
            background: transparent;
            border: none;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
            color: #aaa;
        }

        .password-toggle {
            cursor: pointer;
            z-index: 5;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <img src="../img/logo.png" alt="Logo Kabupaten Kupang" class="logo-img">
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
            <p>&copy; <?= date('Y') ?> Pemerintah Kabupaten Kupang</p>
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