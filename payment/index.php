<?php
require_once '../config/config.php';
requireLogin();

require_once '../includes/payment.php';

$paymentHelper = new PaymentHelper();
$db = getDB();

// Foydalanuvchi ma'lumotlarini olish
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Foydalanuvchining to'lovlarini tekshirish
$user_payments = $paymentHelper->getUserPayments($_SESSION['user_id']);
$has_paid = false;
foreach ($user_payments as $payment) {
    if ($payment['payment_status'] === 'completed') {
        $has_paid = true;
        break;
    }
}

// Agar to'lov qilingan bo'lsa, test sahifasiga yo'naltirish
if ($has_paid) {
    if ($user['test_completed']) {
        redirect(BASE_URL . 'results/view.php');
    } else {
        redirect(BASE_URL . 'test/start.php');
    }
}

$error = '';
$success = '';

// To'lov yaratish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $payment_method = sanitize($_POST['payment_method']);
    
    if (!in_array($payment_method, ['payme', 'click', 'cash'])) {
        $error = 'Noto\'g\'ri to\'lov usuli!';
    } else {
        try {
            $amount = $paymentHelper->getPaymentAmount();
            $payment_data = $paymentHelper->createPayment($_SESSION['user_id'], $payment_method, $amount);
            
            if ($payment_method === 'payme') {
                // .env dan yoki database dan sozlamalarni olish
                $settings = $paymentHelper->getPaymentSettings();
                // Agar database'da bo'lmasa, .env dan olish
                if (empty($settings['payme_merchant_id']) && defined('PAYME_MERCHANT_ID')) {
                    $settings = null; // .env dan olish uchun
                }
                $payme = new PaymePayment($settings);
                $invoice = $payme->createInvoice(
                    $payment_data['order_id'],
                    $payment_data['amount'],
                    BASE_URL . 'payment/success.php'
                );
                
                // Payme formaga redirect
                $_SESSION['payme_data'] = $invoice;
                redirect(BASE_URL . 'payment/payme_form.php');
                
            } elseif ($payment_method === 'click') {
                // .env dan yoki database dan sozlamalarni olish
                $settings = $paymentHelper->getPaymentSettings();
                // Agar database'da bo'lmasa, .env dan olish
                if (empty($settings['click_merchant_id']) && defined('CLICK_MERCHANT_ID')) {
                    $settings = null; // .env dan olish uchun
                }
                $click = new ClickPayment($settings);
                $invoice = $click->createInvoice(
                    $payment_data['order_id'],
                    $payment_data['amount'],
                    BASE_URL . 'payment/success.php'
                );
                
                // Click formaga redirect
                $_SESSION['click_data'] = $invoice;
                redirect(BASE_URL . 'payment/click_form.php');
                
            } elseif ($payment_method === 'cash') {
                // Naqt to'lov - admin tasdiqlashi kerak
                $success = 'To\'lov so\'rovi yuborildi! Admin tomonidan tekshirilgandan keyin sizga xabar beramiz.';
            }
        } catch (PDOException $e) {
            $error = 'Xatolik yuz berdi: ' . $e->getMessage();
        }
    }
}

$amount = $paymentHelper->getPaymentAmount();
$is_discount = $paymentHelper->isDiscountAvailable();
$original_price = $paymentHelper->getTestPrice();
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To'lov - Kasb Tanlash Tizimi</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/payment.css">
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <h1>To'lov</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div class="payment-info">
                <div class="price-display">
                    <?php if ($is_discount): ?>
                        <div class="price-old"><?= number_format($original_price, 0, '.', ' ') ?> so'm</div>
                        <div class="price-new"><?= number_format($amount, 0, '.', ' ') ?> so'm</div>
                        <div class="discount-badge">50% chegirma</div>
                    <?php else: ?>
                        <div class="price-new"><?= number_format($amount, 0, '.', ' ') ?> so'm</div>
                    <?php endif; ?>
                </div>
                
                <p class="payment-description">
                    Testdan o'tish uchun to'lovni amalga oshiring. To'lovdan keyin siz testni boshlashingiz mumkin bo'ladi.
                </p>
            </div>
            
            <form method="POST" class="payment-form">
                <div class="payment-methods">
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="payme" required>
                        <div class="method-content">
                            <div class="method-icon">💳</div>
                            <div class="method-name">Payme</div>
                            <div class="method-desc">Karta orqali to'lov</div>
                        </div>
                    </label>
                    
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="click" required>
                        <div class="method-content">
                            <div class="method-icon">📱</div>
                            <div class="method-name">Click</div>
                            <div class="method-desc">Click orqali to'lov</div>
                        </div>
                    </label>
                    
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="cash" required>
                        <div class="method-content">
                            <div class="method-icon">💵</div>
                            <div class="method-name">Naqt</div>
                            <div class="method-desc">Naqt to'lov (admin tasdiqlashi kerak)</div>
                        </div>
                    </label>
                </div>
                
                <button type="submit" class="btn-primary btn-large">To'lovni amalga oshirish</button>
            </form>
            
            <div class="payment-note">
                <p><strong>Eslatma:</strong> Naqt to'lov holatida admin tomonidan tasdiqlangandan keyin testni boshlashingiz mumkin bo'ladi.</p>
            </div>
        </div>
    </div>
</body>
</html>

