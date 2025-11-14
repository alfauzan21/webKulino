<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<link href="../includes/sidebar-admin.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="sidebar text-center">
    <img src="../assets/icon/kulino-logo-blue.png" alt="Kulino Logo">
    <h4>Kulino</h4>
    <a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="tambah.php"><i class="bi bi-plus-circle"></i> Tambah Berita</a>
    <a href="recap-index.php"><i class="bi bi-graph-up"></i> Recap Visitor</a>
    <a href="sponsor-index.php" class="<?= ($currentPage == 'sponsor-index.php') ? 'active' : '' ?>">
        <i class="bi bi-people"></i> Data Sponsor
    </a>
    <a href="sponsor-tambah.php" class="<?= ($currentPage == 'sponsor-tambah.php') ? 'active' : '' ?>">
        <i class="bi bi-plus-circle-dotted"></i> Tambah Sponsor
    </a>
    <a href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>