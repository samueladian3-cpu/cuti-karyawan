<?php

function koneksi_db() {

    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'cuti_karyawan';

    mysqli_report(MYSQLI_REPORT_OFF);

    $conn = mysqli_connect($host, $user, $pass, $db);

    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    mysqli_set_charset($conn, "utf8mb4");

    return $conn;
}