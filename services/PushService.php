<?php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
require_once __DIR__ . '/../vendor/autoload.php';

class PushService
{
    private $webPush;
    public function __construct()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $tempConf = sys_get_temp_dir() . '/openssl.cnf';
            if (!file_exists($tempConf)) {
                $content = "HOME = .\nRANDFILE = .rnd\n[req]\ndistinguished_name = req_distinguished_name\n[req_distinguished_name]\n";
                file_put_contents($tempConf, $content);
            }
            putenv("OPENSSL_CONF=$tempConf");
        }

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
            if (method_exists($this->webPush, 'setReuseVAPIDHeaders')) {
                $this->webPush->setReuseVAPIDHeaders(true);
            }
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
                'endpoint' => $report->getEndpoint(),
                'message' => $report->isSuccess() ? 'OK' : $report->getReason()
            ];
        }
        return $reports;
    }

    public function sendWeatherAlert($userId, $city, $condition, $temp)
    {
        require_once __DIR__ . '/../models/PushSubscription.php';
        require_once __DIR__ . '/../models/Notification.php';

        $subModel = new PushSubscription();
        $notifModel = new Notification();

        $subscriptions = $subModel->getByUserId($userId);

        if (empty($subscriptions)) return [];

        $payload = [
            'title' => "Peringatan Cuaca: $city",
            'body' => "$condition, $temp°C. Cek aplikasi sekarang!",
            'icon' => 'assets/logo.png',
            'data' => ['url' => $_ENV['BASE_URL']]
        ];

        $reports = $this->sendNotification($subscriptions, $payload);

        $successCount = 0;
        foreach ($reports as $report) {
            if ($report['success']) $successCount++;
        }

        $message = "Web Push: Peringatan cuaca dikirim ke $successCount perangkat.";
        $status = $successCount > 0 ? 'sent' : 'failed';
        $notifModel->create($userId, $message, $status);

        return $reports;
    }

}
