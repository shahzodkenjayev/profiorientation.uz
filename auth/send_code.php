<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$phone = sanitize($_POST['phone'] ?? '');

if (empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Telefon raqamni kiriting!']);
    exit;
}

// Telefon raqam formatini tekshirish
if (!preg_match('/^\+998[0-9]{9}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Noto\'g\'ri telefon raqam formati! +998901234567 formatida kiriting.']);
    exit;
}

// SMS kod yuborish
$code = rand(1000, 9999);
$_SESSION['phone_verification_code'] = $code;
$_SESSION['phone_verification_number'] = $phone;
$_SESSION['phone_verification_time'] = time();

// TODO: SMS API integratsiya qiling
// Hozircha test uchun kodni qaytaramiz
// Production'da bu qismni o'chirish kerak

echo json_encode([
    'success' => true, 
    'message' => 'Tasdiqlash kodi yuborildi!',
    'code' => $code // Test uchun - production da o'chirish kerak
]);

