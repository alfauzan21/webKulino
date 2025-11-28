<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>📊 Dashboard Recap Visitor</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>

  <body class="bg-gray-100 text-gray-900">
    <header class="bg-white shadow-sm sticky top-0 z-40">
      <div
        class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between"
      >
        <h1 class="flex items-center gap-2 text-xl font-semibold text-gray-800">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="w-7 h-7 text-indigo-600"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3 12h7V3H3v9zm0 9h7v-7H3v7zm11 0h7v-9h-7v9zm0-18v7h7V3h-7z"
            />
          </svg>
          Dashboard Recap Visitor
        </h1>

        <a
          href="index.php"
          class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-full shadow hover:bg-indigo-700 transition transform hover:scale-105"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 19l-7-7 7-7"
            />
          </svg>
          <span>Kembali</span>
        </a>
      </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10 space-y-8">
      <!-- Connection Status -->
      <div id="connectionStatus" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-yellow-800 text-sm">⚠️ <span id="statusMessage"></span></p>
      </div>

      <!-- Stats -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow">
          <h2 class="text-lg font-semibold">Total Visits</h2>
          <p id="totalVisits" class="text-4xl font-bold text-indigo-600 mt-2">
            -
          </p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
          <h2 class="text-lg font-semibold">Unique Visitors</h2>
          <p id="totalUnique" class="text-4xl font-bold text-green-600 mt-2">
            -
          </p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
          <h2 class="text-lg font-semibold">Active Visitors (10 menit)</h2>
          <p id="activeVisitor" class="text-4xl font-bold text-rose-600 mt-2">
            -
          </p>
        </div>
      </section>

      <!-- Chart -->
      <section class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-lg font-semibold mb-4">Grafik 7 Hari Terakhir</h2>
        <canvas id="weeklyChart" height="120"></canvas>
      </section>

      <section class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-lg font-semibold mb-4">
          Frekuensi Kunjungan (7 Hari Terakhir)
        </h2>
        <div class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-3 py-2 text-left">Tanggal</th>
                <th class="border px-3 py-2 text-left">IP</th>
                <th class="border px-3 py-2 text-left">Device</th>
                <th class="border px-3 py-2 text-left">Jumlah Kunjungan</th>
              </tr>
            </thead>
            <tbody id="freqTable"></tbody>
          </table>
        </div>
      </section>

      <!-- Log aktivitas -->
      <section class="bg-white p-6 rounded-2xl shadow">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold">Log Aktivitas</h2>
          <input
            type="date"
            id="dateFilter"
            class="border px-3 py-2 rounded-lg text-sm"
          />
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-3 py-2 text-left">Waktu</th>
                <th class="border px-3 py-2 text-left">IP</th>
                <th class="border px-3 py-2 text-left">Device</th>
              </tr>
            </thead>
            <tbody id="activityTable"></tbody>
          </table>
        </div>
      </section>
    </main>

    <footer class="max-w-6xl mx-auto px-4 py-6 text-sm text-gray-500">
      📊 Dashboard recap visitor — Kulino Project
    </footer>

    <!-- Script -->
    <script>
      let chartInstance = null;

      async function loadRecap(date = "") {
        const statusDiv = document.getElementById('connectionStatus');
        const statusMsg = document.getElementById('statusMessage');
        
        try {
          const url = date ? `../track.php?date=${date}` : "../track.php";
          console.log('📡 Fetching data from:', url);
          
          const res = await fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json'
            }
          });

          if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
          }

          const data = await res.json();
          console.log('✅ Data received:', data);

          if (data.error) {
            throw new Error(data.error);
          }

          // Hide error message
          statusDiv.classList.add('hidden');

          // update angka
          document.getElementById("totalVisits").innerText = data.today || 0;
          document.getElementById("totalUnique").innerText = data.unique || 0;
          document.getElementById("activeVisitor").innerText = data.active || 0;

          // update chart
          const ctx = document.getElementById("weeklyChart").getContext("2d");
          if (chartInstance) chartInstance.destroy();
          
          chartInstance = new Chart(ctx, {
            type: "line",
            data: {
              labels: data.labels || [],
              datasets: [
                {
                  label: "Visitor",
                  data: data.weekly || [],
                  borderColor: "#4f46e5",
                  backgroundColor: "rgba(79,70,229,0.2)",
                  fill: true,
                  tension: 0.3,
                  pointRadius: 5,
                  pointHoverRadius: 7,
                },
              ],
            },
            options: {
              responsive: true,
              plugins: { legend: { display: false } },
              scales: { y: { beginAtZero: true } },
            },
          });

          // update tabel aktivitas
          const tbody = document.getElementById("activityTable");
          tbody.innerHTML = "";
          
          if (data.activity && data.activity.length > 0) {
            data.activity.forEach((row) => {
              const tr = document.createElement("tr");
              tr.innerHTML = `
                <td class="border px-3 py-2">${row.time}</td>
                <td class="border px-3 py-2">${row.ip}</td>
                <td class="border px-3 py-2">${row.device}</td>
              `;
              tbody.appendChild(tr);
            });
          } else {
            tbody.innerHTML = '<tr><td colspan="3" class="border px-3 py-2 text-center text-gray-500">Tidak ada aktivitas</td></tr>';
          }

          // update tabel frekuensi
          const freqTbody = document.getElementById("freqTable");
          freqTbody.innerHTML = "";
          
          if (data.frequency && data.frequency.length > 0) {
            data.frequency.forEach((row) => {
              const tr = document.createElement("tr");
              tr.innerHTML = `
                <td class="border px-3 py-2">${row.date}</td>
                <td class="border px-3 py-2">${row.ip}</td>
                <td class="border px-3 py-2">${row.device}</td>
                <td class="border px-3 py-2 font-semibold text-indigo-600">${row.visits}</td>
              `;
              freqTbody.appendChild(tr);
            });
          } else {
            freqTbody.innerHTML = '<tr><td colspan="4" class="border px-3 py-2 text-center text-gray-500">Tidak ada data</td></tr>';
          }

        } catch (e) {
          console.error("❌ Gagal load recap:", e);
          
          // Show error message
          statusDiv.classList.remove('hidden');
          statusMsg.textContent = `Error: ${e.message}. Periksa koneksi database dan file track.php`;
          
          // Set default values
          document.getElementById("totalVisits").innerText = "Error";
          document.getElementById("totalUnique").innerText = "Error";
          document.getElementById("activeVisitor").innerText = "Error";
        }
      }

      // load pertama + refresh tiap 10 detik
      window.onload = () => {
        console.log('🚀 Dashboard loaded');
        loadRecap();
        
        setInterval(() => {
          const date = document.getElementById("dateFilter").value;
          loadRecap(date);
        }, 10000);

        document
          .getElementById("dateFilter")
          .addEventListener("change", (e) => {
            loadRecap(e.target.value);
          });
      };
    </script>
  </body>
</html>