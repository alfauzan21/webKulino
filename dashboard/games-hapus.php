
<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

$id = (int)$_GET['id'];
$sql = mysqli_query($koneksi, "SELECT * FROM tb_games WHERE id=$id");
$data = mysqli_fetch_assoc($sql);

// Hapus file image
if ($data['image'] && file_exists("../assets/" . $data['image'])) {
    unlink("../assets/" . $data['image']);
}

// Hapus file video
if ($data['video_hover'] && file_exists("../assets/video/" . $data['video_hover'])) {
    unlink("../assets/video/" . $data['video_hover']);
}

// Hapus data dari database
mysqli_query($koneksi, "DELETE FROM tb_games WHERE id=$id");

header("Location: games-index.php");
exit;
?>