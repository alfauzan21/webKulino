<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

// Gunakan koneksi database yang sama dengan aplikasi
$servername = "localhost";
$username   = "u433539037_KulinoGame1";
$password   = "admin.Hostinger01";
$dbname     = "u433539037_db_kulino1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(["error" => "Koneksi database gagal"]));
}

// Set charset untuk menghindari masalah encoding
$conn->set_charset("utf8mb4");

// Dapatkan IP address dengan lebih akurat
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

$ip     = getRealIP();
$ua     = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$today  = date("Y-m-d");

// Fungsi deteksi device/browser
function detectDevice($ua) {
    $device = "Unknown Device";
    $browser = "Browser";

    // Deteksi OS/Device
    if (preg_match('/windows/i', $ua)) {
        $device = "Windows PC";
    } elseif (preg_match('/android/i', $ua)) {
        $device = "Android";
    } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
        $device = "iOS";
    } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
        $device = "MacOS";
    } elseif (preg_match('/linux/i', $ua)) {
        $device = "Linux";
    }

    // Deteksi Browser
    if (preg_match('/edg/i', $ua)) {
        $browser = "Edge";
    } elseif (preg_match('/chrome/i', $ua)) {
        $browser = "Chrome";
    } elseif (preg_match('/firefox/i', $ua)) {
        $browser = "Firefox";
    } elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) {
        $browser = "Safari";
    } elseif (preg_match('/opera|opr/i', $ua)) {
        $browser = "Opera";
    }

    return $device . " - " . $browser;
}

$device = detectDevice($ua);

// ====== TAMBAH KUNJUNGAN BARU ======
if (isset($_GET['add']) && $_GET['add'] == "1") {
    try {
        $stmt = $conn->prepare("INSERT INTO visitors (ip_address, device, visited_at) VALUES (?, ?, NOW())");
        
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(["error" => "Database prepare failed"]);
            exit;
        }
        
        $stmt->bind_param("ss", $ip, $device);
        
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            echo json_encode(["error" => "Insert failed"]);
            exit;
        }
        
        $stmt->close();
        
        // Log untuk debugging
        error_log("Visitor tracked: IP=$ip, Device=$device");
        
        // Return success response
        echo json_encode([
            "success" => true,
            "message" => "Visitor tracked",
            "ip" => $ip,
            "device" => $device
        ]);
        exit;
        
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(["error" => $e->getMessage()]);
        exit;
    }
}

// ====== GET STATISTICS ======
try {
    // Filter tanggal (default hari ini)
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

    // Frekuensi kunjungan per visitor
    $resVisitorFreq = $conn->query("
        SELECT ip_address, device, DATE(visited_at) as d, COUNT(*) as visits
        FROM visitors
        WHERE visited_at >= NOW() - INTERVAL 7 DAY
        GROUP BY ip_address, device, DATE(visited_at)
        ORDER BY d DESC, visits DESC
        LIMIT 100
    ");

    $visitorFrequency = [];
    while ($row = $resVisitorFreq->fetch_assoc()) {
        $visitorFrequency[] = [
            "ip" => $row['ip_address'],
            "device" => $row['device'],
            "date" => $row['d'],
            "visits" => (int)$row['visits']
        ];
    }

    // Log aktivitas
    $resActivity = $conn->query("SELECT TIME(visited_at) as t, ip_address, device 
                                 FROM visitors 
                                 WHERE DATE(visited_at)='$filterDate' 
                                 ORDER BY visited_at DESC 
                                 LIMIT 50");
    $activity = [];
    while ($row = $resActivity->fetch_assoc()) {
        $activity[] = [
            "time"   => $row['t'],
            "ip"     => $row['ip_address'],
            "device" => $row['device']
        ];
    }

    // Return JSON response
    echo json_encode([
        "success"   => true,
        "today"     => (int)$totalVisits,
        "unique"    => (int)$totalUnique,
        "active"    => (int)$activeVisitor,
        "weekly"    => $weeklyData,
        "labels"    => $weeklyLabels,
        "activity"  => $activity,
        "frequency" => $visitorFrequency
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
        "activity" => [],
        "frequency" => []
    ]);
}

$conn->close();
?>