<?php
require_once '../models/User.php';
require_once '../models/Notification.php';
require_once '../services/MailService.php';
require_once '../config/Env.php'; 

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $user = $userModel->login($_POST['email'], $_POST['password']);

            if ($user) {
                $_SESSION['user'] = $user;
                if ($user['role'] === 'administrator') {
                    header("Location: index.php?page=admin_dashboard");
                } else {
                    header("Location: index.php?page=dashboard");
                }
                exit;
            } else {
                $_SESSION['error'] = "Email atau password salah!";
                header("Location: index.php?page=login");
                exit;
            }
        } else {
            require '../views/auth/login.php';
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (strpos($username, ' ') !== false) {
                $_SESSION['error'] = "Username tidak boleh mengandung spasi.";
                header("Location: index.php?page=register");
                exit;
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = "Password minimal harus 6 karakter.";
                header("Location: index.php?page=register");
                exit;
            }

            $userModel = new User();

            if ($userModel->getByEmail($email)) {
                $_SESSION['error'] = "Gagal mendaftar. Email sudah digunakan.";
                header("Location: index.php?page=register");
                exit;
            }

            if ($userModel->register($username, $email, $password)) {

                try {
                    $mailService = new MailService();
                    $subject = "Selamat Datang di Zum Celcius!";
                    $body = "<h3>Halo $username,</h3><p>Akun Guest User Anda aktif. Anda dapat mengelola aktivitas harian Anda. Terima kasih telah bergabung!</p>";
                    $mailService->send($email, $username, $subject, $body);

                    $notifModel = new Notification();
                    $notifModel->create(0, "Email selamat datang dikirim ke " . $email);
                } catch (Exception $e) {
                }

                $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
                header("Location: index.php?page=login");
                exit;
            } else {
                $_SESSION['error'] = "Gagal mendaftar. Terjadi kesalahan server/database.";
                header("Location: index.php?page=register");
                exit;
            }
        } else {
            require '../views/auth/register.php';
        }
    }

    public function forgotPasswordProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? null;
            $userModel = new User();
            $user = $userModel->getByEmail($email);

            if (!$user) {
                $_SESSION['error'] = "Email tidak terdaftar dalam sistem.";
                header("Location: index.php?page=forgot_password");
                exit;
            }

            $token = hash('sha256', time() . $user['email']);

            try {
                $mailService = new MailService();
                $subject = "Permintaan Reset Password";
                $appUrl = $_ENV['APP_URL'] ?? 'http://localhost/zum_celcius/public';
                $resetLink = $appUrl . "?page=reset_password&token=" . $token;

                $body = "
                    <h3>Halo " . htmlspecialchars($user['username']) . ",</h3>
                    <p>Kami menerima permintaan untuk mengatur ulang password Anda. Jika ini bukan Anda, abaikan email ini.</p>
                    <p>Untuk melanjutkan proses reset password, silakan klik tautan di bawah:</p>
                    <p><a href='" . $resetLink . "' style='display: inline-block; padding: 10px 20px; background-color: #ffd803; color: #272343; text-decoration: none; border-radius: 5px; font-weight: bold;'>ATUR ULANG PASSWORD</a></p>
                    <p>Tautan ini akan kedaluwarsa dalam 1 jam.</p>
                    <p>Terima kasih.</p>
                ";
                $mailService->send($user['email'], $user['username'], $subject, $body);

                $_SESSION['success'] = "Link reset password telah dikirim ke email Anda.";

                $_SESSION['reset_token'] = $token;
                $_SESSION['reset_email'] = $user['email'];
            } catch (Exception $e) {
                $_SESSION['error'] = "Gagal mengirim email reset password. Cek pengaturan SMTP Anda.";
            }

            header("Location: index.php?page=forgot_password");
            exit;
        }
    }

    public function resetPasswordLogic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? null;
            $password = $_POST['password'] ?? null;
            $confirmPassword = $_POST['confirm_password'] ?? null;

            // 1. Cek Validasi Input
            if ($password !== $confirmPassword || strlen($password) < 6) {
                $_SESSION['error'] = "Password tidak cocok atau kurang dari 6 karakter.";
                header("Location: index.php?page=reset_password&token=" . $token);
                exit;
            }

            // 2. Cek Validitas Token
            if ($token !== ($_SESSION['reset_token'] ?? null)) {
                header("Location: index.php?page=reset_password&status=invalid_token");
                exit;
            }

            // 3. Update Password
            $userModel = new User();
            $emailToUpdate = $_SESSION['reset_email'];
            $updateSuccess = $userModel->updatePasswordByEmail($emailToUpdate, $password);

            unset($_SESSION['reset_token']);
            unset($_SESSION['reset_email']);

            if ($updateSuccess) {
                header("Location: index.php?page=login&status=reset_success.");
            } else {
                $_SESSION['error'] = "Gagal memperbarui password di database.";
                header("Location: index.php?page=reset_password&token=" . $token);
            }
            exit;
        }
    }

    public function updateProfile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $id = $_SESSION['user']['id'];
            $username = trim($_POST['username']);
            $email = $_POST['email'];
            $photoPath = null;

            if (strpos($username, ' ') !== false) {
                header("Location: index.php?page=profile&error=username_space");
                exit;
            }

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $targetDir = "../public/uploads/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES["photo"]["name"]);
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetDir . $fileName)) {
                    $photoPath = $fileName;
                }
            }

            if ($userModel->updateProfile($id, $username, $email, $photoPath)) {
                $_SESSION['user'] = $userModel->getById($id);
                header("Location: index.php?page=profile&msg=updated");
            } else {
                header("Location: index.php?page=profile&error=failed");
            }
        }
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
