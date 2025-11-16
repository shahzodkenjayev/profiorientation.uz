<?php
require_once '../config/config.php';
require_once '../includes/payment.php';

$paymentHelper = new PaymentHelper();
$settings = $paymentHelper->getPaymentSettings();
// Agar database'da bo'lmasa, .env dan olish
if (empty($settings['payme_merchant_id']) && defined('PAYME_MERCHANT_ID')) {
    $settings = null;
}
$payme = new PaymePayment($settings);

// Payme dan kelgan ma'lumotlarni olish
$data = $_POST['data'] ?? '';
$signature = $_POST['signature'] ?? '';

if (empty($data) || empty($signature)) {
    http_response_code(400);
    die('Invalid request');
}

// Signature tekshirish
if (!$payme->verifyCallback($data, $signature)) {
    http_response_code(401);
    die('Invalid signature');
}

// Ma'lumotlarni decode qilish
$params = json_decode(base64_decode($data), true);

if (!$params) {
    http_response_code(400);
    die('Invalid data');
}

$order_id = $params['account']['order_id'] ?? '';

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
$status = ($params['status'] ?? 0) == 2 ? 'completed' : 'failed';
$transaction_id = $params['transaction_id'] ?? '';

$paymentHelper->updatePayment(
    $order_id,
    $status,
    $transaction_id,
    json_encode($params)
);

// Response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'status' => $status
]);
?>

