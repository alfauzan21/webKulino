<?php
// ==================== PHP LOGIC ONLY ====================
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include("../includes/koneksi.php");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>📊 Dashboard Recap Visitor</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }

    header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stats-card {
      background: white;
      border-radius: 1.5rem;
      padding: 1.5rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stats-card .icon {
      width: 60px;
      height: 60px;
      border-radius: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 1rem;
    }

    .stats-card.visits .icon {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stats-card.unique .icon {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stats-card.active .icon {
      background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    }

    .stats-card .value {
      font-size: 2.5rem;
      font-weight: 700;
      line-height: 1;
      margin-bottom: 0.5rem;
    }

    .stats-card.visits .value { color: #667eea; }
    .stats-card.unique .value { color: #10b981; }
    .stats-card.active .value { color: #f43f5e; }

    .stats-card .label {
      font-size: 0.875rem;
      color: #6b7280;
      font-weight: 500;
    }

    .chart-container {
      background: white;
      border-radius: 1.5rem;
      padding: 2rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .chart-container h2 {
      font-size: 1.25rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 1.5rem;
    }

    .table-container {
      background: white;
      border-radius: 1.5rem;
      padding: 2rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .table-wrapper {
      overflow-x: auto;
      border-radius: 0.75rem;
      border: 1px solid #e5e7eb;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    thead th {
      color: white;
      font-weight: 600;
      text-align: left;
      padding: 1rem;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    tbody tr {
      border-bottom: 1px solid #e5e7eb;
      transition: background-color 0.2s ease;
    }

    tbody tr:hover {
      background-color: #f9fafb;
    }

    tbody td {
      padding: 1rem;
      font-size: 0.875rem;
      color: #374151;
    }

    .badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .badge-high {
      background: #fef3c7;
      color: #92400e;
    }

    .badge-medium {
      background: #dbeafe;
      color: #1e40af;
    }

    .badge-low {
      background: #e5e7eb;
      color: #374151;
    }

    .connection-alert {
      background: #fef3c7;
      border: 2px solid #fbbf24;
      border-radius: 0.75rem;
      padding: 1rem;
      margin-bottom: 1.5rem;
    }

    .connection-alert p {
      color: #92400e;
      font-weight: 500;
      font-size: 0.875rem;
    }

    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 1rem;
      background: white;
      padding: 1rem 1.5rem;
      border-radius: 1rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 1.5rem;
    }

    .filter-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .filter-item label {
      font-weight: 600;
      color: #374151;
      font-size: 0.875rem;
      white-space: nowrap;
    }

    .filter-item select,
    .filter-item input {
      border: 2px solid #e5e7eb;
      border-radius: 0.5rem;
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
      transition: border-color 0.2s ease;
      min-width: 180px;
    }

    .filter-item select:focus,
    .filter-item input:focus {
      outline: none;
      border-color: #667eea;
    }

    .timezone-display {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.75rem;
      font-size: 0.875rem;
      font-weight: 600;
    }

    .country-flag {
      font-size: 1.5rem;
      margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
      .stats-card .value {
        font-size: 2rem;
      }

      .chart-container,
      .table-container {
        padding: 1.5rem;
      }

      thead th,
      tbody td {
        padding: 0.75rem;
        font-size: 0.8125rem;
      }

      .filter-bar {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-item {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-item select,
      .filter-item input {
        width: 100%;
      }
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    .loading {
      animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header class="sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
          </svg>
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-900">Dashboard Recap Visitor</h1>
          <p class="text-sm text-gray-600">Real-time Analytics with Timezone</p>
        </div>
      </div>

      <a href="index.php" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="font-semibold">Kembali</span>
      </a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 py-8">

    <!-- Connection Status -->
    <div id="connectionStatus" class="connection-alert hidden">
      <p>⚠️ <span id="statusMessage"></span></p>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
      <div class="filter-item">
        <label for="timezoneFilter">🌍 Timezone:</label>
        <select id="timezoneFilter">
          <optgroup label="Indonesia">
            <option value="Asia/Jakarta" selected>WIB (UTC+7)</option>
            <option value="Asia/Makassar">WITA (UTC+8)</option>
            <option value="Asia/Jayapura">WIT (UTC+9)</option>
          </optgroup>
          <optgroup label="Asia Tenggara">
            <option value="Asia/Singapore">Singapore (UTC+8)</option>
            <option value="Asia/Kuala_Lumpur">Malaysia (UTC+8)</option>
            <option value="Asia/Bangkok">Thailand (UTC+7)</option>
            <option value="Asia/Manila">Philippines (UTC+8)</option>
            <option value="Asia/Ho_Chi_Minh">Vietnam (UTC+7)</option>
          </optgroup>
        </select>
      </div>

      <div class="filter-item">
        <label for="dateFilter">📅 Tanggal:</label>
        <input type="date" id="dateFilter" />
      </div>

      <div class="timezone-display">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span id="currentTimezone">WIB (UTC+7)</span>
      </div>
    </div>

    <!-- Stats Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="stats-card visits">
        <div class="icon">📊</div>
        <div class="value" id="totalVisits">-</div>
        <div class="label">Total Visits</div>
      </div>

      <div class="stats-card unique">
        <div class="icon">👥</div>
        <div class="value" id="totalUnique">-</div>
        <div class="label">Unique Visitors</div>
      </div>

      <div class="stats-card active">
        <div class="icon">🟢</div>
        <div class="value" id="activeVisitor">-</div>
        <div class="label">Active Now (10 min)</div>
      </div>
    </section>

    <!-- Weekly Chart -->
    <section class="chart-container mb-8">
      <h2>📈 Grafik 7 Hari Terakhir</h2>
      <canvas id="weeklyChart" height="100"></canvas>
    </section>

    <!-- Country Chart -->
    <section class="chart-container mb-8">
      <h2>🌍 Top 10 Negara Pengunjung (7 Hari Terakhir)</h2>
      <canvas id="countryChart" height="100"></canvas>
    </section>

    <!-- Browser/Device Stats -->
    <section class="chart-container mb-8">
      <h2>🌐 Top 10 Browser & Device (7 Hari Terakhir)</h2>
      <canvas id="browserChart" height="100"></canvas>
    </section>

    <!-- Location Table -->
    <section class="table-container mb-8">
      <h2 class="text-lg font-bold text-gray-900 mb-4">📍 Lokasi Pengunjung Hari Ini</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Negara</th>
              <th>Kota/Region</th>
              <th>Timezone</th>
              <th>Total Kunjungan</th>
            </tr>
          </thead>
          <tbody id="locationTable">
            <tr>
              <td colspan="4" class="text-center text-gray-500">Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Frequency Table -->
    <section class="table-container mb-8">
      <h2 class="text-lg font-bold text-gray-900 mb-4">🔄 Frekuensi Kunjungan (7 Hari Terakhir)</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>IP Address</th>
              <th>Lokasi</th>
              <th>Timezone</th>
              <th>Device & Browser</th>
              <th>Jumlah</th>
            </tr>
          </thead>
          <tbody id="freqTable">
            <tr>
              <td colspan="6" class="text-center text-gray-500">Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Activity Log -->
    <section class="table-container">
      <h2 class="text-lg font-bold text-gray-900 mb-4">📋 Log Aktivitas</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Waktu (Local)</th>
              <th>IP Address</th>
              <th>Lokasi</th>
              <th>Timezone</th>
              <th>Device & Browser</th>
            </tr>
          </thead>
          <tbody id="activityTable">
            <tr>
              <td colspan="5" class="text-center text-gray-500">Memuat data...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <footer class="max-w-7xl mx-auto px-4 py-6 text-center">
    <p class="text-white text-sm opacity-90">📊 Dashboard Recap Visitor — Kulino Project</p>
  </footer>

  <!-- JavaScript -->
  <script>
    let weeklyChartInstance = null;
    let countryChartInstance = null;
    let browserChartInstance = null;

    const countryFlags = {
      'ID': '🇮🇩', 'US': '🇺🇸', 'SG': '🇸🇬', 'MY': '🇲🇾', 'TH': '🇹🇭',
      'PH': '🇵🇭', 'VN': '🇻🇳', 'JP': '🇯🇵', 'KR': '🇰🇷', 'CN': '🇨🇳',
      'IN': '🇮🇳', 'AU': '🇦🇺', 'GB': '🇬🇧', 'DE': '🇩🇪', 'FR': '🇫🇷',
      'NL': '🇳🇱', 'BR': '🇧🇷', 'IT': '🇮🇹', 'ES': '🇪🇸', 'CA': '🇨🇦'
    };

    function getCountryFlag(countryCode) {
      return countryFlags[countryCode] || '🌐';
    }

    function getVisitBadge(count) {
      if (count >= 10) return '<span class="badge badge-high">' + count + ' kunjungan</span>';
      if (count >= 5) return '<span class="badge badge-medium">' + count + ' kunjungan</span>';
      return '<span class="badge badge-low">' + count + ' kunjungan</span>';
    }

    async function loadRecap(date = "", timezone = "Asia/Jakarta") {
      const statusDiv = document.getElementById('connectionStatus');
      const statusMsg = document.getElementById('statusMessage');

      try {
        // 🔧 FIX: Correct path to track.php
        let url = `../track.php?tz=${encodeURIComponent(timezone)}`;
        if (date) url += `&date=${date}`;

        console.log('📡 Fetching:', url);

        const res = await fetch(url, {
          method: 'GET',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();
        console.log('✅ Data:', data);

        if (data.error) throw new Error(data.error);

        statusDiv.classList.add('hidden');

        // Update stats
        document.getElementById("totalVisits").innerText = data.today || 0;
        document.getElementById("totalUnique").innerText = data.unique || 0;
        document.getElementById("activeVisitor").innerText = data.active || 0;

        // Update timezone display
        if (data.display_timezone) {
          document.getElementById("currentTimezone").innerText = data.display_timezone;
        }

        updateWeeklyChart(data.labels || [], data.weekly || []);
        updateCountryChart(data.country_labels || [], data.country_data || []);
        updateBrowserChart(data.browsers || []);
        updateLocationTable(data.locations || []);
        updateFrequencyTable(data.frequency || []);
        updateActivityTable(data.activity || []);

      } catch (e) {
        console.error("❌ Error:", e);
        statusDiv.classList.remove('hidden');
        statusMsg.textContent = `Error: ${e.message}`;

        document.getElementById("totalVisits").innerText = "Error";
        document.getElementById("totalUnique").innerText = "Error";
        document.getElementById("activeVisitor").innerText = "Error";
      }
    }

    function updateWeeklyChart(labels, data) {
      const ctx = document.getElementById("weeklyChart").getContext("2d");
      if (weeklyChartInstance) weeklyChartInstance.destroy();

      weeklyChartInstance = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [{
            label: "Visitor",
            data: data,
            borderColor: "#667eea",
            backgroundColor: "rgba(102, 126, 234, 0.1)",
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: "#667eea",
            pointBorderColor: "#fff",
            pointBorderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 },
              grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: { grid: { display: false } }
          }
        }
      });
    }

    function updateCountryChart(labels, data) {
      const ctx = document.getElementById("countryChart").getContext("2d");
      if (countryChartInstance) countryChartInstance.destroy();

      const colors = [
        'rgba(102, 126, 234, 0.8)', 'rgba(16, 185, 129, 0.8)',
        'rgba(244, 63, 94, 0.8)', 'rgba(251, 146, 60, 0.8)',
        'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)',
        'rgba(14, 165, 233, 0.8)', 'rgba(245, 158, 11, 0.8)',
        'rgba(34, 197, 94, 0.8)', 'rgba(168, 85, 247, 0.8)'
      ];

      countryChartInstance = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [{
            label: "Kunjungan",
            data: data,
            backgroundColor: colors.slice(0, labels.length),
            borderColor: colors.map(c => c.replace('0.8', '1')),
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 },
              grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: { grid: { display: false } }
          }
        }
      });
    }

    function updateBrowserChart(browsers) {
      const ctx = document.getElementById("browserChart").getContext("2d");
      if (browserChartInstance) browserChartInstance.destroy();

      const labels = browsers.map(b => b.device);
      const data = browsers.map(b => b.total);

      browserChartInstance = new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: [
              'rgba(102, 126, 234, 0.8)', 'rgba(16, 185, 129, 0.8)',
              'rgba(244, 63, 94, 0.8)', 'rgba(251, 146, 60, 0.8)',
              'rgba(139, 92, 246, 0.8)', 'rgba(236, 72, 153, 0.8)',
              'rgba(14, 165, 233, 0.8)', 'rgba(245, 158, 11, 0.8)',
              'rgba(34, 197, 94, 0.8)', 'rgba(168, 85, 247, 0.8)'
            ],
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'right' },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8
            }
          }
        }
      });
    }

    function updateLocationTable(locations) {
      const tbody = document.getElementById("locationTable");
      tbody.innerHTML = "";

      if (locations && locations.length > 0) {
        locations.forEach((row) => {
          const tr = document.createElement("tr");
          const flag = getCountryFlag(row.country_code);

          tr.innerHTML = `
            <td>
              <span class="country-flag">${flag}</span>
              <strong>${row.country}</strong>
            </td>
            <td>${row.city}, ${row.region}</td>
            <td><code class="bg-gray-100 px-2 py-1 rounded text-xs">${row.timezone}</code></td>
            <td>${getVisitBadge(row.total)}</td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-500">Tidak ada data</td></tr>';
      }
    }

    function updateFrequencyTable(frequency) {
      const tbody = document.getElementById("freqTable");
      tbody.innerHTML = "";

      if (frequency && frequency.length > 0) {
        frequency.forEach((row) => {
          const tr = document.createElement("tr");
          const flag = getCountryFlag(row.country.substring(0, 2));

          tr.innerHTML = `
            <td>${row.date}</td>
            <td><code class="bg-gray-100 px-2 py-1 rounded">${row.ip}</code></td>
            <td>
              <span class="country-flag">${flag}</span>
              ${row.city}, ${row.country}
            </td>
            <td><code class="bg-gray-100 px-2 py-1 rounded text-xs">${row.timezone}</code></td>
            <td>${row.device}</td>
            <td>${getVisitBadge(row.visits)}</td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-500">Tidak ada data</td></tr>';
      }
    }

    function updateActivityTable(activity) {
      const tbody = document.getElementById("activityTable");
      tbody.innerHTML = "";

      if (activity && activity.length > 0) {
        activity.forEach((row) => {
          const tr = document.createElement("tr");
          const flag = getCountryFlag(row.country.substring(0, 2));

          tr.innerHTML = `
            <td><strong>${row.time}</strong></td>
            <td><code class="bg-gray-100 px-2 py-1 rounded">${row.ip}</code></td>
            <td>
              <span class="country-flag">${flag}</span>
              ${row.city}, ${row.country}
            </td>
            <td><code class="bg-gray-100 px-2 py-1 rounded text-xs">${row.timezone}</code></td>
            <td>${row.device}</td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-500">Tidak ada data</td></tr>';
      }
    }

    window.onload = () => {
      console.log('🚀 Dashboard loaded');
      loadRecap();

      setInterval(() => {
        const date = document.getElementById("dateFilter").value;
        const tz = document.getElementById("timezoneFilter").value;
        loadRecap(date, tz);
      }, 30000);

      document.getElementById("timezoneFilter").addEventListener("change", (e) => {
        const date = document.getElementById("dateFilter").value;
        loadRecap(date, e.target.value);
      });

      document.getElementById("dateFilter").addEventListener("change", (e) => {
        const tz = document.getElementById("timezoneFilter").value;
        loadRecap(e.target.value, tz);
      });
    };
  </script>
</body>
</html>