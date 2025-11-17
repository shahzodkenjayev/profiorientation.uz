<?php
/**
 * Gemini API mavjud modellarni ko'rish
 * Qaysi modellar ishlayotganini aniqlash uchun
 */

require_once 'config/config.php';
require_once 'includes/gemini.php';

echo "<h2>Gemini API Mavjud Modellar</h2>\n";
echo "<pre>\n";

try {
    $gemini = new GeminiAI();
    
    echo "API Key tekshiruvi...\n";
    echo str_repeat("-", 60) . "\n";
    
    $api_key = env('GEMINI_API_KEY', '');
    if (empty($api_key)) {
        echo "❌ API Key topilmadi!\n";
        exit;
    }
    
    echo "✅ API Key topildi\n";
    echo "API Key uzunligi: " . strlen($api_key) . " belgi\n";
    echo "Oxirgi 5 belgi: ..." . substr($api_key, -5) . "\n\n";
    
    echo "Mavjud modellarni olish...\n";
    echo str_repeat("-", 60) . "\n";
    
    $models = $gemini->listAvailableModels();
    
    if (empty($models)) {
        echo "❌ Modellar topilmadi yoki API Key noto'g'ri!\n";
        echo "\nQanday tuzatish:\n";
        echo "1. API Key ni tekshiring: https://aistudio.google.com/app/apikey\n";
        echo "2. Yangi API Key yarating\n";
        echo "3. .env faylga qo'shing\n";
        exit;
    }
    
    echo "✅ " . count($models) . " ta model topildi\n\n";
    
    echo "Mavjud modellar (generateContent qo'llab-quvvatlaydigan):\n";
    echo str_repeat("=", 60) . "\n";
    
    $supported_models = [];
    foreach ($models as $model) {
        $model_name = $model['name'] ?? '';
        $display_name = $model['displayName'] ?? $model_name;
        $supported_methods = $model['supportedGenerationMethods'] ?? [];
        
        if (in_array('generateContent', $supported_methods)) {
            $short_name = str_replace('models/', '', $model_name);
            $supported_models[] = $short_name;
            
            echo "✅ $short_name\n";
            echo "   To'liq nom: $model_name\n";
            echo "   Ko'rinadigan nom: $display_name\n";
            echo "   Qo'llab-quvvatlanadigan metodlar: " . implode(', ', $supported_methods) . "\n";
            echo "\n";
        }
    }
    
    if (empty($supported_models)) {
        echo "❌ generateContent metodini qo'llab-quvvatlaydigan model topilmadi!\n";
    } else {
        echo str_repeat("=", 60) . "\n";
        echo "Tavsiya etiladigan modellar:\n";
        foreach ($supported_models as $model) {
            if (strpos($model, 'gemini-1.5-flash') !== false) {
                echo "⭐ $model (Tavsiya etiladi - tez va bepul)\n";
            } elseif (strpos($model, 'gemini-1.5-pro') !== false) {
                echo "⭐ $model (Tavsiya etiladi - yuqori sifat)\n";
            } else {
                echo "   $model\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Keyingi qadamlar:\n";
        echo "1. includes/gemini.php faylida api_model ni o'zgartiring\n";
        echo "2. Yoki avtomatik ravishda birinchi mavjud model ishlatiladi\n";
    }
    
} catch (Exception $e) {
    echo "❌ Xatolik: " . $e->getMessage() . "\n";
    echo "\nQanday tuzatish:\n";
    echo "1. API Key ni tekshiring\n";
    echo "2. Internet ulanishini tekshiring\n";
    echo "3. Server loglarini ko'ring\n";
}

echo "</pre>\n";

