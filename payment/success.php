<?php
require_once '../config/config.php';
requireLogin();

$order_id = $_GET['order_id'] ?? '';

if (empty($order_id)) {
    redirect(BASE_URL . 'payment/index.php');
}

require_once '../includes/payment.php';
$paymentHelper = new PaymentHelper();

$payment = $paymentHelper->getPayment($order_id);

if (!$payment || $payment['user_id'] != $_SESSION['user_id']) {
    redirect(BASE_URL . 'payment/index.php');
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To'lov muvaffaqiyatli - Kasb Tanlash Tizimi</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
</head>
<body>
    <div class="container">
        <div class="payment-success">
            <?php if ($payment['payment_status'] === 'completed'): ?>
                <div class="success-icon">✅</div>
                <h1>To'lov muvaffaqiyatli amalga oshirildi!</h1>
                <p>Testni boshlash uchun quyidagi tugmani bosing.</p>
                <a href="<?= BASE_URL ?>test/start.php" class="btn-primary btn-large">Testni boshlash</a>
            <?php else: ?>
                <div class="error-icon">❌</div>
                <h1>To'lov muvaffaqiyatsiz</h1>
                <p>To'lovda xatolik yuz berdi. Iltimos, qayta urinib ko'ring.</p>
                <a href="<?= BASE_URL ?>payment/index.php" class="btn-primary btn-large">Qayta urinish</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

