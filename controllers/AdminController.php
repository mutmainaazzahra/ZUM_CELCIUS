<?php
require_once '../models/User.php';
require_once '../models/Activity.php';
require_once '../models/Notification.php';
require_once '../services/MailService.php';
require_once '../config/Env.php';

class AdminController
{
    public function index()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrator') {
            header("Location: index.php?page=login");
            exit;
        }

        $userModel = new User();
        $activityModel = new Activity();
        $notificationModel = new Notification();

        $notifications = $notificationModel->getByUser($_SESSION['user']['id'], 5);

        // --- HANDLER POST REQUEST (Create & Update) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Tambah User Baru (Create User dari Admin Dashboard)
            if (isset($_POST['action']) && $_POST['action'] === 'create_user') {
                $username = trim($_POST['username']);
                $email = $_POST['email'];
                $password = $_POST['password'];
                $role = $_POST['role'];

                if (strpos($username, ' ') !== false) {
                    header("Location: index.php?page=admin_dashboard&error=username_space");
                    exit;
                }

                if (empty($username) || empty($email) || strlen($password) < 6) {
                    header("Location: index.php?page=admin_dashboard&error=invalid_input");
                    exit;
                }

                if ($userModel->getByEmail($email)) {
                    header("Location: index.php?page=admin_dashboard&error=email_exists");
                    exit;
                }


                if ($userModel->create($username, $email, $password, $role)) {

                    $emailSuccess = true;
                    try {
                        $mailService = new MailService();
                        $subject = "AKUN BARU ANDA TELAH DIBUAT - Zum Celcius";
                        $appUrl = $_ENV['APP_URL'] ?? 'http://localhost/zum_celcius/public';

                        $loginLink = $appUrl . "?page=login";

                        $body = "
                            <h3>Halo $username,</h3>
                            <p>Akun Anda di Zum Celcius telah berhasil dibuat oleh Administrator.</p>
                            <p>Berikut adalah detail akun Anda:</p>
                            <ul>
                                <li><strong>Username:</strong> " . htmlspecialchars($username) . "</li>
                                <li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>
                                <li><strong>Password Awal:</strong> <code>" . htmlspecialchars($password) . "</code></li>
                                <li><strong>Role:</strong> " . ucfirst($role) . "</li>
                            </ul>
                            <p>Silakan segera login dan ubah password Anda demi keamanan.</p>
                            <p><a href='" . $loginLink . "' style='display: inline-block; padding: 10px 20px; background-color: #272343; color: #fff; text-decoration: none; border-radius: 5px;'>KLIK DI SINI UNTUK LOGIN</a></p>
                            <p>Terima kasih.</p>
                        ";
                        $mailService->send($email, $username, $subject, $body);

                        $notificationModel->create(0, "Email notifikasi akun baru dikirim ke " . $email . " (oleh Admin).");
                    } catch (Exception $e) {
                        $emailSuccess = false; 
                        $notificationModel->create(0, "Gagal mengirim email akun baru ke " . $email . ": " . $e->getMessage());
                    }

                    if (!$emailSuccess) {
                        header("Location: index.php?page=admin_dashboard&msg=created_email_failed");
                    } else {
                        header("Location: index.php?page=admin_dashboard&msg=created");
                    }
                    exit;
                } else {
                    header("Location: index.php?page=admin_dashboard&error=create_failed");
                }
                exit;
            }

            // 2. Edit User
            if (isset($_POST['action']) && $_POST['action'] === 'edit_user') {
                $id = $_POST['user_id'];
                $username = trim($_POST['username']);
                $email = $_POST['email'];

                if (strpos($username, ' ') !== false) {
                    header("Location: index.php?page=admin_dashboard&error=username_space");
                    exit;
                }

                $existingUser = $userModel->getById($id);
                $role = $existingUser['role'];
                $password = null;

                if ($userModel->update($id, $username, $email, $role, $password)) {
                    header("Location: index.php?page=admin_dashboard&msg=updated");
                } else {
                    header("Location: index.php?page=admin_dashboard&error=update_failed");
                }
                exit;
            }
        }

        // --- HANDLER GET REQUEST ---
        if (isset($_GET['export'])) {
            if ($_GET['export'] == 'activities') {
                $data = $activityModel->getAllForAdmin();
                $headers = ['ID', 'User ID', 'Username', 'Name', 'Type', 'Notes', 'Date', 'Created At'];
                $this->exportCSV('laporan_aktivitas.csv', $headers, $data);
                exit;
            }
            if ($_GET['export'] == 'notifications') {
                $data = $notificationModel->getAllForAdmin();
                $headers = ['ID', 'User ID', 'Message', 'Status', 'Sent At', 'Username', 'Email'];
                $this->exportCSV('log_notifikasi.csv', $headers, $data);
                exit;
            }
        }

        if (isset($_GET['delete_user'])) {
            if ($_GET['delete_user'] != $_SESSION['user']['id']) {
                $userModel->deleteUser($_GET['delete_user']);
                header("Location: index.php?page=admin_dashboard&msg=deleted");
            } else {
                header("Location: index.php?page=admin_dashboard&error=self_delete");
            }
            exit;
        }

        $users = $userModel->getAllUsers();
        $stats = $activityModel->getStats();

        $pieLabels = [];
        $pieData = [];
        foreach ($stats as $s) {
            $pieLabels[] = $s['type'];
            $pieData[] = $s['count'];
        }

        require '../views/admin/dashboard.php';
    }

    private function exportCSV($filename, $headers, $data)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    }
}
