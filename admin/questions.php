<?php
require_once '../config/config.php';
requireAdmin();

require_once '../includes/gemini.php';

$db = getDB();

$success = '';
$error = '';

// AI yordamida savol generatsiya qilish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_ai'])) {
    $category = sanitize($_POST['category'] ?? '');
    $language = sanitize($_POST['language'] ?? 'uz');
    $order_number = intval($_POST['order_number'] ?? 0);
    
    if (empty($category)) {
        $error = 'Kategoriyani tanlang!';
    } else {
        try {
            $gemini = new GeminiAI();
            
            // API Key ni test qilish
            $test_result = $gemini->testApiKey();
            if (!$test_result['success']) {
                $error = 'API Key xatosi: ' . $test_result['message'];
            } else {
                $result = $gemini->generateFullQuestion($category, $language, $order_number);
                
                // Formani to'ldirish uchun JavaScript ga yuborish
                $success = 'Savol generatsiya qilindi!';
                $_SESSION['generated_question'] = [
                    'category' => $category,
                    'question_text' => $result['question_text'],
                    'options' => $result['options'],
                    'language' => $language,
                    'order_number' => $order_number
                ];
            }
        } catch (Exception $e) {
            $error = 'AI xatosi: ' . $e->getMessage();
            
            // Agar API Key muammosi bo'lsa, batafsil ko'rsatish
            if (strpos($e->getMessage(), 'API Key') !== false || strpos($e->getMessage(), 'Barcha modellar') !== false) {
                $error .= "\n\n<b>Qanday tuzatish:</b>\n";
                $error .= "1. https://aistudio.google.com/app/apikey ga o'ting\n";
                $error .= "2. Yangi API Key yarating\n";
                $error .= "3. .env faylga qo'shing: GEMINI_API_KEY=your_api_key\n";
                $error .= "4. Server ni qayta ishga tushiring";
            }
        }
    }
}

// Savol qo'shish (qo'lda yoki AI generatsiya qilingan)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $category = sanitize($_POST['category'] ?? '');
    $question_text = sanitize($_POST['question_text'] ?? '');
    $question_type = sanitize($_POST['question_type'] ?? 'multiple_choice');
    $language = sanitize($_POST['language'] ?? 'uz');
    $order_number = intval($_POST['order_number'] ?? 0);
    
    if (empty($category) || empty($question_text)) {
        $error = 'Barcha maydonlarni to\'ldiring!';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO questions (category, question_text, question_type, language, order_number) 
                                 VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$category, $question_text, $question_type, $language, $order_number]);
            $question_id = $db->lastInsertId();
            
            // Javob variantlarini qo'shish
            if (isset($_POST['options']) && is_array($_POST['options'])) {
                foreach ($_POST['options'] as $index => $option_data) {
                    if (!empty($option_data['text'])) {
                        $stmt = $db->prepare("INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) 
                                             VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $question_id,
                            sanitize($option_data['text']),
                            intval($option_data['score'] ?? 0),
                            sanitize($option_data['tags'] ?? ''),
                            $language,
                            $index + 1
                        ]);
                    }
                }
            }
            
            $success = 'Savol muvaffaqiyatli qo\'shildi!';
            
            // Generated question session ni tozalash
            if (isset($_SESSION['generated_question'])) {
                unset($_SESSION['generated_question']);
            }
        } catch (PDOException $e) {
            $error = 'Xatolik: ' . $e->getMessage();
        }
    }
}

// Mavjud savolni boshqa tillarga tarjima qilish
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['translate_question'])) {
    $question_id = intval($_POST['question_id'] ?? 0);
    $target_language = sanitize($_POST['target_language'] ?? '');
    
    if ($question_id > 0 && !empty($target_language)) {
        try {
            // Asosiy savolni olish
            $stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
            $stmt->execute([$question_id]);
            $original_question = $stmt->fetch();
            
            if ($original_question) {
                $gemini = new GeminiAI();
                
                // Savolni tarjima qilish
                $translated_text = $gemini->translateQuestion(
                    $original_question['question_text'],
                    $original_question['language'],
                    $target_language
                );
                
                // Tarjima qilingan savolni saqlash
                $stmt = $db->prepare("INSERT INTO questions (category, question_text, question_type, language, order_number) 
                                     VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $original_question['category'],
                    $translated_text,
                    $original_question['question_type'],
                    $target_language,
                    $original_question['order_number']
                ]);
                $new_question_id = $db->lastInsertId();
                
                // Javob variantlarini olish va tarjima qilish
                $stmt = $db->prepare("SELECT * FROM answer_options WHERE question_id = ? ORDER BY order_number");
                $stmt->execute([$question_id]);
                $original_options = $stmt->fetchAll();
                
                $translated_options = $gemini->translateAnswerOptions(
                    $original_options,
                    $original_question['language'],
                    $target_language
                );
                
                // Tarjima qilingan javob variantlarini saqlash
                foreach ($translated_options as $index => $option) {
                    $stmt = $db->prepare("INSERT INTO answer_options (question_id, option_text, score, profession_tags, language, order_number) 
                                         VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $new_question_id,
                        $option['text'],
                        $option['score'],
                        $option['tags'],
                        $target_language,
                        $index + 1
                    ]);
                }
                
                $success = 'Savol muvaffaqiyatli tarjima qilindi!';
            }
        } catch (Exception $e) {
            $error = 'Tarjima xatosi: ' . $e->getMessage();
        }
    }
}

// Savollarni olish (barcha tillar)
$stmt = $db->query("SELECT q.*, 
                    (SELECT COUNT(*) FROM answer_options WHERE question_id = q.id AND language = q.language) as option_count
                    FROM questions q 
                    ORDER BY q.category, q.order_number, q.language");
$questions = $stmt->fetchAll();

// Kategoriyalar
$categories = [
    'interests' => 'Qiziqishlar',
    'abilities' => 'Qobiliyatlar',
    'temperament' => 'Temperament',
    'character' => 'Xarakter',
    'stress_tolerance' => 'Stressga chidamlilik',
    'communication' => 'Muloqot',
    'analytical' => 'Analitik fikrlash',
    'creativity' => 'Ijodiy qobiliyatlar',
    'work_environment' => 'Ish muhiti',
    'work_schedule' => 'Ish jadvali',
    'motivation' => 'Motivatsiya',
    'difficulty' => 'Qiyinchilik',
    'impact' => 'Ta\'sir',
    'career_goals' => 'Karyera maqsadlari',
    'values' => 'Qadriyatlar',
    'work_life_balance' => 'Ish-hayot muvozanati',
    'learning' => 'O\'qish',
    'general_interest' => 'Umumiy qiziqish'
];

$languages = [
    'uz' => 'O\'zbek',
    'ru' => 'Rus',
    'en' => 'Ingliz',
    'tr' => 'Turk'
];

$has_gemini = !empty(GEMINI_API_KEY);
?>
<!DOCTYPE html>
<html lang="<?= Language::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savollar - Admin</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <style>
        .ai-generator-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .ai-generator-card h3 {
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ai-generator-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .form-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-tab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
            color: #666;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }
        .form-tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .language-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 5px;
        }
        .lang-uz { background: #4CAF50; color: white; }
        .lang-ru { background: #2196F3; color: white; }
        .lang-en { background: #FF9800; color: white; }
        .lang-tr { background: #9C27B0; color: white; }
        .translate-btn {
            padding: 5px 10px;
            font-size: 12px;
            margin-left: 5px;
        }
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Test Savollari</h1>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- AI Generator Card -->
            <?php if ($has_gemini): ?>
            <div class="ai-generator-card">
                <h3>
                    <i class="ri-robot-line"></i>
                    AI yordamida savol generatsiya qilish
                </h3>
                <form method="POST" id="aiGeneratorForm">
                    <input type="hidden" name="generate_ai" value="1">
                    <div class="ai-generator-form">
                        <div>
                            <label style="color: rgba(255,255,255,0.9); display: block; margin-bottom: 5px;">Kategoriya</label>
                            <select name="category" required style="width: 100%; padding: 8px; border-radius: 5px; border: none;">
                                <option value="">Tanlang</option>
                                <?php foreach ($categories as $key => $cat): ?>
                                    <option value="<?= $key ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="color: rgba(255,255,255,0.9); display: block; margin-bottom: 5px;">Til</label>
                            <select name="language" required style="width: 100%; padding: 8px; border-radius: 5px; border: none;">
                                <?php foreach ($languages as $key => $lang): ?>
                                    <option value="<?= $key ?>" <?= $key === 'uz' ? 'selected' : '' ?>><?= $lang ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label style="color: rgba(255,255,255,0.9); display: block; margin-bottom: 5px;">Tartib raqami</label>
                            <input type="number" name="order_number" value="0" min="0" style="width: 100%; padding: 8px; border-radius: 5px; border: none;">
                        </div>
                        <div style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn-primary" style="width: 100%; background: white; color: #667eea; border: none;">
                                <i class="ri-magic-line"></i> Generatsiya qilish
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="alert alert-error">
                <i class="ri-error-warning-line"></i>
                Gemini API Key topilmadi! .env faylga GEMINI_API_KEY qo'shing.
            </div>
            <?php endif; ?>
            
            <!-- Form Tabs -->
            <div class="form-tabs">
                <button type="button" class="form-tab active" data-tab="manual">
                    <i class="ri-edit-line"></i> Qo'lda qo'shish
                </button>
                <?php if ($has_gemini): ?>
                <button type="button" class="form-tab" data-tab="ai">
                    <i class="ri-robot-line"></i> AI generatsiya qilingan
                </button>
                <?php endif; ?>
            </div>
            
            <!-- Manual Form -->
            <div class="tab-content active" id="manual-tab">
                <div class="admin-card">
                    <h2>Yangi savol qo'shish (Qo'lda)</h2>
                    <form method="POST" id="questionForm">
                        <input type="hidden" name="add_question" value="1">
                        
                        <div class="form-group">
                            <label>Kategoriya</label>
                            <select name="category" required>
                                <option value="">Tanlang</option>
                                <?php foreach ($categories as $key => $cat): ?>
                                    <option value="<?= $key ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Til</label>
                            <select name="language" required>
                                <?php foreach ($languages as $key => $lang): ?>
                                    <option value="<?= $key ?>" <?= $key === 'uz' ? 'selected' : '' ?>><?= $lang ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Savol matni</label>
                            <textarea name="question_text" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Savol turi</label>
                            <select name="question_type">
                                <option value="multiple_choice">Ko'p tanlovli</option>
                                <option value="scale">Shkala</option>
                                <option value="yes_no">Ha/Yo'q</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Tartib raqami</label>
                            <input type="number" name="order_number" value="0" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label>Javob variantlari</label>
                            <div id="options-container">
                                <div class="option-row">
                                    <input type="text" name="options[0][text]" placeholder="Variant matni" required>
                                    <input type="number" name="options[0][score]" placeholder="Ball" value="0" min="0" max="100" required>
                                    <input type="text" name="options[0][tags]" placeholder="Kasb teglari (doctor,nurse,engineer)">
                                </div>
                            </div>
                            <button type="button" id="add-option" class="btn-secondary">+ Variant qo'shish</button>
                        </div>
                        
                        <button type="submit" class="btn-primary">Qo'shish</button>
                    </form>
                </div>
            </div>
            
            <!-- AI Generated Form -->
            <?php if ($has_gemini): ?>
            <div class="tab-content" id="ai-tab">
                <div class="admin-card">
                    <h2>AI generatsiya qilingan savol</h2>
                    <?php if (isset($_SESSION['generated_question'])): 
                        $gen = $_SESSION['generated_question'];
                    ?>
                    <form method="POST" id="aiQuestionForm">
                        <input type="hidden" name="add_question" value="1">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($gen['category']) ?>">
                        <input type="hidden" name="language" value="<?= htmlspecialchars($gen['language']) ?>">
                        <input type="hidden" name="order_number" value="<?= $gen['order_number'] ?>">
                        <input type="hidden" name="question_type" value="multiple_choice">
                        
                        <div class="form-group">
                            <label>Kategoriya</label>
                            <input type="text" value="<?= htmlspecialchars($categories[$gen['category']] ?? $gen['category']) ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label>Til</label>
                            <input type="text" value="<?= htmlspecialchars($languages[$gen['language']] ?? $gen['language']) ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label>Savol matni</label>
                            <textarea name="question_text" rows="3" required><?= htmlspecialchars($gen['question_text']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Javob variantlari</label>
                            <div id="ai-options-container">
                                <?php foreach ($gen['options'] as $index => $option): ?>
                                <div class="option-row">
                                    <input type="text" name="options[<?= $index ?>][text]" value="<?= htmlspecialchars($option['text']) ?>" required>
                                    <input type="number" name="options[<?= $index ?>][score]" value="<?= $option['score'] ?? 10 ?>" min="0" max="100" required>
                                    <input type="text" name="options[<?= $index ?>][tags]" value="<?= htmlspecialchars($option['tags'] ?? '') ?>" placeholder="Kasb teglari">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="add-ai-option" class="btn-secondary">+ Variant qo'shish</button>
                        </div>
                        
                        <button type="submit" class="btn-primary">Saqlash</button>
                        <button type="button" class="btn-secondary" onclick="location.reload()">Bekor qilish</button>
                    </form>
                    <?php else: ?>
                    <p>AI yordamida savol generatsiya qilish uchun yuqoridagi formadan foydalaning.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Questions List -->
            <div class="admin-card">
                <h2>Mavjud savollar (<?= count($questions) ?>)</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategoriya</th>
                            <th>Savol</th>
                            <th>Til</th>
                            <th>Turi</th>
                            <th>Variantlar</th>
                            <th>Tartib</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $q): ?>
                            <tr>
                                <td><?= $q['id'] ?></td>
                                <td><?= htmlspecialchars($categories[$q['category']] ?? $q['category']) ?></td>
                                <td><?= htmlspecialchars(mb_substr($q['question_text'], 0, 50)) ?>...</td>
                                <td>
                                    <span class="language-badge lang-<?= $q['language'] ?>">
                                        <?= strtoupper($q['language']) ?>
                                    </span>
                                </td>
                                <td><?= $q['question_type'] ?></td>
                                <td><?= $q['option_count'] ?></td>
                                <td><?= $q['order_number'] ?></td>
                                <td>
                                    <?php if ($has_gemini): ?>
                                    <?php 
                                    $missing_langs = array_diff(['uz', 'ru', 'en', 'tr'], [$q['language']]);
                                    foreach ($missing_langs as $lang): 
                                    ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('<?= $languages[$lang] ?> tiliga tarjima qilishni tasdiqlaysizmi?');">
                                        <input type="hidden" name="translate_question" value="1">
                                        <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                                        <input type="hidden" name="target_language" value="<?= $lang ?>">
                                        <button type="submit" class="translate-btn btn-secondary" title="<?= $languages[$lang] ?> tiliga tarjima qilish">
                                            <i class="ri-translate-2"></i> <?= strtoupper($lang) ?>
                                        </button>
                                    </form>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Tab switching
        document.querySelectorAll('.form-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabName = this.dataset.tab;
                
                // Remove active class from all tabs and contents
                document.querySelectorAll('.form-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(tabName + '-tab').classList.add('active');
            });
        });
        
        // Auto-fill form if AI generated question exists
        <?php if (isset($_SESSION['generated_question'])): ?>
        document.querySelector('[data-tab="ai"]').click();
        <?php endif; ?>
        
        // Options management
        let optionIndex = <?= isset($_SESSION['generated_question']) ? count($_SESSION['generated_question']['options']) : 1 ?>;
        
        // Manual form options
        document.getElementById('add-option')?.addEventListener('click', function() {
            const container = document.getElementById('options-container');
            const newRow = document.createElement('div');
            newRow.className = 'option-row';
            newRow.innerHTML = `
                <input type="text" name="options[${optionIndex}][text]" placeholder="Variant matni" required>
                <input type="number" name="options[${optionIndex}][score]" placeholder="Ball" value="0" min="0" max="100" required>
                <input type="text" name="options[${optionIndex}][tags]" placeholder="Kasb teglari">
                <button type="button" class="btn-danger btn-small remove-option">O'chirish</button>
            `;
            container.appendChild(newRow);
            optionIndex++;
            
            newRow.querySelector('.remove-option').addEventListener('click', function() {
                newRow.remove();
            });
        });
        
        // AI form options
        document.getElementById('add-ai-option')?.addEventListener('click', function() {
            const container = document.getElementById('ai-options-container');
            const newRow = document.createElement('div');
            newRow.className = 'option-row';
            newRow.innerHTML = `
                <input type="text" name="options[${optionIndex}][text]" placeholder="Variant matni" required>
                <input type="number" name="options[${optionIndex}][score]" placeholder="Ball" value="10" min="0" max="100" required>
                <input type="text" name="options[${optionIndex}][tags]" placeholder="Kasb teglari">
                <button type="button" class="btn-danger btn-small remove-option">O'chirish</button>
            `;
            container.appendChild(newRow);
            optionIndex++;
            
            newRow.querySelector('.remove-option').addEventListener('click', function() {
                newRow.remove();
            });
        });
        
        // Remove option handlers
        document.querySelectorAll('.remove-option').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.option-row').remove();
            });
        });
        
        // AI Generator form loading
        document.getElementById('aiGeneratorForm')?.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span> Generatsiya qilinmoqda...';
            btn.disabled = true;
        });
    </script>
    
    <style>
        .option-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .option-row input {
            flex: 1;
        }
    </style>
</body>
</html>
