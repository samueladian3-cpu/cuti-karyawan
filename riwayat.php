<?php
session_start();
require_once __DIR__ . '/config/functions.php';

// Cek login
if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['employee_id'];
$user    = get_user_by_id($user_id);
$leaves  = get_user_leaves($user_id);

// Filter status (optional)
$status_filter = $_GET['status'] ?? '';

if ($status_filter) {
    $leaves = array_filter($leaves, function($leave) use ($status_filter) {
        return $leave['status'] === $status_filter;
    });
}

// Stats
$all_leaves    = get_user_leaves($user_id);
$total         = count($all_leaves);
$approved      = count(array_filter($all_leaves, function($l) { return $l['status'] === 'approved'; }));
$pending       = count(array_filter($all_leaves, function($l) { return $l['status'] === 'pending'; }));
$rejected      = count(array_filter($all_leaves, function($l) { return $l['status'] === 'rejected'; }));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Cuti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/riwayat.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <?php
        $active = 'riwayat';
        include __DIR__ . '/assets/sidebar.php';
        ?>
       

        <div class="col-md-10">
            <div class="page-content">

                <!-- TOPBAR -->
                <div class="topbar">
                    <div class="topbar-left">
                        <h4><i class="fas fa-history me-2" style="color:var(--blue)"></i>Riwayat Pengajuan Cuti</h4>
                        <p>Halo, <strong><?= esc($user['nama_lengkap']) ?></strong> — berikut daftar pengajuan cutimu</p>
                    </div>
                    <div class="topbar-actions">
                        <a href="index.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="logout.php" class="btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>

                <!-- STAT CARDS -->
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-icon all"><i class="fas fa-layer-group"></i></div>
                        <div class="stat-info">
                            <div class="num"><?= $total ?></div>
                            <div class="lbl">Total Pengajuan</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon approved"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-info">
                            <div class="num"><?= $approved ?></div>
                            <div class="lbl">Disetujui</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <div class="num"><?= $pending ?></div>
                            <div class="lbl">Menunggu</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon rejected"><i class="fas fa-times-circle"></i></div>
                        <div class="stat-info">
                            <div class="num"><?= $rejected ?></div>
                            <div class="lbl">Ditolak</div>
                        </div>
                    </div>
                </div>

                <!-- FILTER PILLS -->
                <div class="filter-bar">
                    <a href="riwayat.php"
                       class="filter-pill <?= $status_filter === '' ? 'active-all' : '' ?>">
                        <i class="fas fa-list me-1"></i> Semua
                    </a>
                    <a href="riwayat.php?status=pending"
                       class="filter-pill <?= $status_filter === 'pending' ? 'active-pending' : '' ?>">
                        <i class="fas fa-clock me-1"></i> Pending
                    </a>
                    <a href="riwayat.php?status=approved"
                       class="filter-pill <?= $status_filter === 'approved' ? 'active-approved' : '' ?>">
                        <i class="fas fa-check me-1"></i> Approved
                    </a>
                    <a href="riwayat.php?status=rejected"
                       class="filter-pill <?= $status_filter === 'rejected' ? 'active-rejected' : '' ?>">
                        <i class="fas fa-times me-1"></i> Rejected
                    </a>
                </div>

                <!-- TABLE CARD -->
                <div class="table-card">
                    <div class="table-card-header">
                        <div class="tch-icon"><i class="fas fa-table"></i></div>
                        <span>Data Cuti</span>
                        <span class="count-badge"><?= count($leaves) ?> data</span>
                    </div>

                    <?php if (count($leaves) > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Total Hari</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($leaves as $leave): ?>
                                        <tr>
                                            <td class="td-num"><?= $no++ ?></td>
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
                                                    <span class="status-badge approved">
                                                        <span class="status-dot"></span> Approved
                                                    </span>
                                                <?php elseif ($leave['status'] === 'pending'): ?>
                                                    <span class="status-badge pending">
                                                        <span class="status-dot"></span> Pending
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge rejected">
                                                        <span class="status-dot"></span> Rejected
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                            <h6>Tidak Ada Data</h6>
                            <p>Belum ada pengajuan cuti
                                <?= $status_filter ? 'dengan status <strong>' . esc($status_filter) . '</strong>' : '' ?>
                            </p>
                        </div>
                    <?php endif; ?>

                </div><!-- /.table-card -->

            </div><!-- /.page-content -->
        </div><!-- /.col-md-10 -->
    </div><!-- /.row -->
</div><!-- /.container-fluid -->

</body>
</html>