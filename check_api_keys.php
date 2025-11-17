<?php
/**
 * Ikkita API Key ni tekshirish va solishtirish
 * Qaysi API Key ishlayotganini aniqlash uchun
 */

require_once 'config/config.php';

echo "<h2>API Key'lar Tekshiruvi</h2>\n";
echo "<pre>\n";

// .env fayldan API Key ni olish
$env_file = __DIR__ . '/.env';
$env_api_key = '';

if (file_exists($env_file)) {
    $env_content = file_get_contents($env_file);
    if (preg_match('/GEMINI_API_KEY\s*=\s*(.+)/', $env_content, $matches)) {
        $env_api_key = trim($matches[1]);
    }
}

// Config'dan API Key ni olish
$config_api_key = env('GEMINI_API_KEY', '');

echo "=== API Key'lar ===\n\n";

echo "1. .env fayldagi API Key:\n";
if (!empty($env_api_key)) {
    echo "   ✅ Topildi\n";
    echo "   Uzunligi: " . strlen($env_api_key) . " belgi\n";
    echo "   Birinchi 10 belgi: " . substr($env_api_key, 0, 10) . "...\n";
    echo "   Oxirgi 5 belgi: ..." . substr($env_api_key, -5) . "\n";
} else {
    echo "   ❌ Topilmadi\n";
}

echo "\n2. Config'dan olingan API Key:\n";
if (!empty($config_api_key)) {
    echo "   ✅ Topildi\n";
    echo "   Uzunligi: " . strlen($config_api_key) . " belgi\n";
    echo "   Birinchi 10 belgi: " . substr($config_api_key, 0, 10) . "...\n";
    echo "   Oxirgi 5 belgi: ..." . substr($config_api_key, -5) . "\n";
} else {
    echo "   ❌ Topilmadi\n";
}

echo "\n3. Solishtirish:\n";
if ($env_api_key === $config_api_key && !empty($env_api_key)) {
    echo "   ✅ Ikkala API Key bir xil\n";
} elseif (!empty($env_api_key) && !empty($config_api_key)) {
    echo "   ⚠️ API Key'lar farq qilmoqda!\n";
    echo "   Bu server ni qayta ishga tushirish kerakligini ko'rsatadi.\n";
} else {
    echo "   ❌ API Key topilmadi!\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "API Key'lar tekshiruvi:\n";
echo str_repeat("=", 60) . "\n\n";

// Test qilish
$test_keys = [];
if (!empty($env_api_key)) {
    $test_keys['.env fayldagi'] = $env_api_key;
}
if (!empty($config_api_key) && $config_api_key !== $env_api_key) {
    $test_keys['Config\'dagi'] = $config_api_key;
}

if (empty($test_keys)) {
    echo "❌ Test qilish uchun API Key topilmadi!\n";
    echo "\nQanday tuzatish:\n";
    echo "1. .env faylga qo'shing: GEMINI_API_KEY=your_api_key\n";
    echo "2. Yangi API Key (Nov 17, 2025): ...625M\n";
    echo "3. Eski API Key (Sep 24, 2025): ...OxfE (eski, yangisini ishlating!)\n";
    exit;
}

foreach ($test_keys as $source => $api_key) {
    echo "Testing: $source API Key\n";
    echo str_repeat("-", 60) . "\n";
    
    $api_key = trim($api_key);
    
    // API Key ni test qilish
    $test_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($api_key);
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Test']
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 10
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            echo "✅ ISHLAYAPTI!\n";
            echo "   Oxirgi 5 belgi: ..." . substr($api_key, -5) . "\n";
        } else {
            echo "⚠️ Javob berdi, lekin format noto'g'ri\n";
        }
    } else {
        $error_data = json_decode($response, true);
        $error_message = $error_data['error']['message'] ?? 'Noma\'lum xatolik';
        echo "❌ ISHLAMAYAPTI!\n";
        echo "   HTTP: $http_code\n";
        echo "   Xatolik: $error_message\n";
        echo "   Oxirgi 5 belgi: ..." . substr($api_key, -5) . "\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 60) . "\n";
echo "Tavsiya:\n";
echo "1. Yangi API Key ishlating (Nov 17, 2025): ...625M\n";
echo "2. Eski API Key ni o'chiring (Sep 24, 2025): ...OxfE\n";
echo "3. .env faylga yangi API Key ni qo'shing\n";
echo "4. Server ni qayta ishga tushiring\n";
echo "</pre>\n";

