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

    .stats-card.visits .value {
      color: #667eea;
    }

    .stats-card.unique .value {
      color: #10b981;
    }

    .stats-card.active .value {
      color: #f43f5e;
    }

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
      max-height: 600px;
      overflow-y: auto;
    }

    .table-wrapper::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    .table-wrapper::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
      background: #667eea;
      border-radius: 10px;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
      background: #764ba2;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      position: sticky;
      top: 0;
      z-index: 10;
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
      transition: all 0.2s ease;
      animation: fadeIn 0.3s ease;
    }

    tbody tr:hover {
      background-color: #f8f9ff;
      transform: scale(1.01);
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    tbody td {
      padding: 1rem;
      font-size: 0.875rem;
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

    .pagination-controls {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #e5e7eb;
    }

    .pagination-info {
      color: #6b7280;
      font-size: 0.875rem;
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

      .pagination-controls {
        flex-direction: column;
        gap: 1rem;
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.5;
      }
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
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900">📍 Lokasi Pengunjung Hari Ini</h2>

        <div class="flex items-center gap-3">
          <label for="locationLimit" class="text-sm font-semibold text-gray-700">Show:</label>
          <select id="locationLimit"
            class="border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-600 transition">
            <option value="5">5 entries</option>
            <option value="10" selected>10 entries</option>
            <option value="15">15 entries</option>
            <option value="20">20 entries</option>
            <option value="50">50 entries</option>
            <option value="100">100 entries</option>
          </select>
        </div>
      </div>

      <!-- Statistics Summary -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
          <p class="text-xs text-blue-600 font-semibold mb-1">Total Locations</p>
          <p id="totalLocations" class="text-2xl font-bold text-blue-700">-</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200">
          <p class="text-xs text-green-600 font-semibold mb-1">Total Countries</p>
          <p id="totalCountries" class="text-2xl font-bold text-green-700">-</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-200">
          <p class="text-xs text-purple-600 font-semibold mb-1">Total Cities</p>
          <p id="totalCities" class="text-2xl font-bold text-purple-700">-</p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 p-4 rounded-xl border border-orange-200">
          <p class="text-xs text-orange-600 font-semibold mb-1">Timezones</p>
          <p id="totalTimezones" class="text-2xl font-bold text-orange-700">-</p>
        </div>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="20%">Negara</th>
              <th width="40%">Alamat Lengkap</th>
              <th width="15%">Timezone</th>
              <th width="20%">Total Kunjungan</th>
            </tr>
          </thead>
          <tbody id="locationTable">
            <tr>
              <td colspan="5" class="text-center text-gray-500">
                <div class="py-8">
                  <div class="inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                  <p>Memuat data lokasi...</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination-controls">
        <div class="pagination-info">
          Showing <span id="showingCount">0</span> of <span id="totalLocationCount">0</span> locations
        </div>
      </div>
    </section>

    <!-- Frequency Table -->
    <section class="table-container mb-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900">🔄 Frekuensi Kunjungan (7 Hari Terakhir)</h2>
        <div class="flex items-center gap-3">
          <label for="frequencyLimit" class="text-sm font-semibold text-gray-700">Show:</label>
          <select id="frequencyLimit"
            class="border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-600 transition">
            <option value="10" selected>10 entries</option>
            <option value="20">20 entries</option>
            <option value="50">50 entries</option>
            <option value="100">100 entries</option>
          </select>
        </div>
      </div>

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

      <div class="pagination-controls">
        <div class="pagination-info">
          Showing <span id="freqShowingCount">0</span> of <span id="freqTotalCount">0</span> entries
        </div>
      </div>
    </section>

    <!-- Activity Log -->
    <section class="table-container">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900">📋 Log Aktivitas</h2>
        <div class="flex items-center gap-3">
          <label for="activityLimit" class="text-sm font-semibold text-gray-700">Show:</label>
          <select id="activityLimit"
            class="border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-600 transition">
            <option value="10" selected>10 entries</option>
            <option value="20">20 entries</option>
            <option value="50">50 entries</option>
            <option value="100">100 entries</option>
          </select>
        </div>
      </div>

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

      <div class="pagination-controls">
        <div class="pagination-info">
          Showing <span id="activityShowingCount">0</span> of <span id="activityTotalCount">0</span> entries
        </div>
      </div>
    </section>

  </main>

  <footer class="max-w-7xl mx-auto px-4 py-6 text-center">
    <p class="text-white text-sm opacity-90">📊 Dashboard Recap Visitor — Kulino Project</p>
  </footer>

  <!-- JavaScript -->
  <script>
    // ==================== GLOBAL STATE ====================
    let weeklyChartInstance = null;
    let countryChartInstance = null;
    let browserChartInstance = null;

    let allLocationsData = [];
    let allFrequencyData = [];
    let allActivityData = [];

    const countryFlags = {
      'ID': '🇮🇩',
      'US': '🇺🇸',
      'SG': '🇸🇬',
      'MY': '🇲🇾',
      'TH': '🇹🇭',
      'PH': '🇵🇭',
      'VN': '🇻🇳',
      'JP': '🇯🇵',
      'KR': '🇰🇷',
      'CN': '🇨🇳',
      'IN': '🇮🇳',
      'AU': '🇦🇺',
      'GB': '🇬🇧',
      'DE': '🇩🇪',
      'FR': '🇫🇷'
    };

    function getCountryFlag(countryCode) {
      return countryFlags[countryCode] || '🌐';
    }

    function getVisitBadge(count) {
      let badgeClass = '';
      let icon = '';

      if (count >= 50) {
        badgeClass = 'bg-gradient-to-r from-red-500 to-pink-500 text-white';
        icon = '🔥';
      } else if (count >= 20) {
        badgeClass = 'bg-gradient-to-r from-orange-500 to-yellow-500 text-white';
        icon = '⚡';
      } else if (count >= 10) {
        badgeClass = 'bg-gradient-to-r from-yellow-400 to-orange-400 text-gray-900';
        icon = '⭐';
      } else if (count >= 5) {
        badgeClass = 'bg-gradient-to-r from-blue-400 to-indigo-500 text-white';
        icon = '📊';
      } else {
        badgeClass = 'bg-gradient-to-r from-gray-300 to-gray-400 text-gray-800';
        icon = '📍';
      }

      return `
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm shadow-lg ${badgeClass}">
          <span>${icon}</span>
          <span>${count} visit${count > 1 ? 's' : ''}</span>
        </span>
      `;
    }

    // ==================== LOAD DATA ====================
    async function loadRecap(date = "", timezone = "Asia/Jakarta") {
      const statusDiv = document.getElementById('connectionStatus');
      const statusMsg = document.getElementById('statusMessage');

      try {
        let url = `track.php?tz=${encodeURIComponent(timezone)}`;
        if (date) url += `&date=${date}`;

        console.log('📡 Fetching:', url);

        const res = await fetch(url, {
          method: 'GET',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json'
          }
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();
        console.log('✅ Data received:', data);

        if (data.error) throw new Error(data.error);

        statusDiv.classList.add('hidden');

        // Update stats
        document.getElementById("totalVisits").innerText = data.today || 0;
        document.getElementById("totalUnique").innerText = data.unique || 0;
        document.getElementById("activeVisitor").innerText = data.active || 0;

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

    // ==================== CHARTS ====================
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
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            },
            x: {
              grid: // Lanjutan dari updateWeeklyChart - letakkan setelah x: { grid:
              {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            }
          }
        }
      });
    }

    function updateCountryChart(labels, data) {
      const ctx = document.getElementById("countryChart").getContext("2d");
      if (countryChartInstance) countryChartInstance.destroy();

      const colors = [
        'rgba(102, 126, 234, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(244, 63, 94, 0.8)',
        'rgba(251, 191, 36, 0.8)',
        'rgba(147, 51, 234, 0.8)',
        'rgba(59, 130, 246, 0.8)',
        'rgba(236, 72, 153, 0.8)',
        'rgba(34, 197, 94, 0.8)',
        'rgba(249, 115, 22, 0.8)',
        'rgba(168, 85, 247, 0.8)'
      ];

      countryChartInstance = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [{
            label: "Visitors",
            data: data,
            backgroundColor: colors,
            borderColor: colors.map(c => c.replace('0.8', '1')),
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

    function updateBrowserChart(browsers) {
      const ctx = document.getElementById("browserChart").getContext("2d");
      if (browserChartInstance) browserChartInstance.destroy();

      const labels = browsers.map(b => b.label || 'Unknown');
      const data = browsers.map(b => b.count || 0);

      const colors = [
        'rgba(102, 126, 234, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(244, 63, 94, 0.8)',
        'rgba(251, 191, 36, 0.8)',
        'rgba(147, 51, 234, 0.8)',
        'rgba(59, 130, 246, 0.8)',
        'rgba(236, 72, 153, 0.8)',
        'rgba(34, 197, 94, 0.8)',
        'rgba(249, 115, 22, 0.8)',
        'rgba(168, 85, 247, 0.8)'
      ];

      browserChartInstance = new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: colors,
            borderColor: '#fff',
            borderWidth: 3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              position: 'right',
              labels: {
                padding: 15,
                font: {
                  size: 12
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8
            }
          }
        }
      });
    }

    // ==================== TABLES ====================
    function updateLocationTable(locations) {
      allLocationsData = locations;
      const limit = parseInt(document.getElementById('locationLimit').value);
      const tbody = document.getElementById('locationTable');
      const displayData = locations.slice(0, limit);

      if (locations.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-500 py-8">Tidak ada data lokasi</td></tr>';
        updateLocationStats(0, 0, 0, 0);
        document.getElementById('showingCount').textContent = '0';
        document.getElementById('totalLocationCount').textContent = '0';
        return;
      }

      // Calculate statistics
      const uniqueCountries = new Set(locations.map(loc => loc.country)).size;
      const uniqueCities = new Set(locations.map(loc => loc.city)).size;
      const uniqueTimezones = new Set(locations.map(loc => loc.timezone)).size;

      updateLocationStats(locations.length, uniqueCountries, uniqueCities, uniqueTimezones);

      tbody.innerHTML = displayData.map((loc, idx) => `
      <tr>
        <td class="text-center">${idx + 1}</td>
        <td>
          <div class="flex items-center gap-2">
            <span class="country-flag">${getCountryFlag(loc.country_code)}</span>
            <span class="font-semibold">${loc.country || 'Unknown'}</span>
          </div>
        </td>
        <td>
          <div class="text-sm">
            <div class="font-medium text-gray-900">${loc.city || 'Unknown'}</div>
            <div class="text-gray-500">${loc.region || ''}</div>
          </div>
        </td>
        <td>
          <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs font-semibold">
            ${loc.timezone || 'Unknown'}
          </span>
        </td>
        <td class="text-center">${getVisitBadge(loc.visit_count)}</td>
      </tr>
    `).join('');

      document.getElementById('showingCount').textContent = displayData.length;
      document.getElementById('totalLocationCount').textContent = locations.length;
    }

    function updateLocationStats(total, countries, cities, timezones) {
      document.getElementById('totalLocations').textContent = total;
      document.getElementById('totalCountries').textContent = countries;
      document.getElementById('totalCities').textContent = cities;
      document.getElementById('totalTimezones').textContent = timezones;
    }

    function updateFrequencyTable(frequency) {
      allFrequencyData = frequency;
      const limit = parseInt(document.getElementById('frequencyLimit').value);
      const tbody = document.getElementById('freqTable');
      const displayData = frequency.slice(0, limit);

      if (frequency.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-500 py-8">Tidak ada data frekuensi</td></tr>';
        document.getElementById('freqShowingCount').textContent = '0';
        document.getElementById('freqTotalCount').textContent = '0';
        return;
      }

      tbody.innerHTML = displayData.map(item => `
      <tr>
        <td>${item.date || '-'}</td>
        <td class="font-mono text-sm">${item.ip || '-'}</td>
        <td>
          <div class="flex items-center gap-2">
            <span class="country-flag">${getCountryFlag(item.country_code)}</span>
            <div class="text-sm">
              <div class="font-medium">${item.city || 'Unknown'}</div>
              <div class="text-gray-500">${item.country || ''}</div>
            </div>
          </div>
        </td>
        <td>
          <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-lg text-xs font-semibold">
            ${item.timezone || 'Unknown'}
          </span>
        </td>
        <td>
          <div class="text-sm">
            <div class="font-medium">${item.device || 'Unknown'}</div>
            <div class="text-gray-500">${item.browser || 'Unknown'}</div>
          </div>
        </td>
        <td class="text-center">
          <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm shadow-lg bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
            <span>🔢</span>
            <span>${item.count} visits</span>
          </span>
        </td>
      </tr>
    `).join('');

      document.getElementById('freqShowingCount').textContent = displayData.length;
      document.getElementById('freqTotalCount').textContent = frequency.length;
    }

    function updateActivityTable(activity) {
      allActivityData = activity;
      const limit = parseInt(document.getElementById('activityLimit').value);
      const tbody = document.getElementById('activityTable');
      const displayData = activity.slice(0, limit);

      if (activity.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-500 py-8">Tidak ada aktivitas terbaru</td></tr>';
        document.getElementById('activityShowingCount').textContent = '0';
        document.getElementById('activityTotalCount').textContent = '0';
        return;
      }

      tbody.innerHTML = displayData.map(item => `
      <tr>
        <td class="font-medium">${item.time || '-'}</td>
        <td class="font-mono text-sm">${item.ip || '-'}</td>
        <td>
          <div class="flex items-center gap-2">
            <span class="country-flag">${getCountryFlag(item.country_code)}</span>
            <div class="text-sm">
              <div class="font-medium">${item.city || 'Unknown'}</div>
              <div class="text-gray-500">${item.country || ''}</div>
            </div>
          </div>
        </td>
        <td>
          <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-lg text-xs font-semibold">
            ${item.timezone || 'Unknown'}
          </span>
        </td>
        <td>
          <div class="text-sm">
            <div class="font-medium">${item.device || 'Unknown'}</div>
            <div class="text-gray-500">${item.browser || 'Unknown'}</div>
          </div>
        </td>
      </tr>
    `).join('');

      document.getElementById('activityShowingCount').textContent = displayData.length;
      document.getElementById('activityTotalCount').textContent = activity.length;
    }

    // ==================== EVENT LISTENERS ====================
    document.getElementById('timezoneFilter').addEventListener('change', function() {
      const tz = this.value;
      const date = document.getElementById('dateFilter').value;
      loadRecap(date, tz);
    });

    document.getElementById('dateFilter').addEventListener('change', function() {
      const date = this.value;
      const tz = document.getElementById('timezoneFilter').value;
      loadRecap(date, tz);
    });

    document.getElementById('locationLimit').addEventListener('change', function() {
      updateLocationTable(allLocationsData);
    });

    document.getElementById('frequencyLimit').addEventListener('change', function() {
      updateFrequencyTable(allFrequencyData);
    });

    document.getElementById('activityLimit').addEventListener('change', function() {
      updateActivityTable(allActivityData);
    });

    // ==================== INIT ====================
    document.addEventListener('DOMContentLoaded', function() {
      // Set default date to today
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('dateFilter').value = today;

      // Load initial data
      loadRecap(today, 'Asia/Jakarta');

      // Auto refresh every 30 seconds
      setInterval(() => {
        const date = document.getElementById('dateFilter').value;
        const tz = document.getElementById('timezoneFilter').value;
        loadRecap(date, tz);
      }, 30000);
    });
  </script>
</body>

</html>