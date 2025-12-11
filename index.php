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
  <link rel="stylesheet" href="public_html/game/css/style-index.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* ===== Gaming Theme Variables ===== */
    :root {
      --primary: #0a0e27;
      --primary-dark: #1a1f3a;
      --secondary: #3b82f6;
      --accent: #60a5fa;
      --dark: #1e293b;
      --light: #f8fafc;
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

    /* Primary Button */
    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
      transform: translateY(-2px);
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

    <!-- Hero Section -->
    <section class="mb-12 relative overflow-hidden rounded-3xl">
      <div class="glass-card p-8 md:p-12">
        <div class="max-w-4xl">
          <div class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">
            🔥 PLAY TO EARN
          </div>
          <h2 class="text-3xl md:text-5xl font-bold mb-4 leading-tight bg-gradient-to-r from-blue-400 to-blue-600 bg-clip-text text-transparent">
            Welcome to the Future of Gaming
          </h2>
          <p class="text-gray-300 text-lg mb-6 leading-relaxed">
            Connect your Phantom wallet and start earning rewards while playing amazing games on the Solana blockchain.
          </p>

          <!-- Wallet & Balance Cards -->
          <div class="grid md:grid-cols-2 gap-4 max-w-3xl mb-6">
            <!-- Wallet Status Card -->
            <div class="glass-card p-5">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center glow-blue">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-400 font-medium">Wallet Status</p>
                    <p id="walletStatus" class="font-bold text-white text-sm">Not Connected</p>
                  </div>
                </div>
                <button onclick="disconnectWallet()" id="disconnectBtn" class="hidden text-gray-400 hover:text-red-400 transition p-2 rounded-lg hover:bg-red-500/10">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              <div class="glass-card p-3 rounded-xl">
                <p class="text-xs text-gray-400 mb-1">Address</p>
                <p id="addrShort" class="font-mono text-white text-sm">-</p>
              </div>
            </div>

            <!-- Balance Card -->
            <div class="glass-card p-5">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-full flex items-center justify-center glow-blue">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-gray-400 font-medium">Kulino Balance</p>
                    <p id="kulinoBalance" class="font-bold text-white text-xl">0.00 KULINO</p>
                  </div>
                </div>
                <button onclick="updateBalanceDisplay()" id="refreshBalanceBtn" class="text-gray-400 hover:text-blue-400 transition p-2 rounded-lg hover:bg-blue-500/10">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                </button>
              </div>
              <div class="glass-card p-3 rounded-xl">
                <div class="flex items-center justify-between">
                  <p class="text-xs text-gray-400">SOL Balance</p>
                  <p id="solBalance" class="font-mono text-white text-sm">0.0000 SOL</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Swap Container -->
          <div class="swap-container max-w-3xl">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-bold text-white">Exchange</h3>
              <button class="text-gray-400 hover:text-blue-400 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </button>
            </div>

            <!-- From Token -->
            <div class="mb-1">
              <label class="text-sm text-gray-400 mb-2 block">From</label>
              <div class="glass-card p-4 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                  <button class="token-select flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-purple-500"></div>
                    <span class="font-semibold">POL</span>
                    <span class="text-xs text-gray-400">Polygon</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </button>
                  <span class="text-sm text-gray-400">Balance: <span id="fromBalance">0.00</span></span>
                </div>
                <input type="number" id="fromAmount" class="token-input" placeholder="0" step="0.01" min="0" />
              </div>
            </div>

            <!-- Exchange Icon -->
            <div class="exchange-icon" onclick="swapTokens()">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
              </svg>
            </div>

            <!-- To Token -->
            <div class="mb-4">
              <label class="text-sm text-gray-400 mb-2 block">To</label>
              <div class="glass-card p-4 rounded-xl">
                <div class="flex items-center justify-between mb-3">
                  <button class="token-select flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-500"></div>
                    <span class="font-semibold">IXT</span>
                    <span class="text-xs text-gray-400">Polygon</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </button>
                  <span class="text-sm text-gray-400">Balance: <span id="toBalance">0.00</span></span>
                </div>
                <input type="number" id="toAmount" class="token-input" placeholder="0" readonly />
              </div>
            </div>

            <!-- Swap Button -->
            <button id="swapBtn" class="btn-primary w-full px-6 py-4 rounded-xl text-white font-bold text-lg flex items-center justify-center gap-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
              </svg>
              Connect wallet
            </button>

            <!-- Info Text -->
            <p class="text-xs text-gray-500 text-center mt-3">
              Powered by LI.FI
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Swap Container -->
    <div class="swap-container max-w-3xl">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-white">Exchange</h3>
        <button class="text-gray-400 hover:text-blue-400 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </button>
      </div>

      <!-- From Token -->
      <div class="mb-1">
        <label class="text-sm text-gray-400 mb-2 block">From</label>
        <div class="glass-card p-4 rounded-xl">
          <div class="flex items-center justify-between mb-3">
            <button class="token-select flex items-center gap-2">
              <div class="w-6 h-6 rounded-full bg-purple-500"></div>
              <span class="font-semibold">POL</span>
              <span class="text-xs text-gray-400">Polygon</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <span class="text-sm text-gray-400">Balance: <span id="fromBalance">0.00</span></span>
          </div>
          <input type="number" id="fromAmount" class="token-input" placeholder="0" step="0.01" min="0" />
        </div>
      </div>

      <!-- Exchange Icon -->
      <div class="exchange-icon" onclick="swapTokens()">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
        </svg>
      </div>

      <!-- To Token -->
      <div class="mb-4">
        <label class="text-sm text-gray-400 mb-2 block">To</label>
        <div class="glass-card p-4 rounded-xl">
          <div class="flex items-center justify-between mb-3">
            <button class="token-select flex items-center gap-2">
              <div class="w-6 h-6 rounded-full bg-indigo-500"></div>
              <span class="font-semibold">IXT</span>
              <span class="text-xs text-gray-400">Polygon</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <span class="text-sm text-gray-400">Balance: <span id="toBalance">0.00</span></span>
          </div>
          <input type="number" id="toAmount" class="token-input" placeholder="0" readonly />
        </div>
      </div>

      <!-- Swap Button -->
      <button id="swapBtn" class="btn-primary w-full px-6 py-4 rounded-xl text-white font-bold text-lg flex items-center justify-center gap-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
        </svg>
        Connect wallet
      </button>

      <!-- Info Text -->
      <p class="text-xs text-gray-500 text-center mt-3">
        Powered by LI.FI
      </p>
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