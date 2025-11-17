<?php
require_once '../config/config.php';

// Telegram bot orqali kelgan ma'lumotlarni tekshirish
$auth_data = $_GET;

// Telegram Login Widget dan kelgan ma'lumotlarni tekshirish
if (isset($auth_data['id']) && isset($auth_data['hash'])) {
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    
    $secret_key = hash('sha256', TELEGRAM_BOT_TOKEN, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    
    if (strcmp($hash, $check_hash) !== 0) {
        // Hash noto'g'ri
        redirect(BASE_URL . 'auth/register?error=invalid_telegram_auth');
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
                redirect(BASE_URL . 'test/start.php');
            }
        } else {
            // Yangi foydalanuvchi, ma'lumotlarni session'ga saqlash va register sahifasiga yuborish
            $_SESSION['telegram_auth_data'] = $auth_data;
            $_SESSION['telegram_full_name'] = $full_name;
            $_SESSION['telegram_username'] = $username;
            redirect(BASE_URL . 'auth/register?telegram=1');
        }
    } catch (PDOException $e) {
        redirect(BASE_URL . 'auth/register?error=db_error');
    }
} else {
    // Ma'lumotlar yetarli emas
    redirect(BASE_URL . 'auth/register?error=invalid_data');
}

