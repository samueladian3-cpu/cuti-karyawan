
<?php
require_once __DIR__ . '/connection.php';

function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function get_user_leaves($user_id) {

    $conn = koneksi_db();

    $stmt = mysqli_prepare($conn, "
        SELECT start_date, end_date, total_days, status
        FROM leaves
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $leaves = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $leaves[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $leaves;
}

function get_total_approved_leave($user_id) {

    $conn = koneksi_db();

    $stmt = mysqli_prepare($conn, "
        SELECT COALESCE(SUM(total_days),0) as total
        FROM leaves
        WHERE user_id = ?
        AND status = 'approved'
    ");

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $row['total'];
}

function get_sisa_cuti($user_id) {

    $user = get_user_by_id($user_id);
    $used = get_total_approved_leave($user_id);

    return $user['annual_leave_quota'] - $used;
}

function get_user_by_username_npk($username, $npk) {

    $conn = koneksi_db();

    $stmt = mysqli_prepare($conn, "
        SELECT id, username, nama_lengkap, annual_leave_quota
        FROM users
        WHERE username = ? AND npk = ?
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "ss", $username, $npk);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $user;
}

function get_user_by_id($id) {

    $conn = koneksi_db();

    $stmt = mysqli_prepare($conn, "
        SELECT id, username, nama_lengkap, annual_leave_quota
        FROM users
        WHERE id = ?
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $user;
}