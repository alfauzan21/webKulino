// ==================== CONFIGURATION ====================
const KULINO_TOKEN_MINT = 'E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1';
const SOLANA_RPC = 'https://api.mainnet-beta.solana.com';
const WALLET_STORAGE_KEY = 'kulino_connected_wallet';
const SESSION_DURATION = 30 * 24 * 60 * 60 * 1000; // 30 days

// ==================== GLOBAL STATE ====================
let provider = null;
let userAddress = null;
let kulinoBalance = 0;
let solBalance = 0;

console.log('🚀 Script loading...');

// ==================== UTILITY FUNCTIONS ====================
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

function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

function showToast(message, type = 'info') {
  const colors = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    info: 'bg-blue-500'
  };
  
  const toast = document.createElement('div');
  toast.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 300);
  }, 2700);
}

// ==================== BALANCE FUNCTIONS ====================
async function getSOLBalance(walletAddress) {
  try {
    console.log('📊 Fetching SOL balance...');
    const connection = new solanaWeb3.Connection(SOLANA_RPC, 'confirmed');
    const pubkey = new solanaWeb3.PublicKey(walletAddress);
    const balance = await connection.getBalance(pubkey);
    const solAmount = balance / solanaWeb3.LAMPORTS_PER_SOL;
    console.log('✅ SOL balance:', solAmount);
    return solAmount;
  } catch (error) {
    console.error('❌ SOL fetch error:', error);
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
    console.log('ℹ️ No Kulino tokens');
    return 0;
  } catch (error) {
    console.error('❌ Kulino fetch error:', error);
    return 0;
  }
}

async function updateBalanceDisplay(address = userAddress) {
  if (!address) {
    console.log('⚠️ No address');
    return;
  }

  const kulinoEl = document.getElementById("kulinoBalance");
  const solEl = document.getElementById("solBalance");
  const refreshBtn = document.getElementById("refreshBalanceBtn");
  
  if (kulinoEl) kulinoEl.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-yellow-300 border-t-transparent rounded-full animate-spin"></span>';
  if (refreshBtn) {
    refreshBtn.disabled = true;
    refreshBtn.classList.add('opacity-50');
  }
  
  try {
    [kulinoBalance, solBalance] = await Promise.all([
      getKulinoBalance(address),
      getSOLBalance(address)
    ]);
    
    if (kulinoEl) kulinoEl.innerHTML = `<strong>${formatKulinoBalance(kulinoBalance)}</strong> KULINO`;
    if (solEl) solEl.textContent = `${formatSOLBalance(solBalance)} SOL`;
    
    console.log('✅ Balance updated');
  } catch (error) {
    console.error('❌ Balance update failed:', error);
    if (kulinoEl) kulinoEl.innerHTML = '<strong>0.00</strong> KULINO';
    if (solEl) solEl.textContent = '0.0000 SOL';
  } finally {
    if (refreshBtn) {
      refreshBtn.disabled = false;
      refreshBtn.classList.remove('opacity-50');
    }
  }
}

// ==================== UI UPDATE ====================
function updateConnectedUI(address) {
  console.log('🔄 Updating UI for:', shortAddr(address));
  userAddress = address;
  
  // Update wallet status
  const walletStatus = document.getElementById("walletStatus");
  const addrShort = document.getElementById("addrShort");
  const disconnectBtn = document.getElementById("disconnectBtn");
  
  if (walletStatus) walletStatus.innerText = "Connected ✓";
  if (addrShort) addrShort.innerText = shortAddr(address);
  if (disconnectBtn) disconnectBtn.classList.remove("hidden");
  
  // Update connect buttons
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
  
  // Save to storage
  try {
    localStorage.setItem(WALLET_STORAGE_KEY, address);
    localStorage.setItem('kulino_wallet_timestamp', Date.now().toString());
  } catch (e) {
    console.error('Storage error:', e);
  }
  
  // Update balance
  updateBalanceDisplay(address);
  
  console.log('✅ UI updated');
}

function resetDisconnectedUI() {
  console.log('🔄 Resetting UI');
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
  
  if (connectBtn) connectBtn.innerHTML = buttonHTML;
  if (connectBtnMobile) connectBtnMobile.innerHTML = buttonHTML;
  
  try {
    localStorage.removeItem(WALLET_STORAGE_KEY);
    localStorage.removeItem('kulino_wallet_timestamp');
  } catch (e) {
    console.error('Storage error:', e);
  }
  
  console.log('✅ UI reset');
}

// ==================== CONNECT WALLET ====================
async function connectWallet() {
  console.log('🔌 Connect wallet clicked!');
  
  const isMobile = isMobileDevice();
  
  try {
    // Check Phantom
    if (!window.phantom?.solana?.isPhantom) {
      console.log('⚠️ Phantom not found');
      
      if (isMobile) {
        await Swal.fire({
          icon: 'info',
          title: 'Open in Phantom App',
          text: 'Please open this website in the Phantom app browser',
          showCancelButton: true,
          showDenyButton: true,
          confirmButtonText: 'Open Phantom',
          denyButtonText: 'Download App',
          confirmButtonColor: '#667eea',
          denyButtonColor: '#10b981'
        }).then((result) => {
          if (result.isConfirmed) {
            const deepLink = `https://phantom.app/ul/browse/${encodeURIComponent(window.location.href)}`;
            window.location.href = deepLink;
          } else if (result.isDenied) {
            const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
            const url = isIOS 
              ? 'https://apps.apple.com/app/phantom-solana-wallet/id1598432977'
              : 'https://play.google.com/store/apps/details?id=app.phantom';
            window.open(url, '_blank');
          }
        });
      } else {
        await Swal.fire({
          icon: 'warning',
          title: 'Phantom Not Installed',
          text: 'Please install the Phantom Wallet extension',
          showCancelButton: true,
          confirmButtonText: 'Install Extension',
          confirmButtonColor: '#667eea'
        }).then((result) => {
          if (result.isConfirmed) {
            window.open('https://phantom.app/', '_blank');
          }
        });
      }
      return;
    }

    console.log('✅ Phantom detected');
    provider = window.phantom.solana;

    // Show loading
    Swal.fire({
      title: 'Connecting...',
      text: 'Please approve the connection in Phantom',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    // Connect
    console.log('🔄 Requesting connection...');
    const resp = await provider.connect();
    const address = resp.publicKey.toString();
    
    console.log('✅ Connected:', shortAddr(address));
    
    // Update UI
    updateConnectedUI(address);

    // Success message
    await Swal.fire({
      icon: 'success',
      title: 'Connected!',
      html: `
        <div class="space-y-3">
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-3 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Wallet Address:</p>
            <code class="text-xs font-mono text-indigo-600 font-semibold">${shortAddr(address)}</code>
          </div>
          <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <p class="text-sm text-green-800">✓ Auto-reconnect enabled for 30 days</p>
          </div>
        </div>
      `,
      confirmButtonText: 'Start Playing',
      confirmButtonColor: '#667eea',
      timer: 5000
    });

  } catch (err) {
    console.error("❌ Connect error:", err);
    
    Swal.close();
    
    if (err.message?.includes('rejected') || err.code === 4001) {
      await Swal.fire({
        icon: 'info',
        title: 'Connection Cancelled',
        text: 'You cancelled the wallet connection',
        confirmButtonColor: '#667eea'
      });
    } else {
      await Swal.fire({
        icon: 'error',
        title: 'Connection Failed',
        text: err.message || 'Failed to connect wallet',
        confirmButtonColor: '#ef4444'
      });
    }
  }
}

// ==================== DISCONNECT ====================
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
  if (provider?.disconnect) {
    try {
      provider.disconnect();
    } catch (e) {
      console.error('Disconnect error:', e);
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
}

// ==================== AUTO-CONNECT ====================
async function autoConnectWallet() {
  try {
    const savedAddress = localStorage.getItem(WALLET_STORAGE_KEY);
    const timestamp = localStorage.getItem('kulino_wallet_timestamp');
    
    if (!savedAddress || !timestamp) {
      console.log('ℹ️ No saved wallet');
      return;
    }
    
    const timeSince = Date.now() - parseInt(timestamp);
    if (timeSince > SESSION_DURATION) {
      console.log('⏰ Session expired');
      localStorage.removeItem(WALLET_STORAGE_KEY);
      localStorage.removeItem('kulino_wallet_timestamp');
      return;
    }
    
    if (!window.phantom?.solana?.isPhantom) {
      console.log('⚠️ Phantom not available');
      return;
    }
    
    console.log('🔄 Auto-connecting:', shortAddr(savedAddress));
    provider = window.phantom.solana;
    
    const resp = await provider.connect({ onlyIfTrusted: true });
    const address = resp.publicKey.toString();
    
    if (address.toLowerCase() === savedAddress.toLowerCase()) {
      updateConnectedUI(address);
      showToast('Wallet Auto-Connected', 'success');
      console.log('✅ Auto-connected');
    } else {
      console.log('⚠️ Address mismatch');
      localStorage.removeItem(WALLET_STORAGE_KEY);
    }
  } catch (error) {
    console.log('ℹ️ Auto-connect skipped:', error.message);
  }
}

// ==================== GAME & UTILITIES ====================
// ==================== GAME PLAY FUNCTION ====================
function playGame(gameId) {
  console.log('🎮 playGame called with:', gameId);
  
  if (!userAddress) {
    Swal.fire({
      icon: 'warning',
      title: 'Connect Wallet First',
      text: 'Please connect your wallet before playing',
      showCancelButton: true,
      confirmButtonText: 'Connect Now',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#667eea',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) connectWallet();
    });
    return;
  }

  // Construct game URL
  const baseUrl = window.location.origin + window.location.pathname.replace('index.php', '');
  const gameUrl = `${baseUrl}WebUnity/index.html?wallet=${encodeURIComponent(userAddress)}&game=${encodeURIComponent(gameId)}`;
  
  console.log('🚀 Opening game URL:', gameUrl);
  
  // Show loading message
  Swal.fire({
    title: 'Loading Game...',
    html: `
      <div class="text-center">
        <div class="inline-block w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600">Opening <strong>${gameId}</strong></p>
        <p class="text-sm text-gray-500 mt-2">Game will open in a new tab</p>
      </div>
    `,
    showConfirmButton: false,
    timer: 2000,
    allowOutsideClick: false
  });
  
  // Open game in new tab
  setTimeout(() => {
    const newWindow = window.open(gameUrl, "_blank");
    
    // Check if popup was blocked
    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
      Swal.fire({
        icon: 'error',
        title: 'Popup Blocked',
        html: `
          <p>Your browser blocked the game window.</p>
          <p class="text-sm text-gray-600 mt-2">Please allow popups for this site or click the button below:</p>
        `,
        showCancelButton: true,
        confirmButtonText: 'Try Again',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#667eea'
      }).then((result) => {
        if (result.isConfirmed) {
          window.open(gameUrl, "_blank");
        }
      });
    } else {
      Swal.fire({
        icon: 'success',
        title: 'Game Opened!',
        text: 'Check your new tab to play',
        timer: 2000,
        showConfirmButton: false
      });
    }
  }, 500);
}

// ==================== INITIALIZATION ====================
function waitForLibraries() {
  return new Promise((resolve) => {
    if (typeof solanaWeb3 !== 'undefined' && typeof Swal !== 'undefined') {
      resolve();
      return;
    }
    
    const checkInterval = setInterval(() => {
      if (typeof solanaWeb3 !== 'undefined' && typeof Swal !== 'undefined') {
        clearInterval(checkInterval);
        resolve();
      }
    }, 100);
    
    // Timeout after 10 seconds
    setTimeout(() => {
      clearInterval(checkInterval);
      console.error('❌ Libraries failed to load');
      resolve();
    }, 10000);
  });
}

async function initializeApp() {
  console.log('⏳ Waiting for libraries...');
  await waitForLibraries();
  
  if (typeof solanaWeb3 === 'undefined') {
    console.error('❌ Solana Web3 not loaded!');
    return;
  }
  
  if (typeof Swal === 'undefined') {
    console.error('❌ SweetAlert2 not loaded!');
    return;
  }
  
  console.log('✅ Libraries ready');
  console.log('🚀 Initializing Kulino...');
  
  // Track visitor
  trackVisitor();
  
  // Setup buttons with proper event listeners
  const connectBtn = document.getElementById("connectBtn");
  const connectBtnMobile = document.getElementById("connectBtnMobile");
  const disconnectBtn = document.getElementById("disconnectBtn");
  
  if (connectBtn) {
    console.log('✅ Desktop button found');
    connectBtn.removeAttribute('onclick'); // Remove any inline onclick
    connectBtn.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('🖱️ Desktop button clicked');
      connectWallet();
    }, { passive: false });
  }
  
  if (connectBtnMobile) {
    console.log('✅ Mobile button found');
    connectBtnMobile.removeAttribute('onclick');
    connectBtnMobile.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();
      console.log('🖱️ Mobile button clicked');
      connectWallet();
    }, { passive: false });
  }
  
  if (disconnectBtn) {
    disconnectBtn.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation();
      showDisconnectDialog();
    });
  }
  
  // Setup refresh balance button
  const refreshBtn = document.getElementById("refreshBalanceBtn");
  if (refreshBtn) {
    refreshBtn.addEventListener("click", function(e) {
      e.preventDefault();
      updateBalanceDisplay();
    });
  }
  
  // Video hover effects
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
  
  // Mobile menu toggle
  const menuToggle = document.getElementById("menuToggle");
  const mobileMenu = document.getElementById("mobileMenu");
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
      menuToggle.classList.toggle("open");
    });
  }
  
  // Auto-connect
  setTimeout(() => {
    autoConnectWallet();
  }, 1000);
  
  console.log('✅ Kulino Ready!');
}

// Start when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeApp);
} else {
  initializeApp();
}

// Expose functions globally
window.connectWallet = connectWallet;
window.disconnectWallet = disconnectWallet;
window.playGame = playGame;
window.scrollSlider = scrollSlider;
window.updateBalanceDisplay = updateBalanceDisplay;
window.showDisconnectDialog = showDisconnectDialog;
