<?php
$host = "localhost";
$user = "u433539037_KulinoGame1";
$pass = "admin.Hostinger01";
$db   = "u433539037_db_kulino1"; // ganti sesuai nama database

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
