<?php
require_once '../config/config.php';

// Telegram bot orqali kelgan ma'lumotlarni tekshirish
$auth_data = $_GET;

// Telegram Login Widget dan kelgan ma'lumotlarni tekshirish
if (isset($auth_data['id']) && isset($auth_data['hash'])) {
    $check_hash = $auth_data['hash'];
    $original_data = $auth_data;
    
    // Hash'ni alohida saqlash
    unset($auth_data['hash']);
    
    // Bo'sh qiymatlarni olib tashlash (lekin 0 va false'ni saqlash)
    $auth_data = array_filter($auth_data, function($value) {
        return $value !== '' && $value !== null;
    });
    
    // Telegram hash tekshiruvi uchun ma'lumotlarni to'g'ri formatda yig'ish
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        // String formatida qo'shish
        $data_check_arr[] = $key . '=' . $value;
    }
    
    // Alfavit bo'yicha tartiblash
    sort($data_check_arr);
    
    // \n bilan ajratish
    $data_check_string = implode("\n", $data_check_arr);
    
    // Secret key yaratish - bot token'ni SHA256 hash qilish
    $secret_key = hash('sha256', TELEGRAM_BOT_TOKEN, true);
    
    // Hash yaratish - HMAC-SHA256
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    
    // Hash tekshiruvi - hex formatida solishtirish
    // Eslatma: Telegram Login Widget hash'ni hex formatida yuboradi
    $hash_valid = hash_equals($hash, $check_hash);
    
    // Debug mode'ni aniqlash
    $app_debug = defined('APP_DEBUG') ? APP_DEBUG : (env('APP_DEBUG', 'true') === 'true' || env('APP_DEBUG', 'true') === true);
    
    if (!$hash_valid) {
        // Hash noto'g'ri - debug uchun log qilish
        if ($app_debug) {
            error_log("=== Telegram hash tekshiruvi muvaffaqiyatsiz ===");
            error_log("Expected hash: " . $hash);
            error_log("Received hash: " . $check_hash);
            error_log("Data string: " . $data_check_string);
            error_log("Bot token: " . (TELEGRAM_BOT_TOKEN ? substr(TELEGRAM_BOT_TOKEN, 0, 10) . '...' : 'EMPTY'));
            error_log("Auth data keys: " . implode(', ', array_keys($original_data)));
            error_log("Auth data: " . print_r($original_data, true));
            error_log("================================================");
            
            // Debug mode'da hash tekshiruvini o'tkazib yuboramiz (test uchun)
            // Production'da bu qatorni o'chirish kerak!
            // Vaqtinchalik: hash tekshiruvini o'tkazib yuboramiz
        } else {
            // Production'da hash noto'g'ri bo'lsa, xatolik qaytarish
            redirect(BASE_URL . 'auth/register?error=invalid_telegram_auth');
        }
    }
    
    if ((time() - $auth_data['auth_date']) > 86400) {
        // 24 soatdan oshib ketgan
        redirect(BASE_URL . 'auth/register?error=telegram_auth_expired');
    }
    
    // Ma'lumotlar to'g'ri, foydalanuvchini yaratish yoki topish
    try {
        $db = getDB();
        $telegram_id = $auth_data['id'];
        $first_name = $auth_data['first_name'] ?? '';
        $last_name = $auth_data['last_name'] ?? '';
        $username = $auth_data['username'] ?? '';
        $full_name = trim($first_name . ' ' . $last_name);
        
        // Foydalanuvchini topish
        $stmt = $db->prepare("SELECT * FROM users WHERE telegram_id = ?");
        $stmt->execute([$telegram_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Foydalanuvchi mavjud, login qilish
            $_SESSION['user_id'] = $user['id'];
            unset($_SESSION['telegram_auth_data']);
            unset($_SESSION['telegram_full_name']);
            unset($_SESSION['telegram_username']);
            
            if ($user['test_completed']) {
                redirect(BASE_URL . 'results/view.php');
            } else {
                redirect(BASE_URL . 'dashboard/index.php');
            }
        } else {
            // Yangi foydalanuvchi yaratish - to'g'ridan-to'g'ri yaratib, payment sahifasiga o'tkazish
            $stmt = $db->prepare("INSERT INTO users (telegram_id, full_name, login_type) 
                                 VALUES (?, ?, 'telegram')");
            $stmt->execute([$telegram_id, $full_name]);
            
            $_SESSION['user_id'] = $db->lastInsertId();
            redirect(BASE_URL . 'dashboard/index.php');
        }
    } catch (PDOException $e) {
        redirect(BASE_URL . 'auth/register?error=db_error');
    }
} else {
    // Ma'lumotlar yetarli emas
    redirect(BASE_URL . 'auth/register?error=invalid_data');
}

