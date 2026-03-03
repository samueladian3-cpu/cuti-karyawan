<?php
/**
 * API Configuration
 * File konfigurasi untuk semua endpoint API
 */

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// Handle CORS (untuk request dari domain berbeda)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include koneksi database
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../config/functions.php';

// Fungsi helper untuk response
function api_response($success = true, $data = null, $message = '', $code = 200) {
    http_response_code($code);
    
    return json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

// Fungsi untuk check autentikasi
function check_auth() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (!isset($_SESSION['employee_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized - Silakan login terlebih dahulu',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    return $_SESSION['employee_id'];
}

// Fungsi untuk validasi request method
function validate_method($allowed_methods = ['GET']) {
    if (!in_array($_SERVER['REQUEST_METHOD'], $allowed_methods)) {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method ' . $_SERVER['REQUEST_METHOD'] . ' tidak diizinkan',
            'allowed' => $allowed_methods,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}

// Fungsi untuk get request data (GET parameters atau JSON body)
function get_request_data() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return $_GET;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }
    return [];
}
?>
