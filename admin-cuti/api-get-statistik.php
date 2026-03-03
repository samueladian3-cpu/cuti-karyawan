<?php
// Start output buffering
ob_start();

header('Content-Type: application/json; charset=utf-8');

try {
    // Include connection
    require_once '../config/connection.php';
    $conn = koneksi_db();
    
    if (!$conn || !($conn instanceof mysqli)) {
        throw new Exception('Database connection failed');
    }
    
    // Initialize statistik array
    $statistik = [
        'total_karyawan' => 0,
        'karyawan_aktif' => 0,
        'karyawan_hangus' => 0,
        'karyawan_selesai' => 0,
        'total_hak_cuti' => 0,
        'total_cuti_terpakai' => 0,
        'total_sisa_cuti' => 0,
        'avg_sisa_cuti' => 0,
        'cuti_normal' => 0,
        'cuti_tentative' => 0,
        'cuti_pinjam' => 0,
        'persentase_terpakai' => 0,
        'karyawan_terbaru' => []
    ];
    
    // Total karyawan
    $query = "SELECT COUNT(*) as total FROM users";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $statistik['total_karyawan'] = (int)$row['total'];
    }
    
    // Karyawan berdasarkan status
    $query = "SELECT status, COUNT(*) as jumlah FROM users GROUP BY status";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            switch ($row['status']) {
                case 'aktif':
                    $statistik['karyawan_aktif'] = (int)$row['jumlah'];
                    break;
                case 'hangus':
                    $statistik['karyawan_hangus'] = (int)$row['jumlah'];
                    break;
                case 'selesai':
                    $statistik['karyawan_selesai'] = (int)$row['jumlah'];
                    break;
            }
        }
    }
    
    // Total hak cuti, terpakai, dan sisa
    $query = "SELECT 
                SUM(hak_cuti) as total_hak, 
                SUM(hk) as total_terpakai, 
                SUM(sisa) as total_sisa,
                AVG(sisa) as avg_sisa
              FROM users";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $statistik['total_hak_cuti'] = (int)$row['total_hak'];
        $statistik['total_cuti_terpakai'] = (int)$row['total_terpakai'];
        $statistik['total_sisa_cuti'] = (int)$row['total_sisa'];
        $statistik['avg_sisa_cuti'] = round((float)$row['avg_sisa'], 1);
        
        // Hitung persentase terpakai
        if ($statistik['total_hak_cuti'] > 0) {
            $statistik['persentase_terpakai'] = round(
                ($statistik['total_cuti_terpakai'] / $statistik['total_hak_cuti']) * 100, 
                1
            );
        }
    }
    
    // Karyawan berdasarkan tipe cuti
    $query = "SELECT tipe, COUNT(*) as jumlah FROM users GROUP BY tipe";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            switch ($row['tipe']) {
                case 'normal':
                    $statistik['cuti_normal'] = (int)$row['jumlah'];
                    break;
                case 'tentative':
                    $statistik['cuti_tentative'] = (int)$row['jumlah'];
                    break;
                case 'pinjam':
                    $statistik['cuti_pinjam'] = (int)$row['jumlah'];
                    break;
            }
        }
    }
    
    // 5 Karyawan terbaru
    $query = "SELECT id, npk, nama, jabatan, hak_cuti, sisa, status, created_at 
              FROM users 
              ORDER BY created_at DESC 
              LIMIT 5";
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $statistik['karyawan_terbaru'][] = $row;
        }
    }
    
    mysqli_close($conn);
    
    // Return success response
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Statistik berhasil diambil',
        'data' => $statistik,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'data' => null
    ]);
}
?>
