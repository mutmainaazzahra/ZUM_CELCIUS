<?php
require_once '../models/User.php';
require_once '../models/Notification.php';
require_once '../services/MailService.php';

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
            if ($userModel->register($username, $email, $password)) {

                try {
                    $mailService = new MailService();
                    $subject = "Selamat Datang di Zum Celcius!";
                    $body = "<h3>Halo $username,</h3><p>Akun Guest User Anda aktif.</p>";
                    $mailService->send($email, $username, $subject, $body);

                    $notifModel = new Notification();
                    $notifModel->create(0, "Email selamat datang dikirim ke " . $email);
                } catch (Exception $e) {
                }

                $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
                header("Location: index.php?page=login");
                exit;
            } else {
                $_SESSION['error'] = "Gagal mendaftar. Email mungkin sudah digunakan.";
                header("Location: index.php?page=register");
                exit;
            }
        } else {
            require '../views/auth/register.php';
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

    public function forgotPasswordProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $user = $userModel->getByEmail($_POST['email']);

            if ($user) {
                $appUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost/zum_celcius/public', '/');

                $resetLink = $appUrl . "/index.php?page=reset_password&token=" . $user['id'];

                $mailService = new MailService();
                $body = "<p>Halo " . htmlspecialchars($user['username']) . ",</p>
                         <p>Silakan klik link di bawah ini untuk mengatur ulang password Anda:</p>
                         <p><a href='$resetLink'>$resetLink</a></p>";
                $isSent = $mailService->send($user['email'], $user['username'], "Reset Password", $body);

                if ($isSent) $_SESSION['success'] = "Link reset password telah dikirim ke email Anda.";
                else $_SESSION['error'] = "Gagal mengirim email. Pastikan konfigurasi SMTP di .env sudah benar.";
            } else {
                $_SESSION['error'] = "Email tidak terdaftar.";
            }
            header("Location: index.php?page=reset_password");
            exit;
        }
    }

    public function resetPasswordLogic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? null;
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            $userId = (int)$token;

            if (strlen($password) < 6) {
                $_SESSION['error'] = "Password minimal harus 6 karakter.";
                header("Location: index.php?page=reset_password&token=$userId");
                exit;
            }

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = "Konfirmasi password tidak cocok.";
                header("Location: index.php?page=reset_password&token=$userId");
                exit;
            }


            $userModel = new User();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db = Database::getInstance()->getConnection();

            try {
                $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$hash, $userId]);

                header("Location: index.php?page=reset_password&token=$userId&status=success");
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "Terjadi kesalahan saat menyimpan password.";
                header("Location: index.php?page=reset_password&token=$userId");
                exit;
            }
        }
    }

    public function forgotPassword()
    {
        require '../views/auth/reset_password.php';
    }


    public function logout()
    {
        session_destroy();
        header("Location: index.php");
    }
}
