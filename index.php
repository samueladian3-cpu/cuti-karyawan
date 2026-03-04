<?php
session_start();
require_once __DIR__ . '/config/functions.php';

// Cek login
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION['employee_id'];
$user      = get_user_by_id($user_id);
$leaves    = get_user_leaves($user_id);
$used      = get_total_approved_leave($user_id);
$remaining = get_sisa_cuti($user_id);
$quota     = $user['hak_cuti'];
$pct_used  = $quota > 0 ? round(($used / $quota) * 100) : 0;

// Recent 5
$recent = array_slice(array_reverse($leaves), 0, 5);

// Counts
$cnt_approved = count(array_filter($leaves, function($l) { return $l['status'] === 'approved'; }));
$cnt_pending  = count(array_filter($leaves, function($l) { return $l['status'] === 'pending'; }));
$cnt_rejected = count(array_filter($leaves, function($l) { return $l['status'] === 'rejected'; }));

// Greeting
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 17 ? 'Selamat Siang' : 'Selamat Malam');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Cuti System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<!-- Mobile Menu Toggle -->
<button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
    <i class="fas fa-bars"></i>
</button>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php
        $active = 'home';
        $userName = $user['nama'] ?? 'User';
        include 'assets/sidebar.php';
        ?>

        <!-- CONTENT -->
        <div class="col-md-10">
            <div class="page-content">

                <!-- HERO BANNER -->
                <div class="hero-banner">
                    <div class="hero-left">
                        <p class="hero-greeting">
                            <i class="fas fa-sun me-1"></i> <?= $greeting ?>
                        </p>
                        <h2 class="hero-name">
                            <?= esc($user['nama']) ?> <span>👋</span>
                        </h2>
                        <p class="hero-sub">Pantau sisa cuti dan riwayat pengajuanmu di sini.</p>
                    </div>
                    <div class="hero-right">
                        <p class="hero-date"><i class="fas fa-calendar me-1"></i><?= date('d F Y') ?></p>
                    </div>
                </div>

                <!-- QUOTA CARD -->
                <div class="quota-card">
                    <div class="quota-top">
                        <h6><i class="fas fa-chart-pie me-2" style="color:var(--blue)"></i>Kuota Cuti Tahunan</h6>
                        <span class="quota-pct"><?= $pct_used ?>% terpakai</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width:<?= $pct_used ?>%"></div>
                    </div>
                    <div class="quota-blocks">
                        <div class="quota-block total">
                            <div class="qb-num"><?= $quota ?></div>
                            <div class="qb-lbl">Total Kuota</div>
                        </div>
                        <div class="quota-block used">
                            <div class="qb-num"><?= $used ?></div>
                            <div class="qb-lbl">Terpakai</div>
                        </div>
                        <div class="quota-block sisa">
                            <div class="qb-num"><?= $remaining ?></div>
                            <div class="qb-lbl">Sisa Cuti</div>
                        </div>
                    </div>
                </div>

               

                <!-- RECENT TABLE -->
                <div class="table-card">
                    <div class="tc-header">
                        <div class="tc-icon"><i class="fas fa-history"></i></div>
                        <span>Riwayat Terbaru</span>
                        <a href="riwayat.php">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>

                    <?php if (count($recent) > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent as $leave): ?>
                                        <tr>
                                            <td class="td-date">
                                                <i class="fas fa-calendar-day me-2" style="color:var(--blue);font-size:12px"></i>
                                                <?= esc($leave['start_date']) ?>
                                            </td>
                                            <td class="td-date">
                                                <i class="fas fa-calendar-check me-2" style="color:var(--teal);font-size:12px"></i>
                                                <?= esc($leave['end_date']) ?>
                                            </td>
                                            <td>
                                                <span class="td-days"><?= esc($leave['total_days']) ?></span>
                                                <span style="font-size:12px;color:var(--muted)"> hari</span>
                                            </td>
                                            <td>
                                                <?php if ($leave['status'] === 'approved'): ?>
                                                    <span class="status-badge approved"><span class="dot"></span> Approved</span>
                                                <?php elseif ($leave['status'] === 'pending'): ?>
                                                    <span class="status-badge pending"><span class="dot"></span> Pending</span>
                                                <?php else: ?>
                                                    <span class="status-badge rejected"><span class="dot"></span> Rejected</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="ei"><i class="fas fa-inbox"></i></div>
                            <h6>Belum Ada Pengajuan</h6>
                            <p>Kamu belum pernah mengajukan cuti.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div><!-- /.page-content -->
        </div><!-- /.col-md-10 -->
    </div><!-- /.row -->
</div><!-- /.container-fluid -->

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
// Mobile Menu Toggle
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const sidebar = document.getElementById('app-sidebar');
const sidebarWrapper = sidebar ? sidebar.closest('.col-md-2') : null;
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (mobileMenuToggle && sidebar && sidebarWrapper && sidebarOverlay) {
    mobileMenuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        sidebarWrapper.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
    });

    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarWrapper.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
    });
}
</script>

</body>
</html>