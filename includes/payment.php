<?php
// To'lov helper funksiyalari

require_once __DIR__ . '/../config/config.php';

class PaymentHelper {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // To'lov sozlamalarini olish
    public function getPaymentSettings() {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM payment_settings");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $settings;
    }
    
    // Test narxini olish
    public function getTestPrice() {
        $stmt = $this->db->query("SELECT setting_value FROM admin_settings WHERE setting_key = 'test_price'");
        $price = $stmt->fetchColumn();
        return floatval($price ?: 1900000);
    }
    
    // Chegirmali narxni olish
    public function getDiscountPrice() {
        $stmt = $this->db->query("SELECT setting_value FROM admin_settings WHERE setting_key = 'discount_price'");
        $price = $stmt->fetchColumn();
        return floatval($price ?: 950000);
    }
    
    // Chegirma limitini olish
    public function getDiscountLimit() {
        $stmt = $this->db->query("SELECT setting_value FROM admin_settings WHERE setting_key = 'discount_limit'");
        $limit = $stmt->fetchColumn();
        return intval($limit ?: 100);
    }
    
    // Chegirma mavjudligini tekshirish
    public function isDiscountAvailable() {
        $limit = $this->getDiscountLimit();
        $stmt = $this->db->query("SELECT COUNT(*) FROM payments WHERE payment_status = 'completed'");
        $paid_count = $stmt->fetchColumn();
        return $paid_count < $limit;
    }
    
    // To'lov narxini olish (chegirma bilan)
    public function getPaymentAmount() {
        if ($this->isDiscountAvailable()) {
            return $this->getDiscountPrice();
        }
        return $this->getTestPrice();
    }
    
    // Order ID yaratish
    public function generateOrderId($user_id) {
        return 'KT-' . $user_id . '-' . time() . '-' . rand(1000, 9999);
    }
    
    // To'lov yaratish
    public function createPayment($user_id, $payment_method, $amount = null) {
        if ($amount === null) {
            $amount = $this->getPaymentAmount();
        }
        
        $order_id = $this->generateOrderId($user_id);
        
        $stmt = $this->db->prepare("INSERT INTO payments (user_id, order_id, amount, payment_method, payment_status) 
                                    VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $order_id, $amount, $payment_method]);
        
        return [
            'payment_id' => $this->db->lastInsertId(),
            'order_id' => $order_id,
            'amount' => $amount
        ];
    }
    
    // To'lovni yangilash
    public function updatePayment($order_id, $status, $transaction_id = null, $payment_data = null) {
        $paid_at = ($status === 'completed') ? date('Y-m-d H:i:s') : null;
        
        $stmt = $this->db->prepare("UPDATE payments 
                                    SET payment_status = ?, transaction_id = ?, payment_data = ?, paid_at = ? 
                                    WHERE order_id = ?");
        $stmt->execute([$status, $transaction_id, $payment_data, $paid_at, $order_id]);
        
        // Agar to'lov muvaffaqiyatli bo'lsa, foydalanuvchiga test ruxsatini berish
        if ($status === 'completed') {
            $this->grantTestAccess($order_id);
        }
    }
    
    // Test ruxsatini berish
    private function grantTestAccess($order_id) {
        $stmt = $this->db->prepare("SELECT user_id FROM payments WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $payment = $stmt->fetch();
        
        if ($payment) {
            // Bu yerda foydalanuvchiga test ruxsatini berish logikasi
            // Masalan, users jadvalida test_allowed maydoni bo'lishi mumkin
        }
    }
    
    // To'lov ma'lumotlarini olish
    public function getPayment($order_id) {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch();
    }
    
    // Foydalanuvchining to'lovlarini olish
    public function getUserPayments($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}

// Payme integratsiyasi
class PaymePayment {
    private $merchant_id;
    private $secret_key;
    private $test_mode;
    private $api_url;
    
    public function __construct($settings = null) {
        // Agar settings berilgan bo'lsa, undan olish, aks holda .env dan
        if ($settings) {
            $this->merchant_id = $settings['payme_merchant_id'] ?? '';
            $this->secret_key = $settings['payme_secret_key'] ?? '';
            $this->test_mode = ($settings['payme_test_mode'] ?? '1') === '1';
        } else {
            $this->merchant_id = defined('PAYME_MERCHANT_ID') ? PAYME_MERCHANT_ID : '';
            $this->secret_key = defined('PAYME_SECRET_KEY') ? PAYME_SECRET_KEY : '';
            $this->test_mode = (defined('PAYME_TEST_MODE') ? PAYME_TEST_MODE : '1') === '1';
        }
        $this->api_url = $this->test_mode 
            ? 'https://checkout.test.paycom.uz/api'
            : 'https://checkout.paycom.uz/api';
    }
    
    // Payme to'lov yaratish
    public function createInvoice($order_id, $amount, $return_url) {
        $params = [
            'merchant' => $this->merchant_id,
            'amount' => $amount * 100, // Payme tiynlarda ishlaydi
            'account' => [
                'order_id' => $order_id
            ],
            'callback' => BASE_URL . 'payment/payme_callback.php',
            'callback_timeout' => 86400000,
            'return_url' => $return_url
        ];
        
        $data = base64_encode(json_encode($params));
        $signature = hash_hmac('sha256', $data, $this->secret_key);
        
        return [
            'url' => $this->api_url . '/create',
            'data' => $data,
            'signature' => $signature
        ];
    }
    
    // Payme callback tekshirish
    public function verifyCallback($data, $signature) {
        $expected_signature = hash_hmac('sha256', $data, $this->secret_key);
        return hash_equals($expected_signature, $signature);
    }
}

// Click integratsiyasi
class ClickPayment {
    private $merchant_id;
    private $service_id;
    private $secret_key;
    private $test_mode;
    private $api_url;
    
    public function __construct($settings = null) {
        // Agar settings berilgan bo'lsa, undan olish, aks holda .env dan
        if ($settings) {
            $this->merchant_id = $settings['click_merchant_id'] ?? '';
            $this->service_id = $settings['click_service_id'] ?? '';
            $this->secret_key = $settings['click_secret_key'] ?? '';
            $this->test_mode = ($settings['click_test_mode'] ?? '1') === '1';
        } else {
            $this->merchant_id = defined('CLICK_MERCHANT_ID') ? CLICK_MERCHANT_ID : '';
            $this->service_id = defined('CLICK_SERVICE_ID') ? CLICK_SERVICE_ID : '';
            $this->secret_key = defined('CLICK_SECRET_KEY') ? CLICK_SECRET_KEY : '';
            $this->test_mode = (defined('CLICK_TEST_MODE') ? CLICK_TEST_MODE : '1') === '1';
        }
        $this->api_url = $this->test_mode 
            ? 'https://test.click.uz/services/pay'
            : 'https://click.uz/services/pay';
    }
    
    // Click to'lov yaratish
    public function createInvoice($order_id, $amount, $return_url) {
        $params = [
            'merchant_id' => $this->merchant_id,
            'service_id' => $this->service_id,
            'amount' => $amount,
            'transaction_param' => $order_id,
            'return_url' => $return_url,
            'callback_url' => BASE_URL . 'payment/click_callback.php'
        ];
        
        // Click signature yaratish
        $sign_string = $params['merchant_id'] . $params['service_id'] . 
                      $params['amount'] . $params['transaction_param'] . 
                      $this->secret_key;
        $params['sign_time'] = time();
        $params['sign_string'] = md5($sign_string . $params['sign_time']);
        
        return [
            'url' => $this->api_url,
            'params' => $params
        ];
    }
    
    // Click callback tekshirish
    public function verifyCallback($data) {
        $sign_string = $data['merchant_id'] . $data['service_id'] . 
                      $data['amount'] . $data['transaction_param'] . 
                      $this->secret_key;
        $expected_signature = md5($sign_string . $data['sign_time']);
        return hash_equals($expected_signature, $data['sign_string']);
    }
}
?>

