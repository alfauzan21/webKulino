// Konfigurasi build path kalau mau embed/open build
const unityBuildPath = "./WebUnity/index.html";

// Phantom provider + user address
let provider = null;
let userAddress = null;
let unityInstance = null;

// Deteksi apakah menggunakan mobile device
function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Dapatkan deep link untuk membuka Phantom app
function getPhantomDeepLink() {
  const currentUrl = encodeURIComponent(window.location.href);
  const appUrl = `https://phantom.app/ul/browse/${currentUrl}?ref=${currentUrl}`;
  return appUrl;
}

function shortAddr(addr) {
  if (!addr) return "-";
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

// Connect Phantom dengan deteksi mobile
async function connectWallet() {
  const isMobile = isMobileDevice();
  
  try {
    const anyWin = window;
    
    // ===== KHUSUS MOBILE =====
    if (isMobile) {
      // Cek apakah di dalam Phantom in-app browser
      const isPhantomApp = anyWin.phantom?.solana?.isPhantom;
      
      if (!isPhantomApp) {
        // User di mobile browser biasa, bukan di Phantom app
        Swal.fire({
          icon: 'info',
          title: 'Open in Phantom App',
          html: `
            <div class="text-left">
              <p class="mb-3">Untuk connect wallet di mobile, Anda perlu membuka website ini di aplikasi Phantom.</p>
              <p class="mb-2 font-semibold">Pilihan:</p>
              <ol class="list-decimal list-inside space-y-2 text-sm">
                <li>Klik tombol di bawah untuk membuka di Phantom App (Jika sudah terinstall)</li>
                <li>Download Phantom App terlebih dahulu jika belum punya</li>
              </ol>
            </div>
          `,
          showCancelButton: true,
          showDenyButton: true,
          confirmButtonText: '<i class="fas fa-external-link-alt"></i> Open in Phantom',
          denyButtonText: '<i class="fas fa-download"></i> Download Phantom',
          cancelButtonText: 'Cancel',
          confirmButtonColor: '#667eea',
          denyButtonColor: '#10b981',
          cancelButtonColor: '#64748b',
        }).then((result) => {
          if (result.isConfirmed) {
            // Buka di Phantom app
            const deepLink = getPhantomDeepLink();
            window.location.href = deepLink;
            
            // Fallback jika deep link tidak bekerja
            setTimeout(() => {
              Swal.fire({
                icon: 'question',
                title: 'App tidak terbuka?',
                text: 'Pastikan Phantom app sudah terinstall di device Anda',
                confirmButtonText: 'Download Phantom',
                confirmButtonColor: '#10b981',
              }).then((res) => {
                if (res.isConfirmed) {
                  // Deteksi OS untuk link download yang tepat
                  const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
                  const downloadUrl = isIOS 
                    ? 'https://apps.apple.com/us/app/phantom-solana-wallet/id1598432977'
                    : 'https://play.google.com/store/apps/details?id=app.phantom';
                  window.open(downloadUrl, '_blank');
                }
              });
            }, 2000);
            
          } else if (result.isDenied) {
            // Download Phantom app
            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const downloadUrl = isIOS 
              ? 'https://apps.apple.com/us/app/phantom-solana-wallet/id1598432977'
              : 'https://play.google.com/store/apps/details?id=app.phantom';
            
            window.open(downloadUrl, '_blank');
            
            // Instruksi setelah download
            Swal.fire({
              icon: 'info',
              title: 'Langkah Selanjutnya',
              html: `
                <div class="text-left text-sm space-y-2">
                  <p>Setelah install Phantom app:</p>
                  <ol class="list-decimal list-inside space-y-1 ml-2">
                    <li>Buka aplikasi Phantom</li>
                    <li>Login atau buat wallet baru</li>
                    <li>Di dalam app Phantom, buka browser</li>
                    <li>Akses website ini dari dalam app</li>
                    <li>Connect wallet akan otomatis tersedia</li>
                  </ol>
                </div>
              `,
              confirmButtonText: 'Mengerti',
              confirmButtonColor: '#667eea',
            });
          }
        });
        return;
      }
      
      // Jika sudah di dalam Phantom app, lanjutkan connect
      provider = anyWin.phantom.solana;
    } 
    // ===== DESKTOP =====
    else {
      // Cek apakah Phantom extension terinstall
      if (!anyWin.phantom || !anyWin.phantom.solana || !anyWin.phantom.solana.isPhantom) {
        Swal.fire({
          icon: 'warning',
          title: 'Phantom Wallet Not Found',
          html: `
            <p>Phantom wallet extension tidak terdeteksi.</p>
            <p class="mt-2">Silakan install Phantom Wallet terlebih dahulu:</p>
          `,
          showCancelButton: true,
          confirmButtonText: 'Install Phantom',
          cancelButtonText: 'Cancel',
          confirmButtonColor: '#667eea',
          cancelButtonColor: '#64748b',
        }).then((result) => {
          if (result.isConfirmed) {
            window.open('https://phantom.app/', '_blank');
          }
        });
        return;
      }
      
      provider = anyWin.phantom.solana;
    }

    // Tampilkan loading
    Swal.fire({
      title: 'Connecting to Phantom...',
      html: isMobile 
        ? 'Please approve the connection in the app' 
        : 'Please approve the connection in your Phantom wallet',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Connect akan memunculkan popup/dialog Phantom
    const resp = await provider.connect();
    userAddress = resp.publicKey.toString();
    
    // Update UI
    document.getElementById("walletStatus").innerText = "Connected";
    document.getElementById("addrShort").innerText = shortAddr(userAddress);
    
    const connectBtn = document.getElementById("connectBtn");
    const connectBtnMobile = document.getElementById("connectBtnMobile");
    
    if (connectBtn) {
      connectBtn.innerHTML = `
        <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Connected
      `;
      connectBtn.disabled = true;
      connectBtn.classList.add('opacity-75', 'cursor-not-allowed');
    }
    
    if (connectBtnMobile) {
      connectBtnMobile.innerHTML = `
        <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        Connected
      `;
      connectBtnMobile.disabled = true;
      connectBtnMobile.classList.add('opacity-75', 'cursor-not-allowed');
    }

    // Kirim ke Unity jika tersedia
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage("GameManager", "OnWalletConnected", userAddress);
    }

    // Success notification
    Swal.fire({
      icon: 'success',
      title: 'Wallet Connected!',
      html: `
        <p class="text-sm text-gray-600">Your wallet address:</p>
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-3 rounded-lg mt-2">
          <code class="text-xs font-mono text-indigo-600">${shortAddr(userAddress)}</code>
        </div>
        <p class="text-sm text-gray-500 mt-3">You can now play games and claim rewards!</p>
      `,
      showConfirmButton: true,
      confirmButtonText: 'Start Playing',
      confirmButtonColor: '#667eea',
      timer: 3000
    });

  } catch (err) {
    console.error("connectWallet error:", err);
    
    // User membatalkan atau error
    if (err.message?.includes('User rejected') || err.code === 4001) {
      Swal.fire({
        icon: 'info',
        title: 'Connection Cancelled',
        text: 'You cancelled the wallet connection',
        confirmButtonColor: '#667eea',
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Connection Failed',
        text: err.message || 'Failed to connect to Phantom wallet',
        confirmButtonColor: '#ef4444',
      });
    }
  }
}

// Unity akan memanggil window.requestReward(jsonPayload)
async function requestReward(jsonPayload) {
  console.log("Unity requested reward:", jsonPayload);
  const payload = JSON.parse(jsonPayload);

  if (!provider || !userAddress) {
    Swal.fire({
      icon: 'warning',
      title: 'Wallet Not Connected',
      text: 'Please connect your Phantom wallet first',
      confirmButtonColor: '#667eea',
    });
    
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage(
        "GameManager",
        "OnClaimResult",
        JSON.stringify({ success: false, error: "wallet_not_connected" })
      );
    }
    return;
  }

  // Tampilkan loading
  Swal.fire({
    title: 'Processing Reward...',
    html: 'Please sign the message in your Phantom wallet',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  const messageObj = { ...payload, address: userAddress, ts: Date.now() };
  const messageStr = JSON.stringify(messageObj);
  const encoded = new TextEncoder().encode(messageStr);

  try {
    const signed = await provider.signMessage(encoded, "utf8");
    const signatureBase58 = bs58.encode(signed.signature);

    const res = await fetch("/api/claim", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        message: messageStr,
        signature: signatureBase58,
        publicKey: signed.publicKey.toString(),
      }),
    });

    const result = await res.json();
    
    // Kirim hasil ke Unity
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage("GameManager", "OnClaimResult", JSON.stringify(result));
    }

    // Tampilkan hasil
    if (result.success) {
      Swal.fire({
        icon: 'success',
        title: 'Reward Claimed!',
        html: `
          <p class="text-lg font-semibold text-green-600">🎉 Congratulations!</p>
          <p class="text-sm text-gray-600 mt-2">Your reward has been successfully claimed</p>
        `,
        confirmButtonColor: '#10b981',
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Claim Failed',
        text: result.error || 'Failed to claim reward',
        confirmButtonColor: '#ef4444',
      });
    }
  } catch (err) {
    console.error("requestReward error:", err);
    
    Swal.fire({
      icon: 'error',
      title: 'Transaction Failed',
      text: err.message || 'Failed to process reward claim',
      confirmButtonColor: '#ef4444',
    });
    
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage(
        "GameManager",
        "OnClaimResult",
        JSON.stringify({ success: false, error: err.message || String(err) })
      );
    }
  }
}

// Expose functions ke global
window.requestReward = requestReward;
window.connectWallet = connectWallet;

// Set Unity instance
function setUnityInstance(instance) {
  console.log("setUnityInstance called");
  unityInstance = instance;
  if (userAddress && unityInstance && typeof unityInstance.SendMessage === "function") {
    unityInstance.SendMessage("GameManager", "OnWalletConnected", userAddress);
  }
}
window.setUnityInstance = setUnityInstance;

// Play game
function playGame(gameId) {
  if (!userAddress) {
    Swal.fire({
      icon: 'warning',
      title: 'Connect Wallet First',
      text: 'Please connect your Phantom wallet before playing',
      showCancelButton: true,
      confirmButtonText: 'Connect Now',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#667eea',
      cancelButtonColor: '#64748b',
    }).then((result) => {
      if (result.isConfirmed) {
        connectWallet();
      }
    });
    return;
  }

  const url = new URL(unityBuildPath, window.location.href);
  url.searchParams.set("wallet", userAddress);
  url.searchParams.set("game", gameId);
  window.open(url.toString(), "_blank");
}

function previewGame(path) {
  const url = new URL(path, window.location.href);
  window.open(url.toString(), "_blank");
}

function scrollSlider(id, direction) {
  const slider = document.getElementById(id);
  const scrollAmount = 320;
  slider.scrollBy({ left: direction * scrollAmount, behavior: "smooth" });
}

// ====== VISITOR TRACKING - UPDATED ======
// Tracking menggunakan localStorage untuk persistent per browser
// Sistem akan track SETIAP page view, bukan hanya unique visitor

async function trackVisitor() {
  try {
    // Selalu tambah visitor baru setiap kali halaman dibuka
    const res = await fetch("track.php?add=1");
    const data = await res.json();
    
    // Update tampilan total visitor
    const visitorElement = document.getElementById("visitorCount");
    if (visitorElement) {
      visitorElement.innerText = data.today || 0;
    }
    
    console.log("✅ Visitor tracked successfully. Total today:", data.today);
  } catch (error) {
    console.error("❌ Failed to track visitor:", error);
    const visitorElement = document.getElementById("visitorCount");
    if (visitorElement) {
      visitorElement.innerText = "-";
    }
  }
}

async function updateVisitorCount() {
  try {
    // Fetch count tanpa menambah visitor baru
    const res = await fetch("track.php");
    const data = await res.json();
    
    const visitorElement = document.getElementById("visitorCount");
    if (visitorElement) {
      visitorElement.innerText = data.today || 0;
    }
  } catch (error) {
    console.error("Failed to update visitor count:", error);
  }
}

// Init on page load
window.addEventListener('DOMContentLoaded', () => {
  // Track visitor setiap kali halaman dibuka
  trackVisitor();
  
  // Update count setiap 30 detik (untuk melihat visitor baru dari user lain)
  setInterval(updateVisitorCount, 30000);
  
  // Auto-detect jika sudah di Phantom app dan belum connect
  const isMobile = isMobileDevice();
  const isPhantomApp = window.phantom?.solana?.isPhantom;
  
  if (isMobile && isPhantomApp && !userAddress) {
    console.log("✅ Phantom app detected - Ready to connect");
  }
});

// Featured card video preview
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll(".featured-card").forEach((card) => {
    const video = card.querySelector("video");
    if (video) {
      card.addEventListener("mouseenter", () => video.play());
      card.addEventListener("mouseleave", () => {
        video.pause();
        video.currentTime = 0;
      });
    }
  });
});

// Header scroll effect
const header = document.getElementById("mainHeader");
if (header) {
  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.classList.remove("bg-white/50", "backdrop-blur");
      header.classList.add("bg-white", "shadow-md");
    } else {
      header.classList.add("bg-white/50", "backdrop-blur");
      header.classList.remove("bg-white", "shadow-md");
    }
  });
}

// Mobile menu toggle
const menuToggle = document.getElementById("menuToggle");
const mobileMenu = document.getElementById("mobileMenu");

if (menuToggle && mobileMenu) {
  menuToggle.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
    menuToggle.classList.toggle("open");
  });
}

// Connect button event listeners
document.getElementById("connectBtn")?.addEventListener("click", connectWallet);
document.getElementById("connectBtnMobile")?.addEventListener("click", connectWallet);