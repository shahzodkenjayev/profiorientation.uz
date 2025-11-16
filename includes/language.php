<?php
// Ko'p tilli tizim helper

class Language {
    private static $current_lang = 'uz';
    private static $translations = [];
    private static $available_langs = ['uz' => 'O\'zbek', 'ru' => 'Русский', 'en' => 'English', 'tr' => 'Türkçe'];
    
    public static function init() {
        // GET parametridan tilni birinchi tekshirish (ustunlik beradi)
        if (isset($_GET['lang'])) {
            $lang = trim($_GET['lang']);
            $lang = htmlspecialchars(strip_tags($lang));
            if (in_array($lang, array_keys(self::$available_langs))) {
                self::$current_lang = $lang;
                $_SESSION['language'] = $lang;
            }
        } elseif (isset($_SESSION['language'])) {
            // Session dan tilni olish
            $lang = $_SESSION['language'];
            if (in_array($lang, array_keys(self::$available_langs))) {
                self::$current_lang = $lang;
            }
        } else {
            // Browser tilini aniqlash
            $browser_lang = self::detectBrowserLanguage();
            if ($browser_lang && in_array($browser_lang, array_keys(self::$available_langs))) {
                self::$current_lang = $browser_lang;
                $_SESSION['language'] = $browser_lang;
            }
        }
        
        // Translation faylini yuklash
        self::loadTranslations();
    }
    
    private static function detectBrowserLanguage() {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return null;
        }
        
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $lang = strtolower(substr($langs[0], 0, 2));
        
        // Mapping
        $mapping = [
            'uz' => 'uz',
            'ru' => 'ru',
            'en' => 'en',
            'tr' => 'tr'
        ];
        
        return $mapping[$lang] ?? null;
    }
    
    private static function loadTranslations() {
        $lang_file = __DIR__ . '/../lang/' . self::$current_lang . '.php';
        if (file_exists($lang_file)) {
            self::$translations = require $lang_file;
        } else {
            // Default o'zbek tilini yuklash
            $default_file = __DIR__ . '/../lang/uz.php';
            if (file_exists($default_file)) {
                self::$translations = require $default_file;
                self::$current_lang = 'uz'; // Fallback to Uzbek
            }
        }
    }
    
    public static function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = self::$translations;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default !== null ? $default : $key;
            }
        }
        
        return $value;
    }
    
    public static function current() {
        return self::$current_lang;
    }
    
    public static function set($lang) {
        if (in_array($lang, array_keys(self::$available_langs))) {
            self::$current_lang = $lang;
            $_SESSION['language'] = $lang;
            self::loadTranslations();
        }
    }
    
    public static function getAvailableLanguages() {
        return self::$available_langs;
    }
    
    public static function getLanguageName($code) {
        return self::$available_langs[$code] ?? $code;
    }
}

// Helper funksiya
function __($key, $default = null) {
    return Language::get($key, $default);
}
?>

