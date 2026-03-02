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
    <style>
        :root {
            --navy:      #0b1e5b;
            --navy-mid:  #112470;
            --blue:      #4f8cff;
            --teal:      #52d1b0;
            --surface:   #f2f5fb;
            --card:      #ffffff;
            --text:      #1a2340;
            --muted:     #8a94b0;
            --border:    #e4e9f5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--text);
        }

        /* ── PAGE WRAPPER ── */
        .page-content {
            padding: 36px 40px;
            animation: fadeUp .45s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── TOPBAR ── */
        .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .topbar-left h4 {
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
        }

        .topbar-left p {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        .topbar-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            background: white;
            border: 1.5px solid var(--border);
            color: var(--text);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: .2s;
        }

        .btn-back:hover {
            background: var(--navy);
            color: white;
            border-color: var(--navy);
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            background: #fff1f1;
            border: 1.5px solid #ffd6d6;
            color: #e03131;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: .2s;
        }

        .btn-logout:hover {
            background: #e03131;
            color: white;
            border-color: #e03131;
        }

        /* ── STAT CARDS ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--card);
            border-radius: 14px;
            padding: 20px 22px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0,0,0,0.07);
        }

        .stat-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .stat-icon.all      { background: #eef2ff; color: var(--blue); }
        .stat-icon.approved { background: #e6faf5; color: #1a9e77; }
        .stat-icon.pending  { background: #fff8e6; color: #d48806; }
        .stat-icon.rejected { background: #fff0f0; color: #e03131; }

        .stat-info .num {
            font-family: 'Sora', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1;
        }

        .stat-info .lbl {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* ── FILTER PILLS ── */
        .filter-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 7px 18px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border: 1.5px solid var(--border);
            color: var(--muted);
            background: white;
            transition: .2s;
        }

        .filter-pill:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        .filter-pill.active-all      { background: var(--navy); color: white; border-color: var(--navy); }
        .filter-pill.active-pending  { background: #fff8e6; color: #d48806; border-color: #f5c842; }
        .filter-pill.active-approved { background: #e6faf5; color: #1a9e77; border-color: #52d1b0; }
        .filter-pill.active-rejected { background: #fff0f0; color: #e03131; border-color: #ffb3b3; }

        /* ── TABLE CARD ── */
        .table-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-card-header .tch-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--blue), var(--teal));
            display: grid;
            place-items: center;
            color: white;
            font-size: 13px;
        }

        .table-card-header span {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
        }

        .table-card-header .count-badge {
            margin-left: auto;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: var(--surface);
        }

        thead th {
            padding: 12px 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        tbody tr:last-child { border-bottom: none; }

        tbody tr:hover { background: #f8faff; }

        tbody td {
            padding: 14px 20px;
            font-size: 13.5px;
            color: var(--text);
            vertical-align: middle;
        }

        .td-num {
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            color: var(--muted);
            font-size: 12px;
        }

        .td-date {
            font-weight: 500;
        }

        .td-days {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            color: var(--navy);
        }

        /* ── STATUS BADGES ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.approved {
            background: #e6faf5;
            color: #1a9e77;
        }

        .status-badge.pending {
            background: #fff8e6;
            color: #d48806;
        }

        .status-badge.rejected {
            background: #fff0f0;
            color: #e03131;
        }

        .status-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-badge.approved .status-dot { background: #1a9e77; }
        .status-badge.pending  .status-dot { background: #d48806; }
        .status-badge.rejected .status-dot { background: #e03131; }

        /* ── EMPTY STATE ── */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state .empty-icon {
            width: 72px; height: 72px;
            background: var(--surface);
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin: 0 auto 16px;
            font-size: 28px;
            color: var(--muted);
        }

        .empty-state h6 {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--muted);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .page-content { padding: 24px 20px; }
        }
    </style>
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