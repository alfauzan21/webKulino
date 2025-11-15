// Konfigurasi build path kalau mau embed/open build
const unityBuildPath = "./WebUnity/index.html";

// Phantom provider + user address
let provider = null;
let userAddress = null;
let unityInstance = null; // kalau nanti embed Unity instance set dari loader

function shortAddr(addr) {
  if (!addr) return "-";
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

// Connect Phantom
async function connectWallet() {
  try {
    const anyWin = window;
    if (
      anyWin.phantom &&
      anyWin.phantom.solana &&
      anyWin.phantom.solana.isPhantom
    ) {
      provider = anyWin.phantom.solana;
      // connect() will prompt user
      const resp = await provider.connect();
      userAddress = resp.publicKey.toString();
      document.getElementById("walletStatus").innerText = "Connected";
      document.getElementById("addrShort").innerText = shortAddr(userAddress);
      document.getElementById("connectBtn").innerText = "Connected";
      document.getElementById("connectBtn").disabled = true;

      // jika Unity instance tersedia, kirim address
      if (unityInstance && typeof unityInstance.SendMessage === "function") {
        unityInstance.SendMessage(
          "GameManager",
          "OnWalletConnected",
          userAddress
        );
      }
    } else {
      alert(
        "Phantom tidak terdeteksi. Minta user install Phantom (extension) atau buka via browser yang mendukung Phantom."
      );
    }
  } catch (err) {
    console.error("connectWallet error:", err);
    alert("Gagal connect: " + (err.message || err));
  }
}

// Unity akan memanggil window.requestReward(jsonPayload)
// jsonPayload contoh: '{"amount":1,"gameId":"simple-kulino","nonce":"..."}'
async function requestReward(jsonPayload) {
  console.log("Unity requested reward:", jsonPayload);
  const payload = JSON.parse(jsonPayload);

  if (!provider || !userAddress) {
    // kirim hasil error ke Unity jika ada
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage(
        "GameManager",
        "OnClaimResult",
        JSON.stringify({ success: false, error: "wallet_not_connected" })
      );
    }
    return;
  }

  // Standarisasi message yang akan di-sign
  const messageObj = { ...payload, address: userAddress, ts: Date.now() };
  const messageStr = JSON.stringify(messageObj);
  const encoded = new TextEncoder().encode(messageStr);

  try {
    // Phantom signMessage -> returns { signature: Uint8Array, publicKey: PublicKey }
    const signed = await provider.signMessage(encoded, "utf8");
    // encode signature ke base58 agar mudah kirim ke server
    const signatureBase58 = bs58.encode(signed.signature);

    // kirim ke backend untuk verifikasi + mint (endpoint /api/claim)
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
    // kirim balik hasil ke Unity
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage(
        "GameManager",
        "OnClaimResult",
        JSON.stringify(result)
      );
    } else {
      console.log("claim result:", result);
      alert("Claim result: " + JSON.stringify(result));
    }
  } catch (err) {
    console.error("requestReward error:", err);
    if (unityInstance && typeof unityInstance.SendMessage === "function") {
      unityInstance.SendMessage(
        "GameManager",
        "OnClaimResult",
        JSON.stringify({
          success: false,
          error: err.message || String(err),
        })
      );
    }
  }
}

// expose function ke global agar Unity bisa panggil (WebGL)
window.requestReward = requestReward;
window.connectWallet = connectWallet;

// fungsi yang dipanggil oleh Unity loader jika ingin set instance
function setUnityInstance(instance) {
  console.log("setUnityInstance called");
  unityInstance = instance;
  // kirim address jika sudah connect
  if (
    userAddress &&
    unityInstance &&
    typeof unityInstance.SendMessage === "function"
  ) {
    unityInstance.SendMessage("GameManager", "OnWalletConnected", userAddress);
  }
}
window.setUnityInstance = setUnityInstance;

// buka game di tab baru (pass wallet sebagai query param jika sudah connect)
function playGame(gameId) {
  const url = new URL(unityBuildPath, window.location.href);
  if (userAddress) url.searchParams.set("wallet", userAddress);
  url.searchParams.set("game", gameId);
  window.open(url.toString(), "_blank");
}

function previewGame(path) {
  const url = new URL(path, window.location.href);
  window.open(url.toString(), "_blank");
}

function scrollSlider(id, direction) {
  const slider = document.getElementById(id);
  const scrollAmount = 320; // ukuran card
  slider.scrollBy({ left: direction * scrollAmount, behavior: "smooth" });
}

// Visitor
let lastCount = 0;

async function loadVisitors(add = false) {
  try {
    const url = add ? "track.php?add=1" : "track.php";
    const res = await fetch(url);
    const data = await res.json();

    // update tampilan viewers
    document.getElementById("visitorCount").innerText = data.today;

    // kalau ada kenaikan visitor → alert
    if (data.today > lastCount && lastCount > 0) {
      alert("🚀 Ada visitor baru! Total sekarang: " + data.today);
    }

    lastCount = data.today;
  } catch (e) {
    console.error("Gagal ambil visitor:", e);
    document.getElementById("visitorCount").innerText = "-";
  }
}

window.onload = () => {
  // Kalau tab ini belum dihitung → tambahkan
  if (!sessionStorage.getItem("kulino_tab_visited")) {
    loadVisitors(true);
    sessionStorage.setItem("kulino_tab_visited", "1");
  } else {
    loadVisitors(false);
  }

  // auto-refresh tiap 10 detik
  setInterval(() => loadVisitors(false), 10000);
};

// Aktifkan video preview di Game Unggulan
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

const header = document.getElementById("mainHeader");

window.addEventListener("scroll", () => {
  if (window.scrollY > 50) {
    header.classList.remove("bg-white/50", "backdrop-blur");
    header.classList.add("bg-white", "shadow-md");
  } else {
    header.classList.add("bg-white/50", "backdrop-blur");
    header.classList.remove("bg-white", "shadow-md");
  }
});

const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");

menuBtn.addEventListener("click", () => {
  sidebar.classList.toggle("-translate-x-full");
  overlay.classList.toggle("hidden");
});

overlay.addEventListener("click", () => {
  sidebar.classList.add("-translate-x-full");
  overlay.classList.add("hidden");
});

// Show disconnect button when connected
const walletStatusObserver = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.target.id === "walletStatus") {
      const disconnectBtn = document.getElementById("disconnectBtn");
      if (disconnectBtn) {
        if (mutation.target.textContent === "Connected") {
          disconnectBtn.classList.remove("hidden");
        } else {
          disconnectBtn.classList.add("hidden");
        }
      }
    }
  });
});

const walletStatusElement = document.getElementById("walletStatus");
if (walletStatusElement) {
  walletStatusObserver.observe(walletStatusElement, {
    childList: true,
    characterData: true,
    subtree: true,
  });
}


