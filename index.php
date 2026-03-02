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
$quota     = $user['annual_leave_quota'];
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
    <style>
        :root {
            --navy:    #0b1e5b;
            --blue:    #4f8cff;
            --teal:    #52d1b0;
            --amber:   #f5a623;
            --red:     #e03131;
            --surface: #f2f5fb;
            --card:    #ffffff;
            --text:    #1a2340;
            --muted:   #8a94b0;
            --border:  #e4e9f5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--text);
        }

        /* ── PAGE ── */
        .page-content {
            padding: 32px 40px;
            animation: fadeUp .45s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── HERO BANNER ── */
        .hero-banner {
            background: linear-gradient(135deg, var(--navy) 0%, #1a3a9f 60%, #1a6fa8 100%);
            border-radius: 20px;
            padding: 32px 36px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(82,209,176,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            bottom: -70px; left: 30%;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(79,140,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .hero-left { position: relative; z-index: 1; }

        .hero-greeting {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            font-weight: 500;
            letter-spacing: 0.3px;
            margin-bottom: 6px;
        }

        .hero-name {
            font-family: 'Sora', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .hero-name span {
            background: linear-gradient(90deg, #52d1b0, #a0d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            font-size: 13px;
            color: rgba(255,255,255,0.5);
        }

        .hero-right {
            position: relative;
            z-index: 1;
            text-align: right;
            flex-shrink: 0;
        }

        .hero-date {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 4px;
        }

        .hero-apply-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, var(--teal), #3ab99c);
            color: #fff;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(82,209,176,0.35);
            transition: .2s;
            margin-top: 10px;
        }

        .hero-apply-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(82,209,176,0.45);
            color: #fff;
        }

        /* ── QUOTA PROGRESS CARD ── */
        .quota-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 24px 26px;
            margin-bottom: 24px;
        }

        .quota-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .quota-top h6 {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
        }

        .quota-pct {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--blue);
        }

        .progress-track {
            height: 10px;
            background: var(--surface);
            border-radius: 99px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, var(--blue), var(--teal));
            transition: width .8s cubic-bezier(.4,0,.2,1);
        }

        .quota-blocks {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .quota-block {
            background: var(--surface);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }

        .quota-block .qb-num {
            font-family: 'Sora', sans-serif;
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }

        .quota-block .qb-lbl {
            font-size: 12px;
            color: var(--muted);
            margin-top: 5px;
        }

        .quota-block.total .qb-num  { color: var(--navy); }
        .quota-block.used  .qb-num  { color: var(--amber); }
        .quota-block.sisa  .qb-num  { color: #1a9e77; }

        /* ── 2-COL GRID ── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        /* ── MINI STAT CARDS ── */
        .mini-stat {
            background: var(--card);
            border-radius: 14px;
            border: 1px solid var(--border);
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: .2s;
        }

        .mini-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0,0,0,0.07);
        }

        .ms-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .ms-icon.approved { background: #e6faf5; color: #1a9e77; }
        .ms-icon.pending  { background: #fff8e6; color: #d48806; }
        .ms-icon.rejected { background: #fff0f0; color: var(--red); }
        .ms-icon.total    { background: #eef2ff; color: var(--blue); }

        .ms-info .num {
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1;
        }

        .ms-info .lbl {
            font-size: 12px;
            color: var(--muted);
            margin-top: 3px;
        }

        /* ── TABLE CARD ── */
        .table-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .tc-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tc-header .tc-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--blue), var(--teal));
            display: grid;
            place-items: center;
            color: white;
            font-size: 13px;
        }

        .tc-header span {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
        }

        .tc-header a {
            margin-left: auto;
            font-size: 12px;
            color: var(--blue);
            text-decoration: none;
            font-weight: 600;
        }

        .tc-header a:hover { text-decoration: underline; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr { background: var(--surface); }

        thead th {
            padding: 11px 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover      { background: #f8faff; }

        tbody td {
            padding: 13px 20px;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .td-date { font-weight: 500; }

        .td-days {
            font-family: 'Sora', sans-serif;
            font-weight: 700;
            color: var(--navy);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .status-badge .dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-badge.approved { background: #e6faf5; color: #1a9e77; }
        .status-badge.approved .dot { background: #1a9e77; }
        .status-badge.pending  { background: #fff8e6; color: #d48806; }
        .status-badge.pending  .dot { background: #d48806; }
        .status-badge.rejected { background: #fff0f0; color: var(--red); }
        .status-badge.rejected .dot { background: var(--red); }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
        }

        .empty-state .ei {
            width: 64px; height: 64px;
            background: var(--surface);
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            font-size: 24px;
            color: var(--muted);
        }

        .empty-state h6 {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 5px;
        }

        .empty-state p { font-size: 13px; color: var(--muted); }

        @media (max-width: 900px) {
            .two-col { grid-template-columns: 1fr; }
            .page-content { padding: 22px 18px; }
            .quota-blocks { grid-template-columns: repeat(3, 1fr); }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'assets/sidebar.php'; ?>
        <div class="col-md-9 col-lg-10">

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
                            <?= esc($user['nama_lengkap']) ?> <span>👋</span>
                        </h2>
                        <p class="hero-sub">Pantau sisa cuti dan riwayat pengajuanmu di sini.</p>
                    </div>
                    <div class="hero-right">
                        <p class="hero-date"><i class="fas fa-calendar me-1"></i><?= date('d F Y') ?></p>
                        <a href="ajukan.php" class="hero-apply-btn">
                            <i class="fas fa-paper-plane"></i> Ajukan Cuti Sekarang
                        </a>
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

                <!-- STATUS MINI STATS -->
                <div class="two-col" style="grid-template-columns: repeat(3,1fr); margin-bottom:24px">
                    <div class="mini-stat">
                        <div class="ms-icon total"><i class="fas fa-layer-group"></i></div>
                        <div class="ms-info">
                            <div class="num"><?= count($leaves) ?></div>
                            <div class="lbl">Total Pengajuan</div>
                        </div>
                    </div>
                    <div class="mini-stat">
                        <div class="ms-icon approved"><i class="fas fa-check-circle"></i></div>
                        <div class="ms-info">
                            <div class="num"><?= $cnt_approved ?></div>
                            <div class="lbl">Disetujui</div>
                        </div>
                    </div>
                    <div class="mini-stat">
                        <div class="ms-icon pending"><i class="fas fa-clock"></i></div>
                        <div class="ms-info">
                            <div class="num"><?= $cnt_pending ?></div>
                            <div class="lbl">Menunggu</div>
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



</body>
</html>