<?php
// Asosiy konfiguratsiya

session_start();

// .env faylini yuklash
require_once __DIR__ . '/env.php';

// Timezone
date_default_timezone_set(env('TIMEZONE', 'Asia/Tashkent'));

// Error reporting
$app_debug = env('APP_DEBUG', 'true') === 'true' || env('APP_DEBUG', 'true') === true;
if ($app_debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Base URL
define('BASE_URL', env('BASE_URL', 'http://localhost/ptest/'));

// Paths
define('ROOT_PATH', __DIR__ . '/../');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ASSETS_PATH', BASE_URL . 'assets/');

// Database
require_once __DIR__ . '/database.php';

// Language system
require_once __DIR__ . '/../includes/language.php';
Language::init();

// Google OAuth sozlamalari
define('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI', BASE_URL . 'auth/google_callback.php');

// Telegram Bot sozlamalari
define('TELEGRAM_BOT_TOKEN', env('TELEGRAM_BOT_TOKEN', ''));
define('TELEGRAM_BOT_USERNAME', env('TELEGRAM_BOT_USERNAME', ''));

// SMS sozlamalari
define('SMS_API_KEY', env('SMS_API_KEY', ''));
define('SMS_API_URL', env('SMS_API_URL', 'https://api.example.com/sms'));

// Payme sozlamalari
define('PAYME_MERCHANT_ID', env('PAYME_MERCHANT_ID', ''));
define('PAYME_SECRET_KEY', env('PAYME_SECRET_KEY', ''));
define('PAYME_TEST_MODE', env('PAYME_TEST_MODE', '1'));

// Click sozlamalari
define('CLICK_MERCHANT_ID', env('CLICK_MERCHANT_ID', ''));
define('CLICK_SERVICE_ID', env('CLICK_SERVICE_ID', ''));
define('CLICK_SECRET_KEY', env('CLICK_SECRET_KEY', ''));
define('CLICK_TEST_MODE', env('CLICK_TEST_MODE', '1'));

// Helper funksiyalar
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'auth/login.php');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . 'admin/login.php');
        exit;
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}
?>

