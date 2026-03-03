<?php
// Start output buffering to prevent any output before JSON
ob_start();

// Set error handling before anything else
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

try {
    // Validasi request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Request method tidak valid');
    }

    // Include connection
    require_once '../config/connection.php';
    $conn = koneksi_db();

    if (!$conn) {
        throw new Exception('Koneksi database gagal - database tidak ditemukan atau kredensial salah');
    }

    // Validate connection is a mysqli object
    if (!($conn instanceof mysqli)) {
        throw new Exception('Koneksi database tidak valid - bukan object mysqli');
    }

    // Ambil ID dari POST
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Validasi ID
    if (empty($id) || $id <= 0) {
        throw new Exception('ID karyawan tidak valid');
    }

    // Cek apakah data karyawan ada
    $stmt = $conn->prepare("SELECT nama FROM users WHERE id = ?");
    if ($stmt === false) {
        throw new Exception('Prepare SELECT error: ' . ($conn->error ? $conn->error : 'Unknown error'));
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Data karyawan tidak ditemukan');
    }

    $row = $result->fetch_assoc();
    $nama = $row['nama'];
    $stmt->close();

    // Hapus data karyawan
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt === false) {
        throw new Exception('Prepare DELETE error: ' . ($conn->error ? $conn->error : 'Unknown error'));
    }

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        throw new Exception('Gagal menghapus data: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Data karyawan berhasil dihapus'
    ]);
    ob_end_flush();

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_detail' => $e->getMessage()
    ]);
}
?>