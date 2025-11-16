<?php
// .env faylini o'qish funksiyasi

if (!function_exists('loadEnv')) {
    function loadEnv($filePath) {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Commentlarni o'tkazib yuborish
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Key=Value formatini parse qilish
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Qo'shtirnoqlarni olib tashlash
                $value = trim($value, '"\'');
                
                // Environment variable'ni set qilish (agar mavjud bo'lmasa)
                if (!array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER)) {
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        
        return true;
    }
    
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        // Boolean qiymatlarni convert qilish
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }
        
        // Null qiymatlarni convert qilish
        if (strtolower($value) === 'null') {
            return null;
        }
        
        return $value;
    }
}

// .env faylini yuklash
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    loadEnv($envFile);
} else {
    // .env.example dan nusxa olish
    $envExample = __DIR__ . '/../.env.example';
    if (file_exists($envExample)) {
        copy($envExample, $envFile);
        loadEnv($envFile);
    }
}

