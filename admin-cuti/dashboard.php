<?php
require_once '../config/connection.php';
$conn = koneksi_db();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Cuti Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../assets/css/spinner.css">
    <style>
        .dashboard-content {
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid #4f8cff;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(79, 140, 255, 0.2);
        }

        .stat-card h5 {
            color: #666;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card h5 i {
            margin-right: 6px;
            opacity: 0.8;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #0b1e5b;
            margin-bottom: 8px;
        }

        .stat-card .description {
            font-size: 12px;
            color: #999;
        }

        .stat-card.active { border-left-color: #52d1b0; }
        .stat-card.pending { border-left-color: #ffc107; }
        .stat-card.rejected { border-left-color: #e03131; }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #4f8cff 0%, #52d1b0 100%);
            color: white;
            border: none;
            border-radius: 15px 15px 0 0;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-section h6 {
            color: #4f8cff;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4f8cff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            color: #0b1e5b;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e0e7ff;
            border-radius: 8px;
            padding: 10px 15px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #4f8cff;
            box-shadow: 0 0 0 0.2rem rgba(79, 140, 255, 0.15);
        }

        .required {
            color: #e03131;
        }

        .row-custom {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
    </style>
</head>
<body>
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="spinner-overlay hidden">
        <div class="spinner-container">
            <div class="spinner-border"></div>
            <span class="spinner-text">Loading...</span>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand Header -->
        <div class="sidebar-header">
            <div class="brand-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 3H6a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h2m8-18h2a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-2m-8-2V5m0 14v-2m0-10v2M8 5h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="brand-text">
                <h3>Cuti Admin</h3>
                <p>Management</p>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="section-label">Menu Utama</div>
                <ul class="nav-links">
                    <li>
                        <a href="#" class="nav-link active" data-page="dashboard">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8M21 3v5h-5M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16M3 21v-5h5"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="nav-link" data-page="data-karyawan">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M16 11a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM23 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            </svg>
                            <span>Data Karyawan</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- User Profile Section -->
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M12 14c-6 0-8 3-8 3v6h16v-6c0 0-2-3-8-3z"/>
                    </svg>
                </div>
                <div class="user-info">
                    <h6>Admin User</h6>
                    <p>Administrator</p>
                </div>
            </div>
            <a href="../admin-cuti/logout.php" class="btn-logout" onclick="showSpinner()">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
    </svg>
</a>
        </div>
    </aside>

    <!-- Toggle Button (Mobile) -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Dashboard Page -->
        <div id="dashboard-page" class="page-content active">
            <div class="dashboard-content">
                <h2>Dashboard Statistik</h2>
                <p class="subtitle">Selamat datang di sistem manajemen cuti karyawan</p>
                
                <!-- Statistik Cards -->
                <div class="stat-grid">
                    <!-- Total Karyawan -->
                    <div class="stat-card" style="border-left-color: #4f8cff;">
                        <h5><i class="fas fa-users"></i> Total Karyawan</h5>
                        <div class="number" id="stat-total-karyawan">0</div>
                        <div class="description">Terdaftar dalam sistem</div>
                    </div>
                    
                    <!-- Karyawan Aktif -->
                    <div class="stat-card active" style="border-left-color: #52d1b0;">
                        <h5><i class="fas fa-user-check"></i> Status Aktif</h5>
                        <div class="number" id="stat-karyawan-aktif">0</div>
                        <div class="description">Karyawan dengan status aktif</div>
                    </div>
                    
                    <!-- Total Hak Cuti -->
                    <div class="stat-card" style="border-left-color: #ffc107;">
                        <h5><i class="fas fa-calendar-alt"></i> Total Hak Cuti</h5>
                        <div class="number" id="stat-total-hak">0</div>
                        <div class="description">Hari kerja</div>
                    </div>
                    
                    <!-- Cuti Terpakai -->
                    <div class="stat-card rejected" style="border-left-color: #e03131;">
                        <h5><i class="fas fa-calendar-times"></i> Cuti Terpakai</h5>
                        <div class="number" id="stat-cuti-terpakai">0</div>
                        <div class="description">Hari kerja (<span id="stat-persentase">0%</span>)</div>
                    </div>
                    
                    <!-- Sisa Cuti -->
                    <div class="stat-card" style="border-left-color: #52d1b0;">
                        <h5><i class="fas fa-calendar-check"></i> Sisa Cuti</h5>
                        <div class="number" id="stat-sisa-cuti">0</div>
                        <div class="description">Hari kerja tersisa</div>
                    </div>
                    
                    <!-- Rata-rata Sisa -->
                    <div class="stat-card" style="border-left-color: #4f8cff;">
                        <h5><i class="fas fa-chart-line"></i> Rata-rata Sisa</h5>
                        <div class="number" id="stat-avg-sisa">0</div>
                        <div class="description">Hari per karyawan</div>
                    </div>
                </div>
                
                <!-- Breakdown by Status -->
                <div style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h4 style="color: #0b1e5b; margin-bottom: 20px; font-weight: 700;"><i class="fas fa-chart-bar" style="margin-right: 8px;"></i> Breakdown Status Karyawan</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <div style="background: #e8f8f0; padding: 16px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 700; color: #52d1b0;" id="breakdown-aktif">0</div>
                            <div style="color: #666; font-size: 14px; margin-top: 4px;">Aktif</div>
                        </div>
                        <div style="background: #fff4e8; padding: 16px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 700; color: #ff6d00;" id="breakdown-hangus">0</div>
                            <div style="color: #666; font-size: 14px; margin-top: 4px;">Hangus</div>
                        </div>
                        <div style="background: #e8f0ff; padding: 16px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 700; color: #4f8cff;" id="breakdown-selesai">0</div>
                            <div style="color: #666; font-size: 14px; margin-top: 4px;">Selesai</div>
                        </div>
                    </div>
                </div>
                
                <!-- Breakdown by Tipe Cuti -->
                <div style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h4 style="color: #0b1e5b; margin-bottom: 20px; font-weight: 700;"><i class="fas fa-clipboard-list" style="margin-right: 8px;"></i> Breakdown Tipe Cuti</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <div style="background: #e8f0ff; padding: 16px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 700; color: #4f8cff;" id="breakdown-normal">0</div>
                            <div style="color: #666; font-size: 14px; margin-top: 4px;">Normal</div>
                        </div>
                        <div style="background: #fff4e8; padding: 16px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 700; color: #ff6d00;" id="breakdown-tentative">0</div>
                            <div style="color: #666; font-size: 14px; margin-top: 4px;">Tentative</div>
                        </div>
                        <div style="background: #ffe8e8; padding: 16px; border-radius: 8px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 700; color: #e03131;" id="breakdown-pinjam">0</div>
                            <div style="color: #666; font-size: 14px; margin-top: 4px;">Pinjam</div>
                        </div>
                    </div>
                </div>
                
                <!-- Karyawan Terbaru -->
                <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h4 style="color: #0b1e5b; margin-bottom: 20px; font-weight: 700;"><i class="fas fa-users" style="margin-right: 8px;"></i> 5 Karyawan Terbaru</h4>
                    <div id="karyawan-terbaru-list">
                        <div style="padding: 20px; text-align: center; color: #999;">Loading...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Karyawan Page -->
        <div id="data-karyawan-page" class="page-content">
            <div class="dashboard-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div>
                        <h2>Data Karyawan</h2>
                        <p class="subtitle">Kelola data dan jadwal cuti karyawan</p>
                    </div>
                    <button class="btn-add-karyawan" id="btnAddKaryawan" style="padding: 10px 20px; background: #4f8cff; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">+ Tambah Karyawan</button>
                </div>

                <!-- Tabel Karyawan -->
                <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 1200px;">
                        <thead>
                            <tr style="background: #f8faff; border-bottom: 2px solid #e0e7ff;">
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">No</th>
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">NPK</th>
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Nama</th>
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Jabatan</th>
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Tgl Masuk</th>
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Tahun Hak</th>
                                <th style="padding: 16px; text-align: center; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Hak Cuti</th>
                                <th style="padding: 16px; text-align: center; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Terpakai</th>
                                <th style="padding: 16px; text-align: center; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Sisa</th>
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Tipe</th>
                                <th style="padding: 16px; text-align: left; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Status</th>
                                <th style="padding: 16px; text-align: center; color: #0b1e5b; font-weight: 600; font-size: 13px; white-space: nowrap;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableKaryawan">
                            <!-- Data akan dimuat dari database -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Karyawan -->
    <div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Tambah Data Karyawan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form id="formTambahKaryawan">
                        <!-- Data Pribadi -->
                        <div class="form-section">
                            <h6><i class="fas fa-id-card"></i> Data Pribadi</h6>
                            <div class="row-custom">
                                <div class="form-group">
                                    <label class="form-label">NPK <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="npk" placeholder="Contoh: EMP001" required>
                                    <small class="text-muted">Nomor Pegawai Korporat</small>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="nama" placeholder="Nama lengkap karyawan" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jabatan <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="jabatan" placeholder="Contoh: Senior Developer" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Tanggal Masuk <span class="required">*</span></label>
                                    <input type="date" class="form-control" name="tgl_masuk" required>
                                </div>
                            </div>
                        </div>

                        <!-- Hak Cuti -->
                        <div class="form-section">
                            <h6><i class="fas fa-calendar-days"></i> Hak Cuti</h6>
                            <div class="row-custom">
                                <div class="form-group">
                                    <label class="form-label">Tahun Hak <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="tahun_hak" min="2000" max="2099" value="2026" required>
                                    <small class="text-muted">Contoh: 2026</small>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jumlah Hak Cuti <span class="required">*</span></label>
                                    <input type="number" class="form-control" name="hak_cuti" min="0" max="90" value="12" required>
                                    <small class="text-muted">Dalam hari kerja</small>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Berlaku Mulai <span class="required">*</span></label>
                                    <input type="date" class="form-control" name="berlaku_mulai" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Berlaku Sampai <span class="required">*</span></label>
                                    <input type="date" class="form-control" name="berlaku_sampai" required>
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label class="form-label">Tentative Sampai <span class="required">*</span></label>
                                    <input type="date" class="form-control" name="tentative_sampai" required>
                                    <small class="text-muted">Batas akhir cuti tentative</small>
                                </div>
                            </div>
                        </div>

                        <!-- Tipe & Status -->
                        <div class="form-section">
                            <h6><i class="fas fa-tag"></i> Tipe & Status</h6>
                            <div class="row-custom">
                                <div class="form-group">
                                    <label class="form-label">Tipe Cuti <span class="required">*</span></label>
                                    <select class="form-control" name="tipe" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="normal" selected>Normal</option>
                                        <option value="tentative">Tentative</option>
                                        <option value="pinjam">Pinjam</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Status <span class="required">*</span></label>
                                    <select class="form-control" name="status" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="aktif" selected>Aktif</option>
                                        <option value="hangus">Hangus</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-section">
                            <h6><i class="fas fa-note-sticky"></i> Keterangan</h6>
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="3" placeholder="Tambahkan catatan atau keterangan..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer" style="padding: 20px; border-top: 1px solid #e0e7ff;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" form="formTambahKaryawan" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Karyawan -->
    <div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header" style="background: linear-gradient(135deg, #e03131 0%, #c41e3a 100%);">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Konfirmasi Penghapusan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div style="text-align: center; padding: 20px 0;">
                        <i class="fas fa-trash-alt" style="font-size: 48px; color: #e03131; margin-bottom: 15px;"></i>
                        <h6 style="color: #0b1e5b; margin-top: 15px; margin-bottom: 10px; font-size: 16px; font-weight: 600;">Hapus Data Karyawan</h6>
                        <p style="color: #666; margin-bottom: 10px;">Apakah Anda yakin ingin menghapus data karyawan:</p>
                        <p style="color: #0b1e5b; font-weight: 600; font-size: 16px; margin-bottom: 20px;" id="deleteKaryawanNama">-</p>
                        <p style="color: #e03131; font-size: 13px;"><i class="fas fa-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan. Semua data terkait akan dihapus dari sistem.</p>
                    </div>
                    <input type="hidden" id="deleteKaryawanId" value="">
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer" style="padding: 20px; border-top: 1px solid #e0e7ff;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="btnKonfirmasiHapus">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/loading-spinner.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></script>
    <script>
        // ── INISIALISASI SPINNER ──
        function showSpinner() {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) {
                spinner.classList.remove('hidden');
            }
        }

        function hideSpinner() {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) {
                setTimeout(() => {
                    spinner.classList.add('hidden');
                }, 500); // Delay 500ms untuk smooth animation
            }
        }

        // ── SEMBUNYIKAN SPINNER SAAT PAGE LOAD ──
        document.addEventListener('DOMContentLoaded', function() {
            hideSpinner();
        });

        // ── TAMPILKAN SPINNER SAAT PAGE TIDAK RESPONSIVE ──
        window.addEventListener('beforeunload', function() {
            showSpinner();
        });

        // ── SIDEBAR NAVIGATION ──
        const navLinks = document.querySelectorAll('.nav-link');
        const pageContents = document.querySelectorAll('.page-content');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                showSpinner(); // TAMPILKAN SPINNER

                const pageName = link.getAttribute('data-page');

                // Simulasi loading time (hapus jika menggunakan AJAX real)
                setTimeout(() => {
                    // Update active nav link
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');

                    // Update active page
                    pageContents.forEach(page => page.classList.remove('active'));
                    document.getElementById(pageName + '-page').classList.add('active');

                    hideSpinner(); // SEMBUNYIKAN SPINNER

                    // Close sidebar on mobile
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('active');
                    }
                }, 600); // Delay sesuai animasi spinner
            });
        });

        // ── BUTTON ACTIONS ──
        // Edit Button
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                showSpinner();
                
                // Simulasi loading untuk edit
                setTimeout(() => {
                    // Redirect ke form edit
                    window.location.href = 'form-karyawan.php?mode=edit&id=1';
                }, 600);
            });
        });

        // Jadwal Button
        document.querySelectorAll('.btn-jadwal').forEach(btn => {
            btn.addEventListener('click', function() {
                const namaKaryawan = this.closest('tr').cells[2].textContent;
                alert('Fitur jadwal cuti untuk: ' + namaKaryawan + '\n(Akan diimplementasikan)');
            });
        });

        // Add Karyawan Button - Buka Modal
        document.getElementById('btnAddKaryawan').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalTambahKaryawan'));
            modal.show();
        });

        // Handle Form Submission
        document.getElementById('formTambahKaryawan').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = document.querySelector('button[form="formTambahKaryawan"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            }
            
            fetch('proses-tambah-karyawan.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const text = await response.text();
                
                console.log('Status:', response.status);
                console.log('Raw Response:', text);
                
                if (!text || text.trim() === '') {
                    throw new Error('Server mengirimkan respons kosong. Cek logs/php-error.log');
                }
                
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON:', text);
                    throw new Error('Respons tidak valid JSON: ' + text.substring(0, 200));
                }
            })
            .then(data => {
                console.log('Response:', data);
                if(data.success) {
                    showSpinner();
                    setTimeout(() => {
                        alert('✓ Data karyawan berhasil ditambahkan!');
                        location.reload();
                    }, 300);
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Data';
                    }
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                alert('Terjadi kesalahan:\n' + error.message);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Data';
                }
            });
        });

        // Delete Button dengan Modal Konfirmasi
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                
                // Set data ke modal konfirmasi
                document.getElementById('deleteKaryawanNama').textContent = nama;
                document.getElementById('deleteKaryawanId').value = id;
                
                // Buka modal
                const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus'));
                modal.show();
            });
        });

        // Handle Konfirmasi Hapus
        document.getElementById('btnKonfirmasiHapus').addEventListener('click', function() {
            const id = document.getElementById('deleteKaryawanId').value;
            const btn = this;
            
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
            }
            
            fetch('proses-hapus-karyawan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Response is not JSON - this is an error
                    return response.text().then(text => {
                        throw new Error('Server returned non-JSON response: ' + (text.substring(0, 100) || 'empty response'));
                    });
                }
            })
            .then(data => {
                console.log('Response:', data);
                if(data.success) {
                    showSpinner();
                    setTimeout(() => {
                        alert('Data karyawan berhasil dihapus!');
                        location.reload();
                    }, 300);
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Ya, Hapus Data';
                    }
                }
            })
            .catch(error => {
                console.error('❌ Delete Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash-alt"></i> Ya, Hapus Data';
                }
            });
        });

        // Sidebar Toggle
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // ── LOAD STATISTIK DASHBOARD ──
        function loadStatistik() {
            console.log('📊 Loading statistik...');
            
            fetch('api-get-statistik.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data) {
                        const data = result.data;
                        
                        // Update stat cards
                        document.getElementById('stat-total-karyawan').textContent = data.total_karyawan;
                        document.getElementById('stat-karyawan-aktif').textContent = data.karyawan_aktif;
                        document.getElementById('stat-total-hak').textContent = data.total_hak_cuti;
                        document.getElementById('stat-cuti-terpakai').textContent = data.total_cuti_terpakai;
                        document.getElementById('stat-sisa-cuti').textContent = data.total_sisa_cuti;
                        document.getElementById('stat-avg-sisa').textContent = data.avg_sisa_cuti;
                        document.getElementById('stat-persentase').textContent = data.persentase_terpakai + '%';
                        
                        // Update breakdown status
                        document.getElementById('breakdown-aktif').textContent = data.karyawan_aktif;
                        document.getElementById('breakdown-hangus').textContent = data.karyawan_hangus;
                        document.getElementById('breakdown-selesai').textContent = data.karyawan_selesai;
                        
                        // Update breakdown tipe
                        document.getElementById('breakdown-normal').textContent = data.cuti_normal;
                        document.getElementById('breakdown-tentative').textContent = data.cuti_tentative;
                        document.getElementById('breakdown-pinjam').textContent = data.cuti_pinjam;
                        
                        // Update karyawan terbaru
                        const terbaruList = document.getElementById('karyawan-terbaru-list');
                        if (data.karyawan_terbaru.length > 0) {
                            terbaruList.innerHTML = data.karyawan_terbaru.map(k => `
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #e0e7ff;">
                                    <div>
                                        <div style="font-weight: 600; color: #0b1e5b;">${k.nama}</div>
                                        <div style="font-size: 13px; color: #666;">${k.npk} • ${k.jabatan}</div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 20px; font-weight: 700; color: #4f8cff;">${k.sisa}/${k.hak_cuti}</div>
                                        <div style="font-size: 12px; color: #999;">Sisa Cuti</div>
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            terbaruList.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Belum ada data</div>';
                        }
                        
                        console.log('✅ Statistik loaded successfully');
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading statistik:', error);
                });
        }

        // ── LOAD DATA KARYAWAN ──
        function loadDataKaryawan() {
            const tbody = document.getElementById('tableKaryawan');
            
            if (!tbody) return; // Keluar jika element tidak ada
            
            console.log('📥 Loading data karyawan...');
            
            fetch('api-get-karyawan.php')
                .then(response => {
                    console.log('📡 Response status:', response.status, response.statusText);
                    
                    if (!response.ok) {
                        return response.text().then(text => {
                            // Try to parse as JSON for error messages
                            try {
                                const data = JSON.parse(text);
                                throw new Error(`HTTP ${response.status}: ${data.message || response.statusText}`);
                            } catch (e) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Response data:', data);
                    
                    if (!data.success) {
                        const errorMsg = data.message || 'Unknown error';
                        console.error('❌ API Error:', errorMsg);
                        tbody.innerHTML = `<tr><td colspan="12" align="center" style="padding: 40px; color: #e03131;">
                            <strong><i class="fas fa-exclamation-circle"></i> Error:</strong> ${errorMsg}
                            ${data.error_detail ? '<br><small>' + data.error_detail + '</small>' : ''}
                            <br><small><a href="../debug.php" target="_blank">Buka debug.php untuk informasi lebih lanjut</a></small>
                        </td></tr>`;
                        return;
                    }
                    
                    if (data.data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="12" align="center" style="padding: 40px; color: #999;"><i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; display: block; opacity: 0.3;"></i>Belum ada data karyawan</td></tr>`;
                        console.log('ℹ️ No data available');
                        return;
                    }
                    
                    console.log(`✅ Loaded ${data.data.length} records`);
                    
                    // Generate table rows
                    tbody.innerHTML = data.data.map((karyawan, index) => `
                        <tr style="border-bottom: 1px solid #e0e7ff;">
                            <td style="padding: 16px; color: #666;">${index + 1}</td>
                            <td style="padding: 16px; color: #0b1e5b; font-weight: 600;">${karyawan.npk}</td>
                            <td style="padding: 16px; color: #0b1e5b;">${karyawan.nama}</td>
                            <td style="padding: 16px; color: #666;">${karyawan.jabatan || '-'}</td>
                            <td style="padding: 16px; color: #666; white-space: nowrap;">${karyawan.tgl_masuk}</td>
                            <td style="padding: 16px; color: #666;">${karyawan.tahun_hak}</td>
                            <td style="padding: 16px; text-align: center; color: #0b1e5b; font-weight: 600;">${karyawan.hak_cuti}</td>
                            <td style="padding: 16px; text-align: center; color: #666;">${karyawan.hk || 0}</td>
                            <td style="padding: 16px; text-align: center; color: #4f8cff; font-weight: 600;">${karyawan.sisa || karyawan.hak_cuti}</td>
                            <td style="padding: 16px;"><span style="background: #e8f0ff; color: #4f8cff; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">${karyawan.tipe}</span></td>
                            <td style="padding: 16px;"><span style="background: ${karyawan.status === 'aktif' ? '#e8f8f0' : '#fff4e8'}; color: ${karyawan.status === 'aktif' ? '#52d1b0' : '#ff6d00'}; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">${karyawan.status}</span></td>
                            <td style="padding: 16px; text-align: center;">
                                <button class="btn-delete" data-id="${karyawan.id}" data-nama="${karyawan.nama}" style="background: #e03131; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    `).join('');
                    
                    // Re-attach delete event listeners
                    attachDeleteHandlers();
                })
                .catch(error => {
                    console.error('❌ Full Error:', error);
                    tbody.innerHTML = `<tr><td colspan="12" align="center" style="padding: 40px; color: #e03131;">
                        <strong><i class="fas fa-exclamation-triangle"></i> Error mengambil data:</strong> ${error.message}
                        <br><small><a href="../debug.php" target="_blank">Buka debug.php untuk informasi lebih lanjut</a></small>
                    </td></tr>`;
                });
        }
        
        // Attach delete handlers
        function attachDeleteHandlers() {
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    
                    document.getElementById('deleteKaryawanNama').textContent = nama;
                    document.getElementById('deleteKaryawanId').value = id;
                    
                    const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus'));
                    modal.show();
                });
            });
        }
        
        // Load data saat page content di-toggle
        const dataKaryawanLink = document.querySelector('[data-page="data-karyawan"]');
        if (dataKaryawanLink) {
            dataKaryawanLink.addEventListener('click', () => {
                setTimeout(loadDataKaryawan, 600); // Tunggu animasi selesai
            });
        }
        
        // Load statistik saat klik dashboard link
        const dashboardLink = document.querySelector('[data-page="dashboard"]');
        if (dashboardLink) {
            dashboardLink.addEventListener('click', () => {
                setTimeout(loadStatistik, 600); // Tunggu animasi selesai
            });
        }
        
        // Load data jika ada di URL hash
        if (window.location.hash === '#data-karyawan' || document.getElementById('data-karyawan-page')?.classList.contains('active')) {
            loadDataKaryawan();
        } else {
            // Load statistik saat pertama kali buka dashboard
            loadStatistik();
        }
    </script>
</body>
</html>