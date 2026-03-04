<?php
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$active  = $active ?? '';
$userName = $userName ?? ($_SESSION['employee_name'] ?? 'User');
$userRole = $userRole ?? 'Karyawan';
$initial = mb_strtoupper(mb_substr($userName, 0, 1));

function s($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function url($path) { global $baseUrl; $path = '/' . ltrim($path, '/'); return $baseUrl === '' ? ltrim($path, '/') : rtrim($baseUrl, '/') . $path; }
?>

<div class="col-md-2 p-0">
    <div class="sidebar" id="app-sidebar">

    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="logo-text">
                <span class="logo-title">Cuti System</span>
                <span class="logo-sub">PT Gunung Sejahtera Dua Indah & PT Gunung Sejahtera Yoli Makmur</span>
            </div>
        </div>
    </div>

    <div class="sidebar-divider"></div>

    <span class="nav-label">Menu Utama</span>

    <nav class="nav-links">
        <a href="<?= url('/index.php') ?>" class="nav-link <?= $active === 'home' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fas fa-home"></i></span>
            Dashboard
        </a>
        
        <a href="<?= url('/riwayat.php') ?>" class="nav-link <?= $active === 'riwayat' ? 'active' : '' ?>">
            <span class="nav-icon"><i class="fas fa-history"></i></span>
            Riwayat
        </a>
    </nav>

    <!-- User + Logout -->
    <div class="sidebar-footer">
        <div class="user-avatar"><?= s($initial) ?></div>
        <div class="user-info">
            <div class="user-name"><?= s($userName) ?></div>
            <div class="user-role"><?= s($userRole) ?></div>
        </div>
        <a href="<?= url('/logout.php') ?>" class="logout-btn" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>

    </div>
</div>