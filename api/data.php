<?php
/**
 * API Endpoint: /api/data.php
 * Mengembalikan data karyawan dan cuti mereka
 */

// Set header JSON PERTAMA KALI sebelum error handling
header('Content-Type: application/json; charset=utf-8');

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Validasi method
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET'])) {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method ' . $_SERVER['REQUEST_METHOD'] . ' tidak diizinkan'
    ]);
    exit;
}

// Helper function for API response
function api_response($success = true, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

try {
    // Start session jika belum
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check autentikasi
    if (!isset($_SESSION['employee_id'])) {
        http_response_code(401);
        api_response(false, null, 'Unauthorized - Silakan login terlebih dahulu', 401);
        exit;
    }
    
    // Include functions
    require_once __DIR__ . '/../config/connection.php';
    require_once __DIR__ . '/../config/functions.php';
    
    $user_id = $_SESSION['employee_id'];
    $data_type = $_GET['type'] ?? 'dashboard';
    
    switch ($data_type) {
        case 'dashboard':
            // Return data dashboard karyawan
            $user = get_user_by_id($user_id);
            $leaves = get_user_leaves($user_id);
            $used = get_total_approved_leave($user_id);
            $remaining = get_sisa_cuti($user_id);
            
            if (!$user) {
                api_response(false, null, 'User tidak ditemukan', 404);
                exit;
            }
            
            $quota = $user['hak_cuti'] ?? 12;
            $pct_used = $quota > 0 ? round(($used / $quota) * 100) : 0;
            
            api_response(true, [
                'user' => [
                    'id' => $user['id'] ?? null,
                    'name' => $user['nama'] ?? null,
                    'npk' => $user['npk'] ?? null,
                    'jabatan' => $user['jabatan'] ?? null
                ],
                'leaves' => [
                    'approved' => count(array_filter($leaves, fn($l) => $l['status'] === 'approved')),
                    'pending' => count(array_filter($leaves, fn($l) => $l['status'] === 'pending')),
                    'rejected' => count(array_filter($leaves, fn($l) => $l['status'] === 'rejected')),
                    'total' => count($leaves)
                ],
                'quota' => [
                    'total' => $quota,
                    'used' => $used,
                    'remaining' => $remaining,
                    'percentage_used' => $pct_used
                ]
            ], 'Data dashboard berhasil diambil');
            break;
            
        case 'leaves':
            // Return riwayat cuti karyawan
            $leaves = get_user_leaves($user_id);
            api_response(true, [
                'total_leaves' => count($leaves),
                'leaves' => array_slice($leaves, 0, 10)
            ], 'Data cuti berhasil diambil');
            break;
            
        case 'user':
            // Return info user
            $user = get_user_by_id($user_id);
            
            if (!$user) {
                api_response(false, null, 'User tidak ditemukan', 404);
                exit;
            }
            
            api_response(true, $user, 'Data user berhasil diambil');
            break;
            
        case 'stats':
            // Return statistik cuti
            $leaves = get_user_leaves($user_id);
            $used = get_total_approved_leave($user_id);
            $conn = koneksi_db();
            
            if (!$conn) {
                api_response(false, null, 'Database connection error', 500);
                exit;
            }
            
            $query = "SELECT 
                        DATE_FORMAT(start_date, '%Y-%m') as bulan,
                        COUNT(*) as jumlah,
                        SUM(total_days) as total_hari,
                        status
                     FROM leaves
                     WHERE user_id = ?
                     GROUP BY DATE_FORMAT(start_date, '%Y-%m'), status
                     ORDER BY bulan DESC
                     LIMIT 12";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $stats = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $stats[] = $row;
            }
            
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            
            api_response(true, [
                'total_used' => $used,
                'monthly_stats' => $stats
            ], 'Statistik berhasil diambil');
            break;
            
        default:
            api_response(false, null, 'Parameter type tidak valid. Gunakan: dashboard, leaves, user, atau stats', 400);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>

