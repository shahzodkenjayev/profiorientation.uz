<?php
require_once '../config/config.php';
require_once '../includes/payment.php';

$paymentHelper = new PaymentHelper();
$settings = $paymentHelper->getPaymentSettings();
// Agar database'da bo'lmasa, .env dan olish
if (empty($settings['click_merchant_id']) && defined('CLICK_MERCHANT_ID')) {
    $settings = null;
}
$click = new ClickPayment($settings);

// Click dan kelgan ma'lumotlarni olish
$data = $_POST;

if (empty($data)) {
    http_response_code(400);
    die('Invalid request');
}

// Signature tekshirish
if (!$click->verifyCallback($data)) {
    http_response_code(401);
    die('Invalid signature');
}

$order_id = $data['transaction_param'] ?? '';

if (empty($order_id)) {
    http_response_code(400);
    die('Order ID not found');
}

// To'lovni tekshirish
$payment = $paymentHelper->getPayment($order_id);

if (!$payment) {
    http_response_code(404);
    die('Payment not found');
}

// To'lov holatini yangilash
$status = ($data['error'] ?? 0) == 0 ? 'completed' : 'failed';
$transaction_id = $data['click_trans_id'] ?? '';

$paymentHelper->updatePayment(
    $order_id,
    $status,
    $transaction_id,
    json_encode($data)
);

// Response
header('Content-Type: application/json');
echo json_encode([
    'error' => $status === 'completed' ? 0 : -1,
    'error_note' => $status === 'completed' ? 'Success' : 'Payment failed'
]);
?>

