// ==================== CONFIGURATION ====================
const unityBuildPath = "./WebUnity/index.html";
const KULINO_TOKEN_MINT = 'E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1';

// ==================== STATE MANAGEMENT ====================
let provider = null;
let userAddress = null;
let unityInstance = null;
let kulinoBalance = 0;

// Local Storage Keys
const WALLET_STORAGE_KEY = 'kulino_connected_wallet';
const WALLET_TIMESTAMP_KEY = 'kulino_wallet_timestamp';

// ==================== UTILITY FUNCTIONS ====================
function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

function getPhantomDeepLink() {
  const currentUrl = encodeURIComponent(window.location.href);
  return `https://phantom.app/ul/browse/${currentUrl}?ref=${currentUrl}`;
}

function shortAddr(addr) {
  if (!addr) return "-";
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

function formatKulinoBalance(balance) {
  if (balance >= 1000000) {
    return (balance / 1000000).toFixed(2) + 'M';
  }
  if (balance >= 1000) {
    return (balance / 1000).toFixed(2) + 'K';
  }
  return balance.toFixed(2);
}

// ==================== STORAGE FUNCTIONS ====================
function saveWalletToStorage(address) {
  try {
    localStorage.setItem(WALLET_STORAGE_KEY, address);
    localStorage.setItem(WALLET_TIMESTAMP_KEY, Date.now().toString());
    console.log('✅ Wallet saved to storage:', shortAddr(address));
  } catch (error) {
    console.error('Failed to save wallet:', error);
  }
}

function loadWalletFromStorage() {
  try {
    const savedAddress = localStorage.getItem(WALLET_STORAGE_KEY);
    const timestamp = localStorage.getItem(WALLET_TIMESTAMP_KEY);
    
    if (savedAddress && timestamp) {
      const daysSinceLastConnect = (Date.now() - parseInt(timestamp)) / (1000 * 60 * 60 * 24);
      if (daysSinceLastConnect < 30) { // Valid for 30 days
        console.log('✅ Wallet loaded from storage:', shortAddr(savedAddress));
        return savedAddress;
      } else {
        console.log('⏰ Wallet session expired');
        clearWalletFromStorage();
      }
    }
  } catch (error) {
    console.error('Failed to load wallet:', error);
  }
  return null;
}

function clearWalletFromStorage() {
  try {
    localStorage.removeItem(WALLET_STORAGE_KEY);
    localStorage.removeItem(WALLET_TIMESTAMP_KEY);
    console.log('🗑️ Wallet cleared from storage');
  } catch (error) {
    console.error('Failed to clear wallet:', error);
  }
}

// ==================== BALANCE FUNCTIONS ====================
async function getKulinoBalance(walletAddress) {
  try {
    console.log('Fetching Kulino balance for:', shortAddr(walletAddress));
    
    const connection = new solanaWeb3.Connection(
      'https://api.mainnet-beta.solana.com',
      'confirmed'
    );
    
    const pubkey = new solanaWeb3.PublicKey(walletAddress);
    const tokenMint = new solanaWeb3.PublicKey(KULINO_TOKEN_MINT);
    
    const tokenAccounts = await connection.getParsedTokenAccountsByOwner(pubkey, {
      mint: tokenMint
    });
    
    if (tokenAccounts.value.length > 0) {
      const balance = tokenAccounts.value[0].account.data.parsed.info.tokenAmount.uiAmount;
      console.log('✅ Kulino balance:', balance);
      return balance || 0;
    }
    
    console.log('ℹ️ No Kulino tokens found');
    return 0;
  } catch (error) {
    console.error('Error fetching Kulino balance:', error);
    return 0;
  }
}

async function updateBalanceDisplay(address) {
  const balanceElement = document.getElementById("kulinoBalance");
  if (!balanceElement) return;
  
  balanceElement.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-yellow-300 border-t-transparent rounded-full animate-spin"></span> Loading...';
  
  try {
    const balance = await getKulinoBalance(address);
    kulinoBalance = balance;
    const formattedBalance = formatKulinoBalance(balance);
    balanceElement.innerHTML = `<strong>${formattedBalance}</strong> KULINO`;
    console.log('✅ Balance updated:', formattedBalance);
  } catch (error) {
    console.error('Failed to update balance:', error);
    balanceElement.innerHTML = '<strong>0.00</strong> KULINO';
  }
}

// ==================== UI UPDATE FUNCTIONS ====================
function updateConnectedUI(address) {
  userAddress = address;
  
  // Update status display
  const walletStatus = document.getElementById("walletStatus");
  const addrShort = document.getElementById("addrShort");
  
  if (walletStatus) walletStatus.innerText = "Connected ✓";
  if (addrShort) addrShort.innerText = shortAddr(address);
  
  // Update Desktop button
  const connectBtn = document.getElementById("connectBtn");
  if (connectBtn) {
    connectBtn.innerHTML = `
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="font-semibold text-white">Connected</span>
      </div>
    `;
    connectBtn.onclick = showDisconnectDialog;
    connectBtn.disabled = false;
    connectBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    connectBtn.classList.add('cursor-pointer', 'hover:scale-105');
  }
  
  // Update Mobile button
  const connectBtnMobile = document.getElementById("connectBtnMobile");
  if (connectBtnMobile) {
    connectBtnMobile.innerHTML = `
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="font-semibold text-white">Connected</span>
      </div>
    `;
    connectBtnMobile.onclick = showDisconnectDialog;
    connectBtnMobile.disabled = false;
    connectBtnMobile.classList.remove('opacity-75', 'cursor-not-allowed');
    connectBtnMobile.classList.add('cursor-pointer');
  }
  
  // Update balance
  updateBalanceDisplay(address);
  
  // Save to storage
  saveWalletToStorage(address);
  
  // Send to Unity if available
  if (unityInstance && typeof unityInstance.SendMessage === "function") {
    unityInstance.SendMessage("GameManager", "OnWalletConnected", address);
  }
  
  console.log('✅ UI updated for connected wallet');
}

function resetDisconnectedUI() {
  userAddress = null;
  kulinoBalance = 0;
  
  // Reset status display
  const walletStatus = document.getElementById("walletStatus");
  const addrShort = document.getElementById("addrShort");
  const balanceElement = document.getElementById("kulinoBalance");
  
  if (walletStatus) walletStatus.innerText = "Not Connected";
  if (addrShort) addrShort.innerText = "-";
  if (balanceElement) balanceElement.innerHTML = '<strong>0.00</strong> KULINO';
  
  // Reset Desktop button
  const connectBtn = document.getElementById("connectBtn");
  if (connectBtn) {
    connectBtn.innerHTML = `
      <svg class="w-5 h-5 text-white transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>
      <span class="font-semibold text-white">Connect Wallet</span>
    `;
    connectBtn.onclick = connectWallet;
    connectBtn.disabled = false;
    connectBtn.classList.remove('cursor-pointer', 'hover:scale-105');
  }
  
  // Reset Mobile button
  const connectBtnMobile = document.getElementById("connectBtnMobile");
  if (connectBtnMobile) {
    connectBtnMobile.innerHTML = `
      <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>
      <span class="font-semibold text-white">Connect Wallet</span>
    `;
    connectBtnMobile.onclick = connectWallet;
    connectBtnMobile.disabled = false;
  }
  
  console.log('✅ UI reset to disconnected state');
}

// ==================== DISCONNECT FUNCTIONS ====================
function showDisconnectDialog() {
  Swal.fire({
    title: 'Disconnect Wallet?',
    html: `
      <div class="text-left space-y-3">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 rounded-lg">
          <p class="text-sm text-gray-600 mb-1">Connected Wallet:</p>
          <code class="text-sm font-mono text-indigo-600 font-semibold">${shortAddr(userAddress)}</code>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
          <p class="text-sm text-gray-600 mb-1">Kulino Balance:</p>
          <p class="text-lg font-bold text-yellow-600">${formatKulinoBalance(kulinoBalance)} KULINO</p>
        </div>
        <p class="text-sm text-gray-500 mt-3">Are you sure you want to disconnect?</p>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-sign-out-alt mr-2"></i>Disconnect',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    reverseButtons: true,
    customClass: {
      popup: 'rounded-2xl'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      disconnectWallet();
    }
  });
}

function disconnectWallet() {
  // Clear storage
  clearWalletFromStorage();
  
  // Disconnect from provider
  if (provider && provider.disconnect) {
    try {
      provider.disconnect();
    } catch (error) {
      console.error('Error disconnecting provider:', error);
    }
  }
  
  // Reset UI
  resetDisconnectedUI();
  
  // Show success message
  Swal.fire({
    icon: 'success',
    title: 'Wallet Disconnected',
    text: 'Your wallet has been disconnected successfully',
    timer: 2000,
    showConfirmButton: false,
    customClass: {
      popup: 'rounded-2xl'
    }
  });
  
  console.log('✅ Wallet disconnected successfully');
}

// ==================== AUTO-CONNECT FUNCTION ====================
async function autoConnectWallet() {
  const savedAddress = loadWalletFromStorage();
  if (!savedAddress) {
    console.log('ℹ️ No saved wallet found');
    return;
  }
  
  console.log('🔄 Attempting auto-connect...');
  
  try {
    const anyWin = window;
    const isMobile = isMobileDevice();
    
    // Get provider
    if (isMobile && anyWin.phantom?.solana?.isPhantom) {
      provider = anyWin.phantom.solana;
    } else if (!isMobile && anyWin.phantom?.solana?.isPhantom) {
      provider = anyWin.phantom.solana;
    } else {
      console.log('⚠️ Phantom not available, clearing storage');
      clearWalletFromStorage();
      return;
    }
    
    // Try to connect silently (only if already trusted)
    const resp = await provider.connect({ onlyIfTrusted: true });
    const currentAddress = resp.publicKey.toString();
    
    // Verify it matches saved address
    if (currentAddress.toLowerCase() === savedAddress.toLowerCase()) {
      updateConnectedUI(currentAddress);
      console.log('✅ Auto-connected successfully:', shortAddr(currentAddress));
      
      // Show subtle notification
      const toast = document.createElement('div');
      toast.className = 'fixed top-20 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in-down';
      toast.innerHTML = `
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span>Wallet Auto-Connected</span>
        </div>
      `;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 3000);
      
    } else {
      console.log('⚠️ Address mismatch, clearing storage');
      clearWalletFromStorage();
    }
  } catch (error) {
    console.log('ℹ️ Auto-connect not available:', error.message);
    // Silent fail - user needs to manually connect
    clearWalletFromStorage();
  }
}

// ==================== CONNECT WALLET FUNCTION ====================
async function connectWallet() {
  const isMobile = isMobileDevice();
  
  try {
    const anyWin = window;
    
    // ===== MOBILE HANDLING =====
    if (isMobile) {
      const isPhantomApp = anyWin.phantom?.solana?.isPhantom;
      
      if (!isPhantomApp) {
        Swal.fire({
          icon: 'info',
          title: 'Open in Phantom App',
          html: `
            <div class="text-left">
              <p class="mb-3">To connect your wallet on mobile, you need to open this website in the Phantom app.</p>
              <p class="mb-2 font-semibold">Options:</p>
              <ol class="list-decimal list-inside space-y-2 text-sm">
                <li>Click the button below to open in Phantom App (if installed)</li>
                <li>Or download the Phantom App first if you don't have it</li>
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
            window.location.href = getPhantomDeepLink();
          } else if (result.isDenied) {
            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const downloadUrl = isIOS 
              ? 'https://apps.apple.com/us/app/phantom-solana-wallet/id1598432977'
              : 'https://play.google.com/store/apps/details?id=app.phantom';
            window.open(downloadUrl, '_blank');
          }
        });
        return;
      }
      
      provider = anyWin.phantom.solana;
    } 
    // ===== DESKTOP HANDLING =====
    else {
      if (!anyWin.phantom?.solana?.isPhantom) {
        Swal.fire({
          icon: 'warning',
          title: 'Phantom Wallet Not Found',
          html: `
            <p>Phantom wallet extension is not detected.</p>
            <p class="mt-2">Please install Phantom Wallet first:</p>
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

    // Show loading
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

    // Connect to Phantom
    const resp = await provider.connect();
    const address = resp.publicKey.toString();
    
    // Update UI and save
    updateConnectedUI(address);

    // Success message
    Swal.fire({
      icon: 'success',
      title: 'Wallet Connected!',
      html: `
        <div class="space-y-3">
          <p class="text-sm text-gray-600">Your wallet address:</p>
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-3 rounded-lg">
            <code class="text-xs font-mono text-indigo-600">${shortAddr(address)}</code>
          </div>
          <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <p class="text-sm text-green-800">✓ Auto-connect enabled</p>
            <p class="text-xs text-green-600 mt-1">Your wallet will stay connected even after refresh</p>
          </div>
        </div>
      `,
      showConfirmButton: true,
      confirmButtonText: 'Start Playing',
      confirmButtonColor: '#667eea',
      timer: 5000,
      customClass: {
        popup: 'rounded-2xl'
      }
    });

  } catch (err) {
    console.error("connectWallet error:", err);
    
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

// ==================== UNITY & GAME FUNCTIONS ====================
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
    
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage("GameManager", "OnClaimResult", JSON.stringify(result));
    }

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
      
      // Refresh balance
      updateBalanceDisplay(userAddress);
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

function setUnityInstance(instance) {
  console.log("setUnityInstance called");
  unityInstance = instance;
  if (userAddress && unityInstance && typeof unityInstance.SendMessage === "function") {
    unityInstance.SendMessage("GameManager", "OnWalletConnected", userAddress);
  }
}

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

function scrollSlider(id, direction) {
  const slider = document.getElementById(id);
  const scrollAmount = 320;
  slider.scrollBy({ left: direction * scrollAmount, behavior: "smooth" });
}

// ==================== VISITOR TRACKING ====================
async function trackVisitor() {
  try {
    const res = await fetch("track.php?add=1");
    const data = await res.json();
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

// ==================== INITIALIZATION ====================
window.addEventListener('DOMContentLoaded', () => {
  console.log('🚀 Kulino Game Hub Initializing...');
  
  // Track visitor
  trackVisitor();
  setInterval(updateVisitorCount, 30000);
  
  // AUTO-CONNECT WALLET (CRITICAL FOR PERSISTENCE)
  setTimeout(() => {
    autoConnectWallet();
  }, 500); // Small delay to ensure Phantom is loaded
  
  // Setup event listeners
  document.getElementById("connectBtn")?.addEventListener("click", connectWallet);
  document.getElementById("connectBtnMobile")?.addEventListener("click", connectWallet);
  
  // Featured card video preview
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
  
  // Mobile menu toggle
  const menuToggle = document.getElementById("menuToggle");
  const mobileMenu = document.getElementById("mobileMenu");
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
      menuToggle.classList.toggle("open");
    });
  }
  
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
  
  console.log('✅ Kulino Game Hub Initialized');
});

const KULINO_TOKEN_MINT = 'E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1';
    
    async function getKulinoBalance(walletAddress) {
      try {
        const connection = new solanaWeb3.Connection('https://api.mainnet-beta.solana.com');
        const pubkey = new solanaWeb3.PublicKey(walletAddress);
        const tokenMint = new solanaWeb3.PublicKey(KULINO_TOKEN_MINT);
        
        // Get token accounts
        const tokenAccounts = await connection.getParsedTokenAccountsByOwner(pubkey, {
          mint: tokenMint
        });
        
        if (tokenAccounts.value.length > 0) {
          const balance = tokenAccounts.value[0].account.data.parsed.info.tokenAmount.uiAmount;
          return balance || 0;
        }
        return 0;
      } catch (error) {
        console.error('Error fetching Kulino balance:', error);
        return 0;
      }
    }
    
    function formatKulinoBalance(balance) {
      if (balance >= 1000) {
        return (balance / 1000).toFixed(2) + 'K';
      }
      return balance.toFixed(2);
    }
// ==================== EXPOSE GLOBAL FUNCTIONS ====================
window.requestReward = requestReward;
window.connectWallet = connectWallet;
window.setUnityInstance = setUnityInstance;