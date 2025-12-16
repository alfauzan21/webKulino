// ==================== SWAP CONFIGURATION ====================
const KULINO_MINT = "E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1";

// Token configurations
const SUPPORTED_TOKENS = {
  BTC: {
    symbol: 'BTC',
    name: 'Bitcoin',
    icon: 'https://cryptologos.cc/logos/bitcoin-btc-logo.png',
    decimals: 8,
    coingeckoId: 'bitcoin',
    chain: 'bitcoin'
  },
  SOL: {
    symbol: 'SOL',
    name: 'Solana',
    icon: 'https://cryptologos.cc/logos/solana-sol-logo.png',
    decimals: 9,
    coingeckoId: 'solana',
    chain: 'solana',
    mint: 'So11111111111111111111111111111111111111112'
  },
  ETH: {
    symbol: 'ETH',
    name: 'Ethereum',
    icon: 'https://cryptologos.cc/logos/ethereum-eth-logo.png',
    decimals: 18,
    coingeckoId: 'ethereum',
    chain: 'ethereum'
  },
  XRP: {
    symbol: 'XRP',
    name: 'Ripple',
    icon: 'https://cryptologos.cc/logos/xrp-xrp-logo.png',
    decimals: 6,
    coingeckoId: 'ripple',
    chain: 'xrp'
  },
  MATIC: {
    symbol: 'MATIC',
    name: 'Polygon',
    icon: 'https://cryptologos.cc/logos/polygon-matic-logo.png',
    decimals: 18,
    coingeckoId: 'matic-network',
    chain: 'polygon'
  },
  SUI: {
    symbol: 'SUI',
    name: 'Sui',
    icon: 'https://cryptologos.cc/logos/sui-sui-logo.png',
    decimals: 9,
    coingeckoId: 'sui',
    chain: 'sui'
  }
};

// Global state
let selectedFromToken = SUPPORTED_TOKENS.BTC;
let selectedToToken = 'KULINO';
let swapSettings = {
  slippage: 0.5,
  deadline: 20
};
let tokenPrices = {};
let isCalculating = false;

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', () => {
  console.log('🔄 Initializing Swap System...');
  
  // Load token prices
  loadTokenPrices();
  
  // Update prices every 30 seconds
  setInterval(loadTokenPrices, 30000);
  
  // Initialize UI
  updateSwapButton();
});

// ==================== TOKEN PRICE FETCHING ====================
async function loadTokenPrices() {
  try {
    const tokenIds = Object.values(SUPPORTED_TOKENS)
      .map(t => t.coingeckoId)
      .join(',');
    
    const response = await fetch(
      `https://api.coingecko.com/api/v3/simple/price?ids=${tokenIds}&vs_currencies=usd`
    );
    
    if (!response.ok) throw new Error('Failed to fetch prices');
    
    const data = await response.json();
    
    // Map prices to our token symbols
    Object.values(SUPPORTED_TOKENS).forEach(token => {
      if (data[token.coingeckoId]) {
        tokenPrices[token.symbol] = data[token.coingeckoId].usd;
      }
    });
    
    // Add KULINO price (example: $0.01)
    tokenPrices['KULINO'] = 0.01;
    
    console.log('✅ Token prices updated:', tokenPrices);
    
    // Update UI if amount is entered
    if (document.getElementById('fromAmount').value) {
      calculateSwapAmount();
    }
  } catch (error) {
    console.error('❌ Price fetch error:', error);
    showToast('Failed to load token prices', 'error');
  }
}

// ==================== SWAP CALCULATION ====================
async function calculateSwapAmount() {
  if (isCalculating) return;
  
  const fromAmount = parseFloat(document.getElementById('fromAmount').value);
  
  if (!fromAmount || fromAmount <= 0) {
    document.getElementById('toAmount').value = '';
    document.getElementById('fromAmountUSD').textContent = '≈ $0.00 USD';
    document.getElementById('toAmountUSD').textContent = '≈ $0.00 USD';
    document.getElementById('swapInfo').classList.add('hidden');
    return;
  }
  
  isCalculating = true;
  
  try {
    const fromPrice = tokenPrices[selectedFromToken.symbol] || 0;
    const toPrice = tokenPrices['KULINO'] || 0.01;
    
    if (!fromPrice || !toPrice) {
      throw new Error('Token prices not available');
    }
    
    // Calculate USD value
    const fromUSD = fromAmount * fromPrice;
    
    // Calculate output amount (with 0.3% swap fee)
    const fee = 0.003; // 0.3%
    const toAmount = (fromUSD / toPrice) * (1 - fee);
    
    // Update UI with animation
    const toAmountEl = document.getElementById('toAmount');
    toAmountEl.classList.add('number-animate');
    toAmountEl.value = toAmount.toFixed(6);
    setTimeout(() => toAmountEl.classList.remove('number-animate'), 300);
    
    // Update USD values
    document.getElementById('fromAmountUSD').textContent = `≈ $${fromUSD.toFixed(2)} USD`;
    document.getElementById('toAmountUSD').textContent = `≈ $${(toAmount * toPrice).toFixed(2)} USD`;
    
    // Update swap info
    document.getElementById('exchangeRate').textContent = 
      `1 ${selectedFromToken.symbol} = ${(fromPrice / toPrice).toFixed(2)} KULINO`;
    
    document.getElementById('networkFee').textContent = 
      selectedFromToken.chain === 'solana' ? '~$0.01' : '~$2-5';
    
    document.getElementById('slippageTolerance').textContent = `${swapSettings.slippage}%`;
    
    document.getElementById('estimatedTime').textContent = 
      selectedFromToken.chain === 'solana' ? '~30 seconds' : '~2-3 minutes';
    
    // Show swap info
    document.getElementById('swapInfo').classList.remove('hidden');
    
  } catch (error) {
    console.error('❌ Calculation error:', error);
    showToast('Failed to calculate swap amount', 'error');
  } finally {
    isCalculating = false;
  }
}

// ==================== TOKEN SELECTION ====================
function openTokenSelector(type) {
  document.getElementById('tokenSelectorModal').classList.remove('hidden');
  document.getElementById('tokenSelectorModal').classList.add('flex');
  document.getElementById('tokenSelectorModal').dataset.selectType = type;
}

function closeTokenSelector() {
  document.getElementById('tokenSelectorModal').classList.add('hidden');
  document.getElementById('tokenSelectorModal').classList.remove('flex');
}

function selectToken(symbol, name, icon, id) {
  const token = SUPPORTED_TOKENS[symbol];
  if (!token) return;
  
  selectedFromToken = token;
  
  // Update UI
  document.getElementById('fromTokenIcon').src = icon;
  document.getElementById('fromTokenSymbol').textContent = symbol;
  document.getElementById('fromTokenName').textContent = name;
  
  // Reset amounts
  document.getElementById('fromAmount').value = '';
  document.getElementById('toAmount').value = '';
  
  closeTokenSelector();
  calculateSwapAmount();
  
  console.log('✅ Token selected:', symbol);
}

function filterTokenList() {
  const search = document.getElementById('tokenSearchInput').value.toLowerCase();
  const items = document.querySelectorAll('.token-item');
  
  items.forEach(item => {
    const text = item.textContent.toLowerCase();
    item.style.display = text.includes(search) ? 'flex' : 'none';
  });
}

// ==================== SWAP SETTINGS ====================
function openSwapSettings() {
  document.getElementById('swapSettingsModal').classList.remove('hidden');
  document.getElementById('swapSettingsModal').classList.add('flex');
}

function closeSwapSettings() {
  document.getElementById('swapSettingsModal').classList.add('hidden');
  document.getElementById('swapSettingsModal').classList.remove('flex');
}

function setSlippage(value) {
  swapSettings.slippage = value;
  
  // Update UI
  document.querySelectorAll('.slippage-btn').forEach(btn => {
    btn.classList.remove('active', 'bg-blue-500');
    btn.classList.add('bg-white/5');
  });
  
  document.getElementById('customSlippage').value = value;
  
  // Update swap info if visible
  if (!document.getElementById('swapInfo').classList.contains('hidden')) {
    document.getElementById('slippageTolerance').textContent = `${value}%`;
  }
  
  console.log('✅ Slippage set to:', value + '%');
}

// ==================== SWAP UTILITIES ====================
function setMaxAmount() {
  const balance = parseFloat(document.getElementById('fromBalance').textContent);
  if (balance > 0) {
    document.getElementById('fromAmount').value = balance.toFixed(6);
    calculateSwapAmount();
  } else {
    showToast('No balance available', 'warning');
  }
}

function flipSwapDirection() {
  // Animate arrow
  const arrow = document.getElementById('swapArrow');
  arrow.style.transform = 'rotate(180deg)';
  setTimeout(() => {
    arrow.style.transform = 'rotate(0deg)';
  }, 300);
  
  showToast('Swap direction flipped!', 'info');
}

// ==================== MAIN SWAP EXECUTION ====================
async function executeSwap() {
  console.log('🔄 Executing swap...');
  
  // Check wallet connection
  if (!userAddress) {
    Swal.fire({
      icon: 'warning',
      title: 'Connect Wallet',
      text: 'Please connect your wallet first',
      confirmButtonColor: '#667eea'
    });
    return;
  }
  
  const fromAmount = parseFloat(document.getElementById('fromAmount').value);
  const toAmount = parseFloat(document.getElementById('toAmount').value);
  
  if (!fromAmount || fromAmount <= 0) {
    showToast('Please enter an amount', 'warning');
    return;
  }
  
  // Show confirmation dialog
  const result = await Swal.fire({
    title: 'Confirm Swap',
    html: `
      <div class="text-left space-y-3 bg-gray-50 p-4 rounded-lg">
        <div class="flex justify-between">
          <span class="text-gray-600">You Pay:</span>
          <span class="font-bold">${fromAmount} ${selectedFromToken.symbol}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-600">You Receive:</span>
          <span class="font-bold text-green-600">${toAmount.toFixed(6)} KULINO</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Exchange Rate:</span>
          <span class="font-semibold">${document.getElementById('exchangeRate').textContent}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Network Fee:</span>
          <span class="font-semibold">${document.getElementById('networkFee').textContent}</span>
        </div>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Confirm Swap',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#667eea',
    cancelButtonColor: '#6c757d',
  });
  
  if (!result.isConfirmed) return;
  
  // Show loading
  Swal.fire({
    title: 'Processing Swap...',
    html: `
      <div class="text-center">
        <div class="inline-block w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600">Swapping ${selectedFromToken.symbol} to KULINO</p>
        <p class="text-sm text-gray-500 mt-2">Please confirm in your wallet</p>
      </div>
    `,
    allowOutsideClick: false,
    showConfirmButton: false,
  });
  
  try {
    // Execute swap based on chain
    let txHash;
    
    if (selectedFromToken.chain === 'solana') {
      txHash = await executeSOLSwap(fromAmount, toAmount);
    } else if (selectedFromToken.chain === 'ethereum') {
      txHash = await executeETHSwap(fromAmount, toAmount);
    } else if (selectedFromToken.chain === 'polygon') {
      txHash = await executePolygonSwap(fromAmount, toAmount);
    } else {
      throw new Error(`${selectedFromToken.chain} swaps not yet supported`);
    }
    
    // Success
    await Swal.fire({
      icon: 'success',
      title: 'Swap Successful!',
      html: `
        <div class="space-y-3">
          <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Transaction Hash:</p>
            <code class="text-xs text-green-600 break-all">${txHash}</code>
          </div>
          <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-4 rounded-lg border border-yellow-200">
            <p class="text-gray-700">You received:</p>
            <p class="text-2xl font-bold text-yellow-600">${toAmount.toFixed(6)} KULINO</p>
          </div>
        </div>
      `,
      confirmButtonColor: '#10b981',
      timer: 10000
    });
    
    // Reset form
    document.getElementById('fromAmount').value = '';
    document.getElementById('toAmount').value = '';
    
    // Update balances
    updateBalanceDisplay();
    
  } catch (error) {
    console.error('❌ Swap error:', error);
    
    Swal.fire({
      icon: 'error',
      title: 'Swap Failed',
      text: error.message || 'Failed to execute swap',
      confirmButtonColor: '#ef4444'
    });
  }
}

// ==================== CHAIN-SPECIFIC SWAP FUNCTIONS ====================

// Solana Swap via Jupiter
async function executeSOLSwap(fromAmount, toAmount) {
  console.log('🌐 Executing Solana swap via Jupiter...');
  
  try {
    // This is a simplified example - you need Jupiter SDK integration
    const connection = getConnection();
    
    // Get Jupiter quote
    const quoteResponse = await fetch(
      `https://quote-api.jup.ag/v6/quote?inputMint=${selectedFromToken.mint}&outputMint=${KULINO_MINT}&amount=${Math.floor(fromAmount * Math.pow(10, selectedFromToken.decimals))}&slippageBps=${swapSettings.slippage * 100}`
    );
    
    if (!quoteResponse.ok) throw new Error('Failed to get Jupiter quote');
    
    const quoteData = await quoteResponse.json();
    
    // Get swap transaction
    const swapResponse = await fetch('https://quote-api.jup.ag/v6/swap', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        quoteResponse: quoteData,
        userPublicKey: userAddress,
        wrapAndUnwrapSol: true,
      })
    });
    
    if (!swapResponse.ok) throw new Error('Failed to get swap transaction');
    
    const { swapTransaction } = await swapResponse.json();
    
    // Sign and send transaction
    const txBuffer = Buffer.from(swapTransaction, 'base64');
    const transaction = solanaWeb3.Transaction.from(txBuffer);
    
    const signed = await provider.signTransaction(transaction);
    const txHash = await connection.sendRawTransaction(signed.serialize());
    
    // Wait for confirmation
    await connection.confirmTransaction(txHash, 'confirmed');
    
    console.log('✅ Solana swap successful:', txHash);
    return txHash;
    
  } catch (error) {
    console.error('❌ Solana swap error:', error);
    throw error;
  }
}

// Ethereum/Polygon Swap via 1inch
async function executeETHSwap(fromAmount, toAmount) {
  console.log('🌐 Executing Ethereum swap...');
  throw new Error('Ethereum swaps coming soon! Please use Solana for now.');
}

async function executePolygonSwap(fromAmount, toAmount) {
  console.log('🌐 Executing Polygon swap...');
  throw new Error('Polygon swaps coming soon! Please use Solana for now.');
}

// ==================== UI UPDATES ====================
function updateSwapButton() {
  const button = document.getElementById('swapButton');
  const buttonText = document.getElementById('swapButtonText');
  
  if (!userAddress) {
    buttonText.textContent = 'Connect Wallet to Swap';
    button.disabled = true;
  } else if (!document.getElementById('fromAmount').value) {
    buttonText.textContent = 'Enter Amount';
    button.disabled = true;
  } else {
    buttonText.textContent = `Swap ${selectedFromToken.symbol} to KULINO`;
    button.disabled = false;
  }
}

// Update button state on input
document.addEventListener('DOMContentLoaded', () => {
  const fromInput = document.getElementById('fromAmount');
  if (fromInput) {
    fromInput.addEventListener('input', updateSwapButton);
  }
});

// ==================== EXPOSE FUNCTIONS ====================
window.openTokenSelector = openTokenSelector;
window.closeTokenSelector = closeTokenSelector;
window.selectToken = selectToken;
window.filterTokenList = filterTokenList;
window.openSwapSettings = openSwapSettings;
window.closeSwapSettings = closeSwapSettings;
window.setSlippage = setSlippage;
window.setMaxAmount = setMaxAmount;
window.flipSwapDirection = flipSwapDirection;
window.calculateSwapAmount = calculateSwapAmount;
window.executeSwap = executeSwap;

console.log('✅ Swap system loaded');