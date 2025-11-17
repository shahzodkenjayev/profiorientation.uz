<?php
// Google Gemini AI integratsiyasi
// Test savollarini generatsiya qilish uchun

class GeminiAI {
    private $api_key;
    private $api_model = 'gemini-pro'; // Model nomi
    private $api_version = 'v1'; // API versiyasi (v1beta o'rniga v1)
    private $api_base_url = 'https://generativelanguage.googleapis.com/';
    
    public function __construct($api_key = null) {
        $this->api_key = $api_key ?? env('GEMINI_API_KEY', '');
        
        if (empty($this->api_key)) {
            throw new Exception('GEMINI_API_KEY topilmadi! .env faylga qo\'shing.');
        }
    }
    
    /**
     * Gemini AI ga so'rov yuborish
     */
    private function makeRequest($prompt, $temperature = 0.7, $maxTokens = 2000) {
        // v1 versiyasida URL format: v1/models/{model}:generateContent
        $url = $this->api_base_url . $this->api_version . '/models/' . $this->api_model . ':generateContent?key=' . $this->api_key;
        
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
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code !== 200) {
            $error_data = json_decode($response, true);
            $error_message = $error_data['error']['message'] ?? 'Noma\'lum xatolik';
            
            // Agar model topilmasa, alternativ modellarni sinab ko'rish
            if (strpos($error_message, 'not found') !== false || strpos($error_message, 'not supported') !== false) {
                // Alternativ modellarni sinab ko'rish
                return $this->tryAlternativeModels($prompt, $temperature, $maxTokens);
            }
            
            throw new Exception('Gemini API xatosi: ' . $error_message);
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

