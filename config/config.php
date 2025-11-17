<?php
// Asosiy konfiguratsiya

// Output buffering - PHP fayllarining oxiridagi bo'sh qatorlarni yashirish
if (!ob_get_level()) {
    ob_start();
}

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

// Base URL - dinamik (HTTP_HOST dan olinadi)
// Ikkala domen uchun ham ishlaydi: profiorientation.uz va profiorientation.cybernode.uz
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'profiorientation.uz';
    
    // Ruxsat etilgan domenlar ro'yxati
    $allowedDomains = [
        'profiorientation.uz',
        'www.profiorientation.uz',
        'profiorientation.cybernode.uz',
        'www.profiorientation.cybernode.uz'
    ];
    
    // Agar domen ruxsat etilgan ro'yxatda bo'lsa, ishlatish
    if (in_array($host, $allowedDomains)) {
        return $protocol . $host . '/';
    }
    
    // .env dan olish yoki default qiymat
    $envBaseUrl = env('BASE_URL', '');
    if (!empty($envBaseUrl)) {
        return $envBaseUrl;
    }
    
    // Default qiymat
    return 'https://profiorientation.uz/';
}

define('BASE_URL', getBaseUrl());

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

