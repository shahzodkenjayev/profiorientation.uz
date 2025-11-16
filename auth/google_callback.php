<?php
require_once '../config/config.php';

// Google Sign-In callback handler (JWT credential orqali)

$credential = $_GET['credential'] ?? '';

if (empty($credential)) {
    redirect(BASE_URL . 'auth/register.php?error=google_auth_failed');
}

// JWT token'ni decode qilish
$parts = explode('.', $credential);
if (count($parts) !== 3) {
    redirect(BASE_URL . 'auth/register.php?error=invalid_google_token');
}

// Payload'ni decode qilish
$payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

if (!$payload) {
    redirect(BASE_URL . 'auth/register.php?error=invalid_google_token');
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
    redirect(BASE_URL . 'auth/register.php?error=invalid_google_data');
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
        $_SESSION['google_auth_data'] = [
            'id' => $google_id,
            'email' => $email,
            'name' => $name
        ];
        
        if ($user['test_completed']) {
            redirect(BASE_URL . 'results/view.php');
        } else {
            redirect(BASE_URL . 'test/start.php');
        }
    } else {
        // Yangi foydalanuvchi, ma'lumotlarni session'ga saqlash va register sahifasiga yuborish
        $_SESSION['google_auth_data'] = [
            'id' => $google_id,
            'email' => $email,
            'name' => $name
        ];
        $_SESSION['google_full_name'] = $full_name;
        redirect(BASE_URL . 'auth/register.php?google=1');
    }
} catch (PDOException $e) {
    redirect(BASE_URL . 'auth/register.php?error=db_error');
}
?>

