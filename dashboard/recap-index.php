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
  <!-- SheetJS for Excel Export -->
  <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>

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

    /* ==================== ACTIVITY LOG SPECIFIC STYLING ==================== */

    /* Country flag larger for activity log */
    #activityTable .country-flag {
      font-size: 2rem;
      line-height: 1;
    }

    /* Address lines spacing */
    #activityTable .space-y-1>*+* {
      margin-top: 0.5rem;
    }

    /* Hover effects for activity rows */
    #activityTable tbody tr {
      transition: all 0.3s ease;
    }

    #activityTable tbody tr:hover {
      background: linear-gradient(to right, #f0fdf4, #ecfdf5);
      box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
      transform: translateX(2px);
    }

    /* Badge animations */
    @keyframes badge-pulse {

      0%,
      100% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.05);
      }
    }

    #activityTable tbody tr:first-child .rounded-full {
      animation: badge-pulse 2s ease-in-out infinite;
    }

    /* IP Address styling */
    #activityTable .font-mono {
      letter-spacing: -0.5px;
    }

    /* Device badge styling */
    #activityTable .bg-gradient-to-r {
      transition: all 0.3s ease;
    }

    #activityTable tbody tr:hover .bg-gradient-to-r {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Statistics cards animation */
    .table-container>div:first-child+div>div {
      transition: all 0.3s ease;
    }

    .table-container>div:first-child+div>div:hover {
      transform: translateY(-4px);
    }

    /* Loading state */
    @keyframes shimmer {
      0% {
        background-position: -1000px 0;
      }

      100% {
        background-position: 1000px 0;
      }
    }

    .loading-shimmer {
      background: linear-gradient(to right,
          #f3f4f6 0%,
          #e5e7eb 20%,
          #f3f4f6 40%,
          #f3f4f6 100%);
      background-size: 1000px 100%;
      animation: shimmer 2s linear infinite;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      #activityTable .country-flag {
        font-size: 1.5rem;
      }

      #activityTable tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.8125rem;
      }

      .table-wrapper {
        max-height: 500px;
      }
    }


    /* Download Button Style */
    .btn-download {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.75rem;
      font-weight: 600;
      font-size: 0.875rem;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-download:hover {
      background: linear-gradient(135deg, #059669 0%, #047857 100%);
      box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
      transform: translateY(-2px);
    }

    .btn-download:active {
      transform: translateY(0);
    }

    .btn-download svg {
      width: 1.25rem;
      height: 1.25rem;
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
  <header id="mainHeader" class="sticky top-0 z-40">
    <div class="bg-white/95 backdrop-blur-xl shadow-lg border-b border-gray-200/80">
      <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex items-center justify-between">

          <!-- Logo & Title -->
          <div class="flex items-center gap-3">
            <div class="relative w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 p-2.5 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
              <img
                src="../assets/icon/kulino-logo-blue.png"
                alt="Kulino Logo"
                class="w-full h-full object-contain drop-shadow-lg" />
            </div>

            <div class="flex flex-col">
              <h1 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Dashboard Recap Visitor
              </h1>
              <p class="text-xs text-gray-600 font-medium hidden sm:block">
                Real-time Analytics with Timezone
              </p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-2">
            <!-- Download Report Button -->
            <button
              id="downloadReportBtn"
              type="button"
              class="btn-download"
              title="Download Excel Report">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <span class="hidden sm:inline">Download Report</span>
              <span class="sm:hidden">Download</span>
            </button>
            <!-- Back Button -->
            <a href="index.php" class="group relative overflow-hidden bg-gradient-to-r from-gray-700 to-gray-900 hover:from-gray-800 hover:to-black px-4 py-2.5 rounded-xl transition-all duration-300 inline-flex items-center gap-2 shadow-lg hover:shadow-xl hover:scale-105">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white transition-transform group-hover:rotate-12" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
              </svg>
              <span class="hidden lg:inline font-semibold text-white">Kembali</span>
            </a>
          </div>
        </div>
      </div>
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
    <!-- Location Table Section -->
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
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200 hover:shadow-lg transition">
          <p class="text-xs text-blue-600 font-semibold mb-1">Total Locations</p>
          <p id="totalLocations" class="text-2xl font-bold text-blue-700">-</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200 hover:shadow-lg transition">
          <p class="text-xs text-green-600 font-semibold mb-1">Total Countries</p>
          <p id="totalCountries" class="text-2xl font-bold text-green-700">-</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-200 hover:shadow-lg transition">
          <p class="text-xs text-purple-600 font-semibold mb-1">Total Cities</p>
          <p id="totalCities" class="text-2xl font-bold text-purple-700">-</p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 p-4 rounded-xl border border-orange-200 hover:shadow-lg transition">
          <p class="text-xs text-orange-600 font-semibold mb-1">Timezones</p>
          <p id="totalTimezones" class="text-2xl font-bold text-orange-700">-</p>
        </div>
      </div>

      <!-- Location Table -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="15%">Negara</th>
              <th width="50%">Alamat Lengkap</th>
              <th width="15%">Timezone</th>
              <th width="15%">Total Kunjungan</th>
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

    <!-- Activity Log Section -->
    <section class="table-container">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-lg font-bold text-gray-900">📋 Log Aktivitas</h2>
          <p class="text-sm text-gray-500 mt-1">Real-time visitor activity with detailed location</p>
        </div>

        <div class="flex items-center gap-3">
          <label for="activityLimit" class="text-sm font-semibold text-gray-700">Show:</label>
          <select id="activityLimit"
            class="border-2 border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-600 transition">
            <option value="10" selected>10 entries</option>
            <option value="20">20 entries</option>
            <option value="50">50 entries</option>
            <option value="100">100 entries</option>
            <option value="200">200 entries</option>
          </select>
        </div>
      </div>

      <!-- Activity Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200 hover:shadow-lg transition">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white text-xl">
              ✅
            </div>
            <div>
              <p class="text-xs text-green-600 font-semibold">Total Activities</p>
              <p id="totalActivities" class="text-2xl font-bold text-green-700">-</p>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200 hover:shadow-lg transition">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl">
              🌍
            </div>
            <div>
              <p class="text-xs text-blue-600 font-semibold">Unique Locations</p>
              <p id="uniqueActivityLocations" class="text-2xl font-bold text-blue-700">-</p>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-4 rounded-xl border border-purple-200 hover:shadow-lg transition">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-purple-500 flex items-center justify-center text-white text-xl">
              📱
            </div>
            <div>
              <p class="text-xs text-purple-600 font-semibold">Unique Devices</p>
              <p id="uniqueDevices" class="text-2xl font-bold text-purple-700">-</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity Table -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th width="8%" class="text-center">#</th>
              <th width="12%" class="text-center">Waktu (Local)</th>
              <th width="12%">IP Address</th>
              <th width="35%">Lokasi Detail</th>
              <th width="13%" class="text-center">Timezone</th>
              <th width="20%">Device & Browser</th>
            </tr>
          </thead>
          <tbody id="activityTable">
            <tr>
              <td colspan="6" class="text-center text-gray-500">
                <div class="py-8">
                  <div class="inline-block w-8 h-8 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                  <p>Memuat data aktivitas...</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="pagination-controls">
        <div class="pagination-info">
          Showing <span id="activityShowingCount">0</span> of <span id="activityTotalCount">0</span> entries
        </div>

        <div class="flex items-center gap-2">
          <button onclick="exportActivityLog()"
            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg transition">
            📥 Export CSV
          </button>
          <button onclick="refreshActivityLog()"
            class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg transition">
            🔄 Refresh
          </button>
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
    let currentReportData = null;

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

    // ==================== GET COUNTRY FLAG EMOJI ====================
    function getCountryFlag(countryCode) {
      const flags = {
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
        'FR': '🇫🇷',
        'IT': '🇮🇹',
        'ES': '🇪🇸',
        'BR': '🇧🇷',
        'MX': '🇲🇽',
        'CA': '🇨🇦',
        'RU': '🇷🇺',
        'NL': '🇳🇱',
        'BE': '🇧🇪',
        'CH': '🇨🇭',
        'SE': '🇸🇪',
        'NO': '🇳🇴',
        'DK': '🇩🇰',
        'FI': '🇫🇮',
        'PL': '🇵🇱',
        'TR': '🇹🇷',
        'SA': '🇸🇦',
        'AE': '🇦🇪',
        'EG': '🇪🇬',
        'ZA': '🇿🇦',
        'NG': '🇳🇬',
        'KE': '🇰🇪',
        'AR': '🇦🇷',
        'CL': '🇨🇱',
        'CO': '🇨🇴',
        'PE': '🇵🇪',
        'NZ': '🇳🇿',
        'IE': '🇮🇪',
        'AT': '🇦🇹',
        'GR': '🇬🇷',
        'PT': '🇵🇹',
        'CZ': '🇨🇿',
        'HU': '🇭🇺',
        'RO': '🇷🇴',
        'UA': '🇺🇦',
        'IL': '🇮🇱',
        'PK': '🇵🇰',
        'BD': '🇧🇩',
        'LK': '🇱🇰',
        'MM': '🇲🇲',
        'KH': '🇰🇭',
        'LA': '🇱🇦'
      };

      return flags[countryCode?.toUpperCase()] || '🌐';
    }

    // ==================== GET VISIT BADGE WITH STYLING ====================
    function getVisitBadge(count) {
      let badgeClass = '';
      let icon = '';
      let label = '';

      if (count >= 100) {
        badgeClass = 'from-purple-500 to-pink-600';
        icon = '🏆';
        label = 'Platinum';
      } else if (count >= 50) {
        badgeClass = 'from-red-500 to-pink-500';
        icon = '🔥';
        label = 'Hot';
      } else if (count >= 20) {
        badgeClass = 'from-orange-500 to-yellow-500';
        icon = '⚡';
        label = 'Popular';
      } else if (count >= 10) {
        badgeClass = 'from-yellow-400 to-orange-400';
        icon = '⭐';
        label = 'Rising';
      } else if (count >= 5) {
        badgeClass = 'from-blue-400 to-indigo-500';
        icon = '📊';
        label = 'Active';
      } else {
        badgeClass = 'from-gray-400 to-gray-500';
        icon = '📍';
        label = 'Regular';
      }

      return `
    <div class="inline-flex flex-col items-center gap-1">
      <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm shadow-lg bg-gradient-to-r ${badgeClass} text-white">
        <span class="text-lg">${icon}</span>
        <span>${count}</span>
      </span>
      <span class="text-xs font-semibold text-gray-600">${label}</span>
    </div>
  `;
    }

    // ==================== LOAD DATA ====================
    let loadAttempts = 0;
    const MAX_LOAD_ATTEMPTS = 3;

    async function loadRecap(date = "", timezone = "Asia/Jakarta") {
      const statusDiv = document.getElementById('connectionStatus');
      const statusMsg = document.getElementById('statusMessage');

      loadAttempts++;

      console.log(`📡 Attempt ${loadAttempts}/${MAX_LOAD_ATTEMPTS}: Loading data...`);

      try {
        let url = `../track.php?tz=${encodeURIComponent(timezone)}`;
        if (date) url += `&date=${date}`;

        console.log('📡 Fetching:', url);

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000); // 15s timeout

        const res = await fetch(url, {
          method: 'GET',
          credentials: 'same-origin',
          headers: {
            'Accept': 'application/json'
          },
          signal: controller.signal
        });

        clearTimeout(timeoutId);

        if (!res.ok) {
          throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }

        const data = await res.json();

        // Update activity table with statistics
        updateActivityTable(data.activity || []);
        updateActivityStatistics(data.activity || []);
        console.log('✅ Data received:', data);

        // Check for errors in response
        if (data.error) {
          throw new Error(data.error + (data.details ? ': ' + data.details : ''));
        }

        // Validate required data
        if (typeof data.today === 'undefined') {
          throw new Error('Invalid response: missing required data');
        }

        // Store data for export
        currentReportData = data;

        // Hide error status
        if (statusDiv) statusDiv.classList.add('hidden');

        // Reset load attempts on success
        loadAttempts = 0;

        // Update stats
        document.getElementById("totalVisits").innerText = data.today || 0;
        document.getElementById("totalUnique").innerText = data.unique || 0;
        document.getElementById("activeVisitor").innerText = data.active || 0;
        

        if (data.display_timezone) {
          document.getElementById("currentTimezone").innerText = data.display_timezone;
        }

        // Update charts
        updateWeeklyChart(data.labels || [], data.weekly || []);
        updateCountryChart(data.country_labels || [], data.country_data || []);
        updateBrowserChart(data.browsers || []);

        // Update tables
        updateLocationTable(data.locations || []);
        updateFrequencyTable(data.frequency || []);
        updateActivityTable(data.activity || []);

        console.log('✅ Dashboard updated successfully');

      } catch (e) {
        console.error("❌ Error:", e);

        if (statusDiv && statusMsg) {
          statusDiv.classList.remove('hidden');

          if (e.name === 'AbortError') {
            statusMsg.textContent = `Request timeout. Retrying... (${loadAttempts}/${MAX_LOAD_ATTEMPTS})`;
          } else {
            statusMsg.textContent = `Error: ${e.message}. Retrying... (${loadAttempts}/${MAX_LOAD_ATTEMPTS})`;
          }
        }

        // Update UI with error state
        document.getElementById("totalVisits").innerText = "Error";
        document.getElementById("totalUnique").innerText = "Error";
        document.getElementById("activeVisitor").innerText = "Error";

        document.getElementById('activityLimit')?.addEventListener('change', function() {
          updateActivityTable(allActivityData);
          updateActivityStatistics(allActivityData);
        });
        
        // Retry logic
        if (loadAttempts < MAX_LOAD_ATTEMPTS) {
          console.log(`⏳ Retrying in 3 seconds...`);
          setTimeout(() => {
            loadRecap(date, timezone);
          }, 3000);
        } else {
          console.error('❌ Max retry attempts reached');
          if (statusMsg) {
            statusMsg.textContent = `Failed to load data after ${MAX_LOAD_ATTEMPTS} attempts. Please refresh the page.`;
          }

          // Show empty state in tables
          document.getElementById('locationTable').innerHTML =
            '<tr><td colspan="5" class="text-center text-red-500 py-8">Failed to load data. Please refresh the page.</td></tr>';
        }
      }
    }

    // ==================== CHARTS ====================
    function updateWeeklyChart(labels, data) {
      const ctx = document.getElementById("weeklyChart")?.getContext("2d");
      if (!ctx) {
        console.error('Weekly chart canvas not found');
        return;
      }

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
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              }
            }
          }
        }
      });
    }

    function updateCountryChart(labels, data) {
      const ctx = document.getElementById("countryChart")?.getContext("2d");
      if (!ctx) {
        console.error('Country chart canvas not found');
        return;
      }

      if (countryChartInstance) countryChartInstance.destroy();

      const colors = [
        'rgba(102, 126, 234, 0.8)', 'rgba(16, 185, 129, 0.8)',
        'rgba(244, 63, 94, 0.8)', 'rgba(251, 191, 36, 0.8)',
        'rgba(147, 51, 234, 0.8)', 'rgba(59, 130, 246, 0.8)',
        'rgba(236, 72, 153, 0.8)', 'rgba(34, 197, 94, 0.8)',
        'rgba(249, 115, 22, 0.8)', 'rgba(168, 85, 247, 0.8)'
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
      const ctx = document.getElementById("browserChart")?.getContext("2d");
      if (!ctx) {
        console.error('Browser chart canvas not found');
        return;
      }

      if (browserChartInstance) browserChartInstance.destroy();

      const labels = browsers.map(b => b.device || 'Unknown');
      const data = browsers.map(b => b.total || 0);

      const colors = [
        'rgba(102, 126, 234, 0.8)', 'rgba(16, 185, 129, 0.8)',
        'rgba(244, 63, 94, 0.8)', 'rgba(251, 191, 36, 0.8)',
        'rgba(147, 51, 234, 0.8)', 'rgba(59, 130, 246, 0.8)',
        'rgba(236, 72, 153, 0.8)', 'rgba(34, 197, 94, 0.8)',
        'rgba(249, 115, 22, 0.8)', 'rgba(168, 85, 247, 0.8)'
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
      const limit = parseInt(document.getElementById('locationLimit')?.value || 10);
      const tbody = document.getElementById('locationTable');

      if (!tbody) {
        console.error('Location table body not found');
        return;
      }

      const displayData = locations.slice(0, limit);

      if (locations.length === 0) {
        tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center text-gray-500 py-8">
          <div class="flex flex-col items-center">
            <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="font-semibold">Tidak ada data lokasi hari ini</p>
            <p class="text-sm text-gray-400 mt-1">Data akan muncul setelah ada visitor</p>
          </div>
        </td>
      </tr>
    `;
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

      tbody.innerHTML = displayData.map((loc, idx) => {
        // Build detailed address display
        const addressParts = [];

        // Street address (House Number + Street)
        if (loc.house_number && loc.street) {
          addressParts.push(`<strong>${loc.house_number}</strong> ${loc.street}`);
        } else if (loc.street) {
          addressParts.push(`<strong>${loc.street}</strong>`);
        }

        // Administrative divisions
        const adminParts = [];
        if (loc.subdistrict) adminParts.push(loc.subdistrict);
        if (loc.district) adminParts.push(loc.district);
        if (adminParts.length > 0) {
          addressParts.push(adminParts.join(', '));
        }

        // City and Region
        const locationParts = [];
        if (loc.city) locationParts.push(`<strong>${loc.city}</strong>`);
        if (loc.region && loc.region !== loc.city) locationParts.push(loc.region);
        if (locationParts.length > 0) {
          addressParts.push(locationParts.join(', '));
        }

        // Postal Code
        if (loc.postal_code) {
          addressParts.push(`📮 ${loc.postal_code}`);
        }

        const fullAddress = addressParts.length > 0 ?
          addressParts.join('<br>') :
          '<span class="text-gray-400 italic">Address details not available</span>';

        // GPS Coordinates display
        const gpsDisplay = loc.latitude && loc.longitude ? `
      <div class="text-xs text-gray-500 mt-2 p-2 bg-gray-50 rounded">
        📍 ${loc.latitude.toFixed(6)}, ${loc.longitude.toFixed(6)}
      </div>
    ` : '';

        return `
      <tr class="hover:bg-blue-50 transition-colors">
        <td class="text-center font-semibold text-gray-700">${idx + 1}</td>
        <td>
          <div class="flex items-center gap-3">
            <span class="country-flag text-3xl">${getCountryFlag(loc.country_code)}</span>
            <div>
              <p class="font-bold text-gray-900">${loc.country || 'Unknown'}</p>
              <p class="text-xs text-gray-500">${loc.country_code || 'XX'}</p>
            </div>
          </div>
        </td>
        <td>
          <div class="text-sm leading-relaxed">
            ${fullAddress}
            ${gpsDisplay}
          </div>
        </td>
        <td class="text-center">
          <span class="inline-block px-3 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl text-xs font-bold shadow-md">
            ${loc.timezone || 'Unknown'}
          </span>
        </td>
        <td class="text-center">
          ${getVisitBadge(loc.total)}
        </td>
      </tr>
    `;
      }).join('');

      document.getElementById('showingCount').textContent = displayData.length;
      document.getElementById('totalLocationCount').textContent = locations.length;
    }

    // ==================== UPDATE LOCATION STATISTICS ====================
    function updateLocationStats(total, countries, cities, timezones) {
      const elements = {
        totalLocations: document.getElementById('totalLocations'),
        totalCountries: document.getElementById('totalCountries'),
        totalCities: document.getElementById('totalCities'),
        totalTimezones: document.getElementById('totalTimezones')
      };

      // Animate number changes
      Object.entries(elements).forEach(([key, element]) => {
        if (element) {
          element.classList.add('loading');
          setTimeout(() => {
            element.classList.remove('loading');
          }, 300);
        }
      });

      // Update values
      if (elements.totalLocations) elements.totalLocations.textContent = total;
      if (elements.totalCountries) elements.totalCountries.textContent = countries;
      if (elements.totalCities) elements.totalCities.textContent = cities;
      if (elements.totalTimezones) elements.totalTimezones.textContent = timezones;
    }

    function updateFrequencyTable(frequency) {
      allFrequencyData = frequency;
      const limit = parseInt(document.getElementById('frequencyLimit')?.value || 10);
      const tbody = document.getElementById('freqTable');

      if (!tbody) return;

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
        </div>
      </td>
      <td class="text-center">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm shadow-lg bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
          <span>🔢</span>
          <span>${item.visits} visits</span>
        </span>
      </td>
    </tr>
  `).join('');

      document.getElementById('freqShowingCount').textContent = displayData.length;
      document.getElementById('freqTotalCount').textContent = frequency.length;
    }

    // ==================== UPDATE ACTIVITY TABLE WITH DETAILED LOCATION ====================
    function updateActivityTable(activity) {
      allActivityData = activity;
      const limit = parseInt(document.getElementById('activityLimit')?.value || 10);
      const tbody = document.getElementById('activityTable');

      if (!tbody) {
        console.error('Activity table body not found');
        return;
      }

      const displayData = activity.slice(0, limit);

      if (activity.length === 0) {
        tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-gray-500 py-8">
          <div class="flex flex-col items-center">
            <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-semibold">Tidak ada aktivitas terbaru</p>
            <p class="text-sm text-gray-400 mt-1">Log aktivitas akan muncul saat ada pengunjung</p>
          </div>
        </td>
      </tr>
    `;
        document.getElementById('activityShowingCount').textContent = '0';
        document.getElementById('activityTotalCount').textContent = '0';
        return;
      }

      tbody.innerHTML = displayData.map((item, index) => {
        // Build detailed address display
        const addressLines = [];

        // Line 1: Street Address
        if (item.house_number && item.street) {
          addressLines.push(`
        <div class="flex items-start gap-2">
          <span class="text-blue-600 mt-0.5">🏠</span>
          <span><strong class="text-gray-900">${item.house_number}</strong> ${item.street}</span>
        </div>
      `);
        } else if (item.street) {
          addressLines.push(`
        <div class="flex items-start gap-2">
          <span class="text-blue-600 mt-0.5">🏠</span>
          <span class="text-gray-900">${item.street}</span>
        </div>
      `);
        }

        // Line 2: Administrative Divisions
        const adminParts = [];
        if (item.subdistrict) adminParts.push(item.subdistrict);
        if (item.district) adminParts.push(item.district);

        if (adminParts.length > 0) {
          addressLines.push(`
        <div class="flex items-start gap-2">
          <span class="text-purple-600 mt-0.5">📍</span>
          <span class="text-gray-700">${adminParts.join(', ')}</span>
        </div>
      `);
        }

        // Line 3: City & Region
        const locationParts = [];
        if (item.city) locationParts.push(`<strong>${item.city}</strong>`);
        if (item.region && item.region !== item.city) locationParts.push(item.region);

        if (locationParts.length > 0) {
          addressLines.push(`
        <div class="flex items-start gap-2">
          <span class="text-green-600 mt-0.5">🏙️</span>
          <span class="text-gray-800">${locationParts.join(', ')}</span>
        </div>
      `);
        }

        // Line 4: Postal Code
        if (item.postal_code) {
          addressLines.push(`
        <div class="flex items-start gap-2">
          <span class="text-orange-600 mt-0.5">📮</span>
          <span class="text-gray-600">Postal Code: <strong>${item.postal_code}</strong></span>
        </div>
      `);
        }

        // Line 5: GPS Coordinates (if available)
        if (item.latitude && item.longitude) {
          addressLines.push(`
        <div class="flex items-start gap-2 text-xs">
          <span class="text-indigo-600 mt-0.5">📡</span>
          <span class="text-gray-500 font-mono">${item.latitude.toFixed(6)}, ${item.longitude.toFixed(6)}</span>
        </div>
      `);
        }

        const addressDisplay = addressLines.length > 0 ?
          addressLines.join('') :
          '<div class="text-gray-400 italic text-sm">Address details not available</div>';

        // Activity number badge
        const activityBadge = getActivityBadge(index + 1, displayData.length);

        return `
      <tr class="hover:bg-green-50 transition-all duration-200">
        <td class="text-center">
          ${activityBadge}
        </td>
        
        <td class="text-center">
          <div class="flex flex-col items-center gap-1">
            <span class="font-mono text-lg font-bold text-indigo-700">${item.time}</span>
            <span class="text-xs text-gray-500">${item.full_datetime?.split(' ')[0] || ''}</span>
          </div>
        </td>
        
        <td>
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md">
              ${item.ip?.split('.')[3] || '?'}
            </div>
            <div>
              <p class="font-mono text-sm font-semibold text-gray-800">${item.ip || '-'}</p>
              <p class="text-xs text-gray-500">IP Address</p>
            </div>
          </div>
        </td>
        
        <td>
          <div class="space-y-2">
            <div class="flex items-center gap-2 mb-2">
              <span class="country-flag text-2xl">${getCountryFlag(item.country_code)}</span>
              <div>
                <p class="font-bold text-gray-900">${item.country || 'Unknown'}</p>
                <p class="text-xs text-gray-500">${item.country_code || 'XX'}</p>
              </div>
            </div>
            <div class="pl-1 space-y-1 text-sm">
              ${addressDisplay}
            </div>
          </div>
        </td>
        
        <td class="text-center">
          <div class="inline-flex flex-col items-center gap-2">
            <span class="inline-block px-3 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl text-xs font-bold shadow-md">
              ${item.timezone || 'Unknown'}
            </span>
            <span class="text-xs text-gray-500">${item.timezone?.includes('UTC') ? 'Timezone' : 'Local Time'}</span>
          </div>
        </td>
        
        <td>
          <div class="space-y-2">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-3 border border-purple-200">
              <p class="text-xs text-purple-600 font-semibold mb-1">Device & Browser</p>
              <p class="text-sm text-gray-800 font-medium">${item.device || 'Unknown'}</p>
            </div>
            ${getDeviceIcon(item.device)}
          </div>
        </td>
      </tr>
    `;
      }).join('');

      document.getElementById('activityShowingCount').textContent = displayData.length;
      document.getElementById('activityTotalCount').textContent = activity.length;
    }

    // ==================== GET ACTIVITY BADGE ====================
    function getActivityBadge(number, total) {
      let badgeClass = '';
      let icon = '';

      if (number === 1) {
        badgeClass = 'from-yellow-400 to-orange-500';
        icon = '🥇';
      } else if (number === 2) {
        badgeClass = 'from-gray-300 to-gray-400';
        icon = '🥈';
      } else if (number === 3) {
        badgeClass = 'from-orange-400 to-yellow-600';
        icon = '🥉';
      } else if (number <= 5) {
        badgeClass = 'from-blue-400 to-indigo-500';
        icon = '⭐';
      } else {
        badgeClass = 'from-gray-400 to-gray-500';
        icon = '📌';
      }

      return `
    <div class="flex flex-col items-center gap-1">
      <div class="w-12 h-12 rounded-full bg-gradient-to-br ${badgeClass} flex items-center justify-center text-white font-bold text-lg shadow-lg">
        ${icon}
      </div>
      <span class="text-xs font-semibold text-gray-600">#${number}</span>
    </div>
  `;
    }

    // ==================== GET DEVICE ICON ====================
    function getDeviceIcon(device) {
      if (!device) return '';

      const deviceLower = device.toLowerCase();
      let icon = '💻';
      let label = 'Desktop';
      let colorClass = 'blue';

      if (deviceLower.includes('android')) {
        icon = '📱';
        label = 'Android';
        colorClass = 'green';
      } else if (deviceLower.includes('iphone') || deviceLower.includes('ipad')) {
        icon = '📱';
        label = 'iOS';
        colorClass = 'purple';
      } else if (deviceLower.includes('windows')) {
        icon = '💻';
        label = 'Windows';
        colorClass = 'blue';
      } else if (deviceLower.includes('mac')) {
        icon = '🖥️';
        label = 'macOS';
        colorClass = 'gray';
      } else if (deviceLower.includes('linux')) {
        icon = '🐧';
        label = 'Linux';
        colorClass = 'yellow';
      }

      return `
    <div class="flex items-center justify-center gap-2 text-${colorClass}-600 text-xs font-semibold">
      <span class="text-lg">${icon}</span>
      <span>${label}</span>
    </div>
  `;
    }

    // ==================== EXPORT ACTIVITY LOG TO CSV ====================
    function exportActivityLog() {
      if (!allActivityData || allActivityData.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'No Data',
          text: 'No activity data available to export',
          confirmButtonColor: '#ef4444'
        });
        return;
      }

      // Prepare CSV data
      const csvRows = [];

      // Header
      csvRows.push([
        'No',
        'Date',
        'Time',
        'IP Address',
        'Country',
        'City',
        'Street',
        'House Number',
        'District',
        'Subdistrict',
        'Region',
        'Postal Code',
        'Timezone',
        'Latitude',
        'Longitude',
        'Device & Browser'
      ].join(','));

      // Data rows
      allActivityData.forEach((item, index) => {
        csvRows.push([
          index + 1,
          item.full_datetime?.split(' ')[0] || '',
          item.time || '',
          item.ip || '',
          item.country || '',
          item.city || '',
          `"${item.street || ''}"`,
          item.house_number || '',
          `"${item.district || ''}"`,
          `"${item.subdistrict || ''}"`,
          `"${item.region || ''}"`,
          item.postal_code || '',
          item.timezone || '',
          item.latitude || '',
          item.longitude || '',
          `"${item.device || ''}"`
        ].join(','));
      });

      // Create and download CSV
      const csvContent = csvRows.join('\n');
      const blob = new Blob([csvContent], {
        type: 'text/csv;charset=utf-8;'
      });
      const link = document.createElement('a');
      const url = URL.createObjectURL(blob);

      const filename = `Activity_Log_${new Date().toISOString().split('T')[0]}.csv`;

      link.setAttribute('href', url);
      link.setAttribute('download', filename);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      Swal.fire({
        icon: 'success',
        title: 'Exported!',
        text: `Activity log exported to ${filename}`,
        timer: 2000,
        showConfirmButton: false
      });
    }

    // ==================== REFRESH ACTIVITY LOG ====================
    function refreshActivityLog() {
      const date = document.getElementById('dateFilter')?.value;
      const timezone = document.getElementById('timezoneFilter')?.value;

      Swal.fire({
        title: 'Refreshing...',
        text: 'Loading latest activity data',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      loadRecap(date, timezone).then(() => {
        Swal.fire({
          icon: 'success',
          title: 'Refreshed!',
          text: 'Activity log updated successfully',
          timer: 1500,
          showConfirmButton: false
        });
      }).catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Refresh Failed',
          text: error.message,
          confirmButtonColor: '#ef4444'
        });
      });
    }

    // ==================== EVENT LISTENERS ====================
    document.getElementById('timezoneFilter')?.addEventListener('change', function() {
      const tz = this.value;
      const date = document.getElementById('dateFilter').value;
      loadRecap(date, tz);
    });

    document.getElementById('dateFilter')?.addEventListener('change', function() {
      const date = this.value;
      const tz = document.getElementById('timezoneFilter').value;
      loadRecap(date, tz);
    });

    document.getElementById('locationLimit')?.addEventListener('change', function() {
      updateLocationTable(allLocationsData);
    });

    document.getElementById('frequencyLimit')?.addEventListener('change', function() {
      updateFrequencyTable(allFrequencyData);
    });

    document.getElementById('activityLimit')?.addEventListener('change', function() {
      updateActivityTable(allActivityData);
    });

    // ==================== INIT ====================
    document.addEventListener('DOMContentLoaded', function() {
      console.log('🚀 Dashboard initializing...');

      // Set default date to today
      const today = new Date().toISOString().split('T')[0];
      const dateFilter = document.getElementById('dateFilter');
      if (dateFilter) {
        dateFilter.value = today;
      }

      // Load initial data
      loadRecap(today, 'Asia/Jakarta');

      // Auto refresh every 30 seconds
      setInterval(() => {
        const date = document.getElementById('dateFilter')?.value;
        const tz = document.getElementById('timezoneFilter')?.value;
        console.log('🔄 Auto-refreshing data...');
        loadRecap(date, tz);
      }, 30000);
    });

    // ==================== ENHANCED EXCEL REPORT GENERATOR ====================

    async function downloadExcelReport() {
      const downloadBtn = document.getElementById('downloadReportBtn');

      // Disable button
      if (downloadBtn) {
        downloadBtn.disabled = true;
        downloadBtn.innerHTML = `
      <div class="inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
      <span>Generating...</span>
    `;
      }

      try {
        console.log('📥 Starting Excel report generation...');

        // Get current filter values
        const timezone = document.getElementById('timezoneFilter')?.value || 'Asia/Jakarta';
        const date = document.getElementById('dateFilter')?.value || new Date().toISOString().split('T')[0];

        // Fetch fresh data
        let url = `../track.php?tz=${encodeURIComponent(timezone)}`;
        if (date) url += `&date=${date}`;

        const response = await fetch(url);
        if (!response.ok) throw new Error('Failed to fetch data');

        const data = await response.json();
        console.log('✅ Data fetched for export:', data);

        // Create workbook
        const wb = XLSX.utils.book_new();

        // Set workbook properties
        wb.Props = {
          Title: "Kulino Visitor Dashboard Report",
          Subject: "Analytics Report",
          Author: "Kulino Game Hub",
          CreatedDate: new Date()
        };

        // ========== SHEET 1: DASHBOARD SUMMARY ==========
        const summaryData = [];

        // Title row (merged)
        summaryData.push(['📊 KULINO VISITOR DASHBOARD REPORT']);
        summaryData.push([]);

        // Report metadata
        summaryData.push(['Generated:', new Date().toLocaleString('id-ID', {
          timeZone: timezone
        })]);
        summaryData.push(['Timezone:', data.display_timezone || timezone]);
        summaryData.push(['Filter Date:', date]);
        summaryData.push(['Report Period:', 'Last 7 Days']);
        summaryData.push([]);

        // Summary Statistics Section
        summaryData.push(['═══════════════════════════════════════']);
        summaryData.push(['SUMMARY STATISTICS']);
        summaryData.push(['═══════════════════════════════════════']);
        summaryData.push([]);

        summaryData.push(['Metric', 'Value', 'Description']);
        summaryData.push(['Total Visits Today', data.today || 0, 'Total page visits on selected date']);
        summaryData.push(['Unique Visitors', data.unique || 0, 'Unique visitors based on IP + Device']);
        summaryData.push(['Active Now (10 min)', data.active || 0, 'Currently active visitors']);
        summaryData.push([]);

        // Weekly Trends Section
        summaryData.push(['═══════════════════════════════════════']);
        summaryData.push(['WEEKLY VISITOR TRENDS']);
        summaryData.push(['═══════════════════════════════════════']);
        summaryData.push([]);

        summaryData.push(['Date', 'Total Visitors', 'Change', 'Trend']);

        // Add weekly data with trends
        if (data.labels && data.weekly) {
          data.labels.forEach((label, idx) => {
            const current = data.weekly[idx] || 0;
            const previous = idx > 0 ? (data.weekly[idx - 1] || 0) : 0;
            const change = previous > 0 ? (((current - previous) / previous) * 100).toFixed(1) + '%' : 'N/A';
            const trend = current > previous ? '↑' : current < previous ? '↓' : '→';

            summaryData.push([label, current, change, trend]);
          });
        }

        summaryData.push([]);
        summaryData.push(['═══════════════════════════════════════']);
        summaryData.push(['Total Visits (7 days):', data.weekly ? data.weekly.reduce((a, b) => a + b, 0) : 0]);
        summaryData.push(['Average Visitors/Day:', data.weekly ? Math.round(data.weekly.reduce((a, b) => a + b, 0) / data.weekly.length) : 0]);

        const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);

        // Column widths
        wsSummary['!cols'] = [{
            wch: 30
          },
          {
            wch: 20
          },
          {
            wch: 35
          },
          {
            wch: 10
          }
        ];

        // Merge title cell
        wsSummary['!merges'] = [{
          s: {
            r: 0,
            c: 0
          },
          e: {
            r: 0,
            c: 3
          }
        }];

        XLSX.utils.book_append_sheet(wb, wsSummary, 'Dashboard Summary');

        // ========== SHEET 2: TOP 10 COUNTRIES ==========
        const countryData = [];

        countryData.push(['🌍 TOP 10 COUNTRIES (Last 7 Days)']);
        countryData.push([]);
        countryData.push(['Generated:', new Date().toLocaleString('id-ID')]);
        countryData.push([]);

        countryData.push(['Rank', 'Country', 'Total Visitors', '% of Total', 'Trend']);

        const totalCountryVisits = data.country_data ? data.country_data.reduce((a, b) => a + b, 0) : 0;

        if (data.country_labels && data.country_data) {
          data.country_labels.forEach((country, idx) => {
            const visitors = data.country_data[idx] || 0;
            const percentage = totalCountryVisits > 0 ? ((visitors / totalCountryVisits) * 100).toFixed(1) + '%' : '0%';
            const trend = idx === 0 ? '🥇' : idx === 1 ? '🥈' : idx === 2 ? '🥉' : '⭐';

            countryData.push([
              idx + 1,
              country,
              visitors,
              percentage,
              trend
            ]);
          });
        }

        countryData.push([]);
        countryData.push(['Total Visits:', totalCountryVisits]);

        const wsCountry = XLSX.utils.aoa_to_sheet(countryData);
        wsCountry['!cols'] = [{
            wch: 8
          },
          {
            wch: 30
          },
          {
            wch: 18
          },
          {
            wch: 15
          },
          {
            wch: 10
          }
        ];

        wsCountry['!merges'] = [{
          s: {
            r: 0,
            c: 0
          },
          e: {
            r: 0,
            c: 4
          }
        }];

        XLSX.utils.book_append_sheet(wb, wsCountry, 'Top Countries');

        // ========== SHEET 3: DETAILED LOCATIONS ==========
        if (data.locations && data.locations.length > 0) {
          const locationData = [];

          locationData.push(['📍 VISITOR LOCATIONS - ' + date]);
          locationData.push([]);
          locationData.push(['Generated:', new Date().toLocaleString('id-ID')]);
          locationData.push(['Total Unique Locations:', data.locations.length]);
          locationData.push([]);

          locationData.push([
            'No',
            'Country',
            'City',
            'Street',
            'House No',
            'District',
            'Subdistrict',
            'Region',
            'Postal Code',
            'Timezone',
            'Latitude',
            'Longitude',
            'Total Visits',
            'Full Address'
          ]);

          data.locations.forEach((loc, idx) => {
            locationData.push([
              idx + 1,
              loc.country || '',
              loc.city || '',
              loc.street || '',
              loc.house_number || '',
              loc.district || '',
              loc.subdistrict || '',
              loc.region || '',
              loc.postal_code || '',
              loc.timezone || '',
              loc.latitude || '',
              loc.longitude || '',
              loc.total || 0,
              loc.full_location || ''
            ]);
          });

          locationData.push([]);
          locationData.push(['Total Visits from All Locations:', data.locations.reduce((sum, loc) => sum + (loc.total || 0), 0)]);

          const wsLocation = XLSX.utils.aoa_to_sheet(locationData);
          wsLocation['!cols'] = [{
              wch: 6
            },
            {
              wch: 20
            },
            {
              wch: 20
            },
            {
              wch: 25
            },
            {
              wch: 10
            },
            {
              wch: 20
            },
            {
              wch: 20
            },
            {
              wch: 20
            },
            {
              wch: 12
            },
            {
              wch: 20
            },
            {
              wch: 12
            },
            {
              wch: 12
            },
            {
              wch: 12
            },
            {
              wch: 50
            }
          ];

          wsLocation['!merges'] = [{
            s: {
              r: 0,
              c: 0
            },
            e: {
              r: 0,
              c: 13
            }
          }];

          XLSX.utils.book_append_sheet(wb, wsLocation, 'Location Details');
        }

        // ========== SHEET 4: VISIT FREQUENCY ==========
        if (data.frequency && data.frequency.length > 0) {
          const freqData = [];

          freqData.push(['🔄 VISIT FREQUENCY ANALYSIS (Last 7 Days)']);
          freqData.push([]);
          freqData.push(['Generated:', new Date().toLocaleString('id-ID')]);
          freqData.push(['Total Records:', data.frequency.length]);
          freqData.push([]);

          freqData.push([
            'No',
            'Date',
            'IP Address',
            'City',
            'Country',
            'Timezone',
            'Device & Browser',
            'Visit Count',
            'Status'
          ]);

          data.frequency.forEach((item, idx) => {
            const status = item.visits >= 10 ? 'High Frequency' :
              item.visits >= 5 ? 'Medium Frequency' : 'Regular';

            freqData.push([
              idx + 1,
              item.date || '',
              item.ip || '',
              item.city || '',
              item.country || '',
              item.timezone || '',
              item.device || '',
              item.visits || 0,
              status
            ]);
          });

          freqData.push([]);
          freqData.push(['Total Visits:', data.frequency.reduce((sum, item) => sum + (item.visits || 0), 0)]);
          freqData.push(['Average Visits per User:', (data.frequency.reduce((sum, item) => sum + (item.visits || 0), 0) / data.frequency.length).toFixed(2)]);

          const wsFreq = XLSX.utils.aoa_to_sheet(freqData);
          wsFreq['!cols'] = [{
              wch: 6
            },
            {
              wch: 12
            },
            {
              wch: 18
            },
            {
              wch: 20
            },
            {
              wch: 20
            },
            {
              wch: 20
            },
            {
              wch: 40
            },
            {
              wch: 12
            },
            {
              wch: 18
            }
          ];

          wsFreq['!merges'] = [{
            s: {
              r: 0,
              c: 0
            },
            e: {
              r: 0,
              c: 8
            }
          }];

          XLSX.utils.book_append_sheet(wb, wsFreq, 'Visit Frequency');
        }

        // ========== SHEET 5: ACTIVITY LOG ==========
        if (data.activity && data.activity.length > 0) {
          const activityData = [];

          activityData.push(['📋 ACTIVITY LOG - ' + date]);
          activityData.push([]);
          activityData.push(['Generated:', new Date().toLocaleString('id-ID')]);
          activityData.push(['Total Activities:', data.activity.length]);
          activityData.push([]);

          activityData.push([
            'No',
            'Time (Local)',
            'IP Address',
            'City',
            'Country',
            'Timezone',
            'Device & Browser',
            'Full Location'
          ]);

          data.activity.forEach((item, idx) => {
            activityData.push([
              idx + 1,
              item.time || '',
              item.ip || '',
              item.city || '',
              item.country || '',
              item.timezone || '',
              item.device || '',
              item.full_location || ''
            ]);
          });

          const wsActivity = XLSX.utils.aoa_to_sheet(activityData);
          wsActivity['!cols'] = [{
              wch: 6
            },
            {
              wch: 15
            },
            {
              wch: 18
            },
            {
              wch: 20
            },
            {
              wch: 20
            },
            {
              wch: 20
            },
            {
              wch: 40
            },
            {
              wch: 50
            }
          ];

          wsActivity['!merges'] = [{
            s: {
              r: 0,
              c: 0
            },
            e: {
              r: 0,
              c: 7
            }
          }];

          XLSX.utils.book_append_sheet(wb, wsActivity, 'Activity Log');
        }

        // ========== SHEET 6: BROWSER & DEVICE STATS ==========
        if (data.browsers && data.browsers.length > 0) {
          const browserData = [];

          browserData.push(['🌐 BROWSER & DEVICE STATISTICS (Last 7 Days)']);
          browserData.push([]);
          browserData.push(['Generated:', new Date().toLocaleString('id-ID')]);
          browserData.push(['Total Devices:', data.browsers.length]);
          browserData.push([]);

          browserData.push([
            'Rank',
            'Device & Browser',
            'Total Users',
            '% of Total',
            'Category'
          ]);

          const totalBrowserUsers = data.browsers.reduce((sum, b) => sum + (b.total || 0), 0);

          data.browsers.forEach((item, idx) => {
            const percentage = totalBrowserUsers > 0 ?
              ((item.total / totalBrowserUsers) * 100).toFixed(1) + '%' : '0%';

            // Categorize device
            let category = 'Other';
            if (item.device.includes('Windows')) category = 'Desktop';
            else if (item.device.includes('Android')) category = 'Mobile';
            else if (item.device.includes('iPhone') || item.device.includes('iPad')) category = 'iOS';
            else if (item.device.includes('Mac')) category = 'Desktop';

            browserData.push([
              idx + 1,
              item.device || 'Unknown',
              item.total || 0,
              percentage,
              category
            ]);
          });

          browserData.push([]);
          browserData.push(['Total Users:', totalBrowserUsers]);

          // Add category summary
          browserData.push([]);
          browserData.push(['Category Summary:']);
          const categories = {};
          data.browsers.forEach(item => {
            let cat = 'Other';
            if (item.device.includes('Windows') || item.device.includes('Mac')) cat = 'Desktop';
            else if (item.device.includes('Android')) cat = 'Android';
            else if (item.device.includes('iPhone') || item.device.includes('iPad')) cat = 'iOS';

            categories[cat] = (categories[cat] || 0) + (item.total || 0);
          });

          Object.entries(categories).forEach(([cat, count]) => {
            const pct = totalBrowserUsers > 0 ? ((count / totalBrowserUsers) * 100).toFixed(1) + '%' : '0%';
            browserData.push([cat, count, pct]);
          });

          const wsBrowser = XLSX.utils.aoa_to_sheet(browserData);
          wsBrowser['!cols'] = [{
              wch: 8
            },
            {
              wch: 45
            },
            {
              wch: 15
            },
            {
              wch: 15
            },
            {
              wch: 15
            }
          ];

          wsBrowser['!merges'] = [{
            s: {
              r: 0,
              c: 0
            },
            e: {
              r: 0,
              c: 4
            }
          }];

          XLSX.utils.book_append_sheet(wb, wsBrowser, 'Browsers & Devices');
        }

        // ========== SHEET 7: ANALYTICS INSIGHTS ==========
        const insightsData = [];

        insightsData.push(['💡 ANALYTICS INSIGHTS & RECOMMENDATIONS']);
        insightsData.push([]);
        insightsData.push(['Report Date:', date]);
        insightsData.push(['Generated:', new Date().toLocaleString('id-ID')]);
        insightsData.push([]);

        // Key Metrics
        insightsData.push(['═══════════════════════════════════════']);
        insightsData.push(['KEY PERFORMANCE INDICATORS']);
        insightsData.push(['═══════════════════════════════════════']);
        insightsData.push([]);

        const avgVisitors = data.weekly ? Math.round(data.weekly.reduce((a, b) => a + b, 0) / data.weekly.length) : 0;
        const peakDay = data.weekly && data.labels ?
          data.labels[data.weekly.indexOf(Math.max(...data.weekly))] : 'N/A';

        insightsData.push(['Metric', 'Value', 'Status', 'Recommendation']);
        insightsData.push([
          'Average Daily Visitors',
          avgVisitors,
          avgVisitors > 50 ? 'Good' : 'Needs Improvement',
          avgVisitors > 50 ? 'Maintain current strategies' : 'Consider marketing campaigns'
        ]);
        insightsData.push([
          'Peak Traffic Day',
          peakDay,
          'Info',
          'Focus promotions on peak days'
        ]);
        insightsData.push([
          'Active User Rate',
          data.active + ' / ' + data.today,
          data.active > (data.today * 0.1) ? 'Excellent' : 'Average',
          'Engage users with interactive content'
        ]);
        insightsData.push([]);

        // Geographic Insights
        insightsData.push(['═══════════════════════════════════════']);
        insightsData.push(['GEOGRAPHIC DISTRIBUTION']);
        insightsData.push(['═══════════════════════════════════════']);
        insightsData.push([]);

        if (data.country_labels && data.country_labels.length > 0) {
          insightsData.push(['Top Country:', data.country_labels[0] || 'N/A']);
          insightsData.push(['Countries Reached:', data.country_labels.length]);
          insightsData.push(['Cities Tracked:', data.locations ? data.locations.length : 0]);
          insightsData.push([]);
          insightsData.push(['Recommendation:', 'Consider localization for top 3 countries']);
        }

        // Traffic Patterns
        insightsData.push([]);
        insightsData.push(['═══════════════════════════════════════']);
        insightsData.push(['TRAFFIC PATTERNS']);
        insightsData.push(['═══════════════════════════════════════']);
        insightsData.push([]);

        insightsData.push(['Observation', 'Action Item']);
        insightsData.push(['Weekly trends show...', 'Monitor daily for patterns']);
        insightsData.push(['User retention rate...', 'Implement loyalty programs']);
        insightsData.push(['Device diversity...', 'Optimize for all platforms']);

        const wsInsights = XLSX.utils.aoa_to_sheet(insightsData);
        wsInsights['!cols'] = [{
            wch: 35
          },
          {
            wch: 20
          },
          {
            wch: 20
          },
          {
            wch: 40
          }
        ];

        wsInsights['!merges'] = [{
          s: {
            r: 0,
            c: 0
          },
          e: {
            r: 0,
            c: 3
          }
        }];

        XLSX.utils.book_append_sheet(wb, wsInsights, 'Analytics Insights');

        // Generate filename with timestamp
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, -5);
        const filename = `Kulino_Visitor_Report_${date.replace(/-/g, '')}_${timestamp}.xlsx`;

        // Write and download
        XLSX.writeFile(wb, filename);

        console.log('✅ Excel report generated successfully');

        // Show success message
        await Swal.fire({
          icon: 'success',
          title: 'Report Downloaded Successfully!',
          html: `
        <div class="text-left space-y-3">
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-xl border border-green-200">
            <p class="text-sm font-semibold text-green-800 mb-2">📊 Report Contents:</p>
            <ul class="text-sm text-green-700 space-y-1 ml-4">
              <li>✓ Dashboard Summary with Weekly Trends</li>
              <li>✓ Top 10 Countries Analysis</li>
              <li>✓ ${data.locations?.length || 0} Detailed Location Records</li>
              <li>✓ ${data.frequency?.length || 0} Visit Frequency Data</li>
              <li>✓ ${data.activity?.length || 0} Activity Log Entries</li>
              <li>✓ ${data.browsers?.length || 0} Browser & Device Stats</li>
              <li>✓ Analytics Insights & Recommendations</li>
            </ul>
          </div>
          <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
            <p class="text-sm text-blue-800">
              <strong>Filename:</strong><br>
              <code class="text-xs">${filename}</code>
            </p>
          </div>
          <p class="text-xs text-gray-500 text-center">
            Check your Downloads folder
          </p>
        </div>
      `,
          confirmButtonText: 'Great!',
          confirmButtonColor: '#10b981',
          width: '600px',
          timer: 8000
        });

      } catch (error) {
        console.error('❌ Excel export error:', error);

        await Swal.fire({
          icon: 'error',
          title: 'Export Failed',
          html: `
        <div class="text-left">
          <p class="mb-3">Failed to generate Excel report:</p>
          <div class="bg-red-50 p-3 rounded-lg border border-red-200">
            <code class="text-sm text-red-700">${error.message}</code>
          </div>
          <p class="text-sm text-gray-600 mt-3">Please try again or contact support if the issue persists.</p>
        </div>
      `,
          confirmButtonColor: '#ef4444'
        });
      } finally {
        // Re-enable button
        if (downloadBtn) {
          downloadBtn.disabled = false;
          downloadBtn.innerHTML = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span class="hidden sm:inline">Download Report</span>
        <span class="sm:hidden">Download</span>
      `;
        }
      }
    }

    // Initialize event listener
    document.addEventListener('DOMContentLoaded', function() {
      const downloadBtn = document.getElementById('downloadReportBtn');
      if (downloadBtn) {
        downloadBtn.addEventListener('click', downloadExcelReport);
        console.log('✅ Excel download button initialized');
      }
    });

    console.log('✅ Enhanced Excel Report Generator loaded');
  </script>
</body>

</html>