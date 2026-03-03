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

// Ambil ID dari POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validasi ID
if (empty($id) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID karyawan tidak valid']);
    exit;
}

// Cek apakah data karyawan ada
$stmt = $conn->prepare("SELECT nama FROM users WHERE id = ?");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Data karyawan tidak ditemukan']);
    $stmt->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$nama = $row['nama'];
$stmt->close();

// Hapus data karyawan
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Data karyawan berhasil dihapus'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menghapus data: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
