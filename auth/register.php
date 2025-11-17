<?php
require_once '../config/config.php';

$error = '';
$success = '';

// Telegram callback dan kelgan xatolarni tekshirish
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_telegram_auth':
            $error = 'Telegram autentifikatsiya xatosi!';
            break;
        case 'telegram_auth_expired':
            $error = 'Telegram autentifikatsiya muddati tugagan!';
            break;
        case 'db_error':
            $error = 'Ma\'lumotlar bazasi xatosi!';
            break;
        case 'invalid_data':
            $error = 'Noto\'g\'ri ma\'lumotlar!';
            break;
        case 'google_auth_failed':
            $error = 'Google autentifikatsiya xatosi!';
            break;
        case 'invalid_google_token':
            $error = 'Noto\'g\'ri Google token!';
            break;
        case 'invalid_google_data':
            $error = 'Noto\'g\'ri Google ma\'lumotlari!';
            break;
    }
}

// Telegram orqali kelgan ma'lumotlarni tekshirish
$telegram_mode = isset($_GET['telegram']) || isset($_SESSION['telegram_auth_data']);

// Google orqali kelgan ma'lumotlarni tekshirish
$google_mode = isset($_GET['google']) || isset($_SESSION['google_auth_data']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_type = sanitize($_POST['login_type'] ?? '');
    $full_name = sanitize($_POST['full_name'] ?? '');
    $class_number = intval($_POST['class_number'] ?? 0);
    $school_name = sanitize($_POST['school_name'] ?? '');
    $exam_date = sanitize($_POST['exam_date'] ?? '');

    // Telegram rejimida faqat class_number, school_name va exam_date kerak
    if ($login_type === 'telegram' && isset($_SESSION['telegram_auth_data'])) {
        if (empty($class_number) || empty($school_name)) {
            $error = 'Barcha maydonlarni to\'ldiring!';
        } else {
            try {
                $db = getDB();
                $telegram_data = $_SESSION['telegram_auth_data'];
                $telegram_id = $telegram_data['id'];
                $telegram_full_name = $_SESSION['telegram_full_name'] ?? $full_name;
                
                // Foydalanuvchini topish
                $stmt = $db->prepare("SELECT * FROM users WHERE telegram_id = ?");
                $stmt->execute([$telegram_id]);
                $existing_user = $stmt->fetch();
                
                if ($existing_user) {
                    // Foydalanuvchi mavjud, login qilish
                    $_SESSION['user_id'] = $existing_user['id'];
                    unset($_SESSION['telegram_auth_data']);
                    unset($_SESSION['telegram_full_name']);
                    unset($_SESSION['telegram_username']);
                    
                    if ($existing_user['test_completed']) {
                        redirect(BASE_URL . 'results/view.php');
                    } else {
                        redirect(BASE_URL . 'payment/index.php');
                    }
                } else {
                    // Yangi foydalanuvchi yaratish
                    $stmt = $db->prepare("INSERT INTO users (telegram_id, full_name, class_number, school_name, login_type, exam_date) 
                                         VALUES (?, ?, ?, ?, 'telegram', ?)");
                    $stmt->execute([$telegram_id, $telegram_full_name, $class_number, $school_name, $exam_date]);
                    
                    $_SESSION['user_id'] = $db->lastInsertId();
                    unset($_SESSION['telegram_auth_data']);
                    unset($_SESSION['telegram_full_name']);
                    redirect(BASE_URL . 'payment/index.php');
                }
            } catch (PDOException $e) {
                $error = 'Xatolik yuz berdi: ' . $e->getMessage();
            }
        }
    } elseif (empty($full_name) || empty($class_number) || empty($school_name)) {
        $error = 'Barcha maydonlarni to\'ldiring!';
    } else {
        try {
            $db = getDB();
            
            if ($login_type === 'phone') {
                $phone = sanitize($_POST['phone'] ?? '');
                $verification_code = sanitize($_POST['verification_code'] ?? '');
                
                if (empty($phone)) {
                    $error = 'Telefon raqamni kiriting!';
                } elseif (empty($verification_code)) {
                    // SMS kod yuborish
                    $code = rand(1000, 9999);
                    $_SESSION['phone_verification_code'] = $code;
                    $_SESSION['phone_verification_number'] = $phone;
                    $_SESSION['phone_verification_time'] = time();
                    // Bu yerda SMS API chaqiriladi
                    // TODO: SMS API integratsiya qiling
                    $success = 'Tasdiqlash kodi yuborildi: ' . $code; // Test uchun - production da o'chirish kerak
                } else {
                    // Kodni tekshirish (5 daqiqa ichida)
                    if (isset($_SESSION['phone_verification_code']) && 
                        isset($_SESSION['phone_verification_time']) &&
                        $_SESSION['phone_verification_code'] == $verification_code &&
                        $_SESSION['phone_verification_number'] == $phone &&
                        (time() - $_SESSION['phone_verification_time']) < 300) {
                        
                        $stmt = $db->prepare("INSERT INTO users (phone, full_name, class_number, school_name, login_type, exam_date) 
                                             VALUES (?, ?, ?, ?, 'phone', ?)");
                        $stmt->execute([$phone, $full_name, $class_number, $school_name, $exam_date]);
                        
                        $_SESSION['user_id'] = $db->lastInsertId();
                        unset($_SESSION['phone_verification_code']);
                        redirect(BASE_URL . 'payment/index.php');
                    } else {
                        $error = 'Noto\'g\'ri tasdiqlash kodi!';
                    }
                }
            } elseif ($login_type === 'telegram') {
                $error = 'Telegram orqali avval autentifikatsiya qiling!';
            } elseif ($login_type === 'google') {
                // Google rejimida faqat class_number, school_name va exam_date kerak
                if (isset($_SESSION['google_auth_data'])) {
                    if (empty($class_number) || empty($school_name)) {
                        $error = 'Barcha maydonlarni to\'ldiring!';
                    } else {
                        $google_data = $_SESSION['google_auth_data'];
                        $google_id = $google_data['id'];
                        $email = $google_data['email'] ?? '';
                        $google_full_name = $_SESSION['google_full_name'] ?? $full_name;
                        
                        // Foydalanuvchini topish - google_id yoki email orqali
                        $stmt = $db->prepare("SELECT * FROM users WHERE google_id = ? OR email = ?");
                        $stmt->execute([$google_id, $email]);
                        $existing_user = $stmt->fetch();
                        
                        if ($existing_user) {
                            // Foydalanuvchi mavjud, google_id ni yangilash va login qilish
                            if (empty($existing_user['google_id'])) {
                                $stmt = $db->prepare("UPDATE users SET google_id = ? WHERE id = ?");
                                $stmt->execute([$google_id, $existing_user['id']]);
                            }
                            
                            $_SESSION['user_id'] = $existing_user['id'];
                            unset($_SESSION['google_auth_data']);
                            unset($_SESSION['google_full_name']);
                            
                            if ($existing_user['test_completed']) {
                                redirect(BASE_URL . 'results/view.php');
                            } else {
                                redirect(BASE_URL . 'payment/index.php');
                            }
                        } else {
                            // Yangi foydalanuvchi yaratish
                            $stmt = $db->prepare("INSERT INTO users (google_id, email, full_name, class_number, school_name, login_type, exam_date) 
                                                 VALUES (?, ?, ?, ?, ?, 'google', ?)");
                            $stmt->execute([$google_id, $email, $google_full_name, $class_number, $school_name, $exam_date]);
                            
                            $_SESSION['user_id'] = $db->lastInsertId();
                            unset($_SESSION['google_auth_data']);
                            unset($_SESSION['google_full_name']);
                            redirect(BASE_URL . 'payment/index.php');
                        }
                    }
                } else {
                    $error = 'Google orqali avval autentifikatsiya qiling!';
                }
            }
        } catch (PDOException $e) {
            $error = 'Xatolik yuz berdi: ' . $e->getMessage();
        }
    }
}

// Imtihon kunlarini olish
$db = getDB();
$stmt = $db->query("SELECT * FROM exam_dates WHERE is_active = 1 AND exam_date > NOW() ORDER BY exam_date ASC");
$exam_dates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ro'yxatdan o'tish - Kasb Tanlash Tizimi</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
</head>
<body>
    <div class="container">
        <div class="register-box">
            <h1>Ro'yxatdan o'tish</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <input type="hidden" name="login_type" id="login_type" value="phone">
                
                <!-- Login type selector -->
                <div class="login-type-selector">
                    <button type="button" class="login-btn active" data-type="phone">
                        📱 Telefon
                    </button>
                    <button type="button" class="login-btn" data-type="telegram">
                        ✈️ Telegram
                    </button>
                    <button type="button" class="login-btn" data-type="google">
                        🔵 Google
                    </button>
                </div>
                
                <!-- Phone login -->
                <div id="phone-section" class="login-section">
                    <div class="form-group">
                        <label>Telefon raqam</label>
                        <input type="tel" name="phone" placeholder="+998901234567" required>
                    </div>
                    <div class="form-group" id="verification-group" style="display:none;">
                        <label>Tasdiqlash kodi</label>
                        <input type="text" name="verification_code" placeholder="4 xonali kod" maxlength="4" pattern="[0-9]{4}">
                        <small class="text-muted">Telefoningizga yuborilgan kodni kiriting</small>
                        <button type="button" id="resend-code" class="btn-link">Kodni qayta yuborish</button>
                    </div>
                </div>
                
                <!-- Telegram login -->
                <div id="telegram-section" class="login-section" style="display:none;">
                    <div class="form-group">
                        <p style="text-align: center; margin-bottom: 20px; color: #666;">
                            Telegram orqali kirish uchun quyidagi tugmani bosing:
                        </p>
                        <div style="text-align: center; margin-bottom: 15px;">
                            <a href="https://t.me/<?= TELEGRAM_BOT_USERNAME ?>?start=register" 
                               target="_blank" 
                               class="btn-primary" 
                               style="display: inline-block; text-decoration: none; padding: 12px 24px; border-radius: 8px;">
                                ✈️ Telegram Bot orqali kirish
                            </a>
                        </div>
                        <p style="text-align: center; font-size: 12px; color: #999; margin-top: 10px;">
                            Yoki quyidagi Telegram Login Widget orqali:
                        </p>
                        <div style="text-align: center; margin: 20px 0;">
                            <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                    data-telegram-login="<?= TELEGRAM_BOT_USERNAME ?>" 
                                    data-size="large" 
                                    data-onauth="onTelegramAuth(user)" 
                                    data-request-access="write"
                                    data-userpic="true"
                                    data-auth-url="<?= BASE_URL ?>auth/telegram_callback.php"></script>
                        </div>
                        <div id="telegram-user-info" style="display:none; margin-top: 15px; padding: 15px; background: #f0f0f0; border-radius: 8px;">
                            <p><strong>Telegram orqali kirildi!</strong></p>
                            <p id="telegram-user-name"></p>
                        </div>
                        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 12px; margin-top: 15px; font-size: 12px; color: #856404;">
                            <strong>Eslatma:</strong> Agar "Bot domain invalid" xatosi chiqsa, BotFather orqali bot sozlamalarida domain qo'shing: <code>profiorientation.uz</code>
                        </div>
                    </div>
                </div>
                
                <!-- Google login -->
                <div id="google-section" class="login-section" style="display:none;">
                    <div class="form-group">
                        <p style="text-align: center; margin-bottom: 20px; color: #666;">
                            Google orqali kirish uchun quyidagi tugmani bosing:
                        </p>
                        <div style="text-align: center; margin: 20px 0;">
                            <div id="g_id_onload"
                                 data-client_id="<?= GOOGLE_CLIENT_ID ?>"
                                 data-callback="onGoogleSignIn"
                                 data-auto_prompt="false">
                            </div>
                            <div class="g_id_signin"
                                 data-type="standard"
                                 data-size="large"
                                 data-theme="outline"
                                 data-text="sign_in_with"
                                 data-shape="rectangular"
                                 data-logo_alignment="left">
                            </div>
                        </div>
                        <input type="hidden" name="google_id" id="google_id">
                        <input type="hidden" name="email" id="google_email">
                        <div id="google-user-info" style="display:none; margin-top: 15px; padding: 15px; background: #f0f0f0; border-radius: 8px;">
                            <p><strong>Google orqali kirildi!</strong></p>
                            <p id="google-user-name"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Common fields - Telegram va Google rejimida yashiriladi -->
                <div id="common-fields">
                    <div class="form-group" id="full-name-group">
                        <label>To'liq ism</label>
                        <input type="text" name="full_name" id="full_name_input" 
                               value="<?= htmlspecialchars(($_SESSION['telegram_full_name'] ?? $_SESSION['google_full_name'] ?? '')) ?>" 
                               <?= ($telegram_mode || $google_mode) ? 'readonly' : 'required' ?>>
                    </div>
                    
                    <div class="form-group">
                        <label>Sinf</label>
                        <select name="class_number" required>
                            <option value="">Tanlang</option>
                            <option value="10">10-sinf</option>
                            <option value="11">11-sinf</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Maktab nomi</label>
                        <input type="text" name="school_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Imtihon sanasi</label>
                        <select name="exam_date" required>
                            <option value="">Tanlang</option>
                            <?php foreach ($exam_dates as $exam): ?>
                                <option value="<?= $exam['exam_date'] ?>">
                                    <?= date('d.m.Y H:i', strtotime($exam['exam_date'])) ?> 
                                    (<?= $exam['current_participants'] ?>/<?= $exam['max_participants'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">Ro'yxatdan o'tish</button>
            </form>
            
            <p class="text-center" id="login-link" style="<?= ($telegram_mode || $google_mode) ? 'display:none;' : '' ?>">
                Allaqachon ro'yxatdan o'tganmisiz? <a href="login.php">Kirish</a>
            </p>
        </div>
    </div>
    
    <input type="hidden" id="google_client_id" value="<?= GOOGLE_CLIENT_ID ?>">
    <script src="<?= ASSETS_PATH ?>js/register.js"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        // Telegram Login Widget callback
        function onTelegramAuth(user) {
            // Telegram ma'lumotlarini serverga yuborish
            const callbackUrl = '<?= BASE_URL ?>auth/telegram_callback.php';
            const params = new URLSearchParams(user);
            window.location.href = callbackUrl + '?' + params.toString();
        }
        
        // Google Sign-In callback
        function onGoogleSignIn(response) {
            // JWT credential'ni serverga yuborish
            const credential = response.credential;
            const callbackUrl = '<?= BASE_URL ?>auth/google_callback.php';
            window.location.href = callbackUrl + '?credential=' + encodeURIComponent(credential);
        }
        
        // Google Identity Services'ni initialize qilish
        window.addEventListener('load', function() {
            if (typeof google !== 'undefined' && google.accounts) {
                google.accounts.id.initialize({
                    client_id: '<?= GOOGLE_CLIENT_ID ?>',
                    callback: onGoogleSignIn
                });
            }
        });
        
        // Telegram va Google rejimida common fields yashirish
        document.addEventListener('DOMContentLoaded', function() {
            const telegramMode = <?= $telegram_mode ? 'true' : 'false' ?>;
            const googleMode = <?= $google_mode ? 'true' : 'false' ?>;
            const loginTypeInput = document.getElementById('login_type');
            
            if (telegramMode) {
                // Telegram rejimida
                loginTypeInput.value = 'telegram';
                document.getElementById('phone-section').style.display = 'none';
                document.getElementById('telegram-section').style.display = 'block';
                document.getElementById('google-section').style.display = 'none';
                document.getElementById('full-name-group').style.display = 'none';
                document.getElementById('login-link').style.display = 'none';
                
                // Login type selector buttonlarni yangilash
                document.querySelectorAll('.login-btn').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.dataset.type === 'telegram') {
                        btn.classList.add('active');
                    }
                });
            } else if (googleMode) {
                // Google rejimida
                loginTypeInput.value = 'google';
                document.getElementById('phone-section').style.display = 'none';
                document.getElementById('telegram-section').style.display = 'none';
                document.getElementById('google-section').style.display = 'block';
                document.getElementById('full-name-group').style.display = 'block';
                document.getElementById('login-link').style.display = 'none';
                
                // Login type selector buttonlarni yangilash
                document.querySelectorAll('.login-btn').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.dataset.type === 'google') {
                        btn.classList.add('active');
                    }
                });
            }
        });
    </script>
</body>
</html>

