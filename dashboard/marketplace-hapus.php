<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid ID!";
    header("Location: marketplace-index.php");
    exit;
}

$id = (int) $_GET['id'];

// Get product data
$sql = mysqli_query($koneksi, "SELECT * FROM tb_marketplace WHERE id=$id");
$data = mysqli_fetch_assoc($sql);

if (!$data) {
    $_SESSION['error_message'] = "Product not found!";
    header("Location: marketplace-index.php");
    exit;
}

// Delete image file
if ($data['image'] && file_exists("../uploads/marketplace/" . $data['image'])) {
    unlink("../uploads/marketplace/" . $data['image']);
}

// Delete from database
$delete = mysqli_query($koneksi, "DELETE FROM tb_marketplace WHERE id=$id");

if ($delete) {
    $_SESSION['success_message'] = "Product deleted successfully!";
} else {
    $_SESSION['error_message'] = "Failed to delete product!";
}

header("Location: marketplace-index.php");
exit;
?>