<?php
require_once '../config/config.php';

// Google Sign-In callback handler (JWT credential orqali)

$credential = $_GET['credential'] ?? '';

if (empty($credential)) {
    redirect(BASE_URL . 'auth/register?error=google_auth_failed');
}

// JWT token'ni decode qilish
$parts = explode('.', $credential);
if (count($parts) !== 3) {
    redirect(BASE_URL . 'auth/register?error=invalid_google_token');
}

// Payload'ni decode qilish
$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

if (!$payload) {
    redirect(BASE_URL . 'auth/register?error=invalid_google_token');
}

// Ma'lumotlarni olish
$google_id = $payload['sub'] ?? '';
$email = $payload['email'] ?? '';
$name = $payload['name'] ?? '';
$first_name = $payload['given_name'] ?? '';
$last_name = $payload['family_name'] ?? '';
$full_name = trim($first_name . ' ' . $last_name);
if (empty($full_name)) {
    $full_name = $name;
}

if (empty($google_id)) {
    redirect(BASE_URL . 'auth/register?error=invalid_google_data');
}

// Foydalanuvchini tekshirish
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE google_id = ?");
    $stmt->execute([$google_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Foydalanuvchi mavjud, login qilish
        $_SESSION['user_id'] = $user['id'];
        unset($_SESSION['google_auth_data']);
        unset($_SESSION['google_full_name']);
        
        if ($user['test_completed']) {
            redirect(BASE_URL . 'results/view.php');
        } else {
            redirect(BASE_URL . 'dashboard/index.php');
        }
    } else {
        // Yangi foydalanuvchi, email orqali ham tekshirish
        if (!empty($email)) {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user_by_email = $stmt->fetch();
            
            if ($user_by_email) {
                // Email orqali foydalanuvchi topildi, google_id ni yangilash va login qilish
                $stmt = $db->prepare("UPDATE users SET google_id = ? WHERE id = ?");
                $stmt->execute([$google_id, $user_by_email['id']]);
                
                $_SESSION['user_id'] = $user_by_email['id'];
                unset($_SESSION['google_auth_data']);
                unset($_SESSION['google_full_name']);
                
                if ($user_by_email['test_completed']) {
                    redirect(BASE_URL . 'results/view.php');
                } else {
                    redirect(BASE_URL . 'dashboard/index.php');
                }
                exit;
            }
        }
        
        // Yangi foydalanuvchi yaratish - to'g'ridan-to'g'ri yaratib, dashboard'ga o'tkazish
        $stmt = $db->prepare("INSERT INTO users (google_id, email, full_name, login_type) 
                             VALUES (?, ?, ?, 'google')");
        $stmt->execute([
            $google_id, 
            $email ?: null, 
            $full_name
        ]);
        
        $_SESSION['user_id'] = $db->lastInsertId();
        unset($_SESSION['google_auth_data']);
        unset($_SESSION['google_full_name']);
        redirect(BASE_URL . 'dashboard/index.php');
    }
} catch (PDOException $e) {
    redirect(BASE_URL . 'auth/register?error=db_error');
}
?>

