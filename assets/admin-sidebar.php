<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
}

$active = $active ?? 'dashboard';
$adminName = $_SESSION['admin_name'] ?? 'Administrator';
$adminInitial = mb_strtoupper(mb_substr($adminName, 0, 1));

function esc_admin($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<div class="col-md-2 p-0">
    <aside class="admin-sidebar" id="adminSidebar">
        
        <!-- Brand Header -->
        <div class="admin-sidebar-header">
            <div class="admin-logo">
                <div class="admin-logo-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="admin-logo-text">
                    <span class="admin-logo-title">Admin Panel</span>
                    <span class="admin-logo-sub">Cuti Management</span>
                </div>
            </div>
        </div>

        <div class="admin-sidebar-divider"></div>

        <!-- Navigation -->
        <span class="admin-nav-label">Menu Utama</span>

        <nav class="admin-nav-links">
            <a href="#" class="admin-nav-link <?= $active === 'dashboard' ? 'active' : '' ?>" data-page="dashboard">
                <span class="admin-nav-icon">
                    <i class="fas fa-chart-line"></i>
                </span>
                <span>Dashboard</span>
            </a>
            
            <a href="#" class="admin-nav-link <?= $active === 'data-karyawan' ? 'active' : '' ?>" data-page="data-karyawan">
                <span class="admin-nav-icon">
                    <i class="fas fa-users"></i>
                </span>
                <span>Data Karyawan</span>
            </a>
        </nav>

        <!-- User Profile Footer -->
        <div class="admin-sidebar-footer">
            <div class="admin-user-avatar"><?= esc_admin($adminInitial) ?></div>
            <div class="admin-user-info">
                <div class="admin-user-name"><?= esc_admin($adminName) ?></div>
                <div class="admin-user-role">Administrator</div>
            </div>
            <a href="../admin-cuti/logout.php" class="admin-logout-btn" title="Logout" onclick="showSpinner()">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>

    </aside>
</div>
