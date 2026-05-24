<?php
$host = "localhost";
$user = "root";       // Username default XAMPP
$pass = "";           // Password default XAMPP (kosong)
$dbname = "kinema_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>