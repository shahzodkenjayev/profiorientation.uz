<?php
require_once '../config/config.php';

// Google OAuth callback handler
// Bu sahifa Google OAuth dan qaytganidan keyin chaqiriladi

$code = $_GET['code'] ?? '';

if (empty($code)) {
    redirect(BASE_URL . 'auth/login.php?error=google_auth_failed');
}

// Google token olish
$token_url = 'https://oauth2.googleapis.com/token';
$data = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);

if (isset($token_data['access_token'])) {
    // User ma'lumotlarini olish
    $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token_data['access_token'];
    $user_info = json_decode(file_get_contents($user_info_url), true);
    
    if ($user_info) {
        $google_id = $user_info['id'];
        $email = $user_info['email'] ?? '';
        $name = $user_info['name'] ?? '';
        
        // Foydalanuvchini tekshirish
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$google_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Login
            $_SESSION['user_id'] = $user['id'];
            redirect(BASE_URL . ($user['test_completed'] ? 'results/view.php' : 'test/start.php'));
        } else {
            // Register qilish kerak
            $_SESSION['google_temp_data'] = [
                'google_id' => $google_id,
                'email' => $email,
                'name' => $name
            ];
            redirect(BASE_URL . 'auth/register.php?login_type=google');
        }
    }
}

redirect(BASE_URL . 'auth/login.php?error=google_auth_failed');
?>

