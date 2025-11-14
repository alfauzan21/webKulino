<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: ../login.php");
  exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_sponsor WHERE id=$id"));
if ($data && file_exists("../uploads/" . $data['gambar'])) {
  unlink("../uploads/" . $data['gambar']);
}
mysqli_query($koneksi, "DELETE FROM tb_sponsor WHERE id=$id");
header("Location: sponsor-index.php");
exit;
?>
