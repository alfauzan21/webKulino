// ==================== CONFIGURATION ====================
const unityBuildPath = "./WebUnity/index.html";
const KULINO_TOKEN_MINT = 'E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1';
const SOLANA_RPC = 'https://api.mainnet-beta.solana.com';

// ==================== STATE MANAGEMENT ====================
let provider = null;
let userAddress = null;
let unityInstance = null;
let kulinoBalance = 0;
let solBalance = 0;

// Local Storage Keys
const WALLET_STORAGE_KEY = 'kulino_connected_wallet';
const WALLET_TIMESTAMP_KEY = 'kulino_wallet_timestamp';
const SESSION_DURATION = 30 * 24 * 60 * 60 * 1000; // 30 days

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
  if (balance >= 1000000) return (balance / 1000000).toFixed(2) + 'M';
  if (balance >= 1000) return (balance / 1000).toFixed(2) + 'K';
  return balance.toFixed(2);
}

function formatSOLBalance(balance) {
  return balance.toFixed(4);
}

// ==================== STORAGE FUNCTIONS ====================
function saveWalletToStorage(address) {
  try {
    localStorage.setItem(WALLET_STORAGE_KEY, address);
    localStorage.setItem(WALLET_TIMESTAMP_KEY, Date.now().toString());
    console.log('✅ Wallet saved:', shortAddr(address));
  } catch (error) {
    console.error('❌ Failed to save wallet:', error);
  }
}

function loadWalletFromStorage() {
  try {
    const savedAddress = localStorage.getItem(WALLET_STORAGE_KEY);
    const timestamp = localStorage.getItem(WALLET_TIMESTAMP_KEY);
    
    if (savedAddress && timestamp) {
      const timeSinceLastConnect = Date.now() - parseInt(timestamp);
      if (timeSinceLastConnect < SESSION_DURATION) {
        console.log('✅ Wallet loaded from storage:', shortAddr(savedAddress));
        return savedAddress;
      } else {
        console.log('⏰ Session expired');
        clearWalletFromStorage();
      }
    }
  } catch (error) {
    console.error('❌ Failed to load wallet:', error);
  }
  return null;
}

function clearWalletFromStorage() {
  try {
    localStorage.removeItem(WALLET_STORAGE_KEY);
    localStorage.removeItem(WALLET_TIMESTAMP_KEY);
    console.log('🗑️ Wallet cleared');
  } catch (error) {
    console.error('❌ Failed to clear wallet:', error);
  }
}

// ==================== BALANCE FUNCTIONS ====================
async function getSOLBalance(walletAddress) {
  try {
    console.log('📊 Fetching SOL balance...');
    const connection = new solanaWeb3.Connection(SOLANA_RPC, 'confirmed');
    const pubkey = new solanaWeb3.PublicKey(walletAddress);
    const balance = await connection.getBalance(pubkey);
    console.log('✅ SOL balance:', balance / 1e9);
    return balance / 1e9;
  } catch (error) {
    console.error('❌ Error fetching SOL:', error);
    return 0;
  }
}

async function getKulinoBalance(walletAddress) {
  try {
    console.log('📊 Fetching Kulino balance...');
    const connection = new solanaWeb3.Connection(SOLANA_RPC, 'confirmed');
    const pubkey = new solanaWeb3.PublicKey(walletAddress);
    const tokenMint = new solanaWeb3.PublicKey(KULINO_TOKEN_MINT);
    
    const tokenAccounts = await connection.getParsedTokenAccountsByOwner(pubkey, {
      mint: tokenMint
    });
    
    if (tokenAccounts.value.length > 0) {
      const balance = tokenAccounts.value[0].account.data.parsed.info.tokenAmount.uiAmount || 0;
      console.log('✅ Kulino balance:', balance);
      return balance;
    }
    console.log('ℹ️ No Kulino tokens found');
    return 0;
  } catch (error) {
    console.error('❌ Error fetching Kulino:', error);
    return 0;
  }
}

async function updateBalanceDisplay(address = userAddress) {
  if (!address) {
    console.log('⚠️ No address for balance update');
    return;
  }

  const kulinoEl = document.getElementById("kulinoBalance");
  const solEl = document.getElementById("solBalance");
  const refreshBtn = document.getElementById("refreshBalanceBtn");
  
  // Show loading
  if (kulinoEl) kulinoEl.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-yellow-300 border-t-transparent rounded-full animate-spin"></span>';
  if (refreshBtn) {
    refreshBtn.disabled = true;
    refreshBtn.classList.add('opacity-50', 'cursor-not-allowed');
  }
  
  try {
    [kulinoBalance, solBalance] = await Promise.all([
      getKulinoBalance(address),
      getSOLBalance(address)
    ]);
    
    if (kulinoEl) kulinoEl.innerHTML = `<strong>${formatKulinoBalance(kulinoBalance)}</strong> KULINO`;
    if (solEl) solEl.textContent = `${formatSOLBalance(solBalance)} SOL`;
    
    console.log('✅ Balance updated');
    
    if (unityInstance?.SendMessage) {
      unityInstance.SendMessage("GameManager", "OnBalanceUpdated", kulinoBalance.toString());
    }
  } catch (error) {
    console.error('❌ Failed to update balance:', error);
    if (kulinoEl) kulinoEl.innerHTML = '<strong>0.00</strong> KULINO';
    if (solEl) solEl.textContent = '0.0000 SOL';
  } finally {
    if (refreshBtn) {
      refreshBtn.disabled = false;
      refreshBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
  }
}

// ==================== UI UPDATE FUNCTIONS ====================
function updateConnectedUI(address) {
  console.log('🔄 Updating UI for:', shortAddr(address));
  userAddress = address;
  
  const walletStatus = document.getElementById("walletStatus");
  const addrShort = document.getElementById("addrShort");
  const disconnectBtn = document.getElementById("disconnectBtn");
  
  if (walletStatus) walletStatus.innerText = "Connected ✓";
  if (addrShort) addrShort.innerText = shortAddr(address);
  if (disconnectBtn) disconnectBtn.classList.remove("hidden");
  
  // Update Desktop Button
  const connectBtn = document.getElementById("connectBtn");
  if (connectBtn) {
    connectBtn.innerHTML = `
      <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <span class="font-semibold text-white">Connected</span>
    `;
    // Remove old listener and add new
    const newBtn = connectBtn.cloneNode(true);
    connectBtn.parentNode.replaceChild(newBtn, connectBtn);
    newBtn.addEventListener('click', showDisconnectDialog);
  }
  
  // Update Mobile Button
  const connectBtnMobile = document.getElementById("connectBtnMobile");
  if (connectBtnMobile) {
    connectBtnMobile.innerHTML = `
      <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <span class="font-semibold text-white">Connected</span>
    `;
    const newBtnMobile = connectBtnMobile.cloneNode(true);
    connectBtnMobile.parentNode.replaceChild(newBtnMobile, connectBtnMobile);
    newBtnMobile.addEventListener('click', showDisconnectDialog);
  }
  
  updateBalanceDisplay(address);
  saveWalletToStorage(address);
  
  if (unityInstance?.SendMessage) {
    unityInstance.SendMessage("GameManager", "OnWalletConnected", address);
  }
  
  console.log('✅ UI updated');
}

function resetDisconnectedUI() {
  console.log('🔄 Resetting UI to disconnected state');
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
  if (kulinoEl) kulinoEl.innerHTML = '<strong>0.00</strong> KULINO';
  if (solEl) solEl.textContent = '0.0000 SOL';
  if (disconnectBtn) disconnectBtn.classList.add("hidden");
  
  const buttonHTML = `
    <svg class="w-5 h-5 text-white transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
      <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
    </svg>
    <span class="font-semibold text-white">Connect Wallet</span>
  `;
  
  const connectBtn = document.getElementById("connectBtn");
  const connectBtnMobile = document.getElementById("connectBtnMobile");
  
  if (connectBtn) {
    connectBtn.innerHTML = buttonHTML;
    const newBtn = connectBtn.cloneNode(true);
    connectBtn.parentNode.replaceChild(newBtn, connectBtn);
    newBtn.addEventListener('click', connectWallet);
  }
  
  if (connectBtnMobile) {
    connectBtnMobile.innerHTML = buttonHTML;
    const newBtnMobile = connectBtnMobile.cloneNode(true);
    connectBtnMobile.parentNode.replaceChild(newBtnMobile, connectBtnMobile);
    newBtnMobile.addEventListener('click', connectWallet);
  }
  
  console.log('✅ UI reset complete');
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
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Disconnect',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#64748b',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) disconnectWallet();
  });
}

function disconnectWallet() {
  clearWalletFromStorage();
  
  if (provider?.disconnect) {
    try {
      provider.disconnect();
    } catch (error) {
      console.error('❌ Disconnect error:', error);
    }
  }
  
  resetDisconnectedUI();
  
  Swal.fire({
    icon: 'success',
    title: 'Disconnected',
    text: 'Wallet disconnected successfully',
    timer: 2000,
    showConfirmButton: false
  });
  
  console.log('✅ Wallet disconnected');
}

// ==================== AUTO-CONNECT ====================
async function autoConnectWallet() {
  const savedAddress = loadWalletFromStorage();
  if (!savedAddress) {
    console.log('ℹ️ No saved wallet');
    return;
  }
  
  console.log('🔄 Auto-connecting:', shortAddr(savedAddress));
  
  try {
    await waitForPhantom();
    
    if (!window.phantom?.solana?.isPhantom) {
      console.log('⚠️ Phantom not found');
      clearWalletFromStorage();
      return;
    }
    
    provider = window.phantom.solana;
    
    const resp = await provider.connect({ onlyIfTrusted: true });
    const currentAddress = resp.publicKey.toString();
    
    if (currentAddress.toLowerCase() === savedAddress.toLowerCase()) {
      updateConnectedUI(currentAddress);
      showToast('Wallet Auto-Connected', 'success');
    } else {
      console.log('⚠️ Address mismatch');
      clearWalletFromStorage();
    }
  } catch (error) {
    console.log('ℹ️ Auto-connect failed:', error.message);
    clearWalletFromStorage();
  }
}

function waitForPhantom(maxWait = 3000) {
  return new Promise((resolve, reject) => {
    if (window.phantom?.solana?.isPhantom) {
      resolve();
      return;
    }
    
    let waited = 0;
    const interval = 100;
    
    const checkInterval = setInterval(() => {
      if (window.phantom?.solana?.isPhantom) {
        clearInterval(checkInterval);
        resolve();
      } else if (waited >= maxWait) {
        clearInterval(checkInterval);
        reject(new Error('Phantom timeout'));
      }
      waited += interval;
    }, interval);
  });
}

// ==================== CONNECT WALLET ====================
async function connectWallet() {
  console.log('🔌 Connect wallet clicked');
  const isMobile = isMobileDevice();
  
  try {
    // MOBILE
    if (isMobile) {
      if (!window.phantom?.solana?.isPhantom) {
        Swal.fire({
          icon: 'info',
          title: 'Open in Phantom App',
          text: 'Please open this website in the Phantom app',
          showCancelButton: true,
          showDenyButton: true,
          confirmButtonText: 'Open Phantom',
          denyButtonText: 'Download',
          confirmButtonColor: '#667eea',
          denyButtonColor: '#10b981'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = getPhantomDeepLink();
          } else if (result.isDenied) {
            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const url = isIOS 
              ? 'https://apps.apple.com/us/app/phantom-solana-wallet/id1598432977'
              : 'https://play.google.com/store/apps/details?id=app.phantom';
            window.open(url, '_blank');
          }
        });
        return;
      }
      provider = window.phantom.solana;
    } 
    // DESKTOP
    else {
      console.log('🖥️ Desktop mode');
      if (!window.phantom?.solana?.isPhantom) {
        console.log('⚠️ Phantom not detected');
        Swal.fire({
          icon: 'warning',
          title: 'Phantom Not Found',
          text: 'Please install Phantom Wallet extension',
          showCancelButton: true,
          confirmButtonText: 'Install',
          confirmButtonColor: '#667eea'
        }).then((result) => {
          if (result.isConfirmed) {
            window.open('https://phantom.app/', '_blank');
          }
        });
        return;
      }
      console.log('✅ Phantom detected');
      provider = window.phantom.solana;
    }

    Swal.fire({
      title: 'Connecting...',
      text: 'Please approve in Phantom',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    console.log('🔄 Requesting connection...');
    const resp = await provider.connect();
    const address = resp.publicKey.toString();
    
    console.log('✅ Connected:', shortAddr(address));
    updateConnectedUI(address);

    Swal.fire({
      icon: 'success',
      title: 'Connected!',
      html: `
        <div class="space-y-3">
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-3 rounded-lg">
            <code class="text-xs font-mono text-indigo-600">${shortAddr(address)}</code>
          </div>
          <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <p class="text-sm text-green-800">✓ Auto-connect enabled</p>
          </div>
        </div>
      `,
      confirmButtonText: 'Start Playing',
      confirmButtonColor: '#667eea',
      timer: 5000
    });

  } catch (err) {
    console.error("❌ Connect error:", err);
    
    if (err.message?.includes('rejected') || err.code === 4001) {
      Swal.fire({
        icon: 'info',
        title: 'Cancelled',
        text: 'Connection cancelled',
        confirmButtonColor: '#667eea'
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Failed',
        text: err.message || 'Connection failed',
        confirmButtonColor: '#ef4444'
      });
    }
  }
}

// ==================== UTILITY ====================
function showToast(message, type = 'info') {
  const colors = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    info: 'bg-blue-500'
  };
  
  const toast = document.createElement('div');
  toast.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

function playGame(gameId) {
  if (!userAddress) {
    Swal.fire({
      icon: 'warning',
      title: 'Connect Wallet First',
      showCancelButton: true,
      confirmButtonText: 'Connect Now',
      confirmButtonColor: '#667eea'
    }).then((result) => {
      if (result.isConfirmed) connectWallet();
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
  if (slider) slider.scrollBy({ left: direction * 320, behavior: "smooth" });
}

// ==================== VISITOR TRACKING ====================
async function trackVisitor() {
  try {
    const res = await fetch("track.php?add=1");
    const data = await res.json();
    const el = document.getElementById("visitorCount");
    if (el) el.innerText = data.today || 0;
  } catch (error) {
    console.error("❌ Track visitor error:", error);
  }
}

// ==================== INITIALIZATION ====================
console.log('🚀 Script loaded');

// Wait for DOM and libraries
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeApp);
} else {
  initializeApp();
}

async function initializeApp() {
  console.log('🚀 Kulino Initializing...');
  
  // Check if Solana Web3 is available
  if (typeof solanaWeb3 === 'undefined') {
    console.error('❌ Solana Web3.js not loaded!');
    return;
  }
  console.log('✅ Solana Web3.js ready');
  
  // Track visitor
  trackVisitor();
  
  // Setup button listeners
  const connectBtn = document.getElementById("connectBtn");
  const connectBtnMobile = document.getElementById("connectBtnMobile");
  
  if (connectBtn) {
    console.log('✅ Desktop button found');
    connectBtn.addEventListener("click", (e) => {
      e.preventDefault();
      console.log('🖱️ Desktop button clicked');
      connectWallet();
    });
  } else {
    console.warn('⚠️ Desktop button not found');
  }
  
  if (connectBtnMobile) {
    console.log('✅ Mobile button found');
    connectBtnMobile.addEventListener("click", (e) => {
      e.preventDefault();
      console.log('🖱️ Mobile button clicked');
      connectWallet();
    });
  } else {
    console.warn('⚠️ Mobile button not found');
  }
  
  // Auto-connect after delay
  setTimeout(async () => {
    await autoConnectWallet();
  }, 1500);
  
  // Video previews
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
  
  // Mobile menu
  const menuToggle = document.getElementById("menuToggle");
  const mobileMenu = document.getElementById("mobileMenu");
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
      menuToggle.classList.toggle("open");
    });
  }
  
  console.log('✅ Kulino Initialized');
}

// Expose globals
window.connectWallet = connectWallet;
window.playGame = playGame;
window.scrollSlider = scrollSlider;
window.disconnectWallet = disconnectWallet;
window.updateBalanceDisplay = updateBalanceDisplay;
