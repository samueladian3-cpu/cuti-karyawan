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
                <h2>Dashboard</h2>
                <p class="subtitle">Selamat datang di sistem manajemen cuti karyawan</p>

                <div class="stat-grid">
                    <div class="stat-card">
                        <h5>Total Karyawan</h5>
                        <div class="number">42</div>
                        <div class="description">Karyawan aktif</div>
                    </div>
                    <div class="stat-card active">
                        <h5>Cuti Disetujui</h5>
                        <div class="number">18</div>
                        <div class="description">Bulan ini</div>
                    </div>
                    <div class="stat-card pending">
                        <h5>Menunggu Persetujuan</h5>
                        <div class="number">7</div>
                        <div class="description">Pending requests</div>
                    </div>
                    <div class="stat-card rejected">
                        <h5>Ditolak</h5>
                        <div class="number">3</div>
                        <div class="description">Tahun ini</div>
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
                        <p style="color: #e03131; font-size: 13px;">⚠️ Tindakan ini tidak dapat dibatalkan. Semua data terkait akan dihapus dari sistem.</p>
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
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            
            fetch('proses-tambah-karyawan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showSpinner();
                    setTimeout(() => {
                        alert('Data karyawan berhasil ditambahkan!');
                        location.reload();
                    }, 300);
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Data';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Data';
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
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
            
            fetch('proses-hapus-karyawan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showSpinner();
                    setTimeout(() => {
                        alert('Data karyawan berhasil dihapus!');
                        location.reload();
                    }, 300);
                } else {
                    alert('Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash-alt"></i> Ya, Hapus Data';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt"></i> Ya, Hapus Data';
            });
        });

        // Sidebar Toggle
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    </script>
</body>
</html>