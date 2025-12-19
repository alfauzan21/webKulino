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
    die(json_encode(["error" => "Database connection failed", "details" => $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

// ==================== HELPER FUNCTIONS ====================

function getRealIP()
{
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

function detectDeviceAndBrowser($ua)
{
    $device = "Unknown Device";
    $browser = "Unknown Browser";

    // Browser detection
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
    }

    // Device/OS detection
    if (preg_match('/Windows NT 10/i', $ua)) {
        $device = "Windows 10";
    } elseif (preg_match('/Windows NT 11/i', $ua)) {
        $device = "Windows 11";
    } elseif (preg_match('/Windows/i', $ua)) {
        $device = "Windows PC";
    } elseif (preg_match('/Android/i', $ua)) {
        if (preg_match('/Samsung/i', $ua)) {
            $device = "Samsung Android";
        } elseif (preg_match('/Xiaomi|Mi /i', $ua)) {
            $device = "Xiaomi Android";
        } elseif (preg_match('/OPPO/i', $ua)) {
            $device = "OPPO Android";
        } else {
            $device = "Android";
        }
    } elseif (preg_match('/iPhone/i', $ua)) {
        $device = "iPhone";
    } elseif (preg_match('/iPad/i', $ua)) {
        $device = "iPad";
    } elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) {
        $device = "MacOS";
    } elseif (preg_match('/Linux/i', $ua)) {
        $device = "Linux";
    }

    return $device . " - " . $browser;
}

// 🔧 NEW: Reverse geocoding dari GPS coordinates menggunakan Nominatim
function reverseGeocode($latitude, $longitude)
{
    try {
        // URL Nominatim dengan zoom level 18 untuk detail maksimal
        $url = "https://nominatim.openstreetmap.org/reverse?" . http_build_query([
            'format' => 'json',
            'lat' => $latitude,
            'lon' => $longitude,
            'zoom' => 18,
            'addressdetails' => 1,
            'extratags' => 1,
            'namedetails' => 1
        ]);

        $context = stream_context_create([
            'http' => [
                'timeout' => 8,
                'user_agent' => 'KulinoGameHub/1.0 (contact@kulinogame.com)',
                'ignore_errors' => true,
                'header' => [
                    'Accept: application/json',
                    'Accept-Language: id,en;q=0.9'
                ]
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new Exception("Reverse geocoding request failed");
        }

        $data = json_decode($response, true);

        if (!$data || isset($data['error'])) {
            throw new Exception("Invalid geocoding response: " . ($data['error'] ?? 'Unknown error'));
        }

        $address = $data['address'] ?? [];

        // Log untuk debugging
        error_log("Geocoding result for {$latitude},{$longitude}: " . json_encode($address));

        // Extract detailed address components
        $street = $address['road'] ??
            $address['pedestrian'] ??
            $address['footway'] ??
            $address['path'] ??
            $address['street'] ?? '';

        $houseNumber = $address['house_number'] ??
            $address['building'] ??
            $address['house_name'] ?? '';

        $district = $address['suburb'] ??
            $address['neighbourhood'] ??
            $address['quarter'] ??
            $address['district'] ?? '';

        $subdistrict = $address['village'] ??
            $address['hamlet'] ??
            $address['locality'] ??
            $address['town'] ?? '';

        $city = $address['city'] ??
            $address['municipality'] ??
            $address['town'] ??
            $address['county'] ??
            $address['state_district'] ?? '';

        $region = $address['state'] ??
            $address['province'] ??
            $address['region'] ?? '';

        $country = $address['country'] ?? '';

        $countryCode = isset($address['country_code']) ?
            strtoupper($address['country_code']) : '';

        $postalCode = $address['postcode'] ??
            $address['postal_code'] ?? '';

        // Determine timezone based on country code
        $timezone = getTimezoneFromCountry($countryCode);

        return [
            'success' => true,
            'street' => trim($street),
            'house_number' => trim($houseNumber),
            'district' => trim($district),
            'subdistrict' => trim($subdistrict),
            'city' => trim($city),
            'region' => trim($region),
            'country' => trim($country),
            'country_code' => $countryCode,
            'postal_code' => trim($postalCode),
            'full_address' => $data['display_name'] ?? '',
            'timezone' => $timezone,
            'raw_address' => $address // Keep for debugging
        ];
    } catch (Exception $e) {
        error_log("Reverse geocoding error: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// ==================== TIMEZONE HELPER FUNCTION ====================
function getTimezoneFromCountry($countryCode)
{
    $timezones = [
        'ID' => 'Asia/Jakarta',
        'SG' => 'Asia/Singapore',
        'MY' => 'Asia/Kuala_Lumpur',
        'TH' => 'Asia/Bangkok',
        'PH' => 'Asia/Manila',
        'VN' => 'Asia/Ho_Chi_Minh',
        'US' => 'America/New_York',
        'GB' => 'Europe/London',
        'AU' => 'Australia/Sydney',
        'JP' => 'Asia/Tokyo',
        'KR' => 'Asia/Seoul',
        'CN' => 'Asia/Shanghai',
        'IN' => 'Asia/Kolkata',
    ];

    return $timezones[$countryCode] ?? 'Asia/Jakarta';
}

// Fallback: Get location from IP
function getLocationFromIP($ip)
{
    if ($ip == '127.0.0.1' || $ip == '::1' || strpos($ip, '192.168.') === 0) {
        return [
            'city' => 'Localhost',
            'region' => 'Local',
            'country' => 'Local',
            'country_code' => 'LC',
            'timezone' => 'Asia/Jakarta',
        ];
    }

    try {
        $url = "http://ip-api.com/json/{$ip}?fields=status,country,countryCode,region,city,timezone";

        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'ignore_errors' => true
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new Exception("IP API request failed");
        }

        $data = json_decode($response, true);

        if ($data && $data['status'] === 'success') {
            return [
                'city' => $data['city'] ?? 'Unknown',
                'region' => $data['region'] ?? 'Unknown',
                'country' => $data['country'] ?? 'Unknown',
                'country_code' => $data['countryCode'] ?? 'XX',
                'timezone' => $data['timezone'] ?? 'Asia/Jakarta',
            ];
        }
    } catch (Exception $e) {
        error_log("IP Location API error: " . $e->getMessage());
    }

    return [
        'city' => 'Unknown',
        'region' => 'Unknown',
        'country' => 'Unknown',
        'country_code' => 'XX',
        'timezone' => 'Asia/Jakarta',
    ];
}

function getTimezoneName($timezone)
{
    $timezoneNames = [
        'Asia/Jakarta' => 'WIB (UTC+7)',
        'Asia/Makassar' => 'WITA (UTC+8)',
        'Asia/Jayapura' => 'WIT (UTC+9)',
        'Asia/Singapore' => 'SGT (UTC+8)',
        'Asia/Kuala_Lumpur' => 'MYT (UTC+8)',
        'Asia/Bangkok' => 'ICT (UTC+7)',
        'Asia/Manila' => 'PHT (UTC+8)',
    ];

    return $timezoneNames[$timezone] ?? $timezone;
}

$ip = getRealIP();
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$today = date("Y-m-d");
$device = detectDeviceAndBrowser($ua);

// ==================== ADD NEW VISITOR ====================
if (isset($_GET['add']) && $_GET['add'] == "1") {
    try {
        // Get GPS coordinates from parameters
        $latitude = isset($_GET['latitude']) && !empty($_GET['latitude']) ? floatval($_GET['latitude']) : null;
        $longitude = isset($_GET['longitude']) && !empty($_GET['longitude']) ? floatval($_GET['longitude']) : null;
        $accuracy = isset($_GET['accuracy']) && !empty($_GET['accuracy']) ? floatval($_GET['accuracy']) : null;

        $locationData = [];

        // 🔧 PRIORITY 1: Use GPS coordinates if available
        if ($latitude !== null && $longitude !== null) {
            error_log("📍 Using GPS coordinates: {$latitude}, {$longitude}");

            $geoResult = reverseGeocode($latitude, $longitude);

            if ($geoResult['success']) {
                $locationData = $geoResult;
                error_log("✅ Reverse geocoding successful");
            } else {
                error_log("⚠️ Reverse geocoding failed, using IP fallback");
                $locationData = getLocationFromIP($ip);
            }
        }
        // 🔧 FALLBACK: Use IP-based location
        else {
            error_log("⚠️ No GPS data, using IP geolocation");
            $locationData = getLocationFromIP($ip);
        }

        // Extract location data
        $street = $locationData['street'] ?? '';
        $house_number = $locationData['house_number'] ?? '';
        $district = $locationData['district'] ?? '';
        $subdistrict = $locationData['subdistrict'] ?? '';
        $city = $locationData['city'] ?? 'Unknown';
        $region = $locationData['region'] ?? '';
        $country = $locationData['country'] ?? 'Unknown';
        $country_code = $locationData['country_code'] ?? 'XX';
        $postal_code = $locationData['postal_code'] ?? '';
        $full_address = $locationData['full_address'] ?? '';
        $timezone = $locationData['timezone'] ?? 'Asia/Jakarta';

        // Set local time
        $tz = new DateTimeZone($timezone);
        $datetime = new DateTime('now', $tz);
        $localTime = $datetime->format('Y-m-d H:i:s');

        // Insert into database
        $stmt = $conn->prepare("
            INSERT INTO visitors 
            (ip_address, device, latitude, longitude, street, house_number, district, subdistrict, 
             city, region, country, country_code, postal_code, full_address, location_accuracy, timezone, visited_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "ssddssssssssssdss",
            $ip,
            $device,
            $latitude,
            $longitude,
            $street,
            $house_number,
            $district,
            $subdistrict,
            $city,
            $region,
            $country,
            $country_code,
            $postal_code,
            $full_address,
            $accuracy,
            $timezone,
            $localTime
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();

        error_log("✅ Visitor tracked successfully");

        echo json_encode([
            "success" => true,
            "message" => "Visitor tracked successfully",
            "ip" => $ip,
            "location" => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'street' => $street,
                'house_number' => $house_number,
                'city' => $city,
                'region' => $region,
                'country' => $country,
                'timezone' => $timezone
            ]
        ]);
        exit;
    } catch (Exception $e) {
        error_log("❌ Exception: " . $e->getMessage());
        echo json_encode([
            "error" => "Failed to track visitor",
            "details" => $e->getMessage()
        ]);
        exit;
    }
}

// ==================== GET STATISTICS ====================
try {
    $displayTimezone = isset($_GET['tz']) ? $_GET['tz'] : 'Asia/Jakarta';
    $tz = new DateTimeZone($displayTimezone);

    $filterDate = isset($_GET['date']) && $_GET['date'] != "" ? $_GET['date'] : $today;
    $filterDate = $conn->real_escape_string($filterDate);

    // Total visits today
    $resToday = $conn->query("SELECT COUNT(*) as total FROM visitors WHERE DATE(visited_at)='$filterDate'");
    $totalVisits = $resToday ? $resToday->fetch_assoc()['total'] : 0;

    // Unique visitors
    $resUnique = $conn->query("SELECT COUNT(DISTINCT CONCAT(ip_address, '-', device)) as total 
                               FROM visitors WHERE DATE(visited_at)='$filterDate'");
    $totalUnique = $resUnique ? $resUnique->fetch_assoc()['total'] : 0;

    // Active visitors (last 10 minutes)
    $activeVisitor = 0;
    if ($filterDate == $today) {
        $resActive = $conn->query("SELECT COUNT(DISTINCT ip_address) as active 
                                   FROM visitors WHERE visited_at >= NOW() - INTERVAL 10 MINUTE");
        $activeVisitor = $resActive ? $resActive->fetch_assoc()['active'] : 0;
    }

    // Weekly data (last 7 days)
    $resWeekly = $conn->query("SELECT DATE(visited_at) as d, COUNT(*) as total 
                               FROM visitors WHERE visited_at >= NOW() - INTERVAL 7 DAY 
                               GROUP BY DATE(visited_at) ORDER BY d ASC");
    $weeklyData = [];
    $weeklyLabels = [];
    if ($resWeekly) {
        while ($row = $resWeekly->fetch_assoc()) {
            $weeklyLabels[] = $row['d'];
            $weeklyData[] = (int)$row['total'];
        }
    }

    // Country stats
    $resCountry = $conn->query("
        SELECT country, country_code, COUNT(*) as total 
        FROM visitors WHERE visited_at >= NOW() - INTERVAL 7 DAY 
        AND country IS NOT NULL AND country != '' AND country != 'Unknown'
        GROUP BY country, country_code ORDER BY total DESC LIMIT 10
    ");
    $countryData = [];
    $countryLabels = [];
    if ($resCountry) {
        while ($row = $resCountry->fetch_assoc()) {
            $countryLabels[] = $row['country'];
            $countryData[] = (int)$row['total'];
        }
    }

    // Location details for today
    $resLocations = $conn->query("
        SELECT 
            street, 
            house_number, 
            district, 
            subdistrict,
            city, 
            region, 
            country, 
            country_code, 
            timezone, 
            postal_code, 
            latitude, 
            longitude,
            full_address,
            COUNT(*) as total
        FROM visitors
        WHERE DATE(visited_at)='$filterDate'
        AND city IS NOT NULL 
        AND city != '' 
        AND city != 'Unknown'
        GROUP BY 
            street, 
            house_number, 
            district, 
            subdistrict, 
            city, 
            region, 
            country, 
            country_code, 
            timezone, 
            postal_code,
            latitude, 
            longitude,
            full_address
        ORDER BY total DESC
        LIMIT 200
    ");

    $locations = [];
    if ($resLocations) {
        while ($row = $resLocations->fetch_assoc()) {
            // Build structured address
            $addressComponents = [];

            // Add house number and street
            if (!empty($row['house_number']) && !empty($row['street'])) {
                $addressComponents[] = $row['house_number'] . ' ' . $row['street'];
            } elseif (!empty($row['street'])) {
                $addressComponents[] = $row['street'];
            }

            // Add district/subdistrict
            if (!empty($row['subdistrict'])) {
                $addressComponents[] = $row['subdistrict'];
            }
            if (!empty($row['district'])) {
                $addressComponents[] = $row['district'];
            }

            // Add city and region
            if (!empty($row['city'])) {
                $addressComponents[] = $row['city'];
            }
            if (!empty($row['region'])) {
                $addressComponents[] = $row['region'];
            }

            $fullLocation = !empty($addressComponents) ?
                implode(', ', $addressComponents) : ($row['full_address'] ?? $row['city']);

            $locations[] = [
                "street" => $row['street'] ?? '',
                "house_number" => $row['house_number'] ?? '',
                "district" => $row['district'] ?? '',
                "subdistrict" => $row['subdistrict'] ?? '',
                "city" => $row['city'] ?? 'Unknown',
                "region" => $row['region'] ?? '',
                "country" => $row['country'] ?? 'Unknown',
                "country_code" => $row['country_code'] ?? 'XX',
                "postal_code" => $row['postal_code'] ?? '',
                "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
                "latitude" => $row['latitude'] ? floatval($row['latitude']) : null,
                "longitude" => $row['longitude'] ? floatval($row['longitude']) : null,
                "full_location" => $fullLocation,
                "full_address" => $row['full_address'] ?? '',
                "total" => (int)$row['total']
            ];
        }
    }

    // Frequency data (last 7 days)
    $resFreq = $conn->query("
        SELECT 
            ip_address, device, city, country, country_code, timezone, 
            DATE(visited_at) as d, COUNT(*) as visits
        FROM visitors
        WHERE visited_at >= NOW() - INTERVAL 7 DAY
        AND city IS NOT NULL AND city != '' AND city != 'Unknown'
        GROUP BY ip_address, device, city, country, country_code, timezone, DATE(visited_at)
        ORDER BY d DESC, visits DESC
        LIMIT 100
    ");

    $frequency = [];
    if ($resFreq) {
        while ($row = $resFreq->fetch_assoc()) {
            $frequency[] = [
                "ip" => $row['ip_address'],
                "device" => $row['device'],
                "city" => $row['city'] ?? 'Unknown',
                "country" => $row['country'] ?? 'Unknown',
                "country_code" => $row['country_code'] ?? 'XX',
                "full_location" => ($row['city'] ?? 'Unknown') . ', ' . ($row['country'] ?? 'Unknown'),
                "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
                "date" => $row['d'],
                "visits" => (int)$row['visits']
            ];
        }
    }

    // Activity log (today only)
    $resActivity = $conn->query("
    SELECT 
        visited_at, 
        ip_address, 
        device, 
        street, 
        house_number, 
        district,
        subdistrict,
        city, 
        region,
        country, 
        country_code, 
        postal_code,
        timezone,
        latitude,
        longitude,
        full_address
    FROM visitors 
    WHERE DATE(visited_at)='$filterDate'
    AND city IS NOT NULL 
    AND city != '' 
    AND city != 'Unknown'
    ORDER BY visited_at DESC
    LIMIT 200
");

    $activity = [];
    if ($resActivity) {
        while ($row = $resActivity->fetch_assoc()) {
            $dt = new DateTime($row['visited_at']);
            $dt->setTimezone($tz);

            // Build detailed address components
            $addressComponents = [];

            // Street address
            if (!empty($row['house_number']) && !empty($row['street'])) {
                $addressComponents['street_address'] = $row['house_number'] . ' ' . $row['street'];
            } elseif (!empty($row['street'])) {
                $addressComponents['street_address'] = $row['street'];
            }

            // Administrative divisions
            if (!empty($row['subdistrict'])) {
                $addressComponents['subdistrict'] = $row['subdistrict'];
            }
            if (!empty($row['district'])) {
                $addressComponents['district'] = $row['district'];
            }

            // City and region
            if (!empty($row['city'])) {
                $addressComponents['city'] = $row['city'];
            }
            if (!empty($row['region']) && $row['region'] !== $row['city']) {
                $addressComponents['region'] = $row['region'];
            }

            // Build short and full location strings
            $shortLocation = [];
            if (!empty($addressComponents['street_address'])) {
                $shortLocation[] = $addressComponents['street_address'];
            }
            if (!empty($row['city'])) {
                $shortLocation[] = $row['city'];
            }

            $activity[] = [
                "time" => $dt->format('H:i:s'),
                "full_datetime" => $dt->format('Y-m-d H:i:s'),
                "ip" => $row['ip_address'],
                "device" => $row['device'],
                "street" => $row['street'] ?? '',
                "house_number" => $row['house_number'] ?? '',
                "district" => $row['district'] ?? '',
                "subdistrict" => $row['subdistrict'] ?? '',
                "city" => $row['city'] ?? 'Unknown',
                "region" => $row['region'] ?? '',
                "country" => $row['country'] ?? 'Unknown',
                "country_code" => $row['country_code'] ?? 'XX',
                "postal_code" => $row['postal_code'] ?? '',
                "timezone" => getTimezoneName($row['timezone'] ?? 'Asia/Jakarta'),
                "latitude" => $row['latitude'] ? floatval($row['latitude']) : null,
                "longitude" => $row['longitude'] ? floatval($row['longitude']) : null,
                "short_location" => implode(', ', $shortLocation),
                "full_address" => $row['full_address'] ?? '',
                "address_components" => $addressComponents
            ];
        }
    }

    // Browser/Device stats
    $resBrowser = $conn->query("
        SELECT device, COUNT(*) as total
        FROM visitors
        WHERE visited_at >= NOW() - INTERVAL 7 DAY
        GROUP BY device
        ORDER BY total DESC
        LIMIT 10
    ");

    $browserData = [];
    if ($resBrowser) {
        while ($row = $resBrowser->fetch_assoc()) {
            $browserData[] = [
                "device" => $row['device'],
                "total" => (int)$row['total']
            ];
        }
    }

    // Final response
    echo json_encode([
        "success" => true,
        "today" => (int)$totalVisits,
        "unique" => (int)$totalUnique,
        "active" => (int)$activeVisitor,
        "weekly" => $weeklyData,
        "labels" => $weeklyLabels,
        "country_data" => $countryData,
        "country_labels" => $countryLabels,
        "locations" => $locations,
        "frequency" => $frequency,
        "activity" => $activity,
        "browsers" => $browserData,
        "display_timezone" => getTimezoneName($displayTimezone),
        "debug" => [
            "filter_date" => $filterDate,
            "total_locations" => count($locations),
            "has_gps_data" => !empty(array_filter($locations, function ($loc) {
                return $loc['latitude'] !== null;
            }))
        ]
    ]);
} catch (Exception $e) {
    error_log("Statistics error: " . $e->getMessage());
    echo json_encode([
        "error" => "Failed to fetch statistics",
        "details" => $e->getMessage(),
        "today" => 0,
        "unique" => 0,
        "active" => 0,
        "locations" => []
    ]);
}

$conn->close();
