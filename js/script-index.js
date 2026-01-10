// ==================== CONFIGURATION ====================
const KULINO_TOKEN_MINT = "E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1";

// 🔧 FIX: Multiple RPC endpoints dengan fallback
const SOLANA_RPC_ENDPOINTS = [
  "https://api.mainnet-beta.solana.com", // Public endpoint - best compatibility
  "https://solana-api.syndica.io/access-token/HGlwRGMqfQ8LoQWRn83x48fcq4UmTXKT5bLrPqJfpnvPpJtw47wf0nWKd62B46Uo/rpc", // Syndica free tier
  "https://rpc.helius.xyz/?api-key=", // Helius free (works without key for basic calls)
  "https://solana.public-rpc.com", // Community RPC
];

let currentRPCIndex = 0;

// Function untuk mendapatkan RPC connection dengan retry
function getConnection() {
  const rpcUrl = SOLANA_RPC_ENDPOINTS[currentRPCIndex];
  console.log(`🔗 Using RPC: ${rpcUrl.substring(0, 50)}...`);

  return new solanaWeb3.Connection(rpcUrl, {
    commitment: "confirmed",
    confirmTransactionInitialTimeout: 30000, // Reduced from 60000
  });
}

// Function untuk retry dengan RPC endpoint berikutnya
async function retryWithNextRPC(fn) {
  const maxRetries = SOLANA_RPC_ENDPOINTS.length;
  let lastError;

  for (let i = 0; i < maxRetries; i++) {
    try {
      return await fn();
    } catch (error) {
      lastError = error;

      // Only log if it's a real error, not just switching RPC
      if (!error.message?.includes("Failed to fetch")) {
        console.warn(
          `⚠️ RPC ${SOLANA_RPC_ENDPOINTS[currentRPCIndex].substring(
            0,
            40
          )}... failed:`,
          error.message
        );
      }

      currentRPCIndex = (currentRPCIndex + 1) % SOLANA_RPC_ENDPOINTS.length;

      if (i < maxRetries - 1) {
        console.log(
          `🔄 Switching to RPC ${currentRPCIndex + 1}/${
            SOLANA_RPC_ENDPOINTS.length
          }`
        );
        // Add small delay between retries
        await new Promise((resolve) => setTimeout(resolve, 500));
      }
    }
  }

  console.error("❌ All RPC endpoints failed");
  throw lastError;
}

const WALLET_STORAGE_KEY = "kulino_connected_wallet";
const SESSION_DURATION = 30 * 24 * 60 * 60 * 1000; // 30 days

// ==================== GLOBAL STATE ====================
let provider = null;
let userAddress = null;
let kulinoBalance = 0;
let solBalance = 0;

console.log("🚀 Kulino Script Starting...");

// Utility Functions
function shortAddr(addr) {
  if (!addr) return "-";
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

function showToast(message, type = "info") {
  const colors = {
    success: "bg-green-500",
    error: "bg-red-500",
    info: "bg-blue-500",
  };

  const toast = document.createElement("div");
  toast.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 300);
  }, 2700);
}

// Connect Wallet
async function connectWallet() {
  try {
    if (!window.phantom?.solana?.isPhantom) {
      Swal.fire({
        icon: "warning",
        title: "Phantom Not Installed",
        text: "Please install the Phantom Wallet extension",
        confirmButtonColor: "#3b82f6",
      });
      return;
    }

    const provider = window.phantom.solana;
    const resp = await provider.connect();
    const address = resp.publicKey.toString();

    userAddress = address;
    updateConnectedUI(address);

    Swal.fire({
      icon: "success",
      title: "Connected!",
      html: `<div class="text-sm"><code>${shortAddr(address)}</code></div>`,
      confirmButtonColor: "#3b82f6",
      timer: 3000,
    });
  } catch (err) {
    console.error("Connect error:", err);
    Swal.fire({
      icon: "error",
      title: "Connection Failed",
      text: err.message || "Failed to connect wallet",
      confirmButtonColor: "#ef4444",
    });
  }
}

function updateConnectedUI(address) {
  document.getElementById("walletStatus").innerText = "Connected ✓";
  document.getElementById("addrShort").innerText = shortAddr(address);
  document.getElementById("disconnectBtn").classList.remove("hidden");

  const connectBtn = document.getElementById("connectBtn");
  const connectBtnMobile = document.getElementById("connectBtnMobile");

  if (connectBtn)
    connectBtn.innerHTML = '<span class="font-semibold">Connected</span>';
  if (connectBtnMobile)
    connectBtnMobile.innerHTML = '<span class="font-semibold">Connected</span>';

  // Update swap button
  document.getElementById("swapBtn").innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
        </svg>
        Swap Tokens
      `;
}

function disconnectWallet() {
  userAddress = null;
  document.getElementById("walletStatus").innerText = "Not Connected";
  document.getElementById("addrShort").innerText = "-";
  document.getElementById("disconnectBtn").classList.add("hidden");

  showToast("Wallet disconnected", "success");
}

function updateBalanceDisplay() {
  // Placeholder - implement actual balance fetching
  showToast("Balance updated", "success");
}

function swapTokens() {
  const temp = document.getElementById("fromAmount").value;
  document.getElementById("fromAmount").value =
    document.getElementById("toAmount").value;
  document.getElementById("toAmount").value = temp;
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("connectBtn")
    ?.addEventListener("click", connectWallet);
  document
    .getElementById("connectBtnMobile")
    ?.addEventListener("click", connectWallet);

  // Mobile menu toggle
  document.getElementById("menuToggle")?.addEventListener("click", () => {
    document.getElementById("mobileMenu").classList.toggle("hidden");
  });

  // Auto-calculate swap
  document.getElementById("fromAmount")?.addEventListener("input", (e) => {
    const value = parseFloat(e.target.value) || 0;
    document.getElementById("toAmount").value = (value * 1.02).toFixed(2); // Example conversion
  });
});

// Expose functions globally
window.connectWallet = connectWallet;
window.disconnectWallet = disconnectWallet;
window.updateBalanceDisplay = updateBalanceDisplay;
window.swapTokens = swapTokens;

// ==================== UTILITY FUNCTIONS ====================
function shortAddr(addr) {
  if (!addr) return "-";
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

function formatKulinoBalance(balance) {
  if (balance >= 1000000) return (balance / 1000000).toFixed(2) + "M";
  if (balance >= 1000) return (balance / 1000).toFixed(2) + "K";
  return balance.toFixed(2);
}

function formatSOLBalance(balance) {
  return balance.toFixed(4);
}

function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
    navigator.userAgent
  );
}

function showToast(message, type = "info") {
  const colors = {
    success: "bg-green-500",
    error: "bg-red-500",
    info: "bg-blue-500",
    warning: "bg-yellow-500",
  };

  const toast = document.createElement("div");
  toast.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 300);
  }, 2700);
}

// ==================== LOCATION TRACKING SYSTEM ====================
let locationGranted = false;
let userLocation = null;

// Check if location was previously granted
function checkLocationStatus() {
  const locationData = sessionStorage.getItem("kulino_location_granted");

  if (locationData) {
    const data = JSON.parse(locationData);
    const timeSince = Date.now() - data.timestamp;

    // Location valid for 24 hours
    if (timeSince < 24 * 60 * 60 * 1000) {
      console.log("✅ Location already granted (from session)");
      userLocation = data.location;
      showMainContent();
      trackVisitorWithLocation(data.location);
      return true;
    }
  }

  return false;
}

function showLocationAlert() {
  console.log("📍 Showing location alert");
  
  const alert = document.getElementById("locationAlert");
  const overlay = document.getElementById("locationOverlay");
  const mainContent = document.getElementById("mainContent");
  
  if (alert) {
    alert.classList.remove("hidden");
    overlay.classList.remove("hidden");
    mainContent.classList.add("blurred");
  }
}

function hideLocationAlert() {
  const alert = document.getElementById("locationAlert");
  const overlay = document.getElementById("locationOverlay");
  const mainContent = document.getElementById("mainContent");
  
  if (alert) {
    alert.classList.add("hidden");
    overlay.classList.add("hidden");
    mainContent.classList.remove("blurred");
  }
}

function showMainContent() {
  hideLocationAlert();
  document.getElementById("mainContent").classList.add("active");
  console.log("✅ Main content displayed");
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 Initializing location system...");

  // Check if location already granted
  if (!checkLocationStatus()) {
    // Show location alert instead of fullscreen overlay
    showLocationAlert();
    console.log("⚠️ Location not granted, showing alert");
  }
});

// Request location permission
async function requestLocationPermission() {
  console.log("📍 Requesting location permission...");

  Swal.fire({
    title: "Meminta Izin Lokasi...",
    html: "Mohon izinkan akses lokasi pada popup browser Anda",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  if (!navigator.geolocation) {
    Swal.fire({
      icon: "error",
      title: "Browser Tidak Mendukung",
      text: "Browser Anda tidak mendukung geolocation. Mohon gunakan browser modern.",
      confirmButtonColor: "#ef4444",
    });
    return;
  }

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      console.log("✅ Location granted:", position);

      const coords = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        accuracy: position.coords.accuracy,
      };

      Swal.update({
        html: "Mendapatkan detail lokasi...",
      });

      // Get detailed address using Nominatim (OpenStreetMap)
      try {
        const response = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=json&lat=${coords.latitude}&lon=${coords.longitude}&zoom=18&addressdetails=1`,
          {
            headers: {
              "User-Agent": "KulinoGameHub/1.0",
            },
          }
        );

        const data = await response.json();
        console.log("🗺️ Location data:", data);

        const address = data.address || {};
        userLocation = {
          latitude: coords.latitude,
          longitude: coords.longitude,
          accuracy: coords.accuracy,
          street: address.road || address.pedestrian || "",
          houseNumber: address.house_number || "",
          city:
            address.city ||
            address.town ||
            address.village ||
            address.county ||
            "",
          region: address.state || address.province || "",
          country: address.country || "",
          countryCode: address.country_code?.toUpperCase() || "",
          postalCode: address.postcode || "",
          fullAddress: data.display_name || "",
          timestamp: Date.now(),
        };

        // Save to session
        sessionStorage.setItem(
          "kulino_location_granted",
          JSON.stringify({
            location: userLocation,
            timestamp: Date.now(),
          })
        );

        locationGranted = true;

        Swal.fire({
          icon: "success",
          title: "Lokasi Terdeteksi!",
          html: `
            <div class="text-left bg-green-50 rounded-lg p-4 mt-3">
              <p class="text-sm font-semibold text-green-800 mb-2">📍 Detail Lokasi:</p>
              <p class="text-sm text-green-700">
                ${
                  userLocation.street
                    ? userLocation.houseNumber +
                      " " +
                      userLocation.street +
                      "<br>"
                    : ""
                }
                ${userLocation.city}, ${userLocation.region}<br>
                ${userLocation.country} (${userLocation.countryCode})
              </p>
            </div>
          `,
          confirmButtonText: "Lanjutkan",
          confirmButtonColor: "#10b981",
          timer: 3000,
        }).then(() => {
          showMainContent();
          trackVisitorWithLocation(userLocation);
        });
      } catch (error) {
        console.error("❌ Geocoding error:", error);

        // Fallback: use basic location data
        userLocation = {
          latitude: coords.latitude,
          longitude: coords.longitude,
          accuracy: coords.accuracy,
          city: "Unknown",
          country: "Unknown",
          countryCode: "XX",
          timestamp: Date.now(),
        };

        sessionStorage.setItem(
          "kulino_location_granted",
          JSON.stringify({
            location: userLocation,
            timestamp: Date.now(),
          })
        );

        locationGranted = true;

        Swal.fire({
          icon: "success",
          title: "Lokasi Terdeteksi!",
          text: "Lokasi dasar berhasil dideteksi",
          confirmButtonText: "Lanjutkan",
          confirmButtonColor: "#10b981",
          timer: 2000,
        }).then(() => {
          showMainContent();
          trackVisitorWithLocation(userLocation);
        });
      }
    },
    (error) => {
      console.error("❌ Location error:", error);

      let errorMessage = "";
      switch (error.code) {
        case error.PERMISSION_DENIED:
          errorMessage =
            "Anda menolak akses lokasi. Mohon izinkan akses lokasi untuk melanjutkan.";
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage = "Informasi lokasi tidak tersedia. Mohon coba lagi.";
          break;
        case error.TIMEOUT:
          errorMessage = "Permintaan lokasi timeout. Mohon coba lagi.";
          break;
        default:
          errorMessage = "Terjadi kesalahan saat mendapatkan lokasi.";
      }

      Swal.fire({
        icon: "error",
        title: "Gagal Mendapatkan Lokasi",
        text: errorMessage,
        confirmButtonText: "Coba Lagi",
        confirmButtonColor: "#667eea",
      });
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0,
    }
  );
}

// Show main content
function showMainContent() {
  document.getElementById("locationBlockOverlay").classList.remove("active");
  document.getElementById("mainContent").classList.add("active");
  console.log("✅ Main content displayed");
}

// Track visitor with location
// Track visitor with location - IMPROVED VERSION
async function trackVisitorWithLocation(location) {
  console.log("📊 Tracking visitor with GPS location:", location);

  // Build tracking data with GPS coordinates
  const trackData = {
    add: 1,
    latitude: location.latitude || 0,
    longitude: location.longitude || 0,
    accuracy: location.accuracy || 0,
    // Don't send frontend geocoding - let backend handle it
    // Backend will use Nominatim for accurate reverse geocoding
  };

  const params = new URLSearchParams(trackData);

  try {
    console.log("📡 Sending tracking request with GPS data...");

    const response = await fetch(`track.php?${params.toString()}`, {
      method: "GET",
      credentials: "same-origin",
      headers: {
        Accept: "application/json",
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const data = await response.json();

    if (data.error) {
      throw new Error(data.error);
    }

    console.log("✅ Visitor tracked successfully:", data);

    // Update visitor count
    if (data.today !== undefined) {
      const countEl = document.getElementById("visitorCount");
      if (countEl) countEl.textContent = data.today;
    }

    // Store successful tracking
    sessionStorage.setItem("tracking_success", Date.now().toString());
  } catch (error) {
    console.error("❌ Tracking failed:", error);

    // Retry after 2 seconds
    setTimeout(() => {
      console.log("🔄 Retrying tracking request...");
      fetch(`track.php?${params.toString()}`, {
        method: "GET",
        credentials: "same-origin",
      })
        .then((res) => res.json())
        .then((data) => console.log("✅ Retry successful:", data))
        .catch((e) => console.error("❌ Retry also failed:", e));
    }, 2000);
  }
}

// Request location permission - IMPROVED VERSION
async function requestLocationPermission() {
  console.log("📍 Requesting location permission...");

  Swal.fire({
    title: "Meminta Izin Lokasi...",
    html: "Mohon izinkan akses lokasi pada popup browser Anda",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  if (!navigator.geolocation) {
    Swal.fire({
      icon: "error",
      title: "Browser Tidak Mendukung",
      text: "Browser Anda tidak mendukung geolocation. Mohon gunakan browser modern.",
      confirmButtonColor: "#ef4444",
    });
    return;
  }

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      console.log("✅ Location granted:", position);

      const coords = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        accuracy: position.coords.accuracy,
      };

      Swal.update({
        html: "Menyimpan lokasi Anda...",
      });

      // Show basic location info
      userLocation = {
        latitude: coords.latitude,
        longitude: coords.longitude,
        accuracy: coords.accuracy,
        timestamp: Date.now(),
      };

      // Save to session
      sessionStorage.setItem(
        "kulino_location_granted",
        JSON.stringify({
          location: userLocation,
          timestamp: Date.now(),
        })
      );

      locationGranted = true;

      Swal.fire({
        icon: "success",
        title: "Lokasi Terdeteksi!",
        html: `
          <div class="text-left bg-green-50 rounded-lg p-4 mt-3">
            <p class="text-sm font-semibold text-green-800 mb-2">📍 GPS Coordinates:</p>
            <p class="text-sm text-green-700">
              Latitude: ${coords.latitude.toFixed(6)}<br>
              Longitude: ${coords.longitude.toFixed(6)}<br>
              Accuracy: ~${Math.round(coords.accuracy)}m
            </p>
            <p class="text-xs text-green-600 mt-2">
              ✓ Lokasi detail akan diproses oleh server
            </p>
          </div>
        `,
        confirmButtonText: "Lanjutkan",
        confirmButtonColor: "#10b981",
        timer: 3000,
      }).then(() => {
        showMainContent();
        // Send GPS coordinates to backend
        trackVisitorWithLocation(userLocation);
      });
    },
    (error) => {
      console.error("❌ Location error:", error);

      let errorMessage = "";
      switch (error.code) {
        case error.PERMISSION_DENIED:
          errorMessage =
            "Anda menolak akses lokasi. Mohon izinkan akses lokasi untuk melanjutkan.";
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage = "Informasi lokasi tidak tersedia. Mohon coba lagi.";
          break;
        case error.TIMEOUT:
          errorMessage = "Permintaan lokasi timeout. Mohon coba lagi.";
          break;
        default:
          errorMessage = "Terjadi kesalahan saat mendapatkan lokasi.";
      }

      Swal.fire({
        icon: "error",
        title: "Gagal Mendapatkan Lokasi",
        text: errorMessage,
        confirmButtonText: "Coba Lagi",
        confirmButtonColor: "#667eea",
      });
    },
    {
      enableHighAccuracy: true, // Use GPS for better accuracy
      timeout: 10000,
      maximumAge: 0,
    }
  );
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 Initializing location system...");

  // Check if location already granted
  if (!checkLocationStatus()) {
    // Show location block overlay
    document.getElementById("locationBlockOverlay").classList.add("active");
    console.log("⚠️ Location not granted, showing overlay");
  }
});

// ==================== BALANCE FUNCTIONS (FIXED) ====================
// ==================== IMPROVED BALANCE FUNCTIONS ====================
async function getSOLBalance(walletAddress) {
  try {
    console.log("📊 Fetching SOL for balance for:",shortAddr(walletAddress));

    return await retryWithNextRPC(async () => {
      const connection = getConnection();
      const pubkey = new solanaWeb3.PublicKey(walletAddress);
      
      const balance = await Promise.race([
        connection.getBalance(pubkey),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error("Timeout")), 10000)
        ),
      ]);

      const solAmount = balance / solanaWeb3.LAMPORTS_PER_SOL;
      console.log("✅ SOL balance:", solAmount);
      return solAmount;
    });
  } catch (error) {
    console.error("❌ SOL fetch error:", error.message);
    return 0;
  }
}

async function getKulinoBalance(walletAddress) {
  try {
    console.log("📊 Fetching Kulino balance for:", shortAddr(walletAddress));

    return await retryWithNextRPC(async () => {
      const connection = getConnection();
      const pubkey = new solanaWeb3.PublicKey(walletAddress);
      const tokenMint = new solanaWeb3.PublicKey(KULINO_TOKEN_MINT);

      const tokenAccounts = await Promise.race([
        connection.getParsedTokenAccountsByOwner(pubkey, { mint: tokenMint }),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error("Timeout")), 10000)
        ),
      ]);

      if (tokenAccounts.value.length > 0) {
        const balance =
          tokenAccounts.value[0].account.data.parsed.info.tokenAmount
            .uiAmount || 0;
        console.log("✅ Kulino balance:", balance);
        return balance;
      }

      console.log("ℹ️ No Kulino tokens found");
      return 0;
    });
  } catch (error) {
    console.error("❌ Kulino fetch error:", error.message);
    return 0;
  }
}

// ==================== IMPROVED UPDATE BALANCE ====================
async function updateBalanceDisplay(address = userAddress) {
  if (!address) {
    console.log("⚠️ No address to update balance");
    return;
  }

  const kulinoEl = document.getElementById("kulinoBalance");
  const solEl = document.getElementById("solBalance");
  const refreshBtn = document.getElementById("refreshBalanceBtn");

  // Show loading
  if (kulinoEl) {
    kulinoEl.innerHTML =
      '<div class="inline-flex items-center gap-2"><span class="inline-block w-4 h-4 border-2 border-yellow-300 border-t-transparent rounded-full animate-spin"></span><span class="text-sm">Loading...</span></div>';
  }
  if (solEl) {
    solEl.innerHTML =
      '<span class="inline-block w-3 h-3 border-2 border-gray-300 border-t-transparent rounded-full animate-spin"></span>';
  }
  if (refreshBtn) {
    refreshBtn.disabled = true;
    refreshBtn.classList.add("opacity-50", "cursor-not-allowed");
  }

  try {
    console.log("🔄 Starting balance fetch...");

    // Fetch both balances
    const [kulinoResult, solResult] = await Promise.allSettled([
      getKulinoBalance(address),
      getSOLBalance(address),
    ]);

    // Handle results
    kulinoBalance =
      kulinoResult.status === "fulfilled" ? kulinoResult.value : 0;
    solBalance = solResult.status === "fulfilled" ? solResult.value : 0;

    // Update UI
    if (kulinoEl) {
      if (kulinoBalance > 0) {
        kulinoEl.innerHTML = `<strong class="text-2xl">${formatKulinoBalance(
          kulinoBalance
        )}</strong> <span class="text-sm">KULINO</span>`;
      } else {
        kulinoEl.innerHTML =
          '<strong class="text-xl">0.00</strong> <span class="text-sm">KULINO</span>';
      }
    }

    if (solEl) {
      solEl.textContent =
        solBalance > 0 ? `${formatSOLBalance(solBalance)} SOL` : "0.0000 SOL";
    }

    // Show appropriate message
    if (
      kulinoResult.status === "fulfilled" &&
      solResult.status === "fulfilled"
    ) {
      console.log("✅ All balances updated successfully");
      showToast("Balances updated!", "success");
    } else if (
      kulinoResult.status === "fulfilled" ||
      solResult.status === "fulfilled"
    ) {
      console.log("⚠️ Partial balance update");
      showToast("Some balances updated", "info");
    } else {
      console.error("❌ All balance fetches failed");
      showToast("Could not fetch balances", "warning");
    }

    // Update swap UI
    const kulinoBalanceDisplay = document.getElementById(
      "kulinoBalanceDisplay"
    );
    if (kulinoBalanceDisplay) {
      kulinoBalanceDisplay.textContent = formatKulinoBalance(kulinoBalance);
    }
  } catch (error) {
    console.error("❌ Balance update error:", error);

    if (kulinoEl)
      kulinoEl.innerHTML =
        '<strong>--</strong> <span class="text-sm">KULINO</span>';
    if (solEl) solEl.textContent = "-- SOL";

    showToast("Balance fetch failed", "error");
  } finally {
    if (refreshBtn) {
      refreshBtn.disabled = false;
      refreshBtn.classList.remove("opacity-50", "cursor-not-allowed");
    }
  }
}

// ==================== IMPROVED ERROR HANDLING ====================

// Override console.error to reduce spam
const originalConsoleError = console.error;
const errorCache = new Set();

console.error = function (...args) {
  const errorMsg = args.join(" ");

  // Filter out repetitive RPC errors
  if (errorMsg.includes("RPC") || errorMsg.includes("Failed to fetch")) {
    const errorKey = errorMsg.substring(0, 50);
    if (errorCache.has(errorKey)) {
      return; // Skip duplicate errors
    }
    errorCache.add(errorKey);

    // Clear cache after 5 seconds
    setTimeout(() => errorCache.delete(errorKey), 5000);
  }

  originalConsoleError.apply(console, args);
};

// ==================== UI UPDATE ====================
function updateConnectedUI(address) {
  console.log("🔄 Updating UI for:", shortAddr(address));
  userAddress = address;

  const walletStatus = document.getElementById("walletStatus");
  const addrShort = document.getElementById("addrShort");
  const disconnectBtn = document.getElementById("disconnectBtn");

  if (walletStatus) walletStatus.innerText = "Connected ✓";
  if (addrShort) addrShort.innerText = shortAddr(address);
  if (disconnectBtn) disconnectBtn.classList.remove("hidden");

  const updateButton = (btn) => {
    if (!btn) return;
    btn.innerHTML = `
      <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <span class="font-semibold text-white">Connected</span>
    `;
  };

  updateButton(document.getElementById("connectBtn"));
  updateButton(document.getElementById("connectBtnMobile"));

  try {
    localStorage.setItem(WALLET_STORAGE_KEY, address);
    localStorage.setItem("kulino_wallet_timestamp", Date.now().toString());
  } catch (e) {
    console.error("Storage error:", e);
  }

  updateBalanceDisplay(address);
  console.log("✅ UI updated");
}

function resetDisconnectedUI() {
  console.log("🔄 Resetting UI to disconnected state");
  userAddress = null;
  kulinoBalance = 0;
  solBalance = 0;

  const walletStatus = document.getElementById("walletStatus");
  const addrShort = document.getElementById("addrShort");
  const kulinoEl = document.getElementById("kulinoBalance");
  const solEl = document.getElementById("solBalance");
  const disconnectBtn = document.getElementById("disconnectBtn");

  if (walletStatus) walletStatus.innerText = "Not Connected";
  if (addrShort) addrShort.innerText = "-";
  if (kulinoEl) kulinoEl.innerHTML = "<strong>0.00</strong> KULINO";
  if (solEl) solEl.textContent = "0.0000 SOL";
  if (disconnectBtn) disconnectBtn.classList.add("hidden");

  const buttonHTML = `
    <svg class="w-5 h-5 text-white transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
    </svg>
    <span class="font-semibold text-white">Connect Wallet</span>
  `;

  const connectBtn = document.getElementById("connectBtn");
  const connectBtnMobile = document.getElementById("connectBtnMobile");

  if (connectBtn) connectBtn.innerHTML = buttonHTML;
  if (connectBtnMobile) connectBtnMobile.innerHTML = buttonHTML;

  try {
    localStorage.removeItem(WALLET_STORAGE_KEY);
    localStorage.removeItem("kulino_wallet_timestamp");
  } catch (e) {
    console.error("Storage error:", e);
  }

  console.log("✅ UI reset complete");
}
// ==================== WALLET CONNECTION (IMPROVED) ====================
async function connectWallet() {
  console.log("🔌 Connect wallet initiated");

  const isMobile = isMobileDevice();
  const isPhantomBrowser = isInPhantomBrowser(); // NEW DETECTION

  try {
    // ✅ CHECK 1: Apakah sudah di Phantom browser?
    if (isMobile && isPhantomBrowser) {
      await Swal.fire({
        icon: "warning",
        title: "⚠️ Browser Not Supported",
        html: `
          <div class="text-left space-y-3">
            <p>Phantom browser does not support landscape mode properly.</p>
            <p class="font-semibold text-red-600">Please open this game in Chrome browser instead!</p>
            <ol class="list-decimal list-inside text-sm space-y-1">
              <li>Copy this page URL</li>
              <li>Open Chrome browser</li>
              <li>Paste and open the URL</li>
              <li>Connect wallet there</li>
            </ol>
          </div>
        `,
        confirmButtonText: "Copy URL",
        confirmButtonColor: "#667eea",
        showCancelButton: true,
        cancelButtonText: "Continue Anyway",
      }).then((result) => {
        if (result.isConfirmed) {
          copyToClipboard(window.location.href);
          showToast("URL copied! Open in Chrome browser", "success");
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          proceedWithConnection(isMobile);
        }
      });
      return;
    }

    // ✅ CHECK 2: Apakah Phantom terinstall?
    if (!window.phantom?.solana?.isPhantom) {
      console.log("⚠️ Phantom wallet not detected");

      if (isMobile) {
        await Swal.fire({
          icon: "info",
          title: "Install Phantom Extension",
          html: `
            <div class="text-left space-y-3">
              <p class="font-semibold">For best experience on mobile:</p>
              <ol class="list-decimal list-inside text-sm space-y-2">
                <li>Use <strong>Kiwi Browser</strong> or <strong>Chrome</strong></li>
                <li>Install <strong>Phantom extension</strong> (not app)</li>
                <li>Return to this page</li>
                <li>Connect wallet</li>
              </ol>
              <div class="bg-yellow-50 p-3 rounded-lg mt-3 border border-yellow-200">
                <p class="text-xs text-yellow-800">
                  <strong>⚠️ Important:</strong> Phantom mobile app browser does not support landscape mode.
                  Use Chrome + Extension instead!
                </p>
              </div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: "Install Extension",
          denyButtonText: "Use Desktop Instead",
          showDenyButton: true,
          confirmButtonColor: "#667eea",
          denyButtonColor: "#10b981",
        }).then((result) => {
          if (result.isConfirmed) {
            window.open(
              "https://chrome.google.com/webstore/detail/phantom/bfnaelmomeimhlpmgjnjophhpkkoljpa",
              "_blank"
            );
          } else if (result.isDenied) {
            showToast("Please open this page on desktop", "info");
          }
        });
      } else {
        // DESKTOP
        await Swal.fire({
          icon: "warning",
          title: "Phantom Not Installed",
          text: "Please install the Phantom Wallet extension",
          showCancelButton: true,
          confirmButtonText: "Install Extension",
          confirmButtonColor: "#667eea",
        }).then((result) => {
          if (result.isConfirmed) {
            window.open("https://phantom.app/", "_blank");
          }
        });
      }
      return;
    }

    // ✅ Proceed with normal connection
    await proceedWithConnection(isMobile);
  } catch (err) {
    console.error("❌ Connect error:", err);
    handleConnectionError(err);
  }
}

// ==================== MOBILE DETECTION HELPERS ====================

// Detect if currently in Phantom's built-in browser
function isInPhantomBrowser() {
  const ua = navigator.userAgent || "";
  return ua.includes("Phantom") || ua.includes("phantom");
}

// Copy text to clipboard
function copyToClipboard(text) {
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(text).catch((err) => {
      console.error("Clipboard error:", err);
      fallbackCopy(text);
    });
  } else {
    fallbackCopy(text);
  }
}

function fallbackCopy(text) {
  const textarea = document.createElement("textarea");
  textarea.value = text;
  textarea.style.position = "fixed";
  textarea.style.opacity = "0";
  document.body.appendChild(textarea);
  textarea.select();
  try {
    document.execCommand("copy");
  } catch (err) {
    console.error("Copy failed:", err);
  }
  document.body.removeChild(textarea);
}

// Actual connection process
async function proceedWithConnection(isMobile) {
  console.log("✅ Proceeding with wallet connection...");

  provider = window.phantom.solana;

  Swal.fire({
    title: "Connecting...",
    text: "Please approve the connection in Phantom",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  console.log("🔄 Requesting wallet connection...");
  const resp = await provider.connect();
  const address = resp.publicKey.toString();

  console.log("✅ Connected to wallet:", shortAddr(address));
  updateConnectedUI(address);

  await Swal.fire({
    icon: "success",
    title: "Connected!",
    html: `
      <div class="space-y-3">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-3 rounded-lg">
          <p class="text-sm text-gray-600 mb-1">Wallet Address:</p>
          <code class="text-xs font-mono text-indigo-600 font-semibold">${shortAddr(
            address
          )}</code>
        </div>
        ${
          isMobile
            ? `
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
          <p class="text-sm text-yellow-800">
            <strong>📱 Mobile Tip:</strong> Rotate your device to landscape for best experience!
          </p>
        </div>
        `
            : ""
        }
      </div>
    `,
    confirmButtonText: "Start Playing",
    confirmButtonColor: "#667eea",
    timer: 5000,
  });
}

function handleConnectionError(err) {
  Swal.close();

  if (err.message?.includes("rejected") || err.code === 4001) {
    Swal.fire({
      icon: "info",
      title: "Connection Cancelled",
      text: "You cancelled the wallet connection",
      confirmButtonColor: "#667eea",
    });
  } else {
    Swal.fire({
      icon: "error",
      title: "Connection Failed",
      text: err.message || "Failed to connect wallet",
      confirmButtonColor: "#ef4444",
    });
  }
}

function showDisconnectDialog() {
  Swal.fire({
    title: "Disconnect Wallet?",
    html: `
      <div class="text-left space-y-3">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 rounded-lg">
          <p class="text-sm text-gray-600 mb-1">Connected Wallet:</p>
          <code class="text-sm font-mono text-indigo-600 font-semibold">${shortAddr(
            userAddress
          )}</code>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
          <p class="text-sm text-gray-600 mb-1">Kulino Balance:</p>
          <p class="text-lg font-bold text-yellow-600">${formatKulinoBalance(
            kulinoBalance
          )} KULINO</p>
        </div>
      </div>
    `,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Disconnect",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#ef4444",
    cancelButtonColor: "#64748b",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) disconnectWallet();
  });
}

function disconnectWallet() {
  if (provider?.disconnect) {
    try {
      provider.disconnect();
    } catch (e) {
      console.error("Disconnect error:", e);
    }
  }

  resetDisconnectedUI();

  Swal.fire({
    icon: "success",
    title: "Disconnected",
    text: "Wallet disconnected successfully",
    timer: 2000,
    showConfirmButton: false,
  });
}

async function autoConnectWallet() {
  try {
    const savedAddress = localStorage.getItem(WALLET_STORAGE_KEY);
    const timestamp = localStorage.getItem("kulino_wallet_timestamp");

    if (!savedAddress || !timestamp) {
      console.log("ℹ️ No saved wallet session");
      return;
    }

    const timeSince = Date.now() - parseInt(timestamp);
    if (timeSince > SESSION_DURATION) {
      console.log("⏰ Wallet session expired");
      localStorage.removeItem(WALLET_STORAGE_KEY);
      localStorage.removeItem("kulino_wallet_timestamp");
      return;
    }

    if (!window.phantom?.solana?.isPhantom) {
      console.log("⚠️ Phantom not available for auto-connect");
      return;
    }

    console.log("🔄 Auto-connecting wallet:", shortAddr(savedAddress));
    provider = window.phantom.solana;

    const resp = await provider.connect({ onlyIfTrusted: true });
    const address = resp.publicKey.toString();

    if (address.toLowerCase() === savedAddress.toLowerCase()) {
      updateConnectedUI(address);
      showToast("Wallet Auto-Connected", "success");
      console.log("✅ Auto-connected successfully");
    } else {
      console.log("⚠️ Address mismatch during auto-connect");
      localStorage.removeItem(WALLET_STORAGE_KEY);
    }
  } catch (error) {
    console.log("ℹ️ Auto-connect skipped:", error.message);
  }
}

// ==================== GAME FUNCTIONS ====================
function playGame(gameId) {
  console.log("🎮 playGame called with gameId:", gameId);

  if (!userAddress) {
    Swal.fire({
      icon: "warning",
      title: "Connect Wallet First",
      text: "Please connect your wallet before playing",
      showCancelButton: true,
      confirmButtonText: "Connect Now",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#667eea",
      cancelButtonColor: "#6c757d",
    }).then((result) => {
      if (result.isConfirmed) connectWallet();
    });
    return;
  }

  const baseUrl =
    window.location.origin + window.location.pathname.replace("index.php", "");
  const gameUrl = `${baseUrl}WebUnity/index.html?wallet=${encodeURIComponent(
    userAddress
  )}&game=${encodeURIComponent(gameId)}`;

  console.log("🚀 Opening game at:", gameUrl);

  // Check if mobile
  const isMobile = isMobileDevice();

  Swal.fire({
    title: "Loading Game...",
    html: `
      <div class="text-center">
        <div class="inline-block w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600">Opening <strong>${gameId}</strong></p>
        <p class="text-sm text-gray-500 mt-2">Game will open in a new tab</p>
        ${isMobile ? `
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-4 rounded-lg mt-4 border border-yellow-300">
          <p class="text-sm font-semibold text-yellow-800 mb-2">📱 Mobile Instructions:</p>
          <ol class="text-xs text-yellow-700 text-left list-decimal list-inside space-y-1">
            <li>Game will open in new tab</li>
            <li><strong>Rotate device to landscape</strong></li>
            <li>Make sure rotation lock is OFF</li>
            <li>Enjoy the game!</li>
          </ol>
        </div>
        ` : ''}
      </div>
    `,
    showConfirmButton: false,
    timer: isMobile ? 4000 : 2000,
    allowOutsideClick: false,
  });

  setTimeout(() => {
    const newWindow = window.open(gameUrl, "_blank");

    if (
      !newWindow ||
      newWindow.closed ||
      typeof newWindow.closed == "undefined"
    ) {
      Swal.fire({
        icon: "error",
        title: "Popup Blocked",
        html: `
          <p>Your browser blocked the game window.</p>
          <p class="text-sm text-gray-600 mt-2">Please allow popups for this site</p>
        `,
        showCancelButton: true,
        confirmButtonText: "Try Again",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#667eea",
      }).then((result) => {
        if (result.isConfirmed) window.open(gameUrl, "_blank");
      });
    } else {
      Swal.fire({
        icon: "success",
        title: "Game Opened!",
        html: `
          <p>Check your new tab to play</p>
          ${isMobile ? `
          <p class="text-sm text-yellow-600 mt-2">
            <strong>Remember:</strong> Rotate to landscape mode!
          </p>
          ` : ''}
        `,
        timer: 3000,
        showConfirmButton: false,
      });
    }
  }, 500);
}

// ==================== SLIDER FUNCTIONS ====================
function scrollSlider(sliderId, direction) {
  const slider = document.getElementById(sliderId);
  if (!slider) return;

  const scrollAmount = 300;
  slider.scrollBy({
    left: direction * scrollAmount,
    behavior: "smooth",
  });
}

// 🔧 FIX: Marketplace Functions (ADDED)
function filterMarketplace(category) {
  console.log("🔍 Filtering marketplace by category:", category);

  const cards = document.querySelectorAll(".product-card");
  const filterBtns = document.querySelectorAll(".marketplace-filter-btn");

  // Update active button styling
  filterBtns.forEach((btn) => {
    btn.classList.remove(
      "active",
      "from-indigo-600",
      "to-purple-600",
      "text-white"
    );
    btn.classList.add("bg-gray-100", "text-gray-700");
  });

  event.target.classList.add(
    "active",
    "from-indigo-600",
    "to-purple-600",
    "text-white"
  );
  event.target.classList.remove("bg-gray-100", "text-gray-700");

  // Show/hide sub-category filter
  const subFilter = document.getElementById("subCategoryFilter");
  if (subFilter) {
    if (category !== "all") {
      showSubCategories(category);
      subFilter.classList.remove("hidden");
    } else {
      subFilter.classList.add("hidden");
    }
  }

  // Filter product cards
  cards.forEach((card) => {
    if (category === "all" || card.dataset.category === category) {
      card.style.display = "flex";
    } else {
      card.style.display = "none";
    }
  });
}

function showSubCategories(category) {
  const subFilter = document.getElementById("subCategoryFilter");
  if (!subFilter) return;

  let subCategories = [];

  if (category === "Aksesoris") {
    subCategories = ["All", "Baju", "Ganci", "Topi", "Celana", "Gelas"];
  } else if (category === "Board Game") {
    subCategories = ["All", "Monopoly", "Ular Tangga"];
  }

  subFilter.innerHTML =
    '<div class="flex flex-wrap gap-2">' +
    subCategories
      .map(
        (sub) =>
          `<button onclick="filterBySubCategory('${category}', '${sub}')" 
        class="sub-filter-btn px-4 py-2 text-sm rounded-lg bg-white border border-gray-300 text-gray-700 hover:border-indigo-600 hover:text-indigo-600 transition">
        ${sub}
      </button>`
      )
      .join("") +
    "</div>";
}

function filterBySubCategory(category, subCategory) {
  console.log("🔍 Filtering by subcategory:", subCategory);

  const cards = document.querySelectorAll(".product-card");

  cards.forEach((card) => {
    if (subCategory === "All") {
      if (card.dataset.category === category) {
        card.style.display = "flex";
      }
    } else {
      if (
        card.dataset.category === category &&
        card.dataset.subcategory === subCategory
      ) {
        card.style.display = "flex";
      } else {
        card.style.display = "none";
      }
    }
  });
}

// 🔧 FIX: Product Modal Functions (ADDED)
let currentProduct = null;

function openProductModal(product) {
  console.log("🛍️ Opening product modal:", product.product_name);

  currentProduct = product;
  const modal = document.getElementById("productModal");
  if (!modal) {
    console.error("Product modal not found");
    return;
  }

  // Update modal content
  const modalImage = document.getElementById("modalImage");
  const modalTitle = document.getElementById("modalTitle");
  const modalDescription = document.getElementById("modalDescription");
  const modalCategory = document.getElementById("modalCategory");
  const modalDiscount = document.getElementById("modalDiscount");
  const modalPriceSection = document.getElementById("modalPriceSection");
  const modalStock = document.getElementById("modalStock");

  if (modalImage) {
    modalImage.src = "uploads/marketplace/" + product.image;
    modalImage.alt = product.product_name;
  }
  if (modalTitle) modalTitle.textContent = product.product_name;
  if (modalDescription) modalDescription.textContent = product.description;
  if (modalCategory) modalCategory.textContent = product.subcategory;

  // Price section with discount
  const hasDiscount =
    product.original_price && product.original_price > product.price;

  if (hasDiscount) {
    const discount = Math.round(
      ((product.original_price - product.price) / product.original_price) * 100
    );
    if (modalDiscount) {
      modalDiscount.textContent = `-${discount}%`;
      modalDiscount.classList.remove("hidden");
    }

    if (modalPriceSection) {
      modalPriceSection.innerHTML = `
        <div class="flex items-baseline gap-3">
          <span class="text-3xl font-bold text-indigo-600">
            Rp ${parseInt(product.price).toLocaleString("id-ID")}
          </span>
          <span class="text-lg text-gray-400 line-through">
            Rp ${parseInt(product.original_price).toLocaleString("id-ID")}
          </span>
        </div>
      `;
    }
  } else {
    if (modalDiscount) modalDiscount.classList.add("hidden");
    if (modalPriceSection) {
      modalPriceSection.innerHTML = `
        <span class="text-3xl font-bold text-indigo-600">
          Rp ${parseInt(product.price).toLocaleString("id-ID")}
        </span>
      `;
    }
  }

  // Stock status
  if (modalStock) {
    const stockHtml =
      product.stock > 0
        ? `<span class="text-green-600 font-medium">✓ In Stock (${product.stock} available)</span>`
        : `<span class="text-red-600 font-medium">✗ Out of Stock</span>`;
    modalStock.innerHTML = stockHtml;
  }

  // Show modal
  modal.classList.remove("hidden");
  modal.classList.add("flex");
  document.body.style.overflow = "hidden";
}

function closeProductModal() {
  console.log("❌ Closing product modal");

  const modal = document.getElementById("productModal");
  if (!modal) return;

  modal.classList.add("hidden");
  modal.classList.remove("flex");
  document.body.style.overflow = "auto";
  currentProduct = null;
}

function buyNowProduct() {
  if (!currentProduct) {
    console.error("No product selected");
    return;
  }

  console.log("💰 Buy now:", currentProduct.product_name);

  const message =
    `Hi, saya tertarik dengan produk:\n\n` +
    `📦 ${currentProduct.product_name}\n` +
    `💰 Rp ${parseInt(currentProduct.price).toLocaleString("id-ID")}\n\n` +
    `Apakah produk ini masih tersedia?`;

  const instagramUrl = `https://www.instagram.com/kulinohouse.merchant/`;
  window.open(instagramUrl, "_blank");

  setTimeout(() => {
    Swal.fire({
      icon: "info",
      title: "Redirecting to Instagram",
      html: `
        <p>Please send this message to complete your purchase:</p>
        <div class="bg-gray-100 p-4 rounded-lg mt-3 text-left">
          <p class="text-sm text-gray-700 whitespace-pre-line">${message}</p>
        </div>
      `,
      confirmButtonText: "Got it!",
      confirmButtonColor: "#667eea",
    });
  }, 500);
}

// ==================== VISITOR TRACKING (IMPROVED) ====================
function trackVisitor() {
  console.log("📊 Tracking visitor...");

  // Track visitor dengan retry mechanism
  fetch("track.php?add=1", {
    method: "GET",
    credentials: "same-origin",
    headers: {
      Accept: "application/json",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        console.log("✅ Visitor tracked successfully:", data);
      } else {
        console.error("❌ Tracking failed:", data.error);
      }
    })
    .catch((err) => {
      console.error("❌ Tracking request failed:", err);
      // Retry setelah 2 detik
      setTimeout(() => {
        fetch("track.php?add=1", {
          method: "GET",
          credentials: "same-origin",
        }).catch((e) => console.error("Retry failed:", e));
      }, 2000);
    });

  // Update visitor count display
  updateVisitorCount();
}

function updateVisitorCount() {
  fetch("track.php", {
    method: "GET",
    credentials: "same-origin",
    headers: {
      Accept: "application/json",
    },
  })
    .then((res) => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then((data) => {
      const countEl = document.getElementById("visitorCount");
      if (countEl && data.today !== undefined) {
        countEl.textContent = data.today;
        console.log("📊 Visitor count updated:", data.today);
      }
    })
    .catch((err) => console.error("❌ Count fetch failed:", err));
}

// Auto-refresh visitor count setiap 30 detik
setInterval(updateVisitorCount, 30000);

// ==================== INITIALIZATION ====================
function waitForLibraries() {
  return new Promise((resolve) => {
    if (typeof solanaWeb3 !== "undefined" && typeof Swal !== "undefined") {
      resolve();
      return;
    }

    const checkInterval = setInterval(() => {
      if (typeof solanaWeb3 !== "undefined" && typeof Swal !== "undefined") {
        clearInterval(checkInterval);
        resolve();
      }
    }, 100);

    setTimeout(() => {
      clearInterval(checkInterval);
      console.error("❌ Required libraries failed to load");
      resolve();
    }, 10000);
  });
}

// ==================== IMPROVED MOBILE MENU ====================
function setupMobileMenu() {
  const menuToggle = document.getElementById("menuToggle");
  const mobileMenu = document.getElementById("mobileMenu");

  if (!menuToggle || !mobileMenu) {
    console.warn("Mobile menu elements not found");
    return;
  }

  console.log("✅ Setting up mobile menu");

  // Remove any existing listeners
  const newToggle = menuToggle.cloneNode(true);
  menuToggle.parentNode.replaceChild(newToggle, menuToggle);

  // Add new click listener
  newToggle.addEventListener("click", function (e) {
    e.preventDefault();
    e.stopPropagation();

    console.log("🍔 Hamburger clicked");

    // Toggle menu visibility
    mobileMenu.classList.toggle("hidden");

    // Toggle hamburger animation
    newToggle.classList.toggle("open");

    // Log state
    console.log("Menu visible:", !mobileMenu.classList.contains("hidden"));
  });

  // Close menu when clicking outside
  document.addEventListener("click", function (e) {
    if (!newToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
      if (!mobileMenu.classList.contains("hidden")) {
        mobileMenu.classList.add("hidden");
        newToggle.classList.remove("open");
        console.log("📱 Menu closed (clicked outside)");
      }
    }
  });

  // Close menu when clicking menu items
  const menuLinks = mobileMenu.querySelectorAll("a, button");
  menuLinks.forEach((link) => {
    link.addEventListener("click", function () {
      setTimeout(() => {
        mobileMenu.classList.add("hidden");
        newToggle.classList.remove("open");
        console.log("📱 Menu closed (item clicked)");
      }, 100);
    });
  });
}

async function initializeApp() {
  console.log("⏳ Waiting for libraries...");
  await waitForLibraries();

  if (typeof solanaWeb3 === "undefined" || typeof Swal === "undefined") {
    console.error("❌ Required libraries not loaded!");
    return;
  }

  console.log("✅ Libraries ready");
  console.log("🚀 Initializing Kulino...");

  // Track visitor
  trackVisitor();

  // Setup connect buttons
  const setupButton = (btnId) => {
    const btn = document.getElementById(btnId);
    if (btn) {
      console.log(`✅ Found button: ${btnId}`);
      btn.removeAttribute("onclick");
      btn.addEventListener(
        "click",
        function (e) {
          e.preventDefault();
          e.stopPropagation();
          console.log(`🖱️ ${btnId} clicked`);
          connectWallet();
        },
        { passive: false }
      );
    }
  };

  setupButton("connectBtn");
  setupButton("connectBtnMobile");

  // Setup disconnect button
  const disconnectBtn = document.getElementById("disconnectBtn");
  if (disconnectBtn) {
    disconnectBtn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      showDisconnectDialog();
    });
  }

  // Setup refresh balance button
  const refreshBtn = document.getElementById("refreshBalanceBtn");
  if (refreshBtn) {
    refreshBtn.addEventListener("click", function (e) {
      e.preventDefault();
      updateBalanceDisplay();
    });
  }

  // Setup video hover effects
  document.querySelectorAll(".featured-card").forEach((card) => {
    const video = card.querySelector("video");
    if (video) {
      card.addEventListener("mouseenter", () => video.play().catch(() => {}));
      card.addEventListener("mouseleave", () => {
        video.pause();
        video.currentTime = 0;
      });
    }
  });

  // Setup mobile menu toggle
  const menuToggle = document.getElementById("menuToggle");
  const mobileMenu = document.getElementById("mobileMenu");
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
      menuToggle.classList.toggle("open");
    });
  }

  // Setup product modal close on outside click
  const productModal = document.getElementById("productModal");
  if (productModal) {
    productModal.addEventListener("click", function (e) {
      if (e.target === this) {
        closeProductModal();
      }
    });
  }

  // Auto-connect wallet after delay
  setTimeout(() => {
    autoConnectWallet();
  }, 1000);

  console.log("✅ Kulino Ready!");
}

// Start initialization when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initializeApp);
} else {
  initializeApp();
}

// ==================== EXPOSE FUNCTIONS GLOBALLY ====================
window.connectWallet = connectWallet;
window.disconnectWallet = disconnectWallet;
window.showDisconnectDialog = showDisconnectDialog;
window.playGame = playGame;
window.scrollSlider = scrollSlider;
window.updateBalanceDisplay = updateBalanceDisplay;
window.filterMarketplace = filterMarketplace;
window.showSubCategories = showSubCategories;
window.filterBySubCategory = filterBySubCategory;
window.openProductModal = openProductModal;
window.closeProductModal = closeProductModal;
window.buyNowProduct = buyNowProduct;
window.getSOLBalance = getSOLBalance;
window.getKulinoBalance = getKulinoBalance;

console.log("✅ Script loaded successfully");
console.log("✅ All functions exposed globally");
