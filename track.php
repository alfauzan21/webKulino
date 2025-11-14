<?php
header("Content-Type: application/json");

$servername = "sql110.infinityfree.com";
$username   = "if0_39897518";
$password   = "kulinogame123";
$dbname     = "if0_39897518_db_project_kulino1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
}

$ip     = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$ua     = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$today  = date("Y-m-d");

// Fungsi deteksi device/browser sederhana
function detectDevice($ua) {
    $device = "Unknown Device";

    if (preg_match('/windows/i', $ua)) $device = "Windows PC";
    elseif (preg_match('/android/i', $ua)) $device = "Android";
    elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $device = "iOS";
    elseif (preg_match('/macintosh|mac os x/i', $ua)) $device = "MacOS";
    elseif (preg_match('/linux/i', $ua)) $device = "Linux";

    if (preg_match('/chrome/i', $ua)) $browser = "Chrome";
    elseif (preg_match('/firefox/i', $ua)) $browser = "Firefox";
    elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) $browser = "Safari";
    elseif (preg_match('/edg/i', $ua)) $browser = "Edge";
    else $browser = "Browser";

    return $device . " - " . $browser;
}

$device = detectDevice($ua);

// tambah kunjungan baru jika ?add=1
if (isset($_GET['add']) && $_GET['add'] == "1") {
    $stmt = $conn->prepare("INSERT INTO visitors (ip_address, device, visited_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $ip, $device);
    if(!$stmt->execute()){
        error_log("Insert gagal: " . $stmt->error);
    }
    $stmt->close();
}


// filter tanggal (default hari ini)
$filterDate = isset($_GET['date']) && $_GET['date'] != "" ? $_GET['date'] : $today;

// total visits hari ini
$resToday = $conn->query("SELECT COUNT(*) as total FROM visitors WHERE DATE(visited_at)='$filterDate'");
$totalVisits = $resToday->fetch_assoc()['total'];

// unique visitor hari ini
$resUnique = $conn->query("SELECT COUNT(DISTINCT CONCAT(ip_address, '-', device)) as total 
                           FROM visitors 
                           WHERE DATE(visited_at)='$filterDate'");
$totalUnique = $resUnique->fetch_assoc()['total'];

// pengunjung aktif (10 menit terakhir, hanya kalau hari ini)
$activeVisitor = 0;
if ($filterDate == $today) {
    $resActive = $conn->query("SELECT COUNT(DISTINCT ip_address) as active 
                               FROM visitors 
                               WHERE visited_at >= NOW() - INTERVAL 10 MINUTE");
    $activeVisitor = $resActive->fetch_assoc()['active'];
}

// data 7 hari terakhir (visits)
$resWeekly = $conn->query("SELECT DATE(visited_at) as d, COUNT(*) as total 
                           FROM visitors 
                           WHERE visited_at >= NOW() - INTERVAL 7 DAY 
                           GROUP BY DATE(visited_at) 
                           ORDER BY d ASC");
$weeklyData = [];
$weeklyLabels = [];
while($row = $resWeekly->fetch_assoc()) {
    $weeklyLabels[] = $row['d'];
    $weeklyData[] = $row['total'];
}

// Hitung frekuensi kunjungan tiap visitor (7 hari terakhir)
$resVisitorFreq = $conn->query("
    SELECT ip_address, device, DATE(visited_at) as d, COUNT(*) as visits
    FROM visitors
    WHERE visited_at >= NOW() - INTERVAL 7 DAY
    GROUP BY ip_address, device, DATE(visited_at)
    ORDER BY d DESC, visits DESC
");

$visitorFrequency = [];
while ($row = $resVisitorFreq->fetch_assoc()) {
    $visitorFrequency[] = [
        "ip" => $row['ip_address'],
        "device" => $row['device'],
        "date" => $row['d'],
        "visits" => $row['visits']
    ];
}
    

// log aktivitas
$resActivity = $conn->query("SELECT TIME(visited_at) as t, ip_address, device 
                             FROM visitors 
                             WHERE DATE(visited_at)='$filterDate' 
                             ORDER BY visited_at DESC 
                             LIMIT 50");
$activity = [];
while($row = $resActivity->fetch_assoc()) {
    $activity[] = [
        "time"   => $row['t'],
        "ip"     => $row['ip_address'],
        "device" => $row['device']
    ];
}

// hasil JSON
echo json_encode([
    "today"     => $totalVisits,   // total visit hari ini
    "unique"    => $totalUnique,   // unique visitor hari ini
    "active"    => $activeVisitor, // visitor aktif 10 menit terakhir
    "weekly"    => $weeklyData,
    "labels"    => $weeklyLabels,
    "activity"  => $activity,
    "frequency" => $visitorFrequency
]);



$conn->close();
