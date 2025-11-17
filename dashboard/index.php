<?php
require_once '../config/config.php';
requireLogin();

$db = getDB();

// Foydalanuvchi ma'lumotlarini olish
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    redirect(BASE_URL . 'auth/login.php');
}

// Agar test yakunlangan bo'lsa, natijalar sahifasiga yo'naltirish
if ($user['test_completed']) {
    redirect(BASE_URL . 'results/view.php');
}

// Agar foydalanuvchi imtihon kuni va turini tanlagan bo'lsa, to'lov sahifasiga yo'naltirish
if (!empty($user['exam_date']) && !empty($user['exam_type'])) {
    // To'lov tekshiruvi - agar to'lov qilingan bo'lsa, test sahifasiga
    require_once '../includes/payment.php';
    $paymentHelper = new PaymentHelper();
    $user_payments = $paymentHelper->getUserPayments($_SESSION['user_id']);
    $has_paid = false;
    foreach ($user_payments as $payment) {
        if ($payment['payment_status'] === 'completed') {
            $has_paid = true;
            break;
        }
    }
    
    if ($has_paid) {
        redirect(BASE_URL . 'test/start.php');
    } else {
        // To'lov qilinmagan, to'lov sahifasiga yo'naltirish
        redirect(BASE_URL . 'payment/index.php');
    }
}

$error = '';
$success = '';

// Form yuborilganda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_date_id = intval($_POST['exam_date_id'] ?? 0);
    $exam_type = sanitize($_POST['exam_type'] ?? '');
    
    if (empty($exam_date_id)) {
        $error = 'Imtihon kunini tanlang!';
    } elseif (empty($exam_type) || !in_array($exam_type, ['online', 'offline'])) {
        $error = 'Imtihon turini tanlang!';
    } else {
        try {
            // Imtihon kuni ma'lumotlarini olish
            $stmt = $db->prepare("SELECT * FROM exam_dates WHERE id = ? AND is_active = 1");
            $stmt->execute([$exam_date_id]);
            $exam_date = $stmt->fetch();
            
            if (!$exam_date) {
                $error = 'Tanlangan imtihon kuni mavjud emas yoki faol emas!';
            } else {
                // Foydalanuvchi ma'lumotlarini yangilash
                $stmt = $db->prepare("UPDATE users SET exam_date = ?, exam_type = ? WHERE id = ?");
                $stmt->execute([$exam_date['exam_date'], $exam_type, $_SESSION['user_id']]);
                
                // Imtihon kunidagi ishtirokchilar sonini oshirish
                $stmt = $db->prepare("UPDATE exam_dates SET current_participants = current_participants + 1 WHERE id = ?");
                $stmt->execute([$exam_date_id]);
                
                // To'lov sahifasiga yo'naltirish
                redirect(BASE_URL . 'payment/index.php');
            }
        } catch (PDOException $e) {
            $error = 'Xatolik yuz berdi: ' . $e->getMessage();
        }
    }
}

// Imtihon kunlarini olish
$stmt = $db->query("SELECT * FROM exam_dates WHERE is_active = 1 AND exam_date > NOW() ORDER BY exam_date ASC");
$exam_dates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kasb Tanlash Tizimi</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/homepage.css">
    <style>
        .dashboard-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 0 24px;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .dashboard-header h1 {
            font-size: 32px;
            color: #0a0a0a;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .dashboard-header p {
            color: #666;
            font-size: 16px;
        }
        
        .user-info-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
        
        .user-info-section h2 {
            font-size: 20px;
            color: #0a0a0a;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 16px;
            color: #0a0a0a;
            font-weight: 600;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h2 {
            font-size: 20px;
            color: #0a0a0a;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .exam-date-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .exam-date-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        
        .exam-date-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        
        .exam-date-card.selected {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .exam-date-card input[type="radio"] {
            display: none;
        }
        
        .exam-date-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .exam-date-day {
            font-size: 24px;
            font-weight: 700;
            color: #0a0a0a;
        }
        
        .exam-date-month {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
        }
        
        .exam-date-time {
            font-size: 16px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .exam-date-participants {
            font-size: 12px;
            color: #999;
        }
        
        .exam-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .exam-type-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            text-align: center;
        }
        
        .exam-type-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        
        .exam-type-card.selected {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .exam-type-card input[type="radio"] {
            display: none;
        }
        
        .exam-type-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
        
        .exam-type-name {
            font-size: 18px;
            font-weight: 600;
            color: #0a0a0a;
            margin-bottom: 8px;
        }
        
        .exam-type-desc {
            font-size: 14px;
            color: #666;
        }
        
        .btn-continue {
            width: 100%;
            padding: 16px;
            font-size: 18px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-continue:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                margin-top: 80px;
            }
            
            .dashboard-card {
                padding: 24px;
            }
            
            .exam-date-grid {
                grid-template-columns: 1fr;
            }
            
            .exam-type-selector {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header Navigation -->
    <header class="main-header">
        <div class="container">
            <nav class="header-nav">
                <div class="logo">
                    <a href="<?= BASE_URL ?>">Kasb Tanlash Tizimi</a>
                </div>
                <div class="header-buttons">
                    <?php include INCLUDES_PATH . 'language_switcher.php'; ?>
                    <a href="<?= BASE_URL ?>auth/login.php?logout=1" class="btn-header btn-login">Chiqish</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h1>Xush kelibsiz!</h1>
                <p>Imtihon ma'lumotlarini to'ldiring va to'lovni amalga oshiring</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <!-- Foydalanuvchi ma'lumotlari -->
            <div class="user-info-section">
                <h2>Ma'lumotlaringiz</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">To'liq ism</span>
                        <span class="info-value"><?= htmlspecialchars($user['full_name'] ?? 'Kiritilmagan') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Sinf</span>
                        <span class="info-value"><?= $user['class_number'] ? $user['class_number'] . '-sinf' : 'Kiritilmagan' ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Maktab</span>
                        <span class="info-value"><?= htmlspecialchars($user['school_name'] ?? 'Kiritilmagan') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kirish usuli</span>
                        <span class="info-value">
                            <?php
                            $login_types = ['phone' => 'Telefon', 'telegram' => 'Telegram', 'google' => 'Google'];
                            echo $login_types[$user['login_type']] ?? 'Noma\'lum';
                            ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <form method="POST" id="dashboardForm">
                <!-- Imtihon kuni va vaqti -->
                <div class="form-section">
                    <h2>Imtihon kuni va vaqti</h2>
                    <?php if (empty($exam_dates)): ?>
                        <div class="alert alert-error">
                            Hozircha mavjud imtihon kunlari yo'q. Iltimos, keyinroq qayta urinib ko'ring.
                        </div>
                    <?php else: ?>
                        <div class="exam-date-grid">
                            <?php foreach ($exam_dates as $exam): 
                                $exam_datetime = new DateTime($exam['exam_date']);
                                $day = $exam_datetime->format('d');
                                $month = $exam_datetime->format('M');
                                $time = $exam_datetime->format('H:i');
                                $date_formatted = $exam_datetime->format('d.m.Y');
                                $is_full = $exam['current_participants'] >= $exam['max_participants'];
                            ?>
                                <label class="exam-date-card <?= ($is_full ? 'disabled' : '') ?>" 
                                       style="<?= $is_full ? 'opacity: 0.5; cursor: not-allowed;' : '' ?>">
                                    <input type="radio" name="exam_date_id" value="<?= $exam['id'] ?>" 
                                           required <?= $is_full ? 'disabled' : '' ?>>
                                    <div class="exam-date-header">
                                        <div>
                                            <div class="exam-date-day"><?= $day ?></div>
                                            <div class="exam-date-month"><?= $month ?></div>
                                        </div>
                                    </div>
                                    <div class="exam-date-time"><?= $time ?></div>
                                    <div class="exam-date-participants">
                                        <?= $exam['current_participants'] ?>/<?= $exam['max_participants'] ?> ishtirokchi
                                        <?= $is_full ? ' (To\'liq)' : '' ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Imtihon turi (Online/Offline) -->
                <div class="form-section">
                    <h2>Imtihon turi</h2>
                    <div class="exam-type-selector">
                        <label class="exam-type-card">
                            <input type="radio" name="exam_type" value="online" required>
                            <div class="exam-type-icon">💻</div>
                            <div class="exam-type-name">Online</div>
                            <div class="exam-type-desc">Uydan imtihon topshirish</div>
                        </label>
                        
                        <label class="exam-type-card">
                            <input type="radio" name="exam_type" value="offline" required>
                            <div class="exam-type-icon">🏫</div>
                            <div class="exam-type-name">Offline</div>
                            <div class="exam-type-desc">Markazda imtihon topshirish</div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-continue" id="submitBtn" <?= empty($exam_dates) ? 'disabled' : '' ?>>
                    Davom etish - To'lov sahifasiga
                </button>
            </form>
        </div>
    </div>
    
    <script>
        // Radio button selection visual feedback
        document.querySelectorAll('.exam-date-card input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.exam-date-card').forEach(card => {
                    card.classList.remove('selected');
                });
                if (this.checked) {
                    this.closest('.exam-date-card').classList.add('selected');
                }
            });
        });
        
        document.querySelectorAll('.exam-type-card input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.exam-type-card').forEach(card => {
                    card.classList.remove('selected');
                });
                if (this.checked) {
                    this.closest('.exam-type-card').classList.add('selected');
                }
            });
        });
        
        // Form validation
        document.getElementById('dashboardForm').addEventListener('submit', function(e) {
            const examDate = document.querySelector('input[name="exam_date_id"]:checked');
            const examType = document.querySelector('input[name="exam_type"]:checked');
            
            if (!examDate || !examType) {
                e.preventDefault();
                alert('Iltimos, imtihon kunini va turini tanlang!');
                return false;
            }
        });
    </script>
</body>
</html>

