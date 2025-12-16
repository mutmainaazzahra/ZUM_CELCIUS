<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/Env.php';
require_once '../config/Database.php';

try {
    Env::load(__DIR__ . '/../.env');
} catch (Exception $e) {
    die("Gagal memuat konfigurasi: " . $e->getMessage());
}


require_once '../controllers/PageController.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/PushController.php';

$page = $_GET['page'] ?? 'home';

$controller = new PageController();
$authController = new AuthController();
$pushController = new PushController();


// Router
switch ($page) {
    case 'home':
        $controller->home();
        break;
    case 'dashboard':
        $controller->dashboard();
        break;
    case 'profile':
        $controller->profile();
        break;

    // Autentikasi
    case 'login':
        $authController->login();
        break;
    case 'register':
        $authController->register();
        break;
    case 'logout':
        $authController->logout();
        break;

    // Reset Password
    case 'forgot_password_process': 
        $authController->forgotPasswordProcess();
        break;
    case 'reset_password_dummy_logic': 
        $authController->resetPasswordLogic();
        break;
    case 'forgot_password': 
    case 'reset_password': 
        require '../views/auth/reset_password.php';
        break;

    // Admin
    case 'admin_dashboard':
        require '../controllers/AdminController.php';
        (new AdminController())->index();
        break;

    // --- Notifikasi & Utility ---
    case 'mark_read':
        $controller->markRead();
        break;
    case 'update_profile':
        $authController->updateProfile();
        break;
    case 'push_subscribe':
        $pushController->subscribe();
        break;


    default:
        $controller->home();
        break;
}
