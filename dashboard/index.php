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
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('nav.dashboard') ?> - <?= __('site.title') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/homepage.css">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 1000px;
            margin: 120px auto 50px;
            padding: 0 24px;
        }
        
        .dashboard-card {
            background: var(--white);
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            animation: slideUp 0.4s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 48px;
            position: relative;
        }
        
        .welcome-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--white);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.3);
        }
        
        .dashboard-header h1 {
            font-size: 36px;
            color: var(--dark);
            margin-bottom: 12px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        
        .dashboard-header p {
            color: var(--text-muted);
            font-size: 18px;
        }
        
        .user-info-section {
            background: linear-gradient(135deg, var(--bg-light) 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 40px;
            border: 1px solid var(--primary-light);
        }
        
        .user-info-section h2 {
            font-size: 22px;
            color: var(--dark);
            margin-bottom: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-info-section h2 i {
            color: var(--primary);
            font-size: 24px;
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
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-label i {
            font-size: 16px;
            color: var(--primary);
        }
        
        .info-value {
            font-size: 17px;
            color: var(--dark);
            font-weight: 600;
        }
        
        .form-section {
            margin-bottom: 40px;
        }
        
        .form-section h2 {
            font-size: 22px;
            color: var(--dark);
            margin-bottom: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .form-section h2 i {
            color: var(--primary);
            font-size: 24px;
        }
        
        .exam-date-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .exam-date-card {
            border: 2px solid var(--bg-light);
            border-radius: 16px;
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .exam-date-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .exam-date-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.2);
        }
        
        .exam-date-card:hover::before {
            transform: scaleX(1);
        }
        
        .exam-date-card.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, var(--primary-light) 0%, #f0f4ff 100%);
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.2);
        }
        
        .exam-date-card.selected::before {
            transform: scaleX(1);
        }
        
        .exam-date-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
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
            font-size: 32px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
        }
        
        .exam-date-month {
            font-size: 14px;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .exam-date-time {
            font-size: 18px;
            color: var(--primary);
            font-weight: 700;
            margin: 12px 0 8px;
        }
        
        .exam-date-participants {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .exam-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .exam-type-card {
            border: 2px solid var(--bg-light);
            border-radius: 16px;
            padding: 32px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--white);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .exam-type-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .exam-type-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.2);
        }
        
        .exam-type-card:hover::before {
            transform: scaleX(1);
        }
        
        .exam-type-card.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, var(--primary-light) 0%, #f0f4ff 100%);
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.2);
        }
        
        .exam-type-card.selected::before {
            transform: scaleX(1);
        }
        
        .exam-type-card input[type="radio"] {
            display: none;
        }
        
        .exam-type-icon {
            font-size: 56px;
            margin-bottom: 16px;
            color: var(--primary);
            display: flex;
            justify-content: center;
        }
        
        .exam-type-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .exam-type-desc {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.5;
        }
        
        .btn-continue {
            width: 100%;
            padding: 18px 24px;
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }
        
        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
        }
        
        .btn-continue:active {
            transform: translateY(0);
        }
        
        .btn-continue:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-continue i {
            font-size: 20px;
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
                    <a href="<?= BASE_URL ?>"><?= __('site.name') ?></a>
                </div>
                <div class="header-buttons">
                    <?php include INCLUDES_PATH . 'language_switcher.php'; ?>
                    <a href="<?= BASE_URL ?>auth/login.php?logout=1" class="btn-header btn-login">
                        <i class="ri-logout-box-line"></i> <?= __('nav.logout') ?>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <div class="welcome-icon">
                    <i class="ri-user-heart-line"></i>
                </div>
                <h1><?= __('dashboard.welcome') ?>, <?= htmlspecialchars(explode(' ', $user['full_name'] ?? 'Foydalanuvchi')[0]) ?>!</h1>
                <p><?= __('dashboard.subtitle') ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <!-- Foydalanuvchi ma'lumotlari -->
            <div class="user-info-section">
                <h2>
                    <i class="ri-user-settings-line"></i>
                    <?= __('dashboard.your_info') ?>
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">
                            <i class="ri-user-line"></i>
                            <?= __('auth.full_name') ?>
                        </span>
                        <span class="info-value"><?= htmlspecialchars($user['full_name'] ?? __('dashboard.not_provided')) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="ri-login-box-line"></i>
                            <?= __('dashboard.login_method') ?>
                        </span>
                        <span class="info-value">
                            <?php
                            $login_types = [
                                'phone' => __('auth.phone'),
                                'telegram' => __('auth.telegram'),
                                'google' => __('auth.google')
                            ];
                            echo $login_types[$user['login_type']] ?? __('dashboard.unknown');
                            ?>
                        </span>
                    </div>
                    <?php if (!empty($user['email'])): ?>
                    <div class="info-item">
                        <span class="info-label">
                            <i class="ri-mail-line"></i>
                            Email
                        </span>
                        <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="POST" id="dashboardForm">
                <!-- Imtihon kuni va vaqti -->
                <div class="form-section">
                    <h2>
                        <i class="ri-calendar-line"></i>
                        <?= __('dashboard.exam_date_time') ?>
                    </h2>
                    <?php if (empty($exam_dates)): ?>
                        <div class="alert alert-error">
                            <i class="ri-error-warning-line"></i>
                            <?= __('dashboard.no_exam_dates') ?>
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
                    <h2>
                        <i class="ri-book-open-line"></i>
                        <?= __('dashboard.exam_type') ?>
                    </h2>
                    <div class="exam-type-selector">
                        <label class="exam-type-card">
                            <input type="radio" name="exam_type" value="online" required>
                            <div class="exam-type-icon">
                                <i class="ri-computer-line"></i>
                            </div>
                            <div class="exam-type-name"><?= __('dashboard.online') ?></div>
                            <div class="exam-type-desc"><?= __('dashboard.online_desc') ?></div>
                        </label>
                        
                        <label class="exam-type-card">
                            <input type="radio" name="exam_type" value="offline" required>
                            <div class="exam-type-icon">
                                <i class="ri-building-line"></i>
                            </div>
                            <div class="exam-type-name"><?= __('dashboard.offline') ?></div>
                            <div class="exam-type-desc"><?= __('dashboard.offline_desc') ?></div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-continue" id="submitBtn" <?= empty($exam_dates) ? 'disabled' : '' ?>>
                    <i class="ri-arrow-right-line"></i>
                    <?= __('dashboard.continue_payment') ?>
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
                alert('<?= __('dashboard.please_select_exam') ?>');
                return false;
            }
        });
    </script>
</body>
</html>

