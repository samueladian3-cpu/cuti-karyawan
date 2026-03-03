<?php
// Start output buffering to prevent any output before JSON
ob_start();

/**
 * API Endpoint: api-get-karyawan.php
 * Mengambil data karyawan dari database
 */

header('Content-Type: application/json; charset=utf-8');

try {
    // Include connection - set error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // Don't display to user, log instead
    
    // Check if file exists - try both relative and absolute paths
    $conn_file = __DIR__ . '/../config/connection.php';
    if (!file_exists($conn_file)) {
        // Try parent directory approach
        $conn_file = dirname(dirname(__FILE__)) . '/config/connection.php';
        if (!file_exists($conn_file)) {
            throw new Exception('Connection file not found at: ' . __DIR__ . '/../config/connection.php');
        }
    }
    
    require_once $conn_file;
    
    // Check if function exists
    if (!function_exists('koneksi_db')) {
        throw new Exception('koneksi_db function not found in connection.php');
    }
    
    // Try to connect
    $conn = koneksi_db();
    
    if ($conn === false || !$conn) {
        throw new Exception('Database connection failed - mysqli_connect returned false. Check database credentials and if database exists.');
    }
    
    // Check if connection is a mysqli object
    if (!($conn instanceof mysqli)) {
        throw new Exception('Database connection invalid - not a mysqli object');
    }
    
    // Check if connection is active
    if (mysqli_connect_errno()) {
        throw new Exception('MySQL Connection Error: ' . mysqli_connect_error());
    }
    
    // Check table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if (!$check_table) {
        throw new Exception('Cannot check table - Query error: ' . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($check_table) === 0) {
        throw new Exception('Table users tidak ditemukan. Jalankan setup-db.php terlebih dahulu di http://localhost/cuti-karyawan/setup-db.php');
    }

    // Query data karyawan dari tabel users
    $query = "SELECT id, npk, nama, jabatan, tgl_masuk, tahun_hak, hak_cuti, berlaku_mulai, berlaku_sampai, tentative_sampai, sisa, hk, tipe, status 
              FROM users 
              ORDER BY nama ASC";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception('Query error: ' . mysqli_error($conn));
    }
    
    // Fetch all data
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    mysqli_free_result($result);
    mysqli_close($conn);
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Data berhasil diambil',
        'data' => $data,
        'total' => count($data),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    ob_end_flush();

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server Error: ' . $e->getMessage(),
        'data' => [],
        'error_detail' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
?>

