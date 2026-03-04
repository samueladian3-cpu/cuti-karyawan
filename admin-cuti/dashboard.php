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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-sidebar.css">
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
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 28px;
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
            padding: 24px;
            margin-bottom: 24px;
        }

        .form-section h6 {
            color: #4f8cff;
            font-weight: 700;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid #4f8cff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            color: #0b1e5b;
            margin-bottom: 10px;
            display: block;
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
            gap: 18px;
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

    <!-- Mobile Menu Toggle -->
    <button class="admin-mobile-menu-toggle" id="adminMobileMenuToggle" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">

            <!-- SIDEBAR -->
            <?php
            $active = 'dashboard';
            include '../assets/admin-sidebar.php';
            ?>

            <!-- MAIN CONTENT -->
            <div class="col-md-10">
                <main class="main-content">
        <!-- Dashboard Page -->
        <div id="dashboard-page" class="page-content active">
            <div class="dashboard-content">
                <h2>Dashboard Statistik</h2>
                <p class="subtitle">Selamat datang di sistem manajemen cuti karyawan</p>
                
                <!-- Statistik Cards -->
                <div class="stat-grid">
                    <!-- Total Karyawan -->
                    <div class="stat-card stat-card-blue">
                        <h5><i class="fas fa-users"></i> Total Karyawan</h5>
                        <div class="number" id="stat-total-karyawan">0</div>
                        <div class="description">Terdaftar dalam sistem</div>
                    </div>
                    
                    <!-- Karyawan Aktif -->
                    <div class="stat-card stat-card-teal">
                        <h5><i class="fas fa-user-check"></i> Status Aktif</h5>
                        <div class="number" id="stat-karyawan-aktif">0</div>
                        <div class="description">Karyawan dengan status aktif</div>
                    </div>
                    
                    <!-- Total Hak Cuti -->
                    <div class="stat-card stat-card-yellow">
                        <h5><i class="fas fa-calendar-alt"></i> Total Hak Cuti</h5>
                        <div class="number" id="stat-total-hak">0</div>
                        <div class="description">Hari kerja</div>
                    </div>
                    
                    <!-- Cuti Terpakai -->
                    <div class="stat-card stat-card-red">
                        <h5><i class="fas fa-calendar-times"></i> Cuti Terpakai</h5>
                        <div class="number" id="stat-cuti-terpakai">0</div>
                        <div class="description">Hari kerja (<span id="stat-persentase">0%</span>)</div>
                    </div>
                    
                    <!-- Sisa Cuti -->
                    <div class="stat-card stat-card-teal">
                        <h5><i class="fas fa-calendar-check"></i> Sisa Cuti</h5>
                        <div class="number" id="stat-sisa-cuti">0</div>
                        <div class="description">Hari kerja tersisa</div>
                    </div>
                    
                    <!-- Rata-rata Sisa -->
                    <div class="stat-card stat-card-blue">
                        <h5><i class="fas fa-chart-line"></i> Rata-rata Sisa</h5>
                        <div class="number" id="stat-avg-sisa">0</div>
                        <div class="description">Hari per karyawan</div>
                    </div>
                </div>
                
                <!-- Breakdown by Status -->
                <div class="breakdown-section">
                    <h4 class="section-title"><i class="fas fa-chart-bar"></i> Breakdown Status Karyawan</h4>
                    <div class="breakdown-grid">
                        <div class="breakdown-item breakdown-item-teal">
                            <div class="breakdown-number" id="breakdown-aktif">0</div>
                            <div class="breakdown-label">Aktif</div>
                        </div>
                        <div class="breakdown-item breakdown-item-orange">
                            <div class="breakdown-number" id="breakdown-hangus">0</div>
                            <div class="breakdown-label">Hangus</div>
                        </div>
                        <div class="breakdown-item breakdown-item-blue">
                            <div class="breakdown-number" id="breakdown-selesai">0</div>
                            <div class="breakdown-label">Selesai</div>
                        </div>
                    </div>
                </div>
                
                <!-- Breakdown by Tipe Cuti -->
                <div class="breakdown-section">
                    <h4 class="section-title"><i class="fas fa-clipboard-list"></i> Breakdown Tipe Cuti</h4>
                    <div class="breakdown-grid">
                        <div class="breakdown-item breakdown-item-blue">
                            <div class="breakdown-number" id="breakdown-normal">0</div>
                            <div class="breakdown-label">Normal</div>
                        </div>
                        <div class="breakdown-item breakdown-item-orange">
                            <div class="breakdown-number" id="breakdown-tentative">0</div>
                            <div class="breakdown-label">Tentative</div>
                        </div>
                        <div class="breakdown-item breakdown-item-red">
                            <div class="breakdown-number" id="breakdown-pinjam">0</div>
                            <div class="breakdown-label">Pinjam</div>
                        </div>
                    </div>
                </div>
                
                <!-- Karyawan Terbaru -->
                <div class="breakdown-section">
                    <h4 class="section-title"><i class="fas fa-users"></i> 5 Karyawan Terbaru</h4>
                    <div id="karyawan-terbaru-list">
                        <div class="loading-text">Loading...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Karyawan Page -->
        <div id="data-karyawan-page" class="page-content">
            <div class="dashboard-content">
                <div class="page-header">
                    <div>
                        <h2>Data Karyawan</h2>
                        <p class="subtitle">Kelola data dan jadwal cuti karyawan</p>
                    </div>
                    <button class="btn-add-karyawan" id="btnAddKaryawan">
                        <i class="fas fa-plus"></i> Tambah Karyawan
                    </button>
                </div>

                <!-- Tabel Karyawan -->
                <div class="table-container">
                    <table class="table-karyawan">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NPK</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Tgl Masuk</th>
                                <th>Tahun Hak</th>
                                <th class="text-center">Hak Cuti</th>
                                <th class="text-center">Terpakai</th>
                                <th class="text-center">Sisa</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
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
                                <div class="form-group form-group-full">
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
                <div class="modal-footer">
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
                <div class="modal-header modal-header-danger">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Konfirmasi Penghapusan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="delete-confirmation">
                        <i class="fas fa-trash-alt delete-icon"></i>
                        <h6 class="delete-title">Hapus Data Karyawan</h6>
                        <p class="delete-message">Apakah Anda yakin ingin menghapus data karyawan:</p>
                        <p class="delete-name" id="deleteKaryawanNama">-</p>
                        <p class="delete-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Tindakan ini tidak dapat dibatalkan. Semua data terkait akan dihapus dari sistem.
                        </p>
                    </div>
                    <input type="hidden" id="deleteKaryawanId" value="">
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
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
        const navLinks = document.querySelectorAll('.admin-nav-link');
        const pageContents = document.querySelectorAll('.page-content');

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

                    // Close admin sidebar on mobile
                    if (window.innerWidth <= 768) {
                        const adminSidebar = document.getElementById('adminSidebar');
                        const sidebarWrapper = adminSidebar ? adminSidebar.closest('.col-md-2') : null;
                        const adminSidebarOverlay = document.getElementById('adminSidebarOverlay');
                        if (adminSidebar && sidebarWrapper && adminSidebarOverlay) {
                            adminSidebar.classList.remove('active');
                            sidebarWrapper.classList.remove('active');
                            adminSidebarOverlay.classList.remove('active');
                            document.body.classList.remove('admin-sidebar-open');
                        }
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

        // Admin Mobile Menu Toggle
        const adminMobileMenuToggle = document.getElementById('adminMobileMenuToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        const sidebarWrapper = adminSidebar ? adminSidebar.closest('.col-md-2') : null;
        const adminSidebarOverlay = document.getElementById('adminSidebarOverlay');

        if (adminMobileMenuToggle && adminSidebar && sidebarWrapper && adminSidebarOverlay) {
            adminMobileMenuToggle.addEventListener('click', function() {
                adminSidebar.classList.toggle('active');
                sidebarWrapper.classList.toggle('active');
                adminSidebarOverlay.classList.toggle('active');
                document.body.classList.toggle('admin-sidebar-open');
            });

            adminSidebarOverlay.addEventListener('click', function() {
                adminSidebar.classList.remove('active');
                sidebarWrapper.classList.remove('active');
                adminSidebarOverlay.classList.remove('active');
                document.body.classList.remove('admin-sidebar-open');
            });
        }

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
                                <div class="karyawan-item">
                                    <div class="karyawan-info">
                                        <div class="karyawan-name">${k.nama}</div>
                                        <div class="karyawan-details">${k.npk} • ${k.jabatan}</div>
                                    </div>
                                    <div class="karyawan-cuti">
                                        <div class="cuti-number">${k.sisa}/${k.hak_cuti}</div>
                                        <div class="cuti-label">Sisa Cuti</div>
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            terbaruList.innerHTML = '<div class="loading-text">Belum ada data</div>';
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
                        tbody.innerHTML = `<tr><td colspan="12" class="table-error">
                            <strong><i class="fas fa-exclamation-circle"></i> Error:</strong> ${errorMsg}
                            ${data.error_detail ? '<br><small>' + data.error_detail + '</small>' : ''}
                            <br><small><a href="../debug.php" target="_blank">Buka debug.php untuk informasi lebih lanjut</a></small>
                        </td></tr>`;
                        return;
                    }
                    
                    if (data.data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="12" class="table-empty">
                            <i class="fas fa-inbox"></i>
                            <span>Belum ada data karyawan</span>
                        </td></tr>`;
                        console.log('ℹ️ No data available');
                        return;
                    }
                    
                    console.log(`✅ Loaded ${data.data.length} records`);
                    
                    // Generate table rows
                    tbody.innerHTML = data.data.map((karyawan, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="td-npk">${karyawan.npk}</td>
                            <td class="td-nama">${karyawan.nama}</td>
                            <td>${karyawan.jabatan || '-'}</td>
                            <td class="td-date">${karyawan.tgl_masuk}</td>
                            <td>${karyawan.tahun_hak}</td>
                            <td class="text-center td-primary">${karyawan.hak_cuti}</td>
                            <td class="text-center">${karyawan.hk || 0}</td>
                            <td class="text-center td-highlight">${karyawan.sisa || karyawan.hak_cuti}</td>
                            <td><span class="badge badge-tipe">${karyawan.tipe}</span></td>
                            <td><span class="badge badge-${karyawan.status}">${karyawan.status}</span></td>
                            <td class="text-center">
                                <button class="btn-delete" data-id="${karyawan.id}" data-nama="${karyawan.nama}">
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
                    tbody.innerHTML = `<tr><td colspan="12" class="table-error">
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

                </main><!-- /.main-content -->
            </div><!-- /.col-md-10 -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->

    <!-- Admin Sidebar Overlay for Mobile -->
    <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

</body>
</html>