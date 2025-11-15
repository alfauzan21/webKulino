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

  <!-- CSS -->
  <link rel="stylesheet" href="./css/style-index.css" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-indigo-50">

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

  <!-- Header -->
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

            <!-- Connect Wallet Button -->
            <button id="connectBtn" class="group relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 px-6 py-2.5 rounded-xl transition-all duration-300 inline-flex items-center gap-2 shadow-lg hover:shadow-xl hover:scale-105">
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
          <button id="connectBtnMobile" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 w-full px-5 py-3 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
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
            <div class="backdrop-blur-xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-white/20 rounded-2xl p-5 shadow-2xl">
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs text-white/70 font-medium">Your Balance</p>
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

    <!-- Featured Games Section -->
    <section class="mb-12 fade-in">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-2xl font-bold text-gray-800">🌟 Featured Games</h3>
          <p class="text-sm text-gray-500 mt-1">Top picks for you</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Featured Game Card 1 -->
        <article class="featured-card relative bg-white rounded-2xl shadow-lg overflow-hidden cursor-pointer group">
          <div class="relative overflow-hidden aspect-video">
            <img src="assets/game-free-fire.jpg" alt="Free Fire" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
            <video class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300" muted loop>
              <source src="assets/video/hover-ff.mp4" type="video/mp4" />
            </video>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <span class="badge badge-new absolute top-3 left-3">Updated</span>
          </div>
          <div class="p-5">
            <h4 class="font-bold text-lg text-gray-800 mb-2">Free Fire</h4>
            <p class="text-sm text-gray-600 mb-4">Epic battle royale game with intense action.</p>
            <button onclick="playGame('blox-d')" class="btn-gaming btn-play w-full px-4 py-3 text-white rounded-xl inline-flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
              </svg>
              Play Now
            </button>
          </div>
        </article>

        <!-- Featured Game Card 2 -->
        <article class="featured-card relative bg-white rounded-2xl shadow-lg overflow-hidden cursor-pointer group">
          <div class="relative overflow-hidden aspect-video">
            <img src="assets/efootball.jpeg" alt="eFootball" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
            <video class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300" muted loop>
              <source src="assets/video/hover-pes.mp4" type="video/mp4" />
            </video>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <span class="badge badge-hot absolute top-3 left-3">Top Rated</span>
          </div>
          <div class="p-5">
            <h4 class="font-bold text-lg text-gray-800 mb-2">Brainrot Online</h4>
            <p class="text-sm text-gray-600 mb-4">Multiplayer game with unique gameplay.</p>
            <button onclick="playGame('brainrot-online')" class="btn-gaming btn-play w-full px-4 py-3 text-white rounded-xl inline-flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
              </svg>
              Play Now
            </button>
          </div>
        </article>

        <!-- Featured Game Card 3 -->
        <article class="featured-card relative bg-white rounded-2xl shadow-lg overflow-hidden cursor-pointer group">
          <div class="relative overflow-hidden aspect-video">
            <img src="assets/mobile-legends.jpg" alt="Mobile Legends" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <span class="badge badge-top absolute top-3 left-3">Popular</span>
          </div>
          <div class="p-5">
            <h4 class="font-bold text-lg text-gray-800 mb-2">Mobile Legends</h4>
            <p class="text-sm text-gray-600 mb-4">5v5 MOBA battle arena game.</p>
            <button onclick="playGame('mobile-legends')" class="btn-gaming btn-play w-full px-4 py-3 text-white rounded-xl inline-flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
              </svg>
              Play Now
            </button>
          </div>
        </article>

        <!-- Featured Game Card 4 -->
        <article class="featured-card relative bg-white rounded-2xl shadow-lg overflow-hidden cursor-pointer group">
          <div class="relative overflow-hidden aspect-video">
            <img src="assets/game-free-fire.jpg" alt="Simple Kulino" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <span class="badge badge-new absolute top-3 left-3">New</span>
          </div>
          <div class="p-5">
            <h4 class="font-bold text-lg text-gray-800 mb-2">Simple Kulino</h4>
            <p class="text-sm text-gray-600 mb-4">Tap to win and earn rewards.</p>
            <button onclick="playGame('simple-kulino')" class="btn-gaming btn-play w-full px-4 py-3 text-white rounded-xl inline-flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
              </svg>
              Play Now
            </button>
          </div>
        </article>
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
          <!-- Repeat game cards here -->
          <article class="game-card bg-white rounded-2xl shadow-lg min-w-[280px] md:min-w-[320px] overflow-hidden group">
            <div class="relative overflow-hidden">
              <img src="assets/game-free-fire.jpg" alt="Game" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500" />
              <div class="overlay rounded-t-2xl"></div>
            </div>
            <div class="p-5">
              <h4 class="font-semibold text-lg text-gray-800 mb-2">Simple Kulino Demo</h4>
              <p class="text-sm text-gray-600 mb-4">Tap to win, earn rewards instantly.</p>
              <div class="flex gap-3">
                <button onclick="playGame('simple-kulino')" class="btn-gaming btn-play flex-1 px-4 py-2.5 text-white rounded-lg text-sm">
                  Play
                </button>
                <button class="btn-gaming btn-outline flex-1 px-4 py-2.5 rounded-lg text-sm">
                  Preview
                </button>
              </div>
            </div>
          </article>

          <!-- Add more game cards as needed -->
          <article class="game-card bg-white rounded-2xl shadow-lg min-w-[280px] md:min-w-[320px] overflow-hidden group">
            <div class="relative overflow-hidden">
              <img src="assets/mobile-legends.jpg" alt="Game" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500" />
              <div class="overlay rounded-t-2xl"></div>
            </div>
            <div class="p-5">
              <h4 class="font-semibold text-lg text-gray-800 mb-2">Forest Battle</h4>
              <p class="text-sm text-gray-600 mb-4">Coming soon - Epic battles await.</p>
              <div class="flex gap-3">
                <button class="btn-gaming btn-play flex-1 px-4 py-2.5 text-white rounded-lg text-sm opacity-50 cursor-not-allowed" disabled>
                  Coming Soon
                </button>
              </div>
            </div>
          </article>
        </div>

        <!-- Navigation Buttons -->
        <button onclick="scrollSlider('gamesSlider', -1)" class="nav-btn hidden lg:block absolute top-1/2 -left-5 transform -translate-y-1/2 rounded-full p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button onclick="scrollSlider('gamesSlider', 1)" class="nav-btn hidden lg:block absolute top-1/2 -right-5 transform -translate-y-1/2 rounded-full p-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </section>

    <!-- Unity Preview Section -->
    <section class="mb-12 fade-in">
      <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
          <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
            </svg>
            Unity Game Preview
          </h3>
          <p class="text-white/80 text-sm mt-1">Click Play on any game to load it here</p>
        </div>
        <div id="unityWrap" class="p-6 bg-gray-50">
          <div id="unityContainer" class="w-full h-[500px] bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl flex items-center justify-center">
            <div id="unityPlaceholder" class="text-center">
              <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
              </svg>
              <p class="text-gray-500 font-semibold">Unity Preview Not Loaded</p>
              <p class="text-gray-400 text-sm mt-2">Select a game and click Play to start</p>
            </div>
          </div>
        </div>
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

  </main>

  <!-- Sponsor -->
  <?php include("./includes/sponsor.php"); ?>

  <!-- Footer -->
  <?php include("./includes/footer.php"); ?>

  <!-- Scripts -->
  <script src="./js/script-index.js"></script>

</body>

</html>
