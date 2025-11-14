<?php include("./includes/koneksi.php"); ?>
<?php include("./includes/config.php"); ?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>FAQ - Kulino Game Hub</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- CSS -->
  <link rel="stylesheet" href="./css/style-index.css" />

  <style>
    .faq-item {
      transition: all 0.3s ease;
    }

    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease-out;
    }

    .faq-answer.active {
      max-height: 500px;
      transition: max-height 0.5s ease-in;
    }

    .faq-icon {
      transition: transform 0.3s ease;
    }

    .faq-icon.rotate {
      transform: rotate(180deg);
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-900">

  <!-- Header -->
  <header id="mainHeader" class="bg-white shadow-md sticky top-0 z-40">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">

      <!-- Logo -->
      <a href="<?php echo $base_url; ?>index.php" class="flex items-start gap-3">
        <div class="w-10 h-10">
          <img src="assets/icon/kulino-logo-blue.png" alt="Kulino Logo" class="w-full h-full object-contain" />
        </div>
        <div class="flex flex-col">
          <h1 class="text-base sm:text-lg font-semibold leading-snug">
            Kulino Game Hub
          </h1>
          <p class="text-xs text-gray-500 mt-1">FAQ & Bantuan</p>
        </div>
      </a>

      <!-- Back Button -->
      <a href="index.php" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition text-sm">
        Kembali ke Home
      </a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-10">

    <!-- Hero Section -->
    <section class="mb-10 text-center">
      <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
        Frequently Asked Questions
      </h1>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Temukan jawaban untuk pertanyaan yang sering diajukan tentang Kulino Game Hub, Phantom Wallet, dan cara bermain game untuk mendapatkan reward.
      </p>
    </section>

    <!-- Search Box -->
    <section class="mb-8 max-w-2xl mx-auto">
      <div class="relative">
        <input
          type="text"
          id="searchFAQ"
          placeholder="Cari pertanyaan..."
          class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </section>

    <!-- FAQ Categories -->
    <section class="mb-8">
      <div class="flex flex-wrap gap-3 justify-center">
        <button class="category-btn active px-4 py-2 bg-blue-600 text-white rounded-full text-sm hover:bg-blue-700 transition" data-category="all">
          Semua
        </button>
        <button class="category-btn px-4 py-2 bg-white text-gray-700 rounded-full text-sm hover:bg-gray-100 transition border border-gray-300" data-category="wallet">
          Wallet
        </button>
        <button class="category-btn px-4 py-2 bg-white text-gray-700 rounded-full text-sm hover:bg-gray-100 transition border border-gray-300" data-category="game">
          Game
        </button>
        <button class="category-btn px-4 py-2 bg-white text-gray-700 rounded-full text-sm hover:bg-gray-100 transition border border-gray-300" data-category="reward">
          Reward
        </button>
        <button class="category-btn px-4 py-2 bg-white text-gray-700 rounded-full text-sm hover:bg-gray-100 transition border border-gray-300" data-category="technical">
          Teknis
        </button>
      </div>
    </section>

    <!-- FAQ Items -->
    <section id="faqContainer" class="max-w-4xl mx-auto space-y-4">

      <!-- Wallet Category -->
      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="wallet">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Apa itu Phantom Wallet dan bagaimana cara menginstalnya?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Phantom adalah wallet cryptocurrency berbasis browser untuk Solana blockchain. Untuk menginstalnya:</p>
            <ol class="list-decimal ml-5 space-y-2">
              <li>Kunjungi <a href="https://phantom.app" target="_blank" class="text-blue-600 hover:underline">phantom.app</a></li>
              <li>Klik "Download" dan pilih browser Anda (Chrome, Firefox, Edge, atau Brave)</li>
              <li>Install extension dari Chrome Web Store atau Firefox Add-ons</li>
              <li>Buat wallet baru atau import wallet yang sudah ada</li>
              <li>Simpan recovery phrase Anda dengan aman</li>
            </ol>
          </div>
        </div>
      </div>

      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="wallet">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Bagaimana cara menghubungkan Phantom Wallet ke Kulino?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Ikuti langkah berikut:</p>
            <ol class="list-decimal ml-5 space-y-2">
              <li>Pastikan extension Phantom sudah terinstall dan wallet sudah dibuat</li>
              <li>Buka website Kulino Game Hub</li>
              <li>Klik tombol "Connect Wallet" di header</li>
              <li>Pilih account Phantom Anda jika diminta</li>
              <li>Klik "Connect" pada popup Phantom</li>
              <li>Alamat wallet Anda akan muncul di dashboard</li>
            </ol>
          </div>
        </div>
      </div>

      <!-- Game Category -->
      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="game">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Game apa saja yang tersedia di Kulino?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Saat ini kami memiliki beberapa game unggulan:</p>
            <ul class="list-disc ml-5 space-y-2">
              <li><strong>Free Fire Style Battle</strong> - Game battle royale yang mirip Point Blank</li>
              <li><strong>Brainrot Online</strong> - Game multiplayer casual yang lucu dan unik</li>
              <li><strong>Simple Kulino Demo</strong> - Game sederhana untuk mencoba fitur reward</li>
              <li>Dan masih banyak game lainnya yang terus ditambahkan!</li>
            </ul>
            <p class="mt-3">Setiap game memiliki mekanisme reward yang berbeda-beda.</p>
          </div>
        </div>
      </div>

      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="game">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Bagaimana cara memainkan game di Kulino?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Sangat mudah!</p>
            <ol class="list-decimal ml-5 space-y-2">
              <li>Connect Phantom Wallet Anda terlebih dahulu</li>
              <li>Pilih game yang ingin dimainkan dari daftar game</li>
              <li>Klik tombol "Play" pada card game</li>
              <li>Game akan terbuka di tab baru dengan alamat wallet Anda sudah terhubung</li>
              <li>Mainkan game dan selesaikan misi untuk mendapatkan reward</li>
              <li>Klaim reward Anda setelah menyelesaikan game</li>
            </ol>
          </div>
        </div>
      </div>

      <!-- Reward Category -->
      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="reward">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Bagaimana sistem reward bekerja?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Sistem reward Kulino menggunakan teknologi blockchain Solana:</p>
            <ul class="list-disc ml-5 space-y-2">
              <li>Setiap game memiliki target/misi yang harus diselesaikan</li>
              <li>Ketika Anda menyelesaikan misi, sistem akan mendeteksi pencapaian Anda</li>
              <li>Anda akan diminta untuk menandatangani transaksi melalui Phantom Wallet</li>
              <li>Setelah verifikasi, reward berupa token akan dikirim ke wallet Anda</li>
              <li>Proses ini menggunakan smart contract untuk keamanan</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="reward">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Berapa lama waktu yang dibutuhkan untuk menerima reward?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Reward biasanya diterima dengan sangat cepat:</p>
            <ul class="list-disc ml-5 space-y-2">
              <li><strong>Instant</strong> - Untuk reward kecil (< 1 token)</li>
              <li><strong>1-5 menit</strong> - Untuk reward medium (1-10 token)</li>
              <li><strong>5-10 menit</strong> - Untuk reward besar (> 10 token)</li>
            </ul>
            <p class="mt-3">Kecepatan tergantung pada kondisi network Solana. Anda bisa memeriksa status transaksi di Phantom Wallet atau Solana Explorer.</p>
          </div>
        </div>
      </div>

      <!-- Technical Category -->
      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="technical">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Kenapa game tidak bisa dimuat atau loading terus?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Coba solusi berikut:</p>
            <ul class="list-disc ml-5 space-y-2">
              <li>Pastikan koneksi internet Anda stabil</li>
              <li>Clear cache browser Anda (Ctrl+Shift+Delete)</li>
              <li>Gunakan browser yang didukung: Chrome, Firefox, Edge, atau Brave</li>
              <li>Disable ad blocker atau extension yang mungkin memblokir game</li>
              <li>Refresh halaman atau coba buka game di tab baru</li>
              <li>Update browser Anda ke versi terbaru</li>
            </ul>
            <p class="mt-3">Jika masih bermasalah, hubungi support kami.</p>
          </div>
        </div>
      </div>

      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="technical">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Apakah Kulino aman digunakan?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Ya, keamanan adalah prioritas kami:</p>
            <ul class="list-disc ml-5 space-y-2">
              <li>Kami hanya meminta signature untuk verifikasi, bukan private key</li>
              <li>Semua transaksi melalui Phantom Wallet yang sudah terverifikasi</li>
              <li>Tidak ada akses langsung ke wallet Anda tanpa persetujuan</li>
              <li>Semua komunikasi menggunakan HTTPS encryption</li>
              <li>Smart contract sudah diaudit untuk keamanan</li>
            </ul>
            <p class="mt-3 font-semibold">⚠️ Tips Keamanan: Jangan pernah membagikan recovery phrase atau private key Anda kepada siapapun!</p>
          </div>
        </div>
      </div>

      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="wallet">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Apakah saya perlu SOL untuk bermain?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Tidak, Anda tidak perlu SOL untuk bermain game!</p>
            <p class="mb-3">Yang Anda butuhkan:</p>
            <ul class="list-disc ml-5 space-y-2">
              <li><strong>Untuk bermain:</strong> Gratis, tidak perlu SOL</li>
              <li><strong>Untuk claim reward:</strong> Perlu sedikit SOL untuk gas fee (~0.000005 SOL atau sekitar Rp 0.01)</li>
            </ul>
            <p class="mt-3">Jika wallet Anda baru dan kosong, Anda tetap bisa bermain. Reward akan tertahan sampai Anda memiliki SOL untuk gas fee.</p>
          </div>
        </div>
      </div>

      <div class="faq-item bg-white rounded-lg shadow-md overflow-hidden" data-category="reward">
        <button class="faq-question w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition">
          <span class="font-semibold text-gray-900 pr-4">Apakah ada batas claim reward per hari?</span>
          <svg class="faq-icon w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        <div class="faq-answer">
          <div class="px-6 py-4 text-gray-600 border-t border-gray-100">
            <p class="mb-3">Ya, ada beberapa batasan untuk mencegah abuse:</p>
            <ul class="list-disc ml-5 space-y-2">
              <li>Maksimal 10 claim per game per hari</li>
              <li>Maksimal 50 claim total per wallet per hari</li>
              <li>Cooldown 5 menit antar claim pada game yang sama</li>
            </ul>
            <p class="mt-3">Batasan ini akan direset setiap hari pada pukul 00:00 UTC.</p>
          </div>
        </div>
      </div>

    </section>

    <!-- Contact Support -->
    <section class="mt-16 max-w-3xl mx-auto bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-center text-white shadow-xl">
      <h2 class="text-2xl font-bold mb-4">Masih Ada Pertanyaan?</h2>
      <p class="mb-6 text-blue-100">
        Tim support kami siap membantu Anda! Hubungi kami melalui email atau social media.
      </p>
      <div class="flex flex-wrap gap-4 justify-center">
        <a href="mailto:kulinohouse@gmail.com" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition">
          Email Support
        </a>
        <a href="https://discord.gg/kulino" target="_blank" class="px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-400 transition">
          Join Discord
        </a>
      </div>
    </section>

  </main>

  <!-- Sponsor -->
  <?php include("./includes/sponsor.php"); ?>

  <!-- Footer -->
  <?php include("./includes/footer.php"); ?>

  <script>
    // Toggle FAQ answers
    document.querySelectorAll('.faq-question').forEach(button => {
      button.addEventListener('click', () => {
        const answer = button.nextElementSibling;
        const icon = button.querySelector('.faq-icon');

        answer.classList.toggle('active');
        icon.classList.toggle('rotate');
      });
    });

    // Category filter
    document.querySelectorAll('.category-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const category = btn.dataset.category;

        // Update active button
        document.querySelectorAll('.category-btn').forEach(b => {
          b.classList.remove('active', 'bg-blue-600', 'text-white');
          b.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300');
        });
        btn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-300');
        btn.classList.add('active', 'bg-blue-600', 'text-white');

        // Filter FAQ items
        document.querySelectorAll('.faq-item').forEach(item => {
          if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });

    // Search functionality
    const searchInput = document.getElementById('searchFAQ');
    searchInput.addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase();

      document.querySelectorAll('.faq-item').forEach(item => {
        const question = item.querySelector('.faq-question span').textContent.toLowerCase();
        const answer = item.querySelector('.faq-answer').textContent.toLowerCase();

        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });
  </script>

</body>

</html>