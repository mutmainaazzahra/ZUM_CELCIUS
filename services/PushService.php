<?php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
require_once __DIR__ . '/../vendor/autoload.php';

class PushService
{
    private $webPush;
    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => $_ENV['VAPID_SUBJECT'] ?? 'mailto:admin@zumcelcius.com',
                'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
                'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
            ],
        ];

        try {
            $this->webPush = new WebPush($auth, ['TTL' => 2419200], 30, ['timeout' => 30]);
        } catch (Throwable $e) {
            $this->webPush = new WebPush($auth, ['timeout' => 30]);
        }
    }

    public function sendNotification($subscriptions, $payload)
    {
        $reports = [];
        foreach ($subscriptions as $sub) {
            try {
                if (empty($sub['endpoint'])) continue;
                $subscription = Subscription::create([
                    'endpoint' => $sub['endpoint'],
                    'publicKey' => $sub['p256dh'],
                    'authToken' => $sub['auth'],
                    'contentEncoding' => 'aesgcm',
                ]);

                if (method_exists($this->webPush, 'sendNotification')) {
                    $this->webPush->sendNotification($subscription, json_encode($payload), false);
                } elseif (method_exists($this->webPush, 'queueNotification')) {
                    $this->webPush->queueNotification($subscription, json_encode($payload));
                }
            } catch (Throwable $e) {
                error_log("Push error: " . $e->getMessage());
            }
        }

        foreach ($this->webPush->flush() as $report) {
            $reports[] = [
                'success' => $report->isSuccess(),
                'endpoint' => $report->getEndpoint()
            ];
        }
        return $reports;
    }

    public function sendGenericNotification($userId, $title, $message)
    {
        require_once __DIR__ . '/../models/PushSubscription.php';
        require_once __DIR__ . '/../models/Notification.php';

        $subModel = new PushSubscription();
        $notifModel = new Notification();
        $subscriptions = $subModel->getByUserId($userId);

        $payload = [
            'title' => $title,
            'body' => $message,
            'icon' => 'assets/logo.png',
            'data' => ['url' => $_ENV['APP_URL'] ?? 'index.php?page=dashboard']
        ];

        $reports = $this->sendNotification($subscriptions, $payload);

        $isSuccess = false;
        foreach ($reports as $r) {
            if ($r['success']) $isSuccess = true;
        }

        $notifModel->create($userId, $message, $isSuccess ? 'sent' : 'failed');

        return $reports;
    }

    public function sendWeatherAlert($userId, $city, $condition, $temp)
    {
        $alertContent = "📍 {$city}: {$condition} ({$temp}°C). Jangan lupa cek jadwal outdoor Anda!";
        return $this->sendGenericNotification($userId, "Peringatan Cuaca: {$city}", $alertContent);
    }
}
