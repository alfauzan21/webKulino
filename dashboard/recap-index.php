<!DOCTYPE html>
<html lang="id">
<?php
// recap-index.php - Updated with GPS location support

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database connection
$conn = new mysqli('localhost', 'username', 'password', 'database_name');

if ($conn->connect_error) {
  echo json_encode(['error' => 'Database connection failed']);
  exit;
}

// Get timezone parameter (default: Asia/Jakarta)
$requestedTz = $_GET['tz'] ?? 'Asia/Jakarta';
$filterDate = $_GET['date'] ?? date('Y-m-d');

try {
  $tz = new DateTimeZone($requestedTz);
} catch (Exception $e) {
  $tz = new DateTimeZone('Asia/Jakarta');
  $requestedTz = 'Asia/Jakarta';
}

// Function to get timezone display name
function getTimezoneName($tzString)
{
  $tzMap = [
    'Asia/Jakarta' => 'WIB (UTC+7)',
    'Asia/Makassar' => 'WITA (UTC+8)',
    'Asia/Jayapura' => 'WIT (UTC+9)',
    'Asia/Singapore' => 'SGT (UTC+8)',
    'Asia/Kuala_Lumpur' => 'MYT (UTC+8)',
    'Asia/Bangkok' => 'ICT (UTC+7)',
    'Asia/Manila' => 'PHT (UTC+8)',
    'Asia/Ho_Chi_Minh' => 'ICT (UTC+7)',
    'Asia/Tokyo' => 'JST (UTC+9)',
    'Asia/Seoul' => 'KST (UTC+9)',
    'Asia/Shanghai' => 'CST (UTC+8)',
    'Asia/Hong_Kong' => 'HKT (UTC+8)',
    'Asia/Kolkata' => 'IST (UTC+5:30)',
    'Asia/Dubai' => 'GST (UTC+4)',
    'America/New_York' => 'EST (UTC-5)',
    'America/Chicago' => 'CST (UTC-6)',
    'America/Los_Angeles' => 'PST (UTC-8)',
    'Europe/London' => 'GMT (UTC+0)',
    'Europe/Paris' => 'CET (UTC+1)',
    'Europe/Moscow' => 'MSK (UTC+3)',
    'Australia/Sydney' => 'AEDT (UTC+11)',
    'Pacific/Auckland' => 'NZDT (UTC+13)'
  ];
  return $tzMap[$tzString] ?? $tzString;
}

// ========================================
// 1. BASIC STATS
// ========================================

// Total visits today
$resTotal = $conn->query("
    SELECT COUNT(*) as cnt 
    FROM visitors 
    WHERE DATE(visited_at)='$filterDate'
");
$totalVisits = $resTotal->fetch_assoc()['cnt'] ?? 0;

// Unique visitors
$resUnique = $conn->query("
    SELECT COUNT(DISTINCT ip_address) as cnt 
    FROM visitors 
    WHERE DATE(visited_at)='$filterDate'
");
$uniqueVisitors = $resUnique->fetch_assoc()['cnt'] ?? 0;

// Active visitors (last 10 minutes)
$resActive = $conn->query("
    SELECT COUNT(DISTINCT ip_address) as cnt 
    FROM visitors 
    WHERE visited_at >= NOW() - INTERVAL 10 MINUTE
");
$activeVisitors = $resActive->fetch_assoc()['cnt'] ?? 0;

// ========================================
// 2. WEEKLY CHART DATA (7 days)
// ========================================

$resWeekly = $conn->query("
    SELECT DATE(visited_at) as d, COUNT(*) as cnt 
    FROM visitors 
    WHERE visited_at >= NOW() - INTERVAL 7 DAY
    GROUP BY DATE(visited_at) 
    ORDER BY d ASC
");

$weeklyLabels = [];
$weeklyData = [];
while ($row = $resWeekly->fetch_assoc()) {
  $weeklyLabels[] = date('D, j M', strtotime($row['d']));
  $weeklyData[] = (int)$row['cnt'];
}

// ========================================
// 3. TOP 10 COUNTRIES (7 days)
// ========================================

$resCountry = $conn->query("
    SELECT country, COUNT(*) as cnt 
    FROM visitors 
    WHERE visited_at >= NOW() - INTERVAL 7 DAY 
      AND country IS NOT NULL
    GROUP BY country 
    ORDER BY cnt DESC 
    LIMIT 10
");

$countryLabels = [];
$countryData = [];
while ($row = $resCountry->fetch_assoc()) {
  $countryLabels[] = $row['country'];
  $countryData[] = (int)$row['cnt'];
}

// ========================================
// 4. TOP 10 BROWSERS/DEVICES (7 days)
// ========================================

$resBrowser = $conn->query("
    SELECT device, COUNT(*) as cnt 
    FROM visitors 
    WHERE visited_at >= NOW() - INTERVAL 7 DAY
    GROUP BY device 
    ORDER BY cnt DESC 
    LIMIT 10
");

$browsers = [];
while ($row = $resBrowser->fetch_assoc()) {
  $browsers[] = [
    "device" => $row['device'],
    "total" => (int)$row['cnt']
  ];
}

// ========================================
// 5. LOCATION TABLE - WITH GPS DETAILS
// ========================================

$resLocations = $conn->query("
    SELECT 
        street_address,
        city, 
        region, 
        country, 
        country_code, 
        postal_code,
        full_address,
        timezone, 
        COUNT(*) as total,
        MAX(latitude) as lat,
        MAX(longitude) as lon,
        MAX(location_accuracy) as accuracy
    FROM visitors
    WHERE DATE(visited_at)='$filterDate'
      AND country IS NOT NULL
    GROUP BY street_address, city, region, country, country_code, postal_code, full_address, timezone
    ORDER BY total DESC
    LIMIT 50
");

$locations = [];
while ($row = $resLocations->fetch_assoc()) {
  $hasDetailedAddress = !empty($row['street_address']) || !empty($row['postal_code']);

  $locations[] = [
    "street_address" => $row['street_address'] ?? '',
    "city" => $row['city'] ?? 'Unknown',
    "region" => $row['region'] ?? 'Unknown',
    "country" => $row['country'] ?? 'Unknown',
    "country_code" => $row['country_code'] ?? 'XX',
    "postal_code" => $row['postal_code'] ?? '',
    "full_address" => $row['full_address'] ?? '',
    "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
    "total" => (int)$row['total'],
    "has_gps" => $hasDetailedAddress,
    "latitude" => $row['lat'] ?? null,
    "longitude" => $row['lon'] ?? null,
    "accuracy" => $row['accuracy'] ?? 0
  ];
}

// ========================================
// 6. VISITOR FREQUENCY (7 days) - WITH GPS
// ========================================

$resVisitorFreq = $conn->query("
    SELECT 
        ip_address, 
        device, 
        street_address,
        city, 
        region, 
        country, 
        postal_code,
        timezone, 
        DATE(visited_at) as d, 
        COUNT(*) as visits,
        MAX(latitude) as lat,
        MAX(longitude) as lon
    FROM visitors
    WHERE visited_at >= NOW() - INTERVAL 7 DAY
    GROUP BY ip_address, device, street_address, city, region, country, postal_code, timezone, DATE(visited_at)
    ORDER BY d DESC, visits DESC
    LIMIT 100
");

$visitorFrequency = [];
while ($row = $resVisitorFreq->fetch_assoc()) {
  $hasGPS = !empty($row['street_address']) || !empty($row['lat']);

  $locationText = ($row['city'] ?? 'Unknown') . ', ' . ($row['country'] ?? 'Unknown');
  if ($hasGPS && !empty($row['street_address'])) {
    $locationText = $row['street_address'] . ', ' . $locationText;
  }

  $visitorFrequency[] = [
    "ip" => $row['ip_address'],
    "device" => $row['device'],
    "location" => $locationText,
    "street" => $row['street_address'] ?? '',
    "city" => $row['city'] ?? 'Unknown',
    "region" => $row['region'] ?? 'Unknown',
    "country" => $row['country'] ?? 'Unknown',
    "postal_code" => $row['postal_code'] ?? '',
    "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
    "date" => $row['d'],
    "visits" => (int)$row['visits'],
    "has_gps" => $hasGPS
  ];
}

// ========================================
// 7. ACTIVITY LOG - WITH GPS DETAILS
// ========================================

$resActivity = $conn->query("
    SELECT 
        visited_at, 
        ip_address, 
        device, 
        street_address,
        city, 
        country,
        postal_code,
        full_address,
        timezone,
        latitude,
        longitude
    FROM visitors 
    WHERE DATE(visited_at)='$filterDate' 
    ORDER BY visited_at DESC 
    LIMIT 50
");

$activity = [];
while ($row = $resActivity->fetch_assoc()) {
  // Convert to requested timezone
  $dt = new DateTime($row['visited_at']);
  $dt->setTimezone($tz);

  $hasGPS = !empty($row['street_address']) || !empty($row['latitude']);

  $locationText = $row['city'] . ', ' . $row['country'];
  if ($hasGPS && !empty($row['street_address'])) {
    $locationText = $row['street_address'] . ', ' . $locationText;
  }

  $activity[] = [
    "time"     => $dt->format('H:i:s'),
    "datetime" => $dt->format('Y-m-d H:i:s'),
    "ip"       => $row['ip_address'],
    "device"   => $row['device'],
    "location" => $locationText,
    "street"   => $row['street_address'] ?? '',
    "city"     => $row['city'] ?? 'Unknown',
    "country"  => $row['country'] ?? 'Unknown',
    "postal_code" => $row['postal_code'] ?? '',
    "full_address" => $row['full_address'] ?? '',
    "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
    "has_gps"  => $hasGPS,
    "latitude" => $row['latitude'] ?? null,
    "longitude" => $row['longitude'] ?? null
  ];
}

// ========================================
// 8. PREPARE JSON RESPONSE
// ========================================

$response = [
  "success" => true,
  "today" => $totalVisits,
  "unique" => $uniqueVisitors,
  "active" => $activeVisitors,
  "display_timezone" => getTimezoneName($requestedTz),
  "labels" => $weeklyLabels,
  "weekly" => $weeklyData,
  "country_labels" => $countryLabels,
  "country_data" => $countryData,
  "browsers" => $browsers,
  "locations" => $locations,
  "frequency" => $visitorFrequency,
  "activity" => $activity,
  "filter_date" => $filterDate,
  "requested_timezone" => $requestedTz
];

// ==================== TAMBAH KUNJUNGAN BARU (UPDATE SECTION) ====================
if (isset($_GET['add']) && $_GET['add'] == "1") {
  try {
    // Check if GPS coordinates provided
    $hasGPS = isset($_GET['latitude']) && isset($_GET['longitude']);

    if ($hasGPS) {
      // User provided GPS coordinates - use detailed location
      $latitude = floatval($_GET['latitude']);
      $longitude = floatval($_GET['longitude']);
      $street = isset($_GET['street']) ? $conn->real_escape_string($_GET['street']) : '';
      $houseNumber = isset($_GET['house_number']) ? $conn->real_escape_string($_GET['house_number']) : '';
      $city = isset($_GET['city']) ? $conn->real_escape_string($_GET['city']) : 'Unknown';
      $region = isset($_GET['region']) ? $conn->real_escape_string($_GET['region']) : '';
      $country = isset($_GET['country']) ? $conn->real_escape_string($_GET['country']) : 'Unknown';
      $countryCode = isset($_GET['country_code']) ? $conn->real_escape_string($_GET['country_code']) : 'XX';
      $postalCode = isset($_GET['postal_code']) ? $conn->real_escape_string($_GET['postal_code']) : '';
      $fullAddress = isset($_GET['full_address']) ? $conn->real_escape_string($_GET['full_address']) : '';
      $accuracy = isset($_GET['accuracy']) ? floatval($_GET['accuracy']) : 0;

      // Build street address
      $streetAddress = trim($houseNumber . ' ' . $street);

      $location = [
        'city' => $city,
        'region' => $region,
        'country' => $country,
        'country_code' => $countryCode,
        'timezone' => 'Asia/Jakarta' // Default, bisa disesuaikan
      ];

      error_log("📍 GPS Location: lat=$latitude, lon=$longitude, address=$streetAddress, $city");
    } else {
      // No GPS - fallback to IP-based location
      $location = getLocationFromIP($ip);
      $latitude = null;
      $longitude = null;
      $streetAddress = null;
      $city = $location['city'];
      $region = $location['region'];
      $country = $location['country'];
      $countryCode = $location['country_code'];
      $postalCode = '';
      $fullAddress = '';
      $accuracy = 0;

      error_log("📍 IP Location: $city, $country");
    }

    // Hitung waktu berdasarkan timezone pengunjung
    $timezone = new DateTimeZone($location['timezone']);
    $datetime = new DateTime('now', $timezone);
    $localTime = $datetime->format('Y-m-d H:i:s');

    // Prepare statement dengan semua field location detail
    $stmt = $conn->prepare("
            INSERT INTO visitors 
            (ip_address, device, latitude, longitude, street_address, city, region, country, country_code, 
             postal_code, full_address, location_accuracy, timezone, visited_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

    if (!$stmt) {
      error_log("Prepare failed: " . $conn->error);
      echo json_encode(["error" => "Database prepare failed"]);
      exit;
    }

    $stmt->bind_param(
      "ssddsssssssdss",
      $ip,
      $device,
      $latitude,
      $longitude,
      $streetAddress,
      $city,
      $region,
      $country,
      $countryCode,
      $postalCode,
      $fullAddress,
      $accuracy,
      $location['timezone'],
      $localTime
    );

    if (!$stmt->execute()) {
      error_log("Execute failed: " . $stmt->error);
      echo json_encode(["error" => "Insert failed"]);
      exit;
    }

    $stmt->close();

    $responseData = [
      "success" => true,
      "message" => "Visitor tracked",
      "ip" => $ip,
      "device" => $device,
      "location" => $location,
      "local_time" => $localTime,
      "has_gps" => $hasGPS
    ];

    if ($hasGPS) {
      $responseData["gps"] = [
        "latitude" => $latitude,
        "longitude" => $longitude,
        "street" => $streetAddress,
        "city" => $city,
        "accuracy" => $accuracy
      ];
    }

    error_log("✅ Visitor tracked: IP=$ip, Device=$device, GPS=" . ($hasGPS ? 'Yes' : 'No'));

    echo json_encode($responseData);
    exit;
  } catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode(["error" => $e->getMessage()]);
    exit;
  }
}

$conn->close();

echo json_encode($response, JSON_PRETTY_PRINT);
?>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>📊 Dashboard Recap Visitor</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- ========================================
         CUSTOM CSS SECTION
         ======================================== -->
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
  <!-- ========================================
         HEADER SECTION
         ======================================== -->
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

  <!-- ========================================
         MAIN CONTENT
         ======================================== -->
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
          <optgroup label="Asia Timur">
            <option value="Asia/Tokyo">Japan (UTC+9)</option>
            <option value="Asia/Seoul">Korea (UTC+9)</option>
            <option value="Asia/Shanghai">China (UTC+8)</option>
            <option value="Asia/Hong_Kong">Hong Kong (UTC+8)</option>
          </optgroup>
          <optgroup label="Asia Selatan">
            <option value="Asia/Kolkata">India (UTC+5:30)</option>
            <option value="Asia/Dubai">UAE (UTC+4)</option>
          </optgroup>
          <optgroup label="Amerika">
            <option value="America/New_York">EST (UTC-5)</option>
            <option value="America/Chicago">CST (UTC-6)</option>
            <option value="America/Los_Angeles">PST (UTC-8)</option>
          </optgroup>
          <optgroup label="Eropa">
            <option value="Europe/London">GMT (UTC+0)</option>
            <option value="Europe/Paris">CET (UTC+1)</option>
            <option value="Europe/Moscow">Moscow (UTC+3)</option>
          </optgroup>
          <optgroup label="Oceania">
            <option value="Australia/Sydney">Sydney (UTC+11)</option>
            <option value="Pacific/Auckland">New Zealand (UTC+13)</option>
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

      <!-- Legend -->
      <div class="mb-4 flex flex-wrap gap-3 text-sm">
        <div class="flex items-center bg-green-50 px-3 py-2 rounded-lg border border-green-200">
          <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-green-700 font-semibold">GPS Aktif</span>
          <span class="text-green-600 ml-2">- Alamat Detail</span>
        </div>
        <div class="flex items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
          <svg class="w-4 h-4 text-gray-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path>
          </svg>
          <span class="text-gray-700 font-semibold">IP Only</span>
          <span class="text-gray-600 ml-2">- Kota & Negara</span>
        </div>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Negara</th>
              <th>Alamat Detail</th>
              <th>Kota/Region</th>
              <th>Timezone</th>
              <th>Total Kunjungan</th>
            </tr>
          </thead>
          <tbody id="locationTable">
            <tr>
              <td colspan="5" class="text-center text-gray-500">Memuat data...</td>
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

  <!-- ========================================
         JAVASCRIPT SECTION
         ======================================== -->
  <script>
    let weeklyChartInstance = null;
    let countryChartInstance = null;
    let browserChartInstance = null;

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
      'FR': '🇫🇷',
      'NL': '🇳🇱',
      'BR': '🇧🇷',
      'IT': '🇮🇹',
      'ES': '🇪🇸',
      'CA': '🇨🇦'
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
        let url = `../track.php?tz=${encodeURIComponent(timezone)}`;
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
            legend: {
              position: 'right'
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

    // ==================== UPDATE FUNCTION updateLocationTable() ====================

    function updateLocationTable(locations) {
      const tbody = document.getElementById("locationTable");
      tbody.innerHTML = "";

      if (locations && locations.length > 0) {
        locations.forEach((row) => {
          const tr = document.createElement("tr");
          const flag = getCountryFlag(row.country_code);

          // Tentukan icon GPS
          const gpsIcon = row.has_gps ?
            '<span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold ml-2"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>GPS</span>' :
            '<span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs ml-2"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path></svg>IP</span>';

          // Format alamat
          let addressHtml = '';
          if (row.has_gps && row.street_address) {
            addressHtml = `
          <div class="space-y-1">
            <div class="flex items-start">
              <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <div>
                <p class="font-semibold text-gray-800">${row.street_address}</p>
                ${row.postal_code ? `<p class="text-xs text-gray-500">📮 ${row.postal_code}</p>` : ''}
              </div>
            </div>
          </div>
        `;
          } else {
            addressHtml = `<p class="text-sm text-gray-500 italic">Alamat tidak tersedia (IP only)</p>`;
          }

          tr.innerHTML = `
        <td>
          <div class="flex items-center">
            <span class="country-flag text-2xl">${flag}</span>
            <div class="ml-2">
              <strong class="text-gray-800">${row.country}</strong>
              ${gpsIcon}
            </div>
          </div>
        </td>
        <td>
          ${addressHtml}
        </td>
        <td>${row.city}, ${row.region}</td>
        <td><code class="bg-gray-100 px-2 py-1 rounded text-xs">${row.timezone}</code></td>
        <td>${getVisitBadge(row.total)}</td>
      `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-500">Tidak ada data</td></tr>';
      }
    }

    // ==================== UPDATE FUNCTION updateFrequencyTable() ====================
    // Ganti function ini untuk menampilkan detail alamat:

    function updateFrequencyTable(frequency) {
      const tbody = document.getElementById("freqTable");
      tbody.innerHTML = "";

      if (frequency && frequency.length > 0) {
        frequency.forEach((row) => {
          const tr = document.createElement("tr");
          const flag = getCountryFlag(row.country.substring(0, 2));

          const gpsIcon = row.has_gps ?
            '<span class="text-green-600 ml-1">📍</span>' :
            '<span class="text-gray-500 ml-1">🌐</span>';

          let locationDisplay = row.location;
          if (row.has_gps && row.postal_code) {
            locationDisplay += ` (${row.postal_code})`;
          }

          tr.innerHTML = `
        <td>${row.date}</td>
        <td><code class="bg-gray-100 px-2 py-1 rounded">${row.ip}</code></td>
        <td>
          <span class="country-flag">${flag}</span>
          <span class="text-sm">${locationDisplay}</span>
          ${gpsIcon}
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

    // ==================== UPDATE FUNCTION updateActivityTable() ====================
    // Ganti function ini untuk menampilkan detail alamat di activity log:

    function updateActivityTable(activity) {
      const tbody = document.getElementById("activityTable");
      tbody.innerHTML = "";

      if (activity && activity.length > 0) {
        activity.forEach((row) => {
          const tr = document.createElement("tr");
          const flag = getCountryFlag(row.country.substring(0, 2));

          // GPS indicator
          const gpsIcon = row.has_gps ?
            '<span class="text-xs text-green-600 font-semibold ml-1">📍</span>' :
            '<span class="text-xs text-gray-500 ml-1">🌐</span>';

          // Format location display
          let locationDisplay = '';
          if (row.has_gps && row.street) {
            locationDisplay = `
          <div class="text-sm">
            <p class="font-semibold">${row.street}</p>
            <p class="text-xs text-gray-500">${row.city}, ${row.country}</p>
          </div>
        `;
          } else {
            locationDisplay = `<span class="text-sm">${row.city}, ${row.country}</span>`;
          }

          tr.innerHTML = `
        <td><strong>${row.time}</strong></td>
        <td><code class="bg-gray-100 px-2 py-1 rounded">${row.ip}</code></td>
        <td>
          <div class="flex items-start">
            <span class="country-flag">${flag}</span>
            <div class="ml-2">
              ${locationDisplay}
              ${gpsIcon}
            </div>
          </div>
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