<?php include("./includes/koneksi.php"); ?>
<?php include("./includes/config.php"); ?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Documentation - Kulino Game Hub</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- CSS -->
  <link rel="stylesheet" href="./css/style-index.css" />
  
  <style>
    .doc-card {
      transition: all 0.3s ease;
    }
    
    .doc-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .gradient-border {
      position: relative;
      background: linear-gradient(white, white) padding-box,
                  linear-gradient(135deg, #667eea 0%, #764ba2 100%) border-box;
      border: 2px solid transparent;
    }
    
    .code-block {
      background: #1e1e1e;
      color: #d4d4d4;
      border-radius: 8px;
      padding: 1rem;
      overflow-x: auto;
      font-family: 'Courier New', monospace;
      font-size: 0.875rem;
    }
    
    .badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-900">

  <!-- Header -->
  <header id="mainHeader" class="bg-white shadow-md sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      
      <!-- Logo -->
      <a href="<?php echo $base_url; ?>index.php" class="flex items-start gap-3">
        <div class="w-10 h-10">
          <img src="assets/icon/kulino-logo-blue.png" alt="Kulino Logo" class="w-full h-full object-contain" />
        </div>
        <div class="flex flex-col">
          <h1 class="text-base sm:text-lg font-semibold leading-snug">
            Kulino Game Hub
          </h1>
          <p class="text-xs text-gray-500 mt-1">Developer Documentation</p>
        </div>
      </a>

      <!-- Nav Links -->
      <nav class="hidden md:flex items-center gap-6">
        <a href="index.php" class="text-gray-600 hover:text-blue-600 transition">Home</a>
        <a href="faq.php" class="text-gray-600 hover:text-blue-600 transition">FAQ</a>
        <a href="documentation.php" class="text-blue-600 font-semibold">Documentation</a>
      </nav>

      <!-- Back Button Mobile -->
      <a href="index.php" class="md:hidden px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition text-sm">
        Home
      </a>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-10">
    
    <!-- Hero Section -->
    <section class="mb-12 text-center">
      <div class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold mb-4">
        🚀 Developer Resources
      </div>
      <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
        Kulino Documentation
      </h1>
      <p class="text-lg text-gray-600 max-w-3xl mx-auto">
        Panduan lengkap untuk developer yang ingin mengintegrasikan game dengan Kulino Game Hub dan sistem reward blockchain Solana.
      </p>
    </section>

    <!-- Quick Links -->
    <section class="mb-12">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="#getting-started" class="p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition text-center">
          <div class="text-3xl mb-2">🎯</div>
          <div class="font-semibold text-gray-900">Getting Started</div>
        </a>
        <a href="#wallet-integration" class="p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition text-center">
          <div class="text-3xl mb-2">👛</div>
          <div class="font-semibold text-gray-900">Wallet Integration</div>
        </a>
        <a href="#game-integration" class="p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition text-center">
          <div class="text-3xl mb-2">🎮</div>
          <div class="font-semibold text-gray-900">Game Integration</div>
        </a>
        <a href="#api-reference" class="p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition text-center">
          <div class="text-3xl mb-2">📚</div>
          <div class="font-semibold text-gray-900">API Reference</div>
        </a>
      </div>
    </section>

    <!-- Main Documentation Cards -->
    <section class="space-y-8">

      <!-- Getting Started -->
      <div id="getting-started" class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
          <h2 class="text-2xl font-bold flex items-center gap-3">
            <span class="text-3xl">🎯</span>
            Getting Started
          </h2>
          <p class="mt-2 text-blue-100">Mulai integrasi game Anda dengan Kulino dalam 5 menit</p>
        </div>
        <div class="p-6">
          <div class="space-y-6">
            <div>
              <h3 class="text-xl font-semibold mb-3 flex items-center gap-2">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">1</span>
                Prerequisite
              </h3>
              <ul class="list-disc ml-12 space-y-2 text-gray-700">
                <li>Game berbasis Unity WebGL (Unity 2020.3 atau lebih baru)</li>
                <li>Akun developer Kulino (daftar di developer.kulino.com)</li>
                <li>Pengetahuan dasar tentang Solana blockchain</li>
                <li>Node.js 16+ untuk backend API (opsional)</li>
              </ul>
            </div>

            <div>
              <h3 class="text-xl font-semibold mb-3 flex items-center gap-2">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">2</span>
                Install Dependencies
              </h3>
              <div class="code-block">
                <pre>npm install @solana/web3.js bs58
npm install express cors dotenv</pre>
              </div>
            </div>

            <div>
              <h3 class="text-xl font-semibold mb-3 flex items-center gap-2">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">3</span>
                Environment Setup
              </h3>
              <div class="code-block">
                <pre># .env file
SOLANA_RPC_URL=https://api.mainnet-beta.solana.com
WALLET_PRIVATE_KEY=your_private_key_here
API_SECRET=your_api_secret</pre>
              </div>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
              <p class="font-semibold text-blue-900 mb-2">💡 Pro Tip:</p>
              <p class="text-blue-800 text-sm">Gunakan testnet terlebih dahulu untuk development. Ganti RPC URL ke devnet: <code class="bg-blue-200 px-2 py-1 rounded">https://api.devnet.solana.com</code></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Wallet Integration -->
      <div id="wallet-integration" class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white">
          <h2 class="text-2xl font-bold flex items-center gap-3">
            <span class="text-3xl">👛</span>
            Wallet Integration
          </h2>
          <p class="mt-2 text-purple-100">Koneksi Phantom Wallet ke game Anda</p>
        </div>
        <div class="p-6">
          <div class="space-y-6">
            <div>
              <h3 class="text-xl font-semibold mb-3">JavaScript Integration</h3>
              <p class="text-gray-700 mb-3">Tambahkan script berikut ke HTML game Anda:</p>
              <div class="code-block">
                <pre>// Deteksi Phantom Wallet
async function connectWallet() {
  if (window.phantom?.solana?.isPhantom) {
    const provider = window.phantom.solana;
    const resp = await provider.connect();
    const address = resp.publicKey.toString();
    
    console.log("Connected:", address);
    return address;
  } else {
    alert("Install Phantom Wallet!");
    return null;
  }
}

// Panggil saat game load
window.onload = async () => {
  const address = await connectWallet();
  // Kirim address ke Unity
  if (address && unityInstance) {
    unityInstance.SendMessage(
      "GameManager", 
      "OnWalletConnected", 
      address
    );
  }
}</pre>
              </div>
            </div>

            <div>
              <h3 class="text-xl font-semibold mb-3">Unity C# Script</h3>
              <p class="text-gray-700 mb-3">Buat script untuk menerima wallet address:</p>
              <div class="code-block">
                <pre>using UnityEngine;

public class GameManager : MonoBehaviour
{
    private string walletAddress;
    
    // Dipanggil dari JavaScript
    public void OnWalletConnected(string address) 
    {
        walletAddress = address;
        Debug.Log("Wallet connected: " + address);
        
        // Update UI atau mulai game logic
        PlayerPrefs.SetString("WalletAddress", address);
    }
    
    public string GetWalletAddress() 
    {
        return walletAddress;
    }
}</pre>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                  <span class="text-2xl">✅</span>
                  <h4 class="font-semibold">Do's</h4>
                </div>
                <ul class="text-sm text-gray-700 space-y-1 ml-8 list-disc">
                  <li>Cek ketersediaan Phantom</li>
                  <li>Handle connection errors</li>
                  <li>Simpan address dengan aman</li>
                  <li>Validasi address format</li>
                </ul>
              </div>
              <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                  <span class="text-2xl">❌</span>
                  <h4 class="font-semibold">Don'ts</h4>
                </div>
                <ul class="text-sm text-gray-700 space-y-1 ml-8 list-disc">
                  <li>Jangan minta private key</li>
                  <li>Jangan skip error handling</li>
                  <li>Jangan hardcode addresses</li>
                  <li>Jangan skip user confirmation</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Game Integration -->
      <div id="game-integration" class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 p-6 text-white">
          <h2 class="text-2xl font-bold flex items-center gap-3">
            <span class="text-3xl">🎮</span>
            Game Integration
          </h2>
          <p class="mt-2 text-green-100">Implementasi sistem reward di game Anda</p>
        </div>
        <div class="p-6">
          <div class="space-y-6">
            <div>
              <h3 class="text-xl font-semibold mb-3">Reward Flow</h3>
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between text-sm">
                  <div class="text-center flex-1">
                    <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">1</div>
                    <div class="font-semibold">Player Wins</div>
                  </div>
                  <div class="text-gray-400 text-2xl">→</div>
                  <div class="text-center flex-1">
                    <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">2</div>
                    <div class="font-semibold">Request Reward</div>
                  </div>
                  <div class="text-gray-400 text-2xl">→</div>
                  <div class="text-center flex-1">
                    <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">3</div>
                    <div class="font-semibold">Sign Message</div>
                  </div>
                  <div class="text-gray-400 text-2xl">→</div>
                  <div class="text-center flex-1">
                    <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">4</div>
                    <div class="font-semibold">Receive Token</div>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-xl font-semibold mb-3">Unity Request Reward</h3>
              <div class="code-block">
                <pre>public void ClaimReward(float amount)
{
    string gameId = "your-game-id";
    string nonce = System.Guid.NewGuid().ToString();
    
    // Create JSON payload
    var payload = new {
        amount = amount,
        gameId = gameId,
        nonce = nonce,
        timestamp = DateTimeOffset.UtcNow.ToUnixTimeSeconds()
    };
    
    string json = JsonUtility.ToJson(payload);
    
    // Call JavaScript function
    Application.ExternalCall("requestReward", json);
}

// Callback dari JavaScript
public void OnClaimResult(string resultJson)
{
    var result = JsonUtility.FromJson<ClaimResult>(resultJson);
    
    if (result.success) {
        Debug.Log("Reward claimed! TX: " + result.txSignature);
        ShowSuccessUI(result.amount);
    } else {
        Debug.LogError("Claim failed: " + result.error);
        ShowErrorUI(result.error);
    }
}

[System.Serializable]
public class ClaimResult 
{
    public bool success;
    public string error;
    public float amount;
    public string txSignature;
}</pre>
              </div>
            </div>

            <div>
              <h3 class="text-xl font-semibold mb-3">JavaScript Handler</h3>
              <div class="code-block">
                <pre>async function requestReward(jsonPayload) {
  const payload = JSON.parse(jsonPayload);
  const provider = window.phantom.solana;
  
  // Create message to sign
  const message = JSON.stringify({
    ...payload,
    address: provider.publicKey.toString(),
    timestamp: Date.now()
  });
  
  const encoded = new TextEncoder().encode(message);
  
  try {
    // Request signature dari user
    const signed = await provider.signMessage(encoded, "utf8");
    const signature = bs58.encode(signed.signature);
    
    // Kirim ke backend untuk verifikasi
    const response = await fetch("/api/claim", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        message: message,
        signature: signature,
        publicKey: signed.publicKey.toString()
      })
    });
    
    const result = await response.json();
    
    // Send result back to Unity
    unityInstance.SendMessage(
      "GameManager",
      "OnClaimResult", 
      JSON.stringify(result)
    );
  } catch (error) {
    console.error("Claim error:", error);
  }
}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- API Reference -->
      <div id="api-reference" class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 p-6 text-white">
          <h2 class="text-2xl font-bold flex items-center gap-3">
            <span class="text-3xl">📚</span>
            API Reference
          </h2>
          <p class="mt-2 text-orange-100">Endpoint dan spesifikasi API lengkap</p>
        </div>
        <div class="p-6">
          <div class="space-y-6">
            
            <!-- Claim Endpoint -->
            <div class="border-l-4 border-blue-500 pl-4">
              <div class="flex items-center gap-3 mb-2">
                <span class="badge bg-green-100 text-green-700">POST</span>
                <code class="text-lg font-mono">/api/claim</code>
              </div>
              <p class="text-gray-700 mb-4">Claim reward setelah menyelesaikan game</p>
              
              <h4 class="font-semibold mb-2">Request Body:</h4>
              <div class="code-block mb-4">
                <pre>{
  "message": "stringified JSON payload",
  "signature": "base58 encoded signature",
  "publicKey": "wallet public key"
}</pre>
              </div>

              <h4 class="font-semibold mb-2">Response:</h4>
              <div class="code-block">
                <pre>{
  "success": true,
  "amount": 1.5,
  "txSignature": "5xGx...",
  "message": "Reward claimed successfully"
}</pre>
              </div>
            </div>

            <!-- Verify Endpoint -->
            <div class="border-l-4 border-purple-500 pl-4">
              <div class="flex items-center gap-3 mb-2">
                <span class="badge bg-blue-100 text-blue-700">GET</span>
                <code class="text-lg font-mono">/api/verify/:address</code>
              </div>
              <p class="text-gray-700 mb-4">Cek status dan balance wallet</p>
              
              <h4 class="font-semibold mb-2">Response:</h4>
              <div class="code-block">
                <pre>{
  "address": "8xGx...",
  "balance": 10.5,
  "totalClaimed": 50,
  "lastClaim": "2025-10-22T10:30:00Z"
}</pre>
              </div>
            </div>

            <!-- Leaderboard Endpoint -->
            <div class="border-l-4 border-green-500 pl-4">
              <div class="flex items-center gap-3 mb-2">
                <span class="badge bg-blue-100 text-blue-700">GET</span>
                <code class="text-lg font-mono">/api/leaderboard/:gameId</code>
              </div>
              <p class="text-gray-700 mb-4">Ambil leaderboard game tertentu</p>
              
              <h4 class="font-semibold mb-2">Query Parameters:</h4>
              <ul class="list-disc ml-6 mb-4 text-gray-700 text-sm">
                <li><code>limit</code> - Jumlah player (default: 10, max: 100)</li>
                <li><code>period</code> - daily | weekly | monthly | all-time</li>
              </ul>

              <h4 class="font-semibold mb-2">Response:</h4>
              <div class="code-block">
                <pre>{
  "leaderboard": [
    {
      "rank": 1,
      "address": "8xGx...",
      "score": 1500,
      "rewards": 50.5
    }
  ]
}</pre>
              </div>
            </div>

            <!-- Rate Limits -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <h4 class="font-semibold text-yellow-900 mb-2 flex items-center gap-2">
                <span class="text-xl">⚠️</span>
                Rate Limits
              </h4>
              <ul class="text-sm text-yellow-800 space-y-1 ml-6 list-disc">
                <li><strong>/api/claim:</strong> 10 requests per minute per wallet</li>
                <li><strong>/api/verify:</strong> 100 requests per minute</li>
                <li><strong>/api/leaderboard:</strong> 60 requests per minute</li>
              </ul>
            </div>

            <!-- Error Codes -->
            <div>
              <h4 class="font-semibold mb-3">Common Error Codes</h4>
              <div class="overflow-x-auto">
                <table class="w-full text-sm">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="px-4 py-2 text-left">Code</th>
                      <th class="px-4 py-2 text-left">Message</th>
                      <th class="px-4 py-2 text-left">Description</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y">
                    <tr>
                      <td class="px-4 py-2"><code class="text-red-600">400</code></td>
                      <td class="px-4 py-2">Invalid Request</td>
                      <td class="px-4 py-2">Parameter tidak valid</td>
                    </tr>
                    <tr>
                      <td class="px-4 py-2"><code class="text-red-600">401</code></td>
                      <td class="px-4 py-2">Unauthorized</td>
                      <td class="px-4 py-2">Signature tidak valid</td>
                    </tr>
                    <tr>
                      <td class="px-4 py-2"><code class="text-red-600">429</code></td>
                      <td class="px-4 py-2">Rate Limited</td>
                      <td class="px-4 py-2">Terlalu banyak request</td>
                    </tr>
                    <tr>
                      <td class="px-4 py-2"><code class="text-red-600">500</code></td>
                      <td class="px-4 py-2">Server Error</td>
                      <td class="px-4 py-2">Kesalahan internal server</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Resources -->
      <div class="doc-card bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-6 text-white">
          <h2 class="text-2xl font-bold flex items-center gap-3">
            <span class="text-3xl">🔗</span>
            Additional Resources
          </h2>
          <p class="mt-2 text-indigo-100">Link dan resource tambahan untuk developer</p>
        </div>
        <div class="p-6">
          <div class="grid md:grid-cols-2 gap-6">
            
            <a href="https://docs.solana.com" target="_blank" class="group p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition">
              <div class="flex items-start gap-3">
                <div class="text-3xl">📖</div>
                <div>
                  <h4 class="font-semibold text-lg group-hover:text-blue-600">Solana Documentation</h4>
                  <p class="text-sm text-gray-600 mt-1">Dokumentasi resmi Solana blockchain</p>
                </div>
              </div>
            </a>

            <a href="https://phantom.app/developers" target="_blank" class="group p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 transition">
              <div class="flex items-start gap-3">
                <div class="text-3xl">👻</div>
                <div>
                  <h4 class="font-semibold text-lg group-hover:text-purple-600">Phantom Wallet API</h4>
                  <p class="text-sm text-gray-600 mt-1">Dokumentasi API Phantom Wallet</p>
                </div>
              </div>
            </a>

            <a href="https://github.com/kulino/unity-sdk" target="_blank" class="group p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 transition">
              <div class="flex items-start gap-3">
                <div class="text-3xl">🎮</div>
                <div>
                  <h4 class="font-semibold text-lg group-hover:text-green-600">Unity SDK</h4>
                  <p class="text-sm text-gray-600 mt-1">SDK lengkap untuk Unity integration</p>
                </div>
              </div>
            </a>

            <a href="https://discord.gg/kulino-dev" target="_blank" class="group p-4 border-2 border-gray-200 rounded-lg hover:border-indigo-500 transition">
              <div class="flex items-start gap-3">
                <div class="text-3xl">💬</div>
                <div>
                  <h4 class="font-semibold text-lg group-hover:text-indigo-600">Developer Discord</h4>
                  <p class="text-sm text-gray-600 mt-1">Join komunitas developer Kulino</p>
                </div>
              </div>
            </a>

            <a href="#" class="group p-4 border-2 border-gray-200 rounded-lg hover:border-orange-500 transition">
              <div class="flex items-start gap-3">
                <div class="text-3xl">📺</div>
                <div>
                  <h4 class="font-semibold text-lg group-hover:text-orange-600">Video Tutorials</h4>
                  <p class="text-sm text-gray-600 mt-1">Tutorial step-by-step di YouTube</p>
                </div>
              </div>
            </a>

            <a href="#" class="group p-4 border-2 border-gray-200 rounded-lg hover:border-pink-500 transition">
              <div class="flex items-start gap-3">
                <div class="text-3xl">💼</div>
                <div>
                  <h4 class="font-semibold text-lg group-hover:text-pink-600">Example Projects</h4>
                  <p class="text-sm text-gray-600 mt-1">Download contoh project lengkap</p>
                </div>
              </div>
            </a>

          </div>
        </div>
      </div>

    </section>

    <!-- CTA Section -->
    <section class="mt-16 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl p-8 md:p-12 text-center text-white shadow-2xl">
      <h2 class="text-3xl font-bold mb-4">Siap Mengintegrasikan Game Anda?</h2>
      <p class="text-lg mb-8 text-white/90 max-w-2xl mx-auto">
        Daftar sekarang dan dapatkan API key gratis untuk memulai development Anda!
      </p>
      <div class="flex flex-wrap gap-4 justify-center">
        <a href="https://developer.kulino.com/register" class="px-8 py-4 bg-white text-blue-600 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-lg">
          Get API Key
        </a>
        <a href="mailto:dev@kulino.com" class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg font-bold text-lg hover:bg-white/10 transition">
          Contact Support
        </a>
      </div>
    </section>

  </main>

  <!-- Sponsor -->
  <?php include("./includes/sponsor.php"); ?>
  
  <!-- Footer -->
  <?php include("./includes/footer.php"); ?>

  <script>
    // Smooth scroll untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Copy code functionality
    document.querySelectorAll('.code-block').forEach(block => {
      const button = document.createElement('button');
      button.innerHTML = '📋 Copy';
      button.className = 'absolute top-2 right-2 px-3 py-1 bg-gray-700 text-white text-xs rounded hover:bg-gray-600 transition';
      
      const wrapper = document.createElement('div');
      wrapper.className = 'relative';
      block.parentNode.insertBefore(wrapper, block);
      wrapper.appendChild(block);
      wrapper.appendChild(button);
      
      button.addEventListener('click', () => {
        const code = block.querySelector('pre').textContent;
        navigator.clipboard.writeText(code).then(() => {
          button.innerHTML = '✅ Copied!';
          setTimeout(() => {
            button.innerHTML = '📋 Copy';
          }, 2000);
        });
      });
    });

    // Highlight active section on scroll
    const sections = document.querySelectorAll('[id]');
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    window.addEventListener('scroll', () => {
      let current = '';
      sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (pageYOffset >= sectionTop - 100) {
          current = section.getAttribute('id');
        }
      });
      
      navLinks.forEach(link => {
        link.classList.remove('text-blue-600', 'font-bold');
        if (link.getAttribute('href') === '#' + current) {
          link.classList.add('text-blue-600', 'font-bold');
        }
      });
    });
  </script>

</body>
</html>