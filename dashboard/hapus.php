<?php include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}
$id = $_GET['id'];
$sql = mysqli_query($koneksi, "SELECT * FROM tb_berita WHERE id=$id");
$data = mysqli_fetch_assoc($sql);

// hapus file gambar
if ($data['gambar'] && file_exists("../uploads/" . $data['gambar'])) {
    unlink("../uploads/" . $data['gambar']);
}

mysqli_query($koneksi, "DELETE FROM tb_berita WHERE id=$id");

header("Location: index.php");
?>
