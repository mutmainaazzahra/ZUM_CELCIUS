<?php
require_once '../models/PushSubscription.php';
require_once '../services/PushService.php';

class PushController
{
    public function subscribe()
    {
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json');

        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!isset($data['endpoint']) || !isset($data['keys'])) {
                throw new Exception('Invalid subscription data');
            }

            $subModel = new PushSubscription();
            $userId = $_SESSION['user']['id'];
            $endpoint = $data['endpoint'];
            $p256dh = $data['keys']['p256dh'];
            $auth = $data['keys']['auth'];

            if ($subModel->saveSubscription($userId, $endpoint, $p256dh, $auth)) {
                echo json_encode(['success' => true, 'message' => 'Subscription saved.']);
            } else {
                throw new Exception('Failed to save to database');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

}
