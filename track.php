<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

// Database Configuration
$servername = "localhost";
$username   = "u433539037_KulinoGame1";
$password   = "admin.Hostinger01";
$dbname     = "u433539037_db_kulino1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(["error" => "Koneksi database gagal"]));
}

$conn->set_charset("utf8mb4");

// ==================== HELPER FUNCTIONS ====================

// Get Real IP Address
function getRealIP() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    return trim($ip);
}

// Enhanced Browser & Device Detection
function detectDeviceAndBrowser($ua) {
    $device = "Unknown Device";
    $browser = "Unknown Browser";

    // Deteksi Browser (urutan penting!)
    if (preg_match('/OPR\/|Opera/i', $ua)) {
        $browser = "Opera";
    } elseif (preg_match('/Edg/i', $ua)) {
        $browser = "Microsoft Edge";
    } elseif (preg_match('/UCBrowser|UCWEB/i', $ua)) {
        $browser = "UC Browser";
    } elseif (preg_match('/Firefox/i', $ua)) {
        $browser = "Mozilla Firefox";
    } elseif (preg_match('/Chrome/i', $ua)) {
        $browser = "Google Chrome";
    } elseif (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) {
        $browser = "Safari";
    } elseif (preg_match('/MSIE|Trident/i', $ua)) {
        $browser = "Internet Explorer";
    } elseif (preg_match('/Brave/i', $ua)) {
        $browser = "Brave";
    } elseif (preg_match('/Vivaldi/i', $ua)) {
        $browser = "Vivaldi";
    } elseif (preg_match('/SamsungBrowser/i', $ua)) {
        $browser = "Samsung Internet";
    }

    // Deteksi Device/OS
    if (preg_match('/Windows NT 10/i', $ua)) {
        $device = "Windows 10";
    } elseif (preg_match('/Windows NT 11/i', $ua)) {
        $device = "Windows 11";
    } elseif (preg_match('/Windows/i', $ua)) {
        $device = "Windows PC";
    } elseif (preg_match('/Android/i', $ua)) {
        // Deteksi brand Android
        if (preg_match('/Samsung/i', $ua)) {
            $device = "Samsung Android";
        } elseif (preg_match('/Xiaomi|Mi /i', $ua)) {
            $device = "Xiaomi Android";
        } elseif (preg_match('/OPPO/i', $ua)) {
            $device = "OPPO Android";
        } elseif (preg_match('/Vivo/i', $ua)) {
            $device = "Vivo Android";
        } elseif (preg_match('/Huawei/i', $ua)) {
            $device = "Huawei Android";
        } else {
            $device = "Android";
        }
    } elseif (preg_match('/iPhone/i', $ua)) {
        $device = "iPhone";
    } elseif (preg_match('/iPad/i', $ua)) {
        $device = "iPad";
    } elseif (preg_match('/iPod/i', $ua)) {
        $device = "iPod";
    } elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) {
        $device = "MacOS";
    } elseif (preg_match('/Linux/i', $ua)) {
        $device = "Linux";
    }

    return $device . " - " . $browser;
}

// Get Location from IP with Timezone
function getLocationFromIP($ip) {
    // Skip untuk localhost
    if ($ip == '127.0.0.1' || $ip == '::1' || strpos($ip, '192.168.') === 0) {
        return [
            'city' => 'Localhost',
            'region' => 'Local',
            'country' => 'Local',
            'country_code' => 'LC',
            'timezone' => 'Asia/Jakarta', // Default WIB
            'offset' => '+07:00'
        ];
    }
    
    try {
        // Gunakan ip-api.com dengan timezone field
        $url = "http://ip-api.com/json/{$ip}?fields=status,country,countryCode,region,city,timezone,offset";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception("API request failed");
        }
        
        $data = json_decode($response, true);
        
        if ($data && $data['status'] === 'success') {
            return [
                'city' => $data['city'] ?? 'Unknown',
                'region' => $data['region'] ?? 'Unknown',
                'country' => $data['country'] ?? 'Unknown',
                'country_code' => $data['countryCode'] ?? 'XX',
                'timezone' => $data['timezone'] ?? 'Asia/Jakarta',
                'offset' => $data['offset'] ?? '+07:00'
            ];
        }
    } catch (Exception $e) {
        error_log("Location API error: " . $e->getMessage());
    }
    
    // Default jika gagal (WIB timezone)
    return [
        'city' => 'Unknown',
        'region' => 'Unknown',
        'country' => 'Unknown',
        'country_code' => 'XX',
        'timezone' => 'Asia/Jakarta',
        'offset' => '+07:00'
    ];
}

// Get Timezone Name for Display
function getTimezoneName($timezone) {
    $timezoneNames = [
        'Asia/Jakarta' => 'WIB (UTC+7)',
        'Asia/Makassar' => 'WITA (UTC+8)',
        'Asia/Jayapura' => 'WIT (UTC+9)',
        'Asia/Singapore' => 'SGT (UTC+8)',
        'Asia/Kuala_Lumpur' => 'MYT (UTC+8)',
        'Asia/Bangkok' => 'ICT (UTC+7)',
        'Asia/Manila' => 'PHT (UTC+8)',
        'Asia/Tokyo' => 'JST (UTC+9)',
        'Asia/Seoul' => 'KST (UTC+9)',
        'America/New_York' => 'EST (UTC-5)',
        'America/Los_Angeles' => 'PST (UTC-8)',
        'Europe/London' => 'GMT (UTC+0)',
        'Europe/Paris' => 'CET (UTC+1)',
        'Australia/Sydney' => 'AEDT (UTC+11)',
    ];
    
    return $timezoneNames[$timezone] ?? $timezone;
}

$ip     = getRealIP();
$ua     = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$today  = date("Y-m-d");

$device = detectDeviceAndBrowser($ua);

// ==================== TAMBAH KUNJUNGAN BARU ====================
if (isset($_GET['add']) && $_GET['add'] == "1") {
    try {
        // Dapatkan lokasi dengan timezone
        $location = getLocationFromIP($ip);
        
        // Hitung waktu berdasarkan timezone pengunjung
        $timezone = new DateTimeZone($location['timezone']);
        $datetime = new DateTime('now', $timezone);
        $localTime = $datetime->format('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("INSERT INTO visitors (ip_address, device, city, region, country, country_code, timezone, visited_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(["error" => "Database prepare failed"]);
            exit;
        }
        
        $stmt->bind_param("ssssssss", 
            $ip, 
            $device, 
            $location['city'], 
            $location['region'], 
            $location['country'],
            $location['country_code'],
            $location['timezone'],
            $localTime
        );
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            echo json_encode(["error" => "Insert failed"]);
            exit;
        }
        
        $stmt->close();
        
        error_log("Visitor tracked: IP=$ip, Device=$device, Location={$location['city']}, TZ={$location['timezone']}");
        
        echo json_encode([
            "success" => true,
            "message" => "Visitor tracked",
            "ip" => $ip,
            "device" => $device,
            "location" => $location,
            "local_time" => $localTime,
            "timezone_name" => getTimezoneName($location['timezone'])
        ]);
        exit;
        
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(["error" => $e->getMessage()]);
        exit;
    }
}

// ==================== GET STATISTICS ====================
try {
    // Timezone untuk display (default WIB)
    $displayTimezone = isset($_GET['tz']) ? $_GET['tz'] : 'Asia/Jakarta';
    $tz = new DateTimeZone($displayTimezone);
    
    // Filter tanggal
    $filterDate = isset($_GET['date']) && $_GET['date'] != "" ? $_GET['date'] : $today;
    $filterDate = $conn->real_escape_string($filterDate);

    // Total visits hari ini
    $resToday = $conn->query("SELECT COUNT(*) as total FROM visitors WHERE DATE(visited_at)='$filterDate'");
    if (!$resToday) {
        throw new Exception("Query failed: " . $conn->error);
    }
    $totalVisits = $resToday->fetch_assoc()['total'];

    // Unique visitor hari ini
    $resUnique = $conn->query("SELECT COUNT(DISTINCT CONCAT(ip_address, '-', device)) as total 
                               FROM visitors 
                               WHERE DATE(visited_at)='$filterDate'");
    $totalUnique = $resUnique->fetch_assoc()['total'];

    // Pengunjung aktif (10 menit terakhir)
    $activeVisitor = 0;
    if ($filterDate == $today) {
        $resActive = $conn->query("SELECT COUNT(DISTINCT ip_address) as active 
                                   FROM visitors 
                                   WHERE visited_at >= NOW() - INTERVAL 10 MINUTE");
        $activeVisitor = $resActive->fetch_assoc()['active'];
    }

    // Data 7 hari terakhir
    $resWeekly = $conn->query("SELECT DATE(visited_at) as d, COUNT(*) as total 
                               FROM visitors 
                               WHERE visited_at >= NOW() - INTERVAL 7 DAY 
                               GROUP BY DATE(visited_at) 
                               ORDER BY d ASC");
    $weeklyData = [];
    $weeklyLabels = [];
    while ($row = $resWeekly->fetch_assoc()) {
        $weeklyLabels[] = $row['d'];
        $weeklyData[] = (int)$row['total'];
    }

    // Statistik Negara
    $resCountry = $conn->query("
        SELECT country, country_code, COUNT(*) as total 
        FROM visitors 
        WHERE visited_at >= NOW() - INTERVAL 7 DAY 
          AND country IS NOT NULL 
          AND country != ''
        GROUP BY country, country_code 
        ORDER BY total DESC 
        LIMIT 10
    ");
    
    $countryData = [];
    $countryLabels = [];
    while ($row = $resCountry->fetch_assoc()) {
        $countryLabels[] = $row['country'];
        $countryData[] = (int)$row['total'];
    }

    // Frekuensi kunjungan per visitor (dengan timezone)
    $resVisitorFreq = $conn->query("
        SELECT ip_address, device, city, region, country, timezone, DATE(visited_at) as d, COUNT(*) as visits
        FROM visitors
        WHERE visited_at >= NOW() - INTERVAL 7 DAY
        GROUP BY ip_address, device, city, region, country, timezone, DATE(visited_at)
        ORDER BY d DESC, visits DESC
        LIMIT 100
    ");

    $visitorFrequency = [];
    while ($row = $resVisitorFreq->fetch_assoc()) {
        $visitorFrequency[] = [
            "ip" => $row['ip_address'],
            "device" => $row['device'],
            "city" => $row['city'] ?? 'Unknown',
            "region" => $row['region'] ?? 'Unknown',
            "country" => $row['country'] ?? 'Unknown',
            "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
            "date" => $row['d'],
            "visits" => (int)$row['visits']
        ];
    }

    // Log aktivitas dengan timezone conversion
    $resActivity = $conn->query("
        SELECT visited_at, ip_address, device, city, country, timezone
        FROM visitors 
        WHERE DATE(visited_at)='$filterDate' 
        ORDER BY visited_at DESC 
        LIMIT 50
    ");
    
    $activity = [];
    while ($row = $resActivity->fetch_assoc()) {
        // Convert ke timezone yang diminta
        $dt = new DateTime($row['visited_at']);
        $dt->setTimezone($tz);
        
        $activity[] = [
            "time"     => $dt->format('H:i:s'),
            "datetime" => $dt->format('Y-m-d H:i:s'),
            "ip"       => $row['ip_address'],
            "device"   => $row['device'],
            "city"     => $row['city'] ?? 'Unknown',
            "country"  => $row['country'] ?? 'Unknown',
            "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta')
        ];
    }

    // Detail lokasi untuk tabel
    $resLocations = $conn->query("
        SELECT city, region, country, country_code, timezone, COUNT(*) as total
        FROM visitors
        WHERE DATE(visited_at)='$filterDate'
          AND country IS NOT NULL
        GROUP BY city, region, country, country_code, timezone
        ORDER BY total DESC
        LIMIT 50
    ");
    
    $locations = [];
    while ($row = $resLocations->fetch_assoc()) {
        $locations[] = [
            "city" => $row['city'] ?? 'Unknown',
            "region" => $row['region'] ?? 'Unknown',
            "country" => $row['country'] ?? 'Unknown',
            "country_code" => $row['country_code'] ?? 'XX',
            "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
            "total" => (int)$row['total']
        ];
    }

    // Browser Statistics
    $resBrowser = $conn->query("
        SELECT device, COUNT(*) as total
        FROM visitors
        WHERE visited_at >= NOW() - INTERVAL 7 DAY
        GROUP BY device
        ORDER BY total DESC
        LIMIT 10
    ");
    
    $browserData = [];
    while ($row = $resBrowser->fetch_assoc()) {
        $browserData[] = [
            "device" => $row['device'],
            "total" => (int)$row['total']
        ];
    }

    // Return JSON response
    echo json_encode([
        "success"         => true,
        "today"           => (int)$totalVisits,
        "unique"          => (int)$totalUnique,
        "active"          => (int)$activeVisitor,
        "weekly"          => $weeklyData,
        "labels"          => $weeklyLabels,
        "country_data"    => $countryData,
        "country_labels"  => $countryLabels,
        "activity"        => $activity,
        "frequency"       => $visitorFrequency,
        "locations"       => $locations,
        "browsers"        => $browserData,
        "display_timezone" => getTimezoneName($displayTimezone)
    ]);

} catch (Exception $e) {
    error_log("Statistics error: " . $e->getMessage());
    echo json_encode([
        "error" => $e->getMessage(),
        "today" => 0,
        "unique" => 0,
        "active" => 0,
        "weekly" => [],
        "labels" => [],
        "country_data" => [],
        "country_labels" => [],
        "activity" => [],
        "frequency" => [],
        "locations" => [],
        "browsers" => []
    ]);
}

$conn->close();
?>