<?php
require_once __DIR__ . '/../services/OpenWeatherClient.php';
require_once __DIR__ . '/../services/AnalyticsService.php';
require_once __DIR__ . '/../models/Activity.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';

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
        $chartData = ['labels' => [], 'data' => []];
        $error = null;

        if (isset($_GET['lat']) && isset($_GET['lon'])) {
            $lat = $_GET['lat'];
            $lon = $_GET['lon'];
            $cityName = $_GET['name'] ?? null;

            $_SESSION['last_lat'] = $lat;
            $_SESSION['last_lon'] = $lon;
            if ($cityName) $_SESSION['last_city_name'] = $cityName;

            if (isset($_SESSION['user'])) {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE users SET lat = ?, lon = ?, last_location_name = ? WHERE id = ?");
                $stmt->execute([$lat, $lon, $cityName, $_SESSION['user']['id']]);
            }
        } else {
            $lat = $_SESSION['last_lat'] ?? -6.2088;
            $lon = $_SESSION['last_lon'] ?? 106.8456;
        }

        try {
            $forecast = $weatherClient->getForecast($lat, $lon);
            if ($forecast && isset($forecast['list'][0])) {
                if (isset($_SESSION['last_city_name'])) {
                    $forecast['city']['name'] = $_SESSION['last_city_name'];
                }

                $currentWeather = $forecast['list'][0];
                $temp = $currentWeather['main']['temp'];
                $weatherId = $currentWeather['weather'][0]['id'];
                $recommendation = AnalyticsService::getActivityRecommendation($weatherId, $temp);
                $chartData = AnalyticsService::prepareChartData($forecast['list']);
            }
        } catch (Exception $e) {
            $error = "Gagal memuat cuaca: " . $e->getMessage();
        }

        $notifData = $this->getUserNotifications();
        $notifications = $notifData['list'];
        $unreadCount = $notifData['unread'];

        require __DIR__ . '/../views/home.php';
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $activityModel = new Activity();
        $weatherClient = new OpenWeatherClient();
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
        $forecast = $weatherClient->getForecast($lat, $lon);

        if (isset($_SESSION['last_city_name']) && isset($forecast['city'])) {
            $forecast['city']['name'] = $_SESSION['last_city_name'];
        }

        $notifData = $this->getUserNotifications();
        $notifications = $notifData['list'];
        $unreadCount = $notifData['unread'];

        require __DIR__ . '/../views/member/dashboard.php';
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
        require __DIR__ . '/../views/member/profile.php';
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
