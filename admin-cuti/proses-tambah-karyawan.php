<?php
// Set error handling sebelum apapun
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php-error.log');

// Ensure no output before headers
ob_start();

// Set proper headers FIRST
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Set HTTP status code default
http_response_code(200);

try {
    // Validasi request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Request method tidak valid');
    }

    // Include connection dengan validasi
    $connection_file = dirname(__DIR__) . '/config/connection.php';
    if (!file_exists($connection_file)) {
        throw new Exception('File koneksi tidak ditemukan: ' . $connection_file);
    }

    require_once $connection_file;
    
    if (!function_exists('koneksi_db')) {
        throw new Exception('Fungsi koneksi_db tidak ditemukan');
    }

    $conn = koneksi_db();
    
    if (!$conn) {
        throw new Exception('Koneksi database gagal');
    }

    if (!($conn instanceof mysqli)) {
        throw new Exception('Koneksi database tidak valid');
    }

    // Ambil dan sanitasi data dari form
    $npk = isset($_POST['npk']) ? trim($_POST['npk']) : '';
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $jabatan = isset($_POST['jabatan']) ? trim($_POST['jabatan']) : '';
    $tgl_masuk = isset($_POST['tgl_masuk']) ? trim($_POST['tgl_masuk']) : '';
    $tahun_hak = isset($_POST['tahun_hak']) ? intval($_POST['tahun_hak']) : date('Y');
    $hak_cuti = isset($_POST['hak_cuti']) ? intval($_POST['hak_cuti']) : 12;
    $berlaku_mulai = isset($_POST['berlaku_mulai']) ? trim($_POST['berlaku_mulai']) : '';
    $berlaku_sampai = isset($_POST['berlaku_sampai']) ? trim($_POST['berlaku_sampai']) : '';
    $tentative_sampai = isset($_POST['tentative_sampai']) ? trim($_POST['tentative_sampai']) : '';
    $tipe = isset($_POST['tipe']) ? trim($_POST['tipe']) : 'normal';
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'aktif';
    $keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';

    // Validasi field wajib
    $required_fields = [
        'npk' => 'NPK harus diisi',
        'nama' => 'Nama lengkap harus diisi',
        'jabatan' => 'Jabatan harus diisi',
        'tgl_masuk' => 'Tanggal masuk harus diisi',
        'berlaku_mulai' => 'Berlaku mulai harus diisi',
        'berlaku_sampai' => 'Berlaku sampai harus diisi',
        'tentative_sampai' => 'Tentative sampai harus diisi',
        'tipe' => 'Tipe cuti harus dipilih',
        'status' => 'Status harus dipilih'
    ];

    foreach ($required_fields as $field => $message) {
        if (empty($$field)) {
            throw new Exception($message);
        }
    }

    // Cek NPK sudah ada di tabel users
    $stmt = $conn->prepare("SELECT id FROM users WHERE npk = ?");
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param("s", $npk);
    if (!$stmt->execute()) {
        throw new Exception('Database execute error: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        throw new Exception('NPK sudah terdaftar');
    }
    $stmt->close();

    // Validasi format tanggal
    $date_fields = [
        'tgl_masuk' => $tgl_masuk,
        'berlaku_mulai' => $berlaku_mulai,
        'berlaku_sampai' => $berlaku_sampai,
        'tentative_sampai' => $tentative_sampai
    ];

    foreach ($date_fields as $field => $value) {
        if (!strtotime($value)) {
            throw new Exception('Format ' . $field . ' tidak valid');
        }
    }

    // Validasi nilai numerik
    if ($tahun_hak < 2000 || $tahun_hak > 2099) {
        throw new Exception('Tahun hak harus antara 2000 - 2099');
    }
    if ($hak_cuti < 0 || $hak_cuti > 90) {
        throw new Exception('Hak cuti harus antara 0 - 90 hari');
    }

    // Set nilai default untuk kolom yang belum diisi di form
    $sisa = $hak_cuti; // Sisa cuti awal = hak cuti
    $hk = 0; // Hari kerja awal = 0
    $realisasi_awal = null; // Realisasi awal belum ada
    $realisasi_akhir = null; // Realisasi akhir belum ada
    $created_at = date('Y-m-d H:i:s');

    // Insert ke database - TABEL USERS (sesuai struktur yang baru)
    $stmt = $conn->prepare(
        "INSERT INTO users (npk, nama, jabatan, tgl_masuk, tahun_hak, hak_cuti, berlaku_mulai, berlaku_sampai, tentative_sampai, realisasi_awal, realisasi_akhir, hk, tipe, sisa, status, keterangan, created_at) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception('Insert prepare error: ' . $conn->error);
    }

    $stmt->bind_param(
        "ssssiisssssisisss",
        $npk,
        $nama,
        $jabatan,
        $tgl_masuk,
        $tahun_hak,
        $hak_cuti,
        $berlaku_mulai,
        $berlaku_sampai,
        $tentative_sampai,
        $realisasi_awal,
        $realisasi_akhir,
        $hk,
        $tipe,
        $sisa,
        $status,
        $keterangan,
        $created_at
    );

    if (!$stmt->execute()) {
        throw new Exception('Insert execute error: ' . $stmt->error);
    }

    $last_id = $conn->insert_id;
    $stmt->close();
    $conn->close();

    // Clear output buffer dan send response
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Data karyawan berhasil ditambahkan ke tabel users',
        'id' => $last_id
    ]);
    exit;

} catch (Exception $e) {
    // Clear buffer untuk menghindari output sebelumnya
    ob_end_clean();
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>