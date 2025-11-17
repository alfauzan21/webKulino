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
    <h4>Kulino Admin</h4>
    
    <!-- Dashboard -->
    <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    
    <!-- Berita Section -->
    <div class="menu-divider">NEWS</div>
    <a href="tambah.php" class="<?= ($currentPage == 'tambah.php') ? 'active' : '' ?>">
        <i class="bi bi-plus-circle"></i> Tambah Berita
    </a>
    
    <!-- Games Section -->
    <div class="menu-divider">GAMES</div>
    <a href="games-index.php" class="<?= ($currentPage == 'games-index.php') ? 'active' : '' ?>">
        <i class="bi bi-joystick"></i> Data Games
    </a>
    <a href="games-tambah.php" class="<?= ($currentPage == 'games-tambah.php') ? 'active' : '' ?>">
        <i class="bi bi-plus-circle-dotted"></i> Tambah Game
    </a>
    
    <!-- Sponsor Section -->
    <div class="menu-divider">SPONSORS</div>
    <a href="sponsor-index.php" class="<?= ($currentPage == 'sponsor-index.php') ? 'active' : '' ?>">
        <i class="bi bi-people"></i> Data Sponsor
    </a>
    <a href="sponsor-tambah.php" class="<?= ($currentPage == 'sponsor-tambah.php') ? 'active' : '' ?>">
        <i class="bi bi-plus-circle-dotted"></i> Tambah Sponsor
    </a>
    
    <!-- Analytics -->
    <div class="menu-divider">ANALYTICS</div>
    <a href="recap-index.php" class="<?= ($currentPage == 'recap-index.php') ? 'active' : '' ?>">
        <i class="bi bi-graph-up"></i> Recap Visitor
    </a>
    
    <!-- Logout -->
    <a href="../auth/logout.php" class="mt-4" style="background: rgba(220, 53, 69, 0.2);">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<style>
.menu-divider {
    font-size: 0.7rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.5);
    padding: 15px 20px 5px 20px;
    text-align: left;
    letter-spacing: 1px;
}
</style>