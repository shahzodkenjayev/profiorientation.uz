<?php
require_once '../config/config.php';

// AJAX so'rovlar uchun JSON response
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (isLoggedIn()) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'redirect' => BASE_URL . 'test/start.php']);
        exit;
    }
    redirect(BASE_URL . 'test/start.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_type = sanitize($_POST['login_type'] ?? '');
    
    try {
        $db = getDB();
        
        if ($login_type === 'phone') {
            $phone = sanitize($_POST['phone'] ?? '');
            $verification_code = sanitize($_POST['verification_code'] ?? '');
            
            if (empty($phone)) {
                $error = 'Telefon raqamni kiriting!';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit;
                }
            } elseif (empty($verification_code)) {
                // Kod yuborilmagan, send_code.php ga yuborish kerak
                $error = 'Avval kod yuborishingiz kerak!';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit;
                }
            } else {
                if (isset($_SESSION['phone_verification_code']) && 
                    isset($_SESSION['phone_verification_time']) &&
                    $_SESSION['phone_verification_code'] == $verification_code &&
                    $_SESSION['phone_verification_number'] == $phone &&
                    (time() - $_SESSION['phone_verification_time']) < 300) {
                    
                    $stmt = $db->prepare("SELECT * FROM users WHERE phone = ?");
                    $stmt->execute([$phone]);
                    $user = $stmt->fetch();
                    
                    if ($user) {
                        $_SESSION['user_id'] = $user['id'];
                        unset($_SESSION['phone_verification_code']);
                        $redirectUrl = BASE_URL . ($user['test_completed'] ? 'results/view.php' : 'test/start.php');
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
                            exit;
                        }
                        redirect($redirectUrl);
                    } else {
                        $error = 'Foydalanuvchi topilmadi!';
                    }
                } else {
                    $error = 'Noto\'g\'ri tasdiqlash kodi!';
                }
                
                if ($isAjax && $error) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit;
                }
            }
        } elseif ($login_type === 'telegram') {
            $telegram_id = sanitize($_POST['telegram_id'] ?? '');
            $stmt = $db->prepare("SELECT * FROM users WHERE telegram_id = ?");
            $stmt->execute([$telegram_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                redirect(BASE_URL . ($user['test_completed'] ? 'results/view.php' : 'test/start.php'));
            } else {
                $error = 'Foydalanuvchi topilmadi!';
            }
        } elseif ($login_type === 'google') {
            $google_id = sanitize($_POST['google_id'] ?? '');
            $stmt = $db->prepare("SELECT * FROM users WHERE google_id = ?");
            $stmt->execute([$google_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                redirect(BASE_URL . ($user['test_completed'] ? 'results/view.php' : 'test/start.php'));
            } else {
                $error = 'Foydalanuvchi topilmadi! Ro\'yxatdan o\'ting.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Xatolik yuz berdi: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirish - Kasb Tanlash Tizimi</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>Kirish</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <input type="hidden" name="login_type" id="login_type" value="phone">
                
                <div class="login-type-selector">
                    <button type="button" class="login-btn active" data-type="phone">📱 Telefon</button>
                    <button type="button" class="login-btn" data-type="telegram">✈️ Telegram</button>
                    <button type="button" class="login-btn" data-type="google">🔵 Google</button>
                </div>
                
                <div id="phone-section" class="login-section">
                    <div class="form-group">
                        <label>Telefon raqam</label>
                        <input type="tel" name="phone" placeholder="+998901234567" required>
                    </div>
                    <div class="form-group" id="verification-group" style="display:none;">
                        <label>Tasdiqlash kodi</label>
                        <input type="text" name="verification_code" placeholder="4 xonali kod" maxlength="4" pattern="[0-9]{4}">
                        <small class="text-muted">Telefoningizga yuborilgan kodni kiriting</small>
                    </div>
                </div>
                
                <div id="telegram-section" class="login-section" style="display:none;">
                    <div class="form-group">
                        <label>Telegram ID</label>
                        <input type="text" name="telegram_id" placeholder="@username yoki ID">
                    </div>
                </div>
                
                <div id="google-section" class="login-section" style="display:none;">
                    <div class="form-group">
                        <button type="button" id="google-login-btn" class="btn-google">
                            🔵 Google orqali kirish
                        </button>
                        <input type="hidden" name="google_id" id="google_id">
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">Kirish</button>
            </form>
            
            <p class="text-center">
                Ro'yxatdan o'tmaganmisiz? <a href="register.php">Ro'yxatdan o'tish</a>
            </p>
        </div>
    </div>
    
    <input type="hidden" id="google_client_id" value="<?= GOOGLE_CLIENT_ID ?>">
    <script src="<?= ASSETS_PATH ?>js/login.js"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</body>
</html>

