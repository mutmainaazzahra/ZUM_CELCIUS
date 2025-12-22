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
            date_default_timezone_set('Asia/Jakarta');
            $email = $_POST['email'] ?? null;
            $userModel = new User();
            $user = $userModel->getByEmail($email);

            if (!$user) {
                $_SESSION['error'] = "Email tidak terdaftar dalam sistem.";
                header("Location: index.php?page=forgot_password");
                exit;
            }

            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $userModel->setResetToken($email, $token, $expiry);

            try {
                $mailService = new MailService();
                $subject = "Permintaan Reset Password - Zum Celcius";
                $appUrl = $_ENV['APP_URL'] ?? 'http://localhost/zum_celcius/public';
                $resetLink = $appUrl . "?page=reset_password&token=" . $token;

                $body = "<h3>Halo " . htmlspecialchars($user['username']) . ",</h3><p>Silakan klik link berikut untuk reset password: <a href='$resetLink'>$resetLink</a></p>";
                $mailService->send($user['email'], $user['username'], $subject, $body);
                $_SESSION['success'] = "Link reset password telah dikirim ke email Anda.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Gagal mengirim email reset password.";
            }

            header("Location: index.php?page=forgot_password");
            exit;
        }
    }

    public function resetPasswordLogic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            date_default_timezone_set('Asia/Jakarta');
            $token = $_POST['token'] ?? null;
            $password = $_POST['password'] ?? null;
            $confirmPassword = $_POST['confirm_password'] ?? null;

            if ($password !== $confirmPassword || strlen($password) < 6) {
                $_SESSION['error'] = "Password tidak cocok atau kurang dari 6 karakter.";
                header("Location: index.php?page=reset_password&token=" . $token);
                exit;
            }

            $userModel = new User();
            $user = $userModel->getUserByToken($token);

            if (!$user) {
                header("Location: index.php?page=reset_password&status=invalid_token");
                exit;
            }

            if ($userModel->updatePasswordAndClearToken($user['id'], $password)) {
                header("Location: index.php?page=login&status=reset_success");
            } else {
                $_SESSION['error'] = "Gagal memperbarui password.";
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
            $oldPhoto = $_SESSION['user']['profile_photo'] ?? null;
            $photoPath = null;

            if (strpos($username, ' ') !== false) {
                header("Location: index.php?page=profile&error=username_space");
                exit;
            }

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $tmpFile = $_FILES['photo']['tmp_name'];
                $fileName = $_FILES['photo']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($fileExtension, $allowedExtensions)) {
                    header("Location: index.php?page=profile&error=invalid_file_type");
                    exit;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $tmpFile);
                finfo_close($finfo);

                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($mimeType, $allowedMimeTypes)) {
                    header("Location: index.php?page=profile&error=malicious_file_detected");
                    exit;
                }

                if ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
                    header("Location: index.php?page=profile&error=file_too_large");
                    exit;
                }

                if (getimagesize($tmpFile) === false) {
                    header("Location: index.php?page=profile&error=corrupted_image");
                    exit;
                }

                $targetDir = "../public/uploads/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

                $newFileName = bin2hex(random_bytes(16)) . '.' . $fileExtension;

                if (move_uploaded_file($tmpFile, $targetDir . $newFileName)) {
                    $photoPath = $newFileName;

                    if ($oldPhoto && file_exists($targetDir . $oldPhoto)) {
                        unlink($targetDir . $oldPhoto);
                    }
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
