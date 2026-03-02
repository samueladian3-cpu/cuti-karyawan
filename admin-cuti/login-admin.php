<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
$conn = koneksi_db();

$error = '';  // ✅ FIXED: typo "erorr" jadi "error"
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan Password harus diisi!';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap FROM admin WHERE username = ?");
        
        if ($stmt === false) {
            $error = 'Error database: ' . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();  // ✅ TAMBAH INI - get result sebelum cek num_rows

            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();

                // Sementara tanpa hash, langsung compare string
                if ($password === $admin['password']) {
                    $_SESSION['admin_id']   = $admin['id'];
                    $_SESSION['admin_user'] = $admin['username'];
                    $_SESSION['admin_name'] = $admin['nama_lengkap'];

                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = 'Username atau Password salah!';
                }
            } else {
                $error = 'Username atau Password salah!';
            }
            
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Cuti Karyawan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Login Admin</h2>
            
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>