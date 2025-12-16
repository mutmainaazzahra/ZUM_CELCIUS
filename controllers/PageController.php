<?php
require_once '../services/OpenWeatherClient.php';
require_once '../services/AnalyticsService.php';
require_once '../models/Activity.php';
require_once '../models/Notification.php';
require_once '../models/User.php';
require_once '../config/Database.php'; 

class PageController
{

    private function getUserNotifications()
    {
        if (isset($_SESSION['user'])) {
            $notifModel = new Notification();
            return [
                'list' => $notifModel->getByUser($_SESSION['user']['id'], 5),
                'unread' => $notifModel->countUnread($_SESSION['user']['id'])
            ];
        }
        return ['list' => [], 'unread' => 0];
    }

    public function home()
    {
        $weatherClient = new OpenWeatherClient();
        $forecast = null;
        $currentWeather = null;
        $bgClass = "weather-bg-default";
        
        $recommendation = [
            'type' => 'DEFAULT',
            'message' => 'Cuaca hari ini cukup stabil untuk aktivitas normal.',
            'icon' => 'bi-cloud-sun',
            'color' => 'secondary',
            'bg' => '#e3f6f5'
        ];
        $chartData = ['labels' => [], 'data' => []];
        $error = null;

        // L
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $cityData = $weatherClient->searchCity($_GET['search']);
            if ($cityData) {
                $lat = $cityData['coord']['lat'];
                $lon = $cityData['coord']['lon'];
                $cityName = $cityData['name'];
                header("Location: index.php?page=home&lat=" . $lat . "&lon=" . $lon . "&name=" . urlencode($cityName));
                exit;
            } else {
                $error = "Kota tidak ditemukan.";
            }
        } elseif (isset($_GET['lat']) && isset($_GET['lon'])) {
            $lat = $_GET['lat'];
            $lon = $_GET['lon'];
            $cityName = $_GET['name'] ?? null;

            $_SESSION['last_lat'] = $lat;
            $_SESSION['last_lon'] = $lon;
            $_SESSION['last_city_name'] = $cityName;

            if (isset($_SESSION['user'])) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE users SET lat = ?, lon = ?, last_location_name = ? WHERE id = ?");
                $stmt->execute([$lat, $lon, $cityName, $_SESSION['user']['id']]);
            }

            try {
                $forecast = $weatherClient->getForecast($lat, $lon);

                if ($forecast === null || !isset($forecast['list'][0])) {
                    throw new Exception("Gagal mendapatkan data prakiraan cuaca.");
                }

                if ($cityName && isset($forecast['city'])) {
                    $forecast['city']['name'] = htmlspecialchars($cityName);
                }
                $currentWeather = $forecast['list'][0];


                $temp = $currentWeather['main']['temp'];
                $weatherMain = strtolower($currentWeather['weather'][0]['main']);
                $iconCode = $currentWeather['weather'][0]['icon'];
                $weatherId = $currentWeather['weather'][0]['id'];

                if (strpos($iconCode, 'n') !== false) $bgClass = "weather-bg-night";
                elseif (strpos($weatherMain, 'rain') !== false) $bgClass = "weather-bg-rain";
                elseif (strpos($weatherMain, 'cloud') !== false) $bgClass = "weather-bg-cloudy";
                else $bgClass = "weather-bg-clear";

                $currentWeather['weather'][0]['description'] = ucfirst($currentWeather['weather'][0]['description']);
                $recommendation = AnalyticsService::getActivityRecommendation($weatherId, $temp);
                $chartData = AnalyticsService::prepareChartData($forecast['list']);

            } catch (Exception $e) {
                $forecast = null;
                $currentWeather = null;
                $chartData = ['labels' => [], 'data' => []];
                $error = "Gagal memuat cuaca: " . $e->getMessage();
            }

        }

        $notifData = $this->getUserNotifications();
        $notifications = $notifData['list'];
        $unreadCount = $notifData['unread'];

        require '../views/home.php';
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'guest user') {
            header("Location: index.php?page=login");
            exit;
        }

        $activityModel = new Activity();
        $user = $_SESSION['user'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_activity'])) {
            $time = $_POST['time'] ?? '00:00';
            $activityModel->create($user['id'], $_POST['name'], $_POST['type'], $_POST['notes'], $_POST['date'], $time);
            header("Location: index.php?page=dashboard&msg=added");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_activity'])) {
            $time = $_POST['time'] ?? '00:00';
            $activityModel->update($_POST['id'], $user['id'], $_POST['name'], $_POST['type'], $_POST['notes'], $_POST['date'], $time);
            header("Location: index.php?page=dashboard&msg=updated");
            exit;
        }

        if (isset($_GET['delete'])) {
            $activityModel->delete($_GET['delete'], $user['id']);
            header("Location: index.php?page=dashboard&msg=deleted");
            exit;
        }

        $activities = $activityModel->getByUser($user['id']);

        $lat = $_SESSION['last_lat'] ?? -6.2088;
        $lon = $_SESSION['last_lon'] ?? 106.8456;
        $weatherClient = new OpenWeatherClient();
        $forecast = $weatherClient->getForecast($lat, $lon);
        if (isset($_SESSION['last_city_name']) && isset($forecast['city'])) {
            $forecast['city']['name'] = htmlspecialchars($_SESSION['last_city_name']);
        }

        $notifData = $this->getUserNotifications();
        $notifications = $notifData['list'];
        $unreadCount = $notifData['unread'];

        require '../views/member/dashboard.php';
    }

    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $user = $_SESSION['user'];
        $notifData = $this->getUserNotifications();
        $notifications = $notifData['list'];
        $unreadCount = $notifData['unread'];
        require '../views/member/profile.php';
    }

    public function markRead()
    {
        if (isset($_SESSION['user'])) {
            $notifModel = new Notification();
            $notifModel->markAllAsRead($_SESSION['user']['id']);
            echo json_encode(['status' => 'success']);
        }
        exit;
    }
}
