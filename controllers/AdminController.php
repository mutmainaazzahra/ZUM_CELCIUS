<?php
require_once '../models/User.php';
require_once '../models/Activity.php';
require_once '../models/Notification.php';

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

            // 1. Tambah User Baru 
            if (isset($_POST['action']) && $_POST['action'] === 'create_user') {
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $role = $_POST['role'];

                if ($userModel->create($username, $email, $password, $role)) {
                    header("Location: index.php?page=admin_dashboard&msg=created");
                } else {
                    header("Location: index.php?page=admin_dashboard&error=create_failed");
                }
                exit;
            }

            // 2. Edit User 
            if (isset($_POST['action']) && $_POST['action'] === 'edit_user') {
                $id = $_POST['user_id'];
                $username = $_POST['username'];
                $email = $_POST['email'];
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
