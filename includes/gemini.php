<?php
// Google Gemini AI integratsiyasi
// Test savollarini generatsiya qilish uchun

class GeminiAI {
    private $api_key;
    private $api_model = 'gemini-1.5-flash'; // Model nomi
    private $api_version = 'v1beta'; // API versiyasi
    private $api_base_url = 'https://generativelanguage.googleapis.com/';
    
    public function __construct($api_key = null) {
        $this->api_key = $api_key ?? env('GEMINI_API_KEY', '');
        
        if (empty($this->api_key)) {
            throw new Exception('GEMINI_API_KEY topilmadi! .env faylga qo\'shing.');
        }
        
        // API Key formatini tekshirish
        if (strlen($this->api_key) < 20) {
            throw new Exception('GEMINI_API_KEY noto\'g\'ri formatda! API Key kamida 20 belgidan iborat bo\'lishi kerak.');
        }
        
        // API Key ni tozalash (bo'sh joylar, yangi qatorlar)
        $this->api_key = trim($this->api_key);
    }
    
    /**
     * Mavjud modellarni olish
     */
    public function listAvailableModels() {
        $url = $this->api_base_url . $this->api_version . '/models?key=' . urlencode($this->api_key);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
            if (isset($result['models'])) {
                return $result['models'];
            }
        }
        
        return [];
    }
    
    /**
     * API Key ni test qilish
     */
    public function testApiKey() {
        try {
            // Avval mavjud modellarni ko'rish
            $models = $this->listAvailableModels();
            
            if (empty($models)) {
                // Agar ListModels ishlamasa, to'g'ridan-to'g'ri test qilish
                $test_prompt = "Test";
                $result = $this->makeRequest($test_prompt, 0.7, 10);
                return ['success' => true, 'message' => 'API Key ishlayapti!'];
            }
            
            // Mavjud modellardan birini topish
            $available_models = [];
            foreach ($models as $model) {
                $model_name = $model['name'] ?? '';
                if (strpos($model_name, 'gemini') !== false) {
                    // generateContent metodini qo'llab-quvvatlaydimi?
                    $supported_methods = $model['supportedGenerationMethods'] ?? [];
                    if (in_array('generateContent', $supported_methods)) {
                        $available_models[] = str_replace('models/', '', $model_name);
                    }
                }
            }
            
            if (!empty($available_models)) {
                // Birinchi mavjud modelni ishlatish
                $this->api_model = $available_models[0];
                $test_prompt = "Test";
                $result = $this->makeRequest($test_prompt, 0.7, 10);
                return [
                    'success' => true, 
                    'message' => 'API Key ishlayapti!',
                    'available_models' => $available_models,
                    'using_model' => $this->api_model
                ];
            }
            
            return ['success' => false, 'message' => 'Mavjud modellar topilmadi'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Gemini AI ga so'rov yuborish
     */
    private function makeRequest($prompt, $temperature = 0.7, $maxTokens = 2000) {
        // v1beta versiyasida URL format: v1beta/models/{model}:generateContent
        $url = $this->api_base_url . $this->api_version . '/models/' . $this->api_model . ':generateContent?key=' . urlencode($this->api_key);
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
                'topP' => 0.8,
                'topK' => 40
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // CURL xatoliklarini tekshirish
        if ($curl_error) {
            throw new Exception('CURL xatosi: ' . $curl_error);
        }
        
        if ($http_code !== 200) {
            $error_data = json_decode($response, true);
            $error_message = $error_data['error']['message'] ?? 'Noma\'lum xatolik';
            $error_code = $error_data['error']['code'] ?? $http_code;
            
            // Debug uchun to'liq xatolik ma'lumotlari
            if (defined('APP_DEBUG') && APP_DEBUG) {
                error_log('Gemini API xatosi: HTTP ' . $http_code . ' - ' . $error_message);
                error_log('API javobi: ' . $response);
            }
            
            // API Key xatoliklari
            if ($error_code == 401 || strpos($error_message, 'API key') !== false || strpos($error_message, 'invalid') !== false) {
                throw new Exception('Gemini API Key noto\'g\'ri yoki amal qilish muddati tugagan! Iltimos, yangi API Key yarating: https://aistudio.google.com/app/apikey');
            }
            
            // Quota xatoliklari
            if ($error_code == 429 || strpos($error_message, 'quota') !== false || strpos($error_message, 'rate limit') !== false) {
                throw new Exception('Gemini API limiti yetib borgan! Iltimos, keyinroq qayta urinib ko\'ring.');
            }
            
            // Agar model topilmasa, alternativ modellarni sinab ko'rish
            if (strpos($error_message, 'not found') !== false || strpos($error_message, 'not supported') !== false) {
                return $this->tryAlternativeModels($prompt, $temperature, $maxTokens);
            }
            
            throw new Exception('Gemini API xatosi (HTTP ' . $http_code . '): ' . $error_message);
        }
        
        $result = json_decode($response, true);
        
        // Xatolikni tekshirish
        if (isset($result['error'])) {
            $error_message = $result['error']['message'] ?? 'Noma\'lum xatolik';
            
            // Agar model topilmasa, alternativ modellarni sinab ko'rish
            if (strpos($error_message, 'not found') !== false || strpos($error_message, 'not supported') !== false) {
                return $this->tryAlternativeModels($prompt, $temperature, $maxTokens);
            }
            
            throw new Exception('Gemini API xatosi: ' . $error_message);
        }
        
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            // Debug uchun to'liq javobni ko'rsatish
            if (defined('APP_DEBUG') && APP_DEBUG) {
                error_log('Gemini API javobi: ' . json_encode($result));
            }
            throw new Exception('Gemini API dan javob olinmadi. API javobi: ' . json_encode($result));
        }
        
        return $result['candidates'][0]['content']['parts'][0]['text'];
    }
    
    /**
     * Alternativ modellarni sinab ko'rish
     */
    private function tryAlternativeModels($prompt, $temperature = 0.7, $maxTokens = 2000) {
        // Avval mavjud modellarni olish
        try {
            $available_models = $this->listAvailableModels();
            if (!empty($available_models)) {
                $model_configs = [];
                foreach ($available_models as $model) {
                    $model_name = $model['name'] ?? '';
                    if (strpos($model_name, 'gemini') !== false) {
                        $supported_methods = $model['supportedGenerationMethods'] ?? [];
                        if (in_array('generateContent', $supported_methods)) {
                            $short_name = str_replace('models/', '', $model_name);
                            // Versiyani aniqlash
                            $version = (strpos($model_name, 'v1beta') !== false) ? 'v1beta' : 'v1';
                            $model_configs[] = ['version' => $version, 'model' => $short_name];
                        }
                    }
                }
                
                if (!empty($model_configs)) {
                    // Mavjud modellarni sinab ko'rish
                    return $this->tryModelConfigs($prompt, $temperature, $maxTokens, $model_configs);
                }
            }
        } catch (Exception $e) {
            // ListModels ishlamasa, oddiy ro'yxatdan foydalanish
        }
        
        // Sinab ko'riladigan alternativ modellar va versiyalar (fallback)
        $alternative_configs = [
            ['version' => 'v1beta', 'model' => 'gemini-1.5-flash'],
            ['version' => 'v1beta', 'model' => 'gemini-1.5-pro'],
            ['version' => 'v1beta', 'model' => 'gemini-1.5-flash-002'],
            ['version' => 'v1', 'model' => 'gemini-pro'],
            ['version' => 'v1beta', 'model' => 'gemini-pro'],
        ];
        
        return $this->tryModelConfigs($prompt, $temperature, $maxTokens, $alternative_configs);
    }
    
    /**
     * Model konfiguratsiyalarini sinab ko'rish
     */
    private function tryModelConfigs($prompt, $temperature, $maxTokens, $configs) {
        
        foreach ($alternative_configs as $config) {
            try {
                $old_model = $this->api_model;
                $old_version = $this->api_version;
                
                $this->api_model = $config['model'];
                $this->api_version = $config['version'];
                
                $url = $this->api_base_url . $this->api_version . '/models/' . $this->api_model . ':generateContent?key=' . urlencode($this->api_key);
                
                $data = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => $maxTokens,
                        'topP' => 0.8,
                        'topK' => 40
                    ]
                ];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curl_error = curl_error($ch);
                curl_close($ch);
                
                if ($curl_error) {
                    continue; // Keyingi modelni sinab ko'rish
                }
                
                if ($http_code === 200) {
                    $result = json_decode($response, true);
                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        // Model topildi, keyingi safar shu modeldan foydalanish
                        error_log("Gemini API: {$config['version']}/{$config['model']} modeli muvaffaqiyatli ishlatildi");
                        return $result['candidates'][0]['content']['parts'][0]['text'];
                    }
                }
            } catch (Exception $e) {
                // Keyingi modelni sinab ko'rish
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    error_log("Gemini API: {$config['version']}/{$config['model']} sinab ko'rilmoqda - xatolik: " . $e->getMessage());
                }
                continue;
            }
        }
        
        // Barcha modellar sinab ko'rilgan, xatolik
        $error_details = "Barcha modellar sinab ko'rildi:\n";
        foreach ($alternative_configs as $config) {
            $error_details .= "- {$config['version']}/{$config['model']}\n";
        }
        $error_details .= "\nMuammo: API Key ishlamayapti yoki noto'g'ri.\n\n";
        $error_details .= "Qanday tuzatish:\n";
        $error_details .= "1. https://aistudio.google.com/app/apikey ga o'ting\n";
        $error_details .= "2. Yangi API Key yarating (Create API Key)\n";
        $error_details .= "3. API Key ni nusxalang\n";
        $error_details .= "4. .env faylga qo'shing: GEMINI_API_KEY=your_api_key_here\n";
        $error_details .= "5. Server ni qayta ishga tushiring\n\n";
        $error_details .= "Eslatma: API Key 180 kun ishlatilmasa, avtomatik o'chadi. Yangi API Key yarating!";
        
        throw new Exception('Gemini API: ' . $error_details);
    }
    
    /**
     * Test savolini generatsiya qilish
     */
    public function generateQuestion($category, $language = 'uz', $context = '') {
        $language_names = [
            'uz' => 'O\'zbek',
            'ru' => 'Rus',
            'en' => 'Ingliz',
            'tr' => 'Turk'
        ];
        
        $lang_name = $language_names[$language] ?? 'O\'zbek';
        
        $prompt = "Siz professional psixologik test yaratuvchi mutaxassissiz. Kasb tanlash uchun test savoli yarating.

Kategoriya: {$category}
Til: {$lang_name}
{$context}

Talablar:
1. Savol 10-11 sinf o'quvchilariga mos bo'lsin
2. Savol aniq va tushunarli bo'lsin
3. Savol psixologik va kasbiy orientatsiya bilan bog'liq bo'lsin
4. Faqat savol matnini yozing, javob variantlarini yozmang

Savol:";
        
        try {
            $question_text = $this->makeRequest($prompt, 0.8, 500);
            // Qo'shimcha matnlarni olib tashlash
            $question_text = trim($question_text);
            $question_text = preg_replace('/^(Savol|Question|Вопрос|Soru):\s*/i', '', $question_text);
            $question_text = trim($question_text);
            
            return $question_text;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Javob variantlarini generatsiya qilish
     */
    public function generateAnswerOptions($question_text, $category, $language = 'uz', $count = 4) {
        $language_names = [
            'uz' => 'O\'zbek',
            'ru' => 'Rus',
            'en' => 'Ingliz',
            'tr' => 'Turk'
        ];
        
        $lang_name = $language_names[$language] ?? 'O\'zbek';
        
        $profession_examples = [
            'interests' => 'doctor, engineer, teacher, lawyer, businessman, artist',
            'abilities' => 'programmer, designer, manager, psychologist, analyst, writer',
            'temperament' => 'manager, researcher, teacher, consultant, athlete, designer',
            'character' => 'leader, team_player, independent, creative, responsible, empathetic',
            'stress_tolerance' => 'doctor, lawyer, manager, researcher, designer, teacher',
            'communication' => 'teacher, manager, psychologist, programmer, writer, designer',
            'analytical' => 'engineer, programmer, scientist, analyst, researcher, mathematician',
            'creativity' => 'artist, designer, writer, architect, musician, innovator'
        ];
        
        $professions = $profession_examples[$category] ?? 'doctor, engineer, teacher, lawyer, businessman, artist';
        
        $prompt = "Siz professional psixologik test yaratuvchi mutaxassissiz. Test savoli uchun javob variantlarini yarating.

Savol: {$question_text}
Kategoriya: {$category}
Til: {$lang_name}
Javoblar soni: {$count}
Kasb misollari: {$professions}

Talablar:
1. Har bir javob variant aniq va tushunarli bo'lsin
2. Javoblar bir-biridan farq qilsin
3. Har bir javob variant uchun mos kasb teglarini belgilang (masalan: doctor,nurse,engineer,programmer)
4. JSON formatida javob bering:
{
  \"options\": [
    {
      \"text\": \"Javob matni\",
      \"score\": 10,
      \"tags\": \"doctor,nurse,pharmacist\"
    }
  ]
}

Faqat JSON javob bering, boshqa matn yozmang:";
        
        try {
            $response = $this->makeRequest($prompt, 0.7, 1500);
            
            // JSON ni ajratib olish
            $json_start = strpos($response, '{');
            $json_end = strrpos($response, '}');
            
            if ($json_start !== false && $json_end !== false) {
                $json_text = substr($response, $json_start, $json_end - $json_start + 1);
                $data = json_decode($json_text, true);
                
                if (isset($data['options']) && is_array($data['options'])) {
                    return $data['options'];
                }
            }
            
            // Agar JSON topilmasa, oddiy formatdan parse qilish
            throw new Exception('JSON formatida javob olinmadi');
            
        } catch (Exception $e) {
            // Fallback: oddiy formatdan parse qilish
            $lines = explode("\n", $response);
            $options = [];
            $current_option = null;
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                if (preg_match('/^\d+[\.\)]\s*(.+)$/', $line, $matches)) {
                    if ($current_option) {
                        $options[] = $current_option;
                    }
                    $current_option = [
                        'text' => $matches[1],
                        'score' => 10,
                        'tags' => ''
                    ];
                } elseif ($current_option && preg_match('/tags?:\s*(.+)/i', $line, $matches)) {
                    $current_option['tags'] = trim($matches[1]);
                }
            }
            
            if ($current_option) {
                $options[] = $current_option;
            }
            
            if (empty($options)) {
                throw new Exception('Javob variantlari generatsiya qilinmadi');
            }
            
            return $options;
        }
    }
    
    /**
     * To'liq savol va javoblarini generatsiya qilish
     */
    public function generateFullQuestion($category, $language = 'uz', $order_number = 0) {
        try {
            // Savolni generatsiya qilish
            $question_text = $this->generateQuestion($category, $language);
            
            // Javob variantlarini generatsiya qilish
            $options = $this->generateAnswerOptions($question_text, $category, $language, 4);
            
            return [
                'question_text' => $question_text,
                'options' => $options
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Mavjud savolni boshqa tillarga tarjima qilish
     */
    public function translateQuestion($question_text, $from_language = 'uz', $to_language = 'ru') {
        $language_names = [
            'uz' => 'O\'zbek',
            'ru' => 'Rus',
            'en' => 'Ingliz',
            'tr' => 'Turk'
        ];
        
        $from = $language_names[$from_language] ?? 'O\'zbek';
        $to = $language_names[$to_language] ?? 'Rus';
        
        $prompt = "Quyidagi savolni {$from} tilidan {$to} tiliga professional tarjima qiling. Faqat tarjima qiling, boshqa matn qo'shmang.

Savol ({$from}): {$question_text}

Tarjima ({$to}):";
        
        try {
            $translated = $this->makeRequest($prompt, 0.3, 300);
            return trim($translated);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Mavjud javob variantlarini tarjima qilish
     */
    public function translateAnswerOptions($options, $from_language = 'uz', $to_language = 'ru') {
        $translated_options = [];
        
        foreach ($options as $option) {
            try {
                $translated_text = $this->translateQuestion($option['text'], $from_language, $to_language);
                $translated_options[] = [
                    'text' => $translated_text,
                    'score' => $option['score'],
                    'tags' => $option['tags']
                ];
            } catch (Exception $e) {
                // Agar tarjima qilishda xatolik bo'lsa, originalni qoldiramiz
                $translated_options[] = $option;
            }
        }
        
        return $translated_options;
    }
}

