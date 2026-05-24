<?php
$host = "127.0.0.1";  // Optimasi: Mencegah lag DNS lookup IPv6 di Windows/XAMPP
$user = "root";       // Username default XAMPP
$pass = "";           // Password default XAMPP (kosong)
$dbname = "kinema_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Optimasi: Mengunci set karakter untuk menghindari overhead negosiasi antar server
$conn->set_charset("utf8mb4");
?>