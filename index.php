<?php include("./includes/koneksi.php"); ?>
<?php include("./includes/config.php"); ?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Kulino Game Hub — Solana Gaming Platform</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/icon/kulino-logo-blue.png" />

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- bs58 for signature encoding -->
  <script src="https://cdn.jsdelivr.net/npm/bs58/dist/index.min.js"></script>
  <!-- Solana Web3.js Library -->
  <script src="https://cdn.jsdelivr.net/npm/@solana/web3.js@latest/lib/index.iife.min.js"></script>
  <!-- CSS -->
  <link rel="stylesheet" href="css/style-login.css" />
  <link rel="stylesheet" href="css/swap.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ===== Gaming Theme Variables ===== */
    :root {
      --primary-dark: #0a0e27;
      --secondary-dark: #1a1f3a;
      --accent-blue: #3b82f6;
      --accent-light-blue: #60a5fa;
      --text-light: #e2e8f0;
    }

    /* ===== Global Styles ===== */
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* ===== Smooth Scroll ===== */
    html {
      scroll-behavior: smooth;
    }

    /* ===== Hide Scrollbar ===== */
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }

    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    /* ===== Sponsor Container ===== */
    .logo-container-sponsor {
      overflow: hidden;
      position: relative;
      width: 100%;
      display: flex;
      justify-content: flex-start;
      align-items: center;
      background: #fff;
      padding: 1rem 0;
    }

    /* ===== Video Background Mobile Hide ===== */
    .bg-gray-100 video {
      display: none;
    }

    @media (min-width: 768px) {
      .bg-gray-100 video {
        display: block !important;
      }
    }

    /* ===== Hamburger Menu Animation ===== */
    #menuToggle span {
      transition: all 0.3s ease;
    }

    #menuToggle.open span:nth-child(1) {
      transform: rotate(45deg) translate(3px, 4px);
    }

    #menuToggle.open span:nth-child(2) {
      opacity: 0;
    }

    #menuToggle.open span:nth-child(3) {
      transform: rotate(-45deg) translate(4px, -5px);
    }

    /* ===== Enhanced Buttons ===== */
    .btn-gaming {
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      font-size: 0.875rem;
    }

    .btn-gaming::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .btn-gaming:hover::before {
      width: 300px;
      height: 300px;
    }

    .btn-gaming:active {
      transform: scale(0.95);
    }

    /* Primary Button */
    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
      transform: translateY(-2px);
    }

    /* Secondary Button */
    .btn-secondary {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
    }

    .btn-secondary:hover {
      box-shadow: 0 6px 20px rgba(245, 87, 108, 0.6);
      transform: translateY(-2px);
    }

    /* Play Button */
    .btn-play {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
    }

    .btn-play:hover {
      box-shadow: 0 6px 20px rgba(79, 172, 254, 0.6);
      transform: translateY(-2px);
    }

    /* Admin Button */
    .btn-admin {
      background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      box-shadow: 0 4px 15px rgba(250, 112, 154, 0.4);
    }

    .btn-admin:hover {
      box-shadow: 0 6px 20px rgba(250, 112, 154, 0.6);
      transform: translateY(-2px);
    }

    /* Outline Button */
    .btn-outline {
      background: transparent;
      border: 2px solid;
      border-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%) 1;
      color: #667eea;
      box-shadow: none;
    }

    .btn-outline:hover {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      transform: translateY(-2px);
    }

    /* ===== Card Animations ===== */
    .game-card {
      transition: all 0.3s ease;
    }

    .game-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .featured-card {
      transition: all 0.3s ease;
    }

    .featured-card:hover {
      transform: scale(1.05);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .featured-card:hover video {
      opacity: 1;
    }

    .featured-card video {
      transition: opacity 0.3s ease;
    }

    /* ===== News Card ===== */
    .news-card {
      transition: all 0.3s ease;
    }

    .news-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* ===== Overlay Effect ===== */
    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .game-card:hover .overlay {
      opacity: 1;
    }

    /* ===== Badge Styles ===== */
    .badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .badge-new {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .badge-hot {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    .badge-top {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }

    /* ===== Glow Effect ===== */
    .glow {
      animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
      from {
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.5),
          0 0 10px rgba(102, 126, 234, 0.5),
          0 0 15px rgba(102, 126, 234, 0.5);
      }

      to {
        box-shadow: 0 0 10px rgba(102, 126, 234, 0.8),
          0 0 20px rgba(102, 126, 234, 0.8),
          0 0 30px rgba(102, 126, 234, 0.8);
      }
    }

    /* ===== Fade In Animation ===== */
    .fade-in {
      animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ===== Pulse Animation ===== */
    .pulse {
      animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.5;
      }
    }

    /* ===== Wallet Display ===== */
    .wallet-display {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 1rem;
      border-radius: 1rem;
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    /* ===== Hero Section ===== */
    .hero-section {
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
      animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 640px) {
      .btn-gaming {
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
      }
    }

    /* ===== Navigation Buttons ===== */
    .nav-btn {
      transition: all 0.3s ease;
      background: white;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .nav-btn:hover {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      transform: scale(1.1);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .nav-btn:active {
      transform: scale(0.95);
    }

    /* ===== Loading Spinner ===== */
    .spinner {
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top: 3px solid white;
      border-radius: 50%;
      width: 1.5rem;
      height: 1.5rem;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* ===== Featured Games - Force 16:9 Aspect Ratio ===== */
    .featured-card .aspect-video {
      aspect-ratio: 16 / 9;
      position: relative;
      width: 100%;
      background-color: #1f2937;
    }

    .featured-card .aspect-video img,
    .featured-card .aspect-video video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* ===== News Section - Preserve Original Image Aspect Ratio ===== */
    .news-card img {
      max-height: 240px;
      min-height: 180px;
      object-fit: contain;
      background-color: #f3f4f6;
    }

    /* Optional: Add max height for very tall images */
    @media (min-width: 768px) {
      .news-card img {
        max-height: 280px;
      }
    }

    /* ===== Ensure consistent card heights ===== */
    .featured-card {
      display: flex;
      flex-direction: column;
    }

    .news-card {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    /* ===== Image Loading State ===== */
    .news-card img,
    .featured-card img {
      background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
      background-size: 200% 100%;
      animation: loading 1.5s infinite;
    }

    .news-card img[src],
    .featured-card img[src] {
      animation: none;
    }

    @keyframes loading {
      0% {
        background-position: 200% 0;
      }

      100% {
        background-position: -200% 0;
      }
    }

    .badge-new {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .badge-hot {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
    }

    .badge-top {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }

    .badge-top-rated {
      background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
      color: #000;
    }

    .badge-updated {
      background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
      color: #333;
    }

    .badge-popular {
      background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
      color: #333;
    }

    /* Location Block Overlay */
    #locationBlockOverlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
    }

    #locationBlockOverlay.active {
      display: flex;
    }

    .location-block-content {
      background: white;
      border-radius: 24px;
      padding: 3rem;
      max-width: 500px;
      text-align: center;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
      animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .location-icon {
      width: 100px;
      height: 100px;
      margin: 0 auto 1.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.05);
      }
    }

    .retry-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 1rem 2rem;
      border-radius: 12px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 1.5rem;
    }

    .retry-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    /* Main content hidden initially */
    #mainContent {
      display: none;
    }

    #mainContent.active {
      display: block;
    }
  </style>
</head>


<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-indigo-50">
  <div id="locationBlockOverlay">
    <div class="location-block-content">
      <div class="location-icon">
        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </div>

      <h2 class="text-2xl font-bold text-gray-800 mb-3">Aktifkan Lokasi Anda</h2>
      <p class="text-gray-600 mb-4">
        Untuk mengakses Kulino Game Hub, kami memerlukan akses lokasi Anda untuk memberikan pengalaman terbaik dan keamanan.
      </p>

      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
        <p class="text-sm text-yellow-800">
          <strong>⚠️ Penting:</strong> Halaman ini tidak akan ditampilkan sampai Anda mengizinkan akses lokasi.
        </p>
      </div>

      <div class="text-left bg-gray-50 rounded-lg p-4 mb-4">
        <p class="text-sm font-semibold text-gray-700 mb-2">Cara mengaktifkan:</p>
        <ol class="text-sm text-gray-600 space-y-1 ml-4 list-decimal">
          <li>Klik tombol "Izinkan Lokasi" di bawah</li>
          <li>Izinkan akses lokasi pada popup browser</li>
          <li>Tunggu hingga lokasi terdeteksi</li>
          <li>Halaman akan otomatis ditampilkan</li>
        </ol>
      </div>

      <button onclick="requestLocationPermission()" class="retry-btn">
        📍 Izinkan Lokasi
      </button>

      <p class="text-xs text-gray-500 mt-4">
        Lokasi Anda hanya digunakan untuk analitik dan keamanan
      </p>
    </div>
  </div>

  <!-- Top Bar with Visitor Counter -->
  <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-6xl mx-auto px-4 py-2 flex justify-between items-center text-sm">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
          <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
        </svg>
        <span>Total Visitors: <strong id="visitorCount" class="pulse">0</strong></span>
      </div>
      <div class="flex items-center gap-2">
        <span class="hidden sm:inline">🎮 Welcome to Kulino Gaming Platform</span>
      </div>
    </div>
  </div>

  <!-- Header with Enhanced Visibility -->
  <header id="mainHeader" class="sticky top-0 z-50 transition-all duration-300">
    <!-- Navbar Container with Glass Effect -->
    <div class="bg-white/95 backdrop-blur-xl shadow-lg border-b border-gray-200/80">
      <div class="max-w-6xl mx-auto px-4 py-3">
        <div class="flex items-center justify-between">

          <!-- Logo & Title -->
          <div class="flex items-center gap-3">
            <!-- Logo Container -->
            <div class="relative w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 p-2.5 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
              <img
                src="assets/icon/kulino-logo-blue.png"
                alt="Kulino Logo"
                class="w-full h-full object-contain drop-shadow-lg"
                onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ctext y=%22.9em%22 font-size=%2290%22%3E🎮%3C/text%3E%3C/svg%3E';" />
            </div>

            <div class="flex flex-col">
              <h1 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Kulino Game Hub
              </h1>
              <p class="text-xs text-gray-600 font-medium hidden sm:block">
                Play-to-Earn Gaming on Solana
              </p>
            </div>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center gap-3">
            <!-- Admin Login Button -->
            <a href="auth/login.php" class="group relative overflow-hidden bg-gradient-to-r from-gray-700 to-gray-900 hover:from-gray-800 hover:to-black px-5 py-2.5 rounded-xl transition-all duration-300 inline-flex items-center gap-2 shadow-lg hover:shadow-xl hover:scale-105">
              <svg class="w-5 h-5 text-white transition-transform group-hover:rotate-12" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
              </svg>
              <span class="hidden lg:inline font-semibold text-white">Admin</span>
            </a>

            <!-- Connect Wallet Button - REMOVE ALL ONCLICK -->
            <button
              type="button"
              id="connectBtn"
              class="group relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 px-6 py-2.5 rounded-xl transition-all duration-300 inline-flex items-center gap-2 shadow-lg hover:shadow-xl hover:scale-105">
              <svg class="w-5 h-5 text-white transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
              </svg>
              <span class="font-semibold text-white">Connect Wallet</span>
            </button>
          </div>

          <!-- Mobile Menu Toggle -->
          <button id="menuToggle" class="md:hidden flex flex-col items-end space-y-1.5 focus:outline-none z-50 p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <span class="block w-6 h-0.5 bg-gray-800 rounded-full transition-all"></span>
            <span class="block w-5 h-0.5 bg-gray-800 rounded-full transition-all"></span>
            <span class="block w-4 h-0.5 bg-gray-800 rounded-full transition-all"></span>
          </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4 space-y-3 border-t border-gray-200 pt-4">
          <a href="auth/login.php" class="bg-gradient-to-r from-gray-700 to-gray-900 hover:from-gray-800 hover:to-black w-full px-5 py-3 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
            </svg>
            <span class="font-semibold text-white">Admin Login</span>
          </a>
          <!-- Mobile Connect Button - REMOVE ALL ONCLICK -->
          <button
            type="button"
            id="connectBtnMobile"
            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 w-full px-5 py-3 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            <span class="font-semibold text-white">Connect Wallet</span>
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-8">

    <!-- Hero Section with Balance Display -->
    <section class="mb-12 relative overflow-hidden rounded-3xl shadow-2xl hero-section">
      <!-- Video Background -->
      <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover">
        <source src="assets/video/video-lino.mp4" type="video/mp4" />
      </video>

      <!-- Overlay Content -->
      <div class="relative z-10 p-8 md:p-12 bg-gradient-to-r from-black/60 via-black/40 to-transparent">
        <div class="max-w-4xl">
          <div class="inline-block bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">
            🔥 PLAY TO EARN
          </div>
          <h2 class="text-3xl md:text-5xl font-bold text-white mb-4 leading-tight">
            Welcome to the Future of Gaming
          </h2>
          <p class="text-white/90 text-lg mb-6 leading-relaxed">
            Connect your Phantom wallet and start earning rewards while playing amazing games on the Solana blockchain.
          </p>

          <!-- Wallet & Balance Cards -->
          <div class="grid md:grid-cols-2 gap-4 max-w-3xl">

            <!-- Wallet Status Card -->
            <div class="wallet-display backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-5 shadow-2xl">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-white/70 font-medium">Wallet Status</p>
                    <p id="walletStatus" class="font-bold text-white text-sm">Not Connected</p>
                  </div>
                </div>
                <button
                  onclick="disconnectWallet()"
                  id="disconnectBtn"
                  class="hidden text-white/70 hover:text-red-400 transition p-2 rounded-lg hover:bg-white/10"
                  title="Disconnect Wallet">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                <p class="text-xs text-white/60 mb-1">Address</p>
                <p id="addrShort" class="font-mono text-white text-sm">-</p>
              </div>
            </div>

            <!-- Balance Card -->
            <div class="backdrop-blur-xl bg-gradient-to-br from-yellow-500/20 to-orange-600/20 border border-white/20 rounded-2xl p-5 shadow-2xl">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-white/70 font-medium">Kulino Balance</p>
                    <p id="kulinoBalance" class="font-bold text-white text-xl">0.00 KULINO</p>
                  </div>
                </div>
                <button
                  onclick="updateBalanceDisplay()"
                  id="refreshBalanceBtn"
                  class="text-white/70 hover:text-white transition p-2 rounded-lg hover:bg-white/10"
                  title="Refresh Balance">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                </button>
              </div>
              <div class="bg-white/5 rounded-xl p-3 border border-white/10">
                <div class="flex items-center justify-between">
                  <p class="text-xs text-white/60">SOL Balance</p>
                  <p id="solBalance" class="font-mono text-white text-sm">0.0000 SOL</p>
                </div>
              </div>
            </div>

          </div>

          <!-- Quick Actions -->
          <div class="mt-6 flex flex-wrap gap-3">
            <a href="https://phantom.com/tokens/solana/E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1"
              target="_blank"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition backdrop-blur-sm border border-white/20 text-sm font-medium">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
              </svg>
              View Token on Phantom
            </a>

            <button
              onclick="updateBalanceDisplay()"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition backdrop-blur-sm border border-white/20 text-sm font-medium">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Refresh Balance
            </button>
          </div>

        </div>
      </div>
    </section>

    <!-- Token Swap Section -->
<section class="mb-12 fade-in">
  <div class="max-w-2xl mx-auto">
    <div class="text-center mb-8">
      <h3 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-3">
        🔄 Swap to Kulino
      </h3>
      <p class="text-gray-600">Exchange your crypto for Kulino Coins instantly</p>
    </div>

    <div class="swap-container glass-card">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h4 class="text-xl font-bold text-white">Exchange</h4>
        <button onclick="openSwapSettings()" class="text-gray-400 hover:text-blue-400 transition p-2 rounded-lg hover:bg-white/10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </button>
      </div>

      <!-- From Token -->
      <div class="mb-2">
        <label class="text-sm text-gray-400 mb-2 block font-medium">You Pay</label>
        <div class="glass-card p-4 rounded-xl border border-white/10 hover:border-blue-400/30 transition">
          <div class="flex items-center justify-between mb-3">
            <button onclick="openTokenSelector('from')" class="token-select flex items-center gap-3 px-4 py-2 rounded-xl hover:bg-white/10 transition">
              <img id="fromTokenIcon" src="https://cryptologos.cc/logos/bitcoin-btc-logo.png" alt="BTC" class="w-8 h-8 rounded-full">
              <div class="text-left">
                <span id="fromTokenSymbol" class="font-bold text-white text-lg">BTC</span>
                <span id="fromTokenName" class="text-xs text-gray-400 block">Bitcoin</span>
              </div>
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div class="text-right">
              <p class="text-xs text-gray-400">Balance</p>
              <p id="fromBalance" class="text-sm font-semibold text-white">0.00</p>
            </div>
          </div>
          <input 
            type="number" 
            id="fromAmount" 
            class="token-input bg-transparent border-none text-white text-2xl font-bold w-full focus:outline-none" 
            placeholder="0.0" 
            step="0.000001"
            oninput="calculateSwapAmount()"
          />
          <div class="flex items-center justify-between mt-2">
            <p id="fromAmountUSD" class="text-xs text-gray-400">≈ $0.00 USD</p>
            <button onclick="setMaxAmount()" class="text-xs text-blue-400 hover:text-blue-300 font-semibold px-3 py-1 rounded-lg bg-blue-400/10 hover:bg-blue-400/20 transition">
              MAX
            </button>
          </div>
        </div>
      </div>

      <!-- Swap Direction Button -->
      <div class="flex justify-center -my-3 relative z-10">
        <button onclick="flipSwapDirection()" class="exchange-icon bg-gradient-to-br from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700">
          <svg class="w-6 h-6 text-white transition-transform duration-300" id="swapArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
          </svg>
        </button>
      </div>

      <!-- To Token (KULINO) -->
      <div class="mb-6">
        <label class="text-sm text-gray-400 mb-2 block font-medium">You Receive</label>
        <div class="glass-card p-4 rounded-xl border border-yellow-500/30 hover:border-yellow-400/50 transition bg-gradient-to-br from-yellow-500/5 to-orange-500/5">
          <div class="flex items-center justify-between mb-3">
            <div class="token-select flex items-center gap-3 px-4 py-2 rounded-xl bg-gradient-to-r from-yellow-500/20 to-orange-500/20">
              <img src="assets/icon/kulino-logo-blue.png" alt="KULINO" class="w-8 h-8 rounded-full">
              <div class="text-left">
                <span class="font-bold text-white text-lg">KULINO</span>
                <span class="text-xs text-gray-400 block">Kulino Coin</span>
              </div>
            </div>
            <div class="text-right">
              <p class="text-xs text-gray-400">Balance</p>
              <p id="kulinoBalanceDisplay" class="text-sm font-semibold text-yellow-400">0.00</p>
            </div>
          </div>
          <input 
            type="number" 
            id="toAmount" 
            class="token-input bg-transparent border-none text-white text-2xl font-bold w-full focus:outline-none" 
            placeholder="0.0" 
            readonly
          />
          <div class="mt-2">
            <p id="toAmountUSD" class="text-xs text-gray-400">≈ $0.00 USD</p>
          </div>
        </div>
      </div>

      <!-- Swap Info -->
      <div id="swapInfo" class="glass-card p-4 rounded-xl mb-4 space-y-2 text-sm hidden">
        <div class="flex justify-between text-gray-300">
          <span>Exchange Rate</span>
          <span id="exchangeRate" class="font-semibold">-</span>
        </div>
        <div class="flex justify-between text-gray-300">
          <span>Network Fee</span>
          <span id="networkFee" class="font-semibold">~$0.00</span>
        </div>
        <div class="flex justify-between text-gray-300">
          <span>Slippage Tolerance</span>
          <span id="slippageTolerance" class="font-semibold text-blue-400">0.5%</span>
        </div>
        <div class="flex justify-between text-gray-300 pt-2 border-t border-white/10">
          <span class="font-semibold">Estimated Time</span>
          <span id="estimatedTime" class="font-semibold text-green-400">~30 seconds</span>
        </div>
      </div>

      <!-- Swap Button -->
      <button 
        id="swapButton" 
        onclick="executeSwap()" 
        class="btn-primary w-full px-6 py-4 rounded-xl text-white font-bold text-lg flex items-center justify-center gap-3 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
        </svg>
        <span id="swapButtonText">Connect Wallet to Swap</span>
      </button>

      <!-- Powered By -->
      <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span>Secured by Jupiter & 1inch Aggregators</span>
      </div>
    </div>
  </div>
</section>

<!-- Token Selector Modal -->
<div id="tokenSelectorModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl max-w-md w-full max-h-[80vh] overflow-hidden shadow-2xl">
    <!-- Modal Header -->
    <div class="p-6 border-b border-white/10">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-white">Select Token</h3>
        <button onclick="closeTokenSelector()" class="text-gray-400 hover:text-white transition p-2 rounded-lg hover:bg-white/10">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <input 
        type="text" 
        id="tokenSearchInput" 
        placeholder="Search by name or symbol..." 
        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:border-blue-400 transition"
        oninput="filterTokenList()"
      />
    </div>

    <!-- Token List -->
    <div id="tokenList" class="overflow-y-auto max-h-96 p-4">
      <!-- Bitcoin -->
      <button onclick="selectToken('BTC', 'Bitcoin', 'https://cryptologos.cc/logos/bitcoin-btc-logo.png', 'btc')" class="token-item w-full flex items-center gap-4 p-4 rounded-xl hover:bg-white/10 transition">
        <img src="https://cryptologos.cc/logos/bitcoin-btc-logo.png" alt="BTC" class="w-10 h-10 rounded-full">
        <div class="flex-1 text-left">
          <p class="font-bold text-white">Bitcoin</p>
          <p class="text-sm text-gray-400">BTC</p>
        </div>
        <p class="text-sm font-semibold text-gray-300">$0.00</p>
      </button>

      <!-- Solana -->
      <button onclick="selectToken('SOL', 'Solana', 'https://cryptologos.cc/logos/solana-sol-logo.png', 'sol')" class="token-item w-full flex items-center gap-4 p-4 rounded-xl hover:bg-white/10 transition">
        <img src="https://cryptologos.cc/logos/solana-sol-logo.png" alt="SOL" class="w-10 h-10 rounded-full">
        <div class="flex-1 text-left">
          <p class="font-bold text-white">Solana</p>
          <p class="text-sm text-gray-400">SOL</p>
        </div>
        <p class="text-sm font-semibold text-gray-300">$0.00</p>
      </button>

      <!-- Ethereum -->
      <button onclick="selectToken('ETH', 'Ethereum', 'https://cryptologos.cc/logos/ethereum-eth-logo.png', 'eth')" class="token-item w-full flex items-center gap-4 p-4 rounded-xl hover:bg-white/10 transition">
        <img src="https://cryptologos.cc/logos/ethereum-eth-logo.png" alt="ETH" class="w-10 h-10 rounded-full">
        <div class="flex-1 text-left">
          <p class="font-bold text-white">Ethereum</p>
          <p class="text-sm text-gray-400">ETH</p>
        </div>
        <p class="text-sm font-semibold text-gray-300">$0.00</p>
      </button>

      <!-- XRP -->
      <button onclick="selectToken('XRP', 'Ripple', 'https://cryptologos.cc/logos/xrp-xrp-logo.png', 'xrp')" class="token-item w-full flex items-center gap-4 p-4 rounded-xl hover:bg-white/10 transition">
        <img src="https://cryptologos.cc/logos/xrp-xrp-logo.png" alt="XRP" class="w-10 h-10 rounded-full">
        <div class="flex-1 text-left">
          <p class="font-bold text-white">Ripple</p>
          <p class="text-sm text-gray-400">XRP</p>
        </div>
        <p class="text-sm font-semibold text-gray-300">$0.00</p>
      </button>

      <!-- Polygon -->
      <button onclick="selectToken('MATIC', 'Polygon', 'https://cryptologos.cc/logos/polygon-matic-logo.png', 'matic')" class="token-item w-full flex items-center gap-4 p-4 rounded-xl hover:bg-white/10 transition">
        <img src="https://cryptologos.cc/logos/polygon-matic-logo.png" alt="MATIC" class="w-10 h-10 rounded-full">
        <div class="flex-1 text-left">
          <p class="font-bold text-white">Polygon</p>
          <p class="text-sm text-gray-400">MATIC</p>
        </div>
        <p class="text-sm font-semibold text-gray-300">$0.00</p>
      </button>

      <!-- Sui -->
      <button onclick="selectToken('SUI', 'Sui', 'https://cryptologos.cc/logos/sui-sui-logo.png', 'sui')" class="token-item w-full flex items-center gap-4 p-4 rounded-xl hover:bg-white/10 transition">
        <img src="https://cryptologos.cc/logos/sui-sui-logo.png" alt="SUI" class="w-10 h-10 rounded-full">
        <div class="flex-1 text-left">
          <p class="font-bold text-white">Sui</p>
          <p class="text-sm text-gray-400">SUI</p>
        </div>
        <p class="text-sm font-semibold text-gray-300">$0.00</p>
      </button>
    </div>
  </div>
</div>

<!-- Swap Settings Modal -->
<div id="swapSettingsModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
  <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl max-w-md w-full shadow-2xl p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-white">Swap Settings</h3>
      <button onclick="closeSwapSettings()" class="text-gray-400 hover:text-white transition p-2 rounded-lg hover:bg-white/10">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Slippage Tolerance -->
    <div class="mb-6">
      <label class="text-sm font-semibold text-gray-300 mb-3 block">Slippage Tolerance</label>
      <div class="grid grid-cols-4 gap-2 mb-3">
        <button onclick="setSlippage(0.1)" class="slippage-btn px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-white font-semibold transition">0.1%</button>
        <button onclick="setSlippage(0.5)" class="slippage-btn active px-4 py-2 rounded-lg bg-blue-500 text-white font-semibold transition">0.5%</button>
        <button onclick="setSlippage(1.0)" class="slippage-btn px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-white font-semibold transition">1.0%</button>
        <button onclick="setSlippage(3.0)" class="slippage-btn px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-white font-semibold transition">3.0%</button>
      </div>
      <input 
        type="number" 
        id="customSlippage" 
        placeholder="Custom %" 
        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-blue-400"
        step="0.1"
        oninput="setSlippage(parseFloat(this.value) || 0.5)"
      />
    </div>

    <!-- Transaction Deadline -->
    <div class="mb-6">
      <label class="text-sm font-semibold text-gray-300 mb-3 block">Transaction Deadline (minutes)</label>
      <input 
        type="number" 
        id="txDeadline" 
        value="20" 
        class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-blue-400"
        min="1"
      />
    </div>

    <button onclick="closeSwapSettings()" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-3 rounded-xl transition">
      Save Settings
    </button>
  </div>
</div>

    <!-- Featured Games Section -->
    <section class="mb-12 fade-in">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-2xl font-bold text-gray-800">🌟 Featured Games</h3>
          <p class="text-sm text-gray-500 mt-1">Top picks for you</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php
        // Query featured games dari database
        $sqlFeatured = mysqli_query($koneksi, "
      SELECT * FROM tb_games 
      WHERE is_featured = 1 AND is_active = 1 
      ORDER BY sort_order ASC, id DESC 
      LIMIT 4
    ");

        if (mysqli_num_rows($sqlFeatured) > 0) {
          while ($game = mysqli_fetch_assoc($sqlFeatured)) {
            // Tentukan badge class
            $badgeClass = '';
            switch ($game['badge']) {
              case 'New':
                $badgeClass = 'badge-new';
                break;
              case 'Hot':
                $badgeClass = 'badge-hot';
                break;
              case 'Top Rated':
                $badgeClass = 'badge-top-rated';
                break;
              case 'Updated':
                $badgeClass = 'badge-updated';
                break;
              case 'Popular':
                $badgeClass = 'badge-popular';
                break;
              default:
                $badgeClass = 'badge-new';
            }
        ?>
            <!-- Featured Game Card (Dynamic) -->
            <article class="featured-card relative bg-white rounded-2xl shadow-lg overflow-hidden cursor-pointer group">
              <div class="relative overflow-hidden aspect-video">
                <img src="assets/<?= htmlspecialchars($game['image']) ?>"
                  alt="<?= htmlspecialchars($game['title']) ?>"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />

                <?php if (!empty($game['video_hover'])): ?>
                  <video class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300" muted loop>
                    <source src="assets/video/<?= htmlspecialchars($game['video_hover']) ?>" type="video/mp4" />
                  </video>
                <?php endif; ?>

                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <?php if (!empty($game['badge'])): ?>
                  <span class="badge <?= $badgeClass ?> absolute top-3 left-3">
                    <?= htmlspecialchars($game['badge']) ?>
                  </span>
                <?php endif; ?>
              </div>

              <div class="p-5">
                <h4 class="font-bold text-lg text-gray-800 mb-2">
                  <?= htmlspecialchars($game['title']) ?>
                </h4>
                <p class="text-sm text-gray-600 mb-4">
                  <?= htmlspecialchars(substr($game['description'], 0, 50)) ?>...
                </p>
                <button onclick="playGame('<?= htmlspecialchars($game['game_url']) ?>')"
                  class="btn-gaming btn-play w-full px-4 py-3 text-white rounded-xl inline-flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                  </svg>
                  Play Now
                </button>
              </div>
            </article>
          <?php
          }
        } else {
          // Fallback jika tidak ada featured games
          ?>
          <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-gray-500 font-semibold">No Featured Games Available</p>
            <p class="text-sm text-gray-400 mt-2">Check back soon for new games!</p>
          </div>
        <?php } ?>
      </div>
    </section>

    <!-- All Games Slider Section -->
    <section class="mb-12 fade-in">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-2xl font-bold text-gray-800">🎮 All Games</h3>
          <p class="text-sm text-gray-500 mt-1">Explore our complete collection</p>
        </div>
      </div>

      <div class="relative">
        <div id="gamesSlider" class="flex overflow-x-auto gap-6 scroll-smooth no-scrollbar pb-4">
          <?php
          // Query all active games (not featured)
          $sqlAllGames = mysqli_query($koneksi, "
        SELECT * FROM tb_games 
        WHERE is_active = 1 
        ORDER BY is_featured DESC, sort_order ASC, id DESC
      ");

          if (mysqli_num_rows($sqlAllGames) > 0) {
            while ($game = mysqli_fetch_assoc($sqlAllGames)) {
              $badgeClass = '';
              switch ($game['badge']) {
                case 'New':
                  $badgeClass = 'badge-new';
                  break;
                case 'Hot':
                  $badgeClass = 'badge-hot';
                  break;
                case 'Top Rated':
                  $badgeClass = 'badge-top-rated';
                  break;
                case 'Updated':
                  $badgeClass = 'badge-updated';
                  break;
                case 'Popular':
                  $badgeClass = 'badge-popular';
                  break;
              }
          ?>
              <article class="game-card bg-white rounded-2xl shadow-lg min-w-[280px] md:min-w-[320px] overflow-hidden group">
                <div class="relative overflow-hidden">
                  <img src="assets/<?= htmlspecialchars($game['image']) ?>"
                    alt="<?= htmlspecialchars($game['title']) ?>"
                    class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500" />
                  <div class="overlay rounded-t-2xl"></div>

                  <?php if (!empty($game['badge'])): ?>
                    <span class="badge <?= $badgeClass ?> absolute top-3 left-3">
                      <?= htmlspecialchars($game['badge']) ?>
                    </span>
                  <?php endif; ?>
                </div>

                <div class="p-5">
                  <h4 class="font-semibold text-lg text-gray-800 mb-2">
                    <?= htmlspecialchars($game['title']) ?>
                  </h4>
                  <p class="text-sm text-gray-600 mb-4">
                    <?= htmlspecialchars(substr($game['description'], 0, 60)) ?>...
                  </p>
                  <div class="flex gap-3">
                    <button onclick="playGame('<?= htmlspecialchars($game['game_url']) ?>')"
                      class="btn-gaming btn-play flex-1 px-4 py-2.5 text-white rounded-lg text-sm">
                      Play
                    </button>
                    <button class="btn-gaming btn-outline flex-1 px-4 py-2.5 rounded-lg text-sm">
                      Preview
                    </button>
                  </div>
                </div>
              </article>
            <?php
            }
          } else {
            ?>
            <div class="w-full text-center py-12">
              <p class="text-gray-500">No games available at the moment</p>
            </div>
          <?php } ?>
        </div>

        <!-- Navigation Buttons -->
        <button onclick="scrollSlider('gamesSlider', -1)"
          class="nav-btn hidden lg:block absolute top-1/2 -left-5 transform -translate-y-1/2 rounded-full p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button onclick="scrollSlider('gamesSlider', 1)"
          class="nav-btn hidden lg:block absolute top-1/2 -right-5 transform -translate-y-1/2 rounded-full p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </section>

    <!-- News Section -->
    <section class="mb-12 fade-in">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-2xl font-bold text-gray-800">📰 Latest News</h3>
          <p class="text-sm text-gray-500 mt-1">Stay updated with gaming news</p>
        </div>
      </div>

      <div class="relative">
        <div id="newsSlider" class="flex overflow-x-auto gap-6 scroll-smooth no-scrollbar pb-4">
          <?php
          $sql = mysqli_query($koneksi, "SELECT * FROM tb_berita ORDER BY id DESC");
          while ($row = mysqli_fetch_assoc($sql)) {
          ?>
            <article class="news-card bg-white rounded-2xl shadow-lg min-w-[280px] md:min-w-[320px] overflow-hidden group flex flex-col">
              <!-- Image Container with Fixed Height -->
              <div class="relative w-full h-48 overflow-hidden bg-gray-100">
                <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>"
                  alt="<?= htmlspecialchars($row['judul']) ?>"
                  class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
              </div>
              <div class="p-5 flex-1 flex flex-col">
                <h4 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2"><?= htmlspecialchars($row['judul']) ?></h4>
                <p class="text-sm text-gray-600 mb-4 line-clamp-3 flex-1">
                  <?= nl2br(htmlspecialchars(substr($row['deskripsi'], 0, 100))) ?>...
                </p>
                <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank"
                  class="btn-gaming btn-outline w-full px-4 py-2.5 rounded-lg text-sm inline-flex items-center justify-center gap-2">
                  Read More
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </a>
              </div>
            </article>
          <?php } ?>
        </div>

        <!-- Navigation -->
        <button onclick="scrollSlider('newsSlider', -1)" class="nav-btn hidden lg:block absolute top-1/2 -left-5 transform -translate-y-1/2 rounded-full p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button onclick="scrollSlider('newsSlider', 1)" class="nav-btn hidden lg:block absolute top-1/2 -right-5 transform -translate-y-1/2 rounded-full p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </section>

    <!-- Marketplace Section -->
    <section class="mb-12 fade-in">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-2xl font-bold text-gray-800">🛍️ Marketplace</h3>
          <p class="text-sm text-gray-500 mt-1">Shop exclusive Kulino merchandise</p>
        </div>
      </div>

      <!-- Filter Categories -->
      <div class="mb-6 bg-white rounded-2xl shadow-md p-4">
        <div class="flex flex-wrap gap-3">
          <button onclick="filterMarketplace('all')"
            class="marketplace-filter-btn active px-5 py-2.5 rounded-xl font-medium transition-all duration-300 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            All Products
          </button>
          <button onclick="filterMarketplace('Aksesoris')"
            class="marketplace-filter-btn px-5 py-2.5 rounded-xl font-medium transition-all duration-300 bg-gray-100 text-gray-700 hover:bg-gray-200">
            Aksesoris
          </button>
          <button onclick="filterMarketplace('Board Game')"
            class="marketplace-filter-btn px-5 py-2.5 rounded-xl font-medium transition-all duration-300 bg-gray-100 text-gray-700 hover:bg-gray-200">
            Board Game
          </button>
        </div>

        <!-- Sub-category Filter -->
        <div id="subCategoryFilter" class="mt-3 hidden">
          <div class="flex flex-wrap gap-2">
            <!-- Sub-categories will be populated by JavaScript -->
          </div>
        </div>
      </div>

      <!-- Products Grid/Slider -->
      <div class="relative">
        <div id="marketplaceSlider" class="flex overflow-x-auto gap-6 scroll-smooth no-scrollbar pb-4">

          <?php
          $sqlMarket = mysqli_query($koneksi, "SELECT * FROM tb_marketplace WHERE is_active = 1 ORDER BY created_at DESC");

          if (mysqli_num_rows($sqlMarket) > 0) {
            while ($product = mysqli_fetch_assoc($sqlMarket)) {
              $hasDiscount = !empty($product['original_price']) && $product['original_price'] > $product['price'];
              $discountPercent = $hasDiscount ? round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) : 0;
          ?>

              <!-- Product Card -->
              <article class="product-card bg-white rounded-2xl shadow-lg min-w-[280px] md:min-w-[320px] overflow-hidden group flex flex-col"
                data-category="<?= htmlspecialchars($product['category']) ?>"
                data-subcategory="<?= htmlspecialchars($product['subcategory']) ?>"
                data-id="<?= $product['id'] ?>">

                <!-- Product Image -->
                <div class="relative overflow-hidden h-80 bg-gray-100">
                  <img src="uploads/marketplace/<?= htmlspecialchars($product['image']) ?>"
                    alt="<?= htmlspecialchars($product['product_name']) ?>"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                  <!-- Discount Badge -->
                  <?php if ($hasDiscount): ?>
                    <span class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                      -<?= $discountPercent ?>%
                    </span>
                  <?php endif; ?>

                  <!-- Category Badge -->
                  <span class="absolute top-3 left-3 bg-indigo-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    <?= htmlspecialchars($product['subcategory']) ?>
                  </span>
                </div>

                <!-- Product Info -->
                <div class="p-5 flex-1 flex flex-col">
                  <h4 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2">
                    <?= htmlspecialchars($product['product_name']) ?>
                  </h4>

                  <p class="text-sm text-gray-600 mb-4 line-clamp-2 flex-1">
                    <?= htmlspecialchars($product['description']) ?>
                  </p>

                  <!-- Price -->
                  <div class="mb-4">
                    <?php if ($hasDiscount): ?>
                      <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-indigo-600">
                          Rp <?= number_format($product['price'], 0, ',', '.') ?>
                        </span>
                        <span class="text-sm text-gray-400 line-through">
                          Rp <?= number_format($product['original_price'], 0, ',', '.') ?>
                        </span>
                      </div>
                    <?php else: ?>
                      <span class="text-2xl font-bold text-indigo-600">
                        Rp <?= number_format($product['price'], 0, ',', '.') ?>
                      </span>
                    <?php endif; ?>
                  </div>

                  <!-- Stock Info -->
                  <div class="mb-4">
                    <?php if ($product['stock'] > 0): ?>
                      <span class="text-sm text-green-600 font-medium">
                        ✓ In Stock (<?= $product['stock'] ?> available)
                      </span>
                    <?php else: ?>
                      <span class="text-sm text-red-600 font-medium">
                        ✗ Out of Stock
                      </span>
                    <?php endif; ?>
                  </div>

                  <!-- Buy Button -->
                  <button onclick='openProductModal(<?= json_encode($product) ?>)'
                    <?= $product['stock'] <= 0 ? 'disabled' : '' ?>
                    class="btn-gaming btn-play w-full px-4 py-3 text-white rounded-xl inline-flex items-center justify-center gap-2 <?= $product['stock'] <= 0 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Add Buy
                  </button>
                </div>
              </article>

            <?php
            }
          } else {
            ?>
            <div class="w-full text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
              </svg>
              <p class="text-gray-500 font-semibold">No products available</p>
            </div>
          <?php } ?>

        </div>

        <!-- Navigation Buttons -->
        <button onclick="scrollSlider('marketplaceSlider', -1)"
          class="nav-btn hidden lg:block absolute top-1/2 -left-5 transform -translate-y-1/2 rounded-full p-3">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        <button onclick="scrollSlider('marketplaceSlider', 1)"
          class="nav-btn hidden lg:block absolute top-1/2 -right-5 transform -translate-y-1/2 rounded-full p-3">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>
    </section>

    <!-- Product Detail Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="relative">
          <!-- Close Button -->
          <button onclick="closeProductModal()"
            class="absolute top-4 right-4 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>

          <!-- Product Image -->
          <div class="relative h-96 bg-gray-100">
            <img id="modalImage" src="" alt="" class="w-full h-full object-cover">
            <span id="modalDiscount" class="hidden absolute top-4 left-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold"></span>
          </div>

          <!-- Product Details -->
          <div class="p-6">
            <div class="mb-4">
              <span id="modalCategory" class="inline-block bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold mb-3"></span>
              <h3 id="modalTitle" class="text-2xl font-bold text-gray-800 mb-2"></h3>
              <p id="modalDescription" class="text-gray-600"></p>
            </div>

            <!-- Price Section -->
            <div class="mb-6 pb-6 border-b border-gray-200">
              <div id="modalPriceSection"></div>
              <p id="modalStock" class="text-sm mt-2"></p>
            </div>

            <!-- Product Info -->
            <div class="mb-6 space-y-3">
              <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Authentic Kulino Product</span>
              </div>
              <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Secure Payment via Instagram Direct</span>
              </div>
              <div class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <span>Fast Shipping Available</span>
              </div>
            </div>

            <!-- Buy Now Button -->
            <button onclick="buyNowProduct()"
              class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Buy Now on Instagram
            </button>

            <p class="text-xs text-gray-500 text-center mt-3">
              You will be redirected to our Instagram merchant page
            </p>
          </div>
        </div>
      </div>
    </div>

  </main>



  <!-- Sponsor -->
  <?php include("./includes/sponsor.php"); ?>

  <!-- Footer -->
  <?php include("./includes/footer.php"); ?>

  <!-- Kulino Token Balance Checker -->
  <script>
    function formatKulinoBalance(balance) {
      if (balance >= 1000) {
        return (balance / 1000).toFixed(2) + 'K';
      }
      return balance.toFixed(2);
    }
  </script>

  <script src="./js/script-index.js"></script>

</body>

</html>