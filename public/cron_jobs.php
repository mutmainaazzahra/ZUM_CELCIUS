<?php
require_once '../config/Env.php';
require_once '../config/Database.php';
require_once '../services/OpenWeatherClient.php';
require_once '../services/PushService.php';
require_once '../models/PushSubscription.php';
require_once '../models/Notification.php';

try {
    Env::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    die("Error Env: " . $e->getMessage());
}

$db = Database::getInstance()->getConnection();
$weatherClient = new OpenWeatherClient();
$pushService = new PushService();
$notifModel = new Notification();

date_default_timezone_set('Asia/Jakarta');

echo "<h1>🤖 System Automation Run (" . date('H:i:s') . ")</h1><pre>";

echo "\n--- [1] Cek Jadwal Aktivitas ---\n";

$now = new DateTime("now");
$currentTimeStr = $now->format('H:i:00');
$targetTime = clone $now;
$targetTime->modify('+6 minutes');
$targetTimeStr = $targetTime->format('H:i:59');

echo "Mencari aktivitas antara jam $currentTimeStr s.d $targetTimeStr hari ini (" . date('Y-m-d') . ")...\n";

$sql = "SELECT a.*, u.username 
        FROM activities a 
        JOIN users u ON a.user_id = u.id 
        WHERE a.date = CURDATE() 
        AND a.time >= ? AND a.time <= ? 
        AND a.reminder_sent = 0";

$stmt = $db->prepare($sql);
$stmt->execute([$currentTimeStr, $targetTimeStr]);
$activities = $stmt->fetchAll();

if (empty($activities)) {
    echo "Tidak ada aktivitas dalam 5 menit ke depan yang perlu diingatkan.\n";
}

foreach ($activities as $act) {
    echo "FOUND: " . $act['name'] . " (" . $act['time'] . ") - User: " . $act['username'] . "\n";

    $subModel = new PushSubscription();
    $subs = $subModel->getByUserId($act['user_id']);
    $validSubs = [];
    foreach ($subs as $s) {
        if (!empty($s['endpoint'])) $validSubs[] = $s;
    }

    if (!empty($validSubs)) {
        $payload = [
            'title' => "⏰ Pengingat: " . $act['name'],
            'body' => "Halo " . $act['username'] . ", aktivitas Anda dimulai sekitar 5 menit lagi (" . substr($act['time'], 0, 5) . ")! Siapkan diri Anda.",
            'icon' => 'assets/logo.png', 
            'data' => ['url' => $_ENV['APP_URL'] . '/index.php?page=dashboard']
        ];

        $pushService->sendNotification($validSubs, $payload);
        echo " -> Push sent.\n";
    } else {
        echo " -> User ini belum mengizinkan notifikasi di browser (Push Gagal).\n";
    }

    $msgLog = "⏰ Pengingat: " . $act['name'] . " akan dimulai pukul " . substr($act['time'], 0, 5);
    $notifModel->create($act['user_id'], $msgLog, 'sent');

    $upd = $db->prepare("UPDATE activities SET reminder_sent = 1 WHERE id = ?");
    $upd->execute([$act['id']]);
    echo " -> Database updated (Marked as sent).\n";
}


if (date('i') == '00' || isset($_GET['force_weather'])) {
    echo "\n--- [2] Cek Peringatan Cuaca ---\n";

    $stmt = $db->query("SELECT id, username, lat, lon, last_location_name FROM users WHERE lat IS NOT NULL");
    $users = $stmt->fetchAll();

    foreach ($users as $user) {
        $weatherData = $weatherClient->getForecast($user['lat'], $user['lon']);

        if (!isset($weatherData['list'][0])) continue;

        $current = $weatherData['list'][0];
        $temp = $current['main']['temp'];
        $condition = strtolower($current['weather'][0]['main']);
        $desc = $current['weather'][0]['description'];
        $cityName = $user['last_location_name'] ?? "Lokasi Anda";

        $shouldNotify = false;
        $alertMsg = "";

        // Logika Peringatan Cuaca
        if (strpos($condition, 'rain') !== false) {
            $shouldNotify = true;
            $alertMsg = "☔ Hujan turun di $cityName ($desc). Bawa payung!";
        } elseif (strpos($condition, 'thunderstorm') !== false) {
            $shouldNotify = true;
            $alertMsg = "⚡ Badai petir di $cityName! Hati-hati.";
        } elseif ($temp >= 33) {
            $shouldNotify = true;
            $alertMsg = "☀️ Panas terik ($temp°C) di $cityName. Gunakan tabir surya.";
        }

        if ($shouldNotify) {
            echo "Mengirim notif cuaca ke " . $user['username'] . "...\n";
            $pushService->sendWeatherAlert($user['id'], $cityName, ucfirst($desc), $temp);
        }
    }
} else {
    echo "\n--- [2] Cek Cuaca Dilewati (Menunggu jam tepat) ---\n";
}

echo "\nSelesai.</pre>";
