<?php
header('Content-Type: application/json');

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Request method tidak valid']);
    exit;
}

// Include connection
require_once '../config/connection.php';
$conn = koneksi_db();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal']);
    exit;
}

// Ambil data dari form
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
if (empty($npk)) {
    echo json_encode(['success' => false, 'message' => 'NPK harus diisi']);
    exit;
}

if (empty($nama)) {
    echo json_encode(['success' => false, 'message' => 'Nama lengkap harus diisi']);
    exit;
}

if (empty($jabatan)) {
    echo json_encode(['success' => false, 'message' => 'Jabatan harus diisi']);
    exit;
}

if (empty($tgl_masuk)) {
    echo json_encode(['success' => false, 'message' => 'Tanggal masuk harus diisi']);
    exit;
}

if (empty($berlaku_mulai)) {
    echo json_encode(['success' => false, 'message' => 'Berlaku mulai harus diisi']);
    exit;
}

if (empty($berlaku_sampai)) {
    echo json_encode(['success' => false, 'message' => 'Berlaku sampai harus diisi']);
    exit;
}

if (empty($tentative_sampai)) {
    echo json_encode(['success' => false, 'message' => 'Tentative sampai harus diisi']);
    exit;
}

if (empty($tipe)) {
    echo json_encode(['success' => false, 'message' => 'Tipe cuti harus dipilih']);
    exit;
}

if (empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Status harus dipilih']);
    exit;
}

// Cek NPK sudah ada
$stmt = $conn->prepare("SELECT id FROM users WHERE npk = ?");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $npk);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'NPK sudah terdaftar']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Validasi format tanggal
if (!strtotime($tgl_masuk)) {
    echo json_encode(['success' => false, 'message' => 'Format tanggal masuk tidak valid']);
    exit;
}

if (!strtotime($berlaku_mulai)) {
    echo json_encode(['success' => false, 'message' => 'Format berlaku mulai tidak valid']);
    exit;
}

if (!strtotime($berlaku_sampai)) {
    echo json_encode(['success' => false, 'message' => 'Format berlaku sampai tidak valid']);
    exit;
}

if (!strtotime($tentative_sampai)) {
    echo json_encode(['success' => false, 'message' => 'Format tentative sampai tidak valid']);
    exit;
}

// Validasi nilai numerik
if ($tahun_hak < 2000 || $tahun_hak > 2099) {
    echo json_encode(['success' => false, 'message' => 'Tahun hak harus antara 2000 - 2099']);
    exit;
}

if ($hak_cuti < 0 || $hak_cuti > 90) {
    echo json_encode(['success' => false, 'message' => 'Hak cuti harus antara 0 - 90 hari']);
    exit;
}

// Set nilai sisa awal sama dengan hak_cuti
$sisa = $hak_cuti;
$hk = 0;

// Insert ke database
$stmt = $conn->prepare(
    "INSERT INTO users (npk, nama, jabatan, tgl_masuk, tahun_hak, hak_cuti, 
     berlaku_mulai, berlaku_sampai, tentative_sampai, tipe, sisa, hk, status, keterangan) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement error: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssiissssiiss",
    $npk,
    $nama,
    $jabatan,
    $tgl_masuk,
    $tahun_hak,
    $hak_cuti,
    $berlaku_mulai,
    $berlaku_sampai,
    $tentative_sampai,
    $tipe,
    $sisa,
    $hk,
    $status,
    $keterangan
);

if ($stmt->execute()) {
    $last_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Data karyawan berhasil ditambahkan',
        'id' => $last_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menambahkan data: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
